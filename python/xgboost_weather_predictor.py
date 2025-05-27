from flask import Flask, request, jsonify
import xgboost as xgb
import numpy as np
import pandas as pd
from sklearn.preprocessing import LabelEncoder
from datetime import datetime, timedelta
import logging

# Cấu hình logging
logging.basicConfig(
    filename='app.log',
    level=logging.INFO,
    format='%(asctime)s - %(levelname)s - %(message)s'
)

app = Flask(__name__)

# Khởi tạo các mô hình XGBoost
model_temp = xgb.XGBRegressor(objective='reg:squarederror', n_estimators=100, learning_rate=0.1, max_depth=5)
model_humidity = xgb.XGBRegressor(objective='reg:squarederror', n_estimators=100, learning_rate=0.1, max_depth=5)
model_wind = xgb.XGBRegressor(objective='reg:squarederror', n_estimators=100, learning_rate=0.1, max_depth=5)
model_condition = None
le_condition = LabelEncoder()

# Hàm chuẩn hóa điều kiện thời tiết
def map_description_to_main(description):
    if not isinstance(description, str):
        logging.warning(f"Invalid description type: {type(description)}, returning 'unknown'")
        return 'unknown'
    description = description.lower().strip()
    logging.info(f"Mapping description '{description}'")
    
    if 'clear sky' in description:
        logging.info(f"Matched 'clear sky' -> 'clear'")
        return 'clear'
    if any(s in description for s in ['few clouds', 'scattered clouds', 'broken clouds', 'overcast clouds']):
        logging.info(f"Matched clouds -> 'clouds'")
        return 'clouds'
    if any(s in description for s in ['light rain', 'moderate rain', 'heavy intensity rain', 'very heavy rain', 'extreme rain', 'freezing rain', 'light intensity shower rain', 'shower rain', 'heavy intensity shower rain', 'ragged shower rain']):
        logging.info(f"Matched rain -> 'rain'")
        return 'rain'
    if any(s in description for s in ['light snow', 'snow', 'heavy snow', 'sleet', 'light shower sleet', 'shower sleet', 'light rain and snow', 'rain and snow', 'light shower snow', 'shower snow', 'heavy shower snow']):
        logging.info(f"Matched snow -> 'snow'")
        return 'snow'
    if any(s in description for s in ['thunderstorm with light rain', 'thunderstorm with rain', 'thunderstorm with heavy rain', 'light thunderstorm', 'thunderstorm', 'heavy thunderstorm', 'ragged thunderstorm', 'thunderstorm with light drizzle', 'thunderstorm with drizzle', 'thunderstorm with heavy drizzle']):
        logging.info(f"Matched thunderstorm -> 'thunderstorm'")
        return 'thunderstorm'
    if any(s in description for s in ['light intensity drizzle', 'drizzle', 'heavy intensity drizzle', 'light intensity drizzle rain', 'drizzle rain', 'heavy intensity drizzle rain', 'shower rain and drizzle', 'heavy shower rain and drizzle', 'shower drizzle']):
        logging.info(f"Matched drizzle -> 'drizzle'")
        return 'drizzle'
    if any(s in description for s in ['mist', 'smoke', 'haze', 'sand/dust whirls', 'fog', 'sand', 'dust', 'volcanic ash', 'squalls', 'tornado']):
        logging.info(f"Matched mist -> 'mist'")
        return 'mist'
    logging.warning(f"No match found for '{description}', returning 'unknown'")
    return 'unknown'

# Hàm huấn luyện mô hình
def train_models(data):
    global model_condition, le_condition
    if not data or not isinstance(data, list):
        logging.error("Dữ liệu lịch sử trống hoặc không hợp lệ")
        raise ValueError("Dữ liệu lịch sử trống hoặc không hợp lệ")

    required_columns = ['date', 'temperature', 'humidity', 'wind_speed', 'condition']
    df = pd.DataFrame(data)
    missing_columns = [col for col in required_columns if col not in df.columns]
    if missing_columns:
        logging.error(f"Thiếu các cột dữ liệu: {missing_columns}")
        raise ValueError(f"Thiếu các cột dữ liệu: {missing_columns}")

    logging.info(f"Dữ liệu đầu vào: {df.head()}")

    # Chuẩn hóa condition
    df['condition'] = df['condition'].apply(map_description_to_main)

    # Tạo đặc trưng bổ sung
    df['date'] = pd.to_datetime(df['date'])
    df['day_of_week'] = df['date'].dt.dayofweek + 1
    df['month'] = df['date'].dt.month

    # Tạo các đặc trưng trễ
    df['temp_lag1'] = df['temperature'].shift(1).fillna(df['temperature'].mean())
    df['humidity_lag1'] = df['humidity'].shift(1).fillna(df['humidity'].mean())
    df['wind_speed_lag1'] = df['wind_speed'].shift(1).fillna(df['wind_speed'].mean())

    # Log columns and conditions for debugging
    logging.info(f"DataFrame columns after feature engineering: {df.columns.tolist()}")
    logging.info(f"Unique conditions after mapping: {df['condition'].unique()}")

    # Kiểm tra tính đa dạng của condition
    unique_conditions = df['condition'].nunique()
    if unique_conditions < 2:
        logging.warning(f"Chỉ có {unique_conditions} điều kiện duy nhất. Không huấn luyện model_condition.")
        condition_default = df['condition'].iloc[0] if not df.empty else 'unknown'
        model_condition = None
        le_condition.fit([condition_default])
    elif unique_conditions == 0:
        logging.error("Không có điều kiện hợp lệ để huấn luyện model_condition.")
        model_condition = None
        le_condition.fit(['unknown'])
    else:
        try:
            model_condition = xgb.XGBClassifier(n_estimators=100, learning_rate=0.1, max_depth=5, objective='multi:softprob')
            y_condition = le_condition.fit_transform(df['condition'])
            X = df[['day_of_week', 'month', 'temp_lag1', 'humidity_lag1', 'wind_speed_lag1']].fillna(0)
            model_condition.fit(X, y_condition)
            logging.info(f"Huấn luyện model_condition với {unique_conditions} lớp: {le_condition.classes_}")
        except ValueError as e:
            logging.error(f"Lỗi huấn luyện model_condition: {str(e)}. Đặt model_condition là None.")
            model_condition = None
            le_condition.fit(['unknown'])

    # Huấn luyện các mô hình khác
    X = df[['day_of_week', 'month', 'temp_lag1', 'humidity_lag1', 'wind_speed_lag1']].fillna(0)
    y_temp = df['temperature'].astype(float)
    y_humidity = df['humidity'].astype(float)
    y_wind = df['wind_speed'].astype(float)

    model_temp.fit(X, y_temp)
    model_humidity.fit(X, y_humidity)
    model_wind.fit(X, y_wind)
    logging.info("Huấn luyện mô hình thành công")

# Hàm dự đoán
def predict_future(data, days_ahead, current_data):
    df = pd.DataFrame(data)
    df['date'] = pd.to_datetime(df['date'])
    recent_data = df.iloc[0].to_dict() if not df.empty else {}
    recent_data.update({
        'temperature': float(current_data.get('current_temp', recent_data.get('temperature', 0))),
        'humidity': float(current_data.get('current_humidity', recent_data.get('humidity', 0))),
        'wind_speed': float(current_data.get('current_wind_speed', recent_data.get('wind_speed', 0))),
        'condition': map_description_to_main(current_data.get('current_condition', recent_data.get('condition', 'unknown')))
    })

    future_predictions = []
    current_date = datetime.now()

    for i in range(days_ahead):
        next_date = current_date + timedelta(days=i + 1)
        X_future = pd.DataFrame([{
            'day_of_week': next_date.isoweekday(),
            'month': next_date.month,
            'temp_lag1': float(recent_data['temperature']),
            'humidity_lag1': float(recent_data['humidity']),
            'wind_speed_lag1': float(recent_data['wind_speed'])
        }])

        # Dự đoán
        temp_pred = model_temp.predict(X_future)[0]
        humidity_pred = model_humidity.predict(X_future)[0]
        wind_pred = model_wind.predict(X_future)[0]

        # Xử lý điều kiện
        if model_condition is not None:
            condition_pred = le_condition.inverse_transform(model_condition.predict(X_future))[0]
        else:
            condition_pred = le_condition.classes_[0]

        # Ép kiểu rõ ràng thành kiểu Python cơ bản
        temp_pred = int(float(temp_pred))
        humidity_pred = float(humidity_pred)
        wind_pred = float(wind_pred)
        condition_pred = str(condition_pred)

        # Làm tròn giá trị
        temp_pred = int(round(temp_pred))
        humidity_pred = round(humidity_pred, 2)
        wind_pred = round(wind_pred, 2)

        logging.info(f"Dự đoán ngày {next_date}: temp={temp_pred}, humidity={humidity_pred}, wind={wind_pred}, condition={condition_pred}")

        # Cập nhật dữ liệu gần nhất
        recent_data.update({
            'temperature': temp_pred,
            'humidity': humidity_pred,
            'wind_speed': wind_pred,
            'condition': condition_pred
        })

        future_predictions.append({
            'date': next_date.strftime('%Y-%m-%d'),
            'temperature': temp_pred,
            'humidity': humidity_pred,
            'wind_speed': wind_pred,
            'condition': condition_pred
        })

    return future_predictions

# API endpoint
@app.route('/predict', methods=['POST'])
def predict():
    try:
        data = request.get_json()
        logging.info(f"Yêu cầu nhận được: {data}")
        if not data or 'historical_data' not in data or 'days_ahead' not in data:
            logging.error("Dữ liệu không hợp lệ hoặc thiếu historical_data/days_ahead")
            return jsonify({'error': 'Dữ liệu không hợp lệ hoặc thiếu historical_data/days_ahead'}), 400

        # Huấn luyện mô hình với dữ liệu lịch sử
        train_models(data['historical_data'])

        # Dự đoán cho các ngày tới
        # Số ngày muốn dự báo
        days_ahead = min(max(int(data['days_ahead']), 1), 7)
        predictions = predict_future(data['historical_data'], days_ahead, {
            'current_temp': data.get('current_temp', 0),
            'current_humidity': data.get('current_humidity', 0),
            'current_wind_speed': data.get('current_wind_speed', 0),
            'current_condition': data.get('current_condition', 'unknown')
        })

        logging.info(f"Dự đoán cuối cùng: {predictions}")
        return jsonify({'predictions': predictions})
    except Exception as e:
        logging.error(f"Lỗi trong predict: {str(e)}")
        return jsonify({'error': str(e)}), 500

if __name__ == '__main__':
    app.run(debug=True, host='0.0.0.0', port=5000)