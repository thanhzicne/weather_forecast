<?php
require_once __DIR__ . '/../config/database.php';

class WeatherPredictor {
    private $db;

    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();

        if (!$this->db) {
            $_SESSION['error'] = 'Không thể kết nối đến cơ sở dữ liệu.';
            header('Location: /weather_forecast/auth/login');
            exit;
        }
    }

    private function mapDescriptionToMain($description) {
        if (!is_string($description)) {
            return 'broken';
        }
        $description = strtolower($description);

        // Trả về ngay giá trị nếu đã chuẩn hóa từ Flask
        $validConditions = ['clear', 'clouds', 'rain', 'thunderstorm', 'snow', 'drizzle', 'mist'];
        if (in_array($description, $validConditions)) {
            return $description;
        }

        // Xử lý các mô tả chi tiết từ OpenWeatherMap
        // Rain conditions
        if (strpos($description, 'light rain') !== false || strpos($description, 'moderate rain') !== false || strpos($description, 'light intensity shower rain') !== false || strpos($description, 'shower rain') !== false) {
            return 'light_rain';
        }
        if (strpos($description, 'heavy intensity rain') !== false || strpos($description, 'very heavy rain') !== false || strpos($description, 'extreme rain') !== false || strpos($description, 'heavy intensity shower rain') !== false || strpos($description, 'ragged shower rain') !== false || strpos($description, 'freezing rain') !== false) {
            return 'heavy_rain';
        }

        // Cloud conditions
        if (strpos($description, 'broken clouds') !== false) {
            return 'broken';
        }
        if (strpos($description, 'overcast clouds') !== false) {
            return 'overcast';
        }

        // Clear conditions
        if (strpos($description, 'clear sky') !== false || strpos($description, 'few clouds') !== false || strpos($description, 'scattered clouds') !== false) {
            return 'clear';
        }

        // Snow conditions
        if (strpos($description, 'light snow') !== false || strpos($description, 'snow') !== false || strpos($description, 'light shower snow') !== false || strpos($description, 'shower snow') !== false) {
            return 'snow';
        }
        if (strpos($description, 'heavy snow') !== false || strpos($description, 'heavy shower snow') !== false || strpos($description, 'sleet') !== false || strpos($description, 'light shower sleet') !== false || strpos($description, 'shower sleet') !== false || strpos($description, 'light rain and snow') !== false || strpos($description, 'rain and snow') !== false) {
            return 'heavy_snow';
        }

        // Thunderstorm conditions
        if (strpos($description, 'thunderstorm') !== false || strpos($description, 'light thunderstorm') !== false || strpos($description, 'thunderstorm with light rain') !== false) {
            return 'thunderstorm';
        }
        if (strpos($description, 'thunderstorm with rain') !== false || strpos($description, 'thunderstorm with heavy rain') !== false || strpos($description, 'heavy thunderstorm') !== false || strpos($description, 'ragged thunderstorm') !== false) {
            return 'heavy_thunderstorm';
        }

        // Drizzle conditions
        if (strpos($description, 'light intensity drizzle') !== false || strpos($description, 'drizzle') !== false || strpos($description, 'light intensity drizzle rain') !== false || strpos($description, 'shower drizzle') !== false || strpos($description, 'light shower drizzle') !== false) {
            return 'drizzle';
        }
        if (strpos($description, 'heavy intensity drizzle') !== false || strpos($description, 'heavy intensity drizzle rain') !== false || strpos($description, 'heavy shower drizzle') !== false || strpos($description, 'ragged shower drizzle') !== false) {
            return 'heavy_drizzle';
        }

        // Mist/Fog conditions
        if (strpos($description, 'mist') !== false || strpos($description, 'fog') !== false || strpos($description, 'smoke') !== false || strpos($description, 'haze') !== false || strpos($description, 'sand/dust whirls') !== false || strpos($description, 'sand') !== false || strpos($description, 'dust') !== false || strpos($description, 'volcanic ash') !== false || strpos($description, 'squalls') !== false || strpos($description, 'tornado') !== false) {
            return 'mist';
        }

        return 'broken'; // Mặc định nếu không khớp
    }

    private function getCurrentWeatherData($city) {
        $apiKey = 'e2c222797835642c23ca9f8d6fda7d2b';
        $url = "http://api.openweathermap.org/data/2.5/weather?q=" . urlencode($city) . "&appid=" . $apiKey . "&units=metric";
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode !== 200 || !$response) {
            error_log("Lỗi khi lấy dữ liệu thời tiết hiện tại cho $city: HTTP $httpCode", 3, __DIR__ . '/../logs/errors.log');
            return [
                'main' => ['temp' => 0, 'humidity' => 0],
                'wind' => ['speed' => 0],
                'weather' => [['description' => 'broken']]
            ];
        }

        $weatherData = json_decode($response, true);
        
        if (isset($weatherData['cod']) && $weatherData['cod'] !== 200) {
            error_log("Lỗi API OpenWeatherMap cho $city: " . ($weatherData['message'] ?? 'Unknown error'), 3, __DIR__ . '/../logs/errors.log');
            return [
                'main' => ['temp' => 0, 'humidity' => 0],
                'wind' => ['speed' => 0],
                'weather' => [['description' => 'broken']]
            ];
        }

        return [
            'main' => [
                'temp' => $weatherData['main']['temp'] ?? 0,
                'humidity' => $weatherData['main']['humidity'] ?? 0
            ],
            'wind' => [
                'speed' => $weatherData['wind']['speed'] ?? 0
            ],
            'weather' => [
                ['description' => $weatherData['weather'][0]['description'] ?? 'broken']
            ]
        ];
    }

    private function callFlaskAI($payload) {
        $flaskUrl = 'http://localhost:5000/predict';

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $flaskUrl);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30); // Thêm timeout để tránh treo
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        error_log("Flask Response for $flaskUrl (HTTP $httpCode): " . $response, 3, __DIR__ . '/../logs/debug.log');

        if ($httpCode !== 200) {
            error_log("Lỗi khi gọi Flask AI: HTTP $httpCode - $response", 3, __DIR__ . '/../logs/errors.log');
            return [
                'success' => false,
                'message' => "Lỗi HTTP $httpCode - $response"
            ];
        }

        $data = json_decode($response, true);
        if (isset($data['error'])) {
            error_log("Lỗi từ Flask AI: " . $data['error'], 3, __DIR__ . '/../logs/errors.log');
            return [
                'success' => false,
                'message' => $data['error']
            ];
        }

        if (empty($data['predictions'])) {
            error_log("Flask trả về predictions rỗng cho payload: " . json_encode($payload), 3, __DIR__ . '/../logs/errors.log');
            return [
                'success' => false,
                'message' => 'Dự đoán từ Flask rỗng'
            ];
        }

        return [
            'success' => true,
            'data' => $data['predictions']
        ];
    }

    private function predictForCity($city, $days_ahead) {
        $weatherDescriptions = [
            "clear" => "Trời quang",
            "clouds" => "Có mây",
            "rain" => "Có mưa",
            "thunderstorm" => "Dông",
            "snow" => "Tuyết",
            "drizzle" => "Mưa phùn",
            "mist" => "Sương mù",
            'broken' => 'Có mây'
        ];
        // 
        
        // Lấy dữ liệu lịch sử từ cơ sở dữ liệu
        $stmtHistory = $this->db->prepare(
            "SELECT date, temperature, humidity, `condition`, wind_speed 
            FROM weather_history 
            WHERE city = :city AND date <= CURDATE() 
            ORDER BY date DESC "
        );
        $stmtHistory->bindValue(':city', $city, PDO::PARAM_STR);
        $stmtHistory->execute();
        $historicalData = $stmtHistory->fetchAll(PDO::FETCH_ASSOC);

        if (empty($historicalData)) {
            error_log("Không có dữ liệu lịch sử cho tỉnh $city.", 3, __DIR__ . '/../logs/errors.log');
            $_SESSION['error'] = "Không có dữ liệu lịch sử cho $city.";
            return [];
        }

        // Lấy dữ liệu hiện tại
        $weatherData = $this->getCurrentWeatherData($city);
        $currentTemp = floatval($weatherData['main']['temp'] ?? 0);
        $currentHumidity = floatval($weatherData['main']['humidity'] ?? 0);
        $currentCondition = $weatherData['weather'][0]['description'] ?? 'broken';
        $currentWindSpeed = floatval($weatherData['wind']['speed'] ?? 0);

        // Chuẩn bị payload cho Flask
        $payload = [
            'historical_data' => array_map(function ($row) {
                return [
                    'date' => $row['date'],
                    'temperature' => floatval($row['temperature']),
                    'humidity' => floatval($row['humidity']),
                    'wind_speed' => floatval($row['wind_speed']),
                    'condition' => $row['condition']
                ];
            }, $historicalData),
            'current_temp' => $currentTemp,
            'current_humidity' => $currentHumidity,
            'current_condition' => $currentCondition,
            'current_wind_speed' => $currentWindSpeed,
            'days_ahead' => $days_ahead
        ];

        error_log("Payload gửi đến Flask cho $city: " . json_encode($payload), 3, __DIR__ . '/../logs/debug.log');

        // Gọi Flask để dự đoán
        $flaskResponse = $this->callFlaskAI($payload);

        if (!$flaskResponse['success']) {
            error_log("Lỗi từ Flask AI cho tỉnh $city: " . ($flaskResponse['message'] ?? 'Unknown error'), 3, __DIR__ . '/../logs/errors.log');
            $_SESSION['error'] = "Không thể dự đoán cho $city: " . ($flaskResponse['message'] ?? 'Lỗi không xác định.');
            return [];
        }

        $stmtInsert = $this->db->prepare(
            "INSERT INTO forecast (city, date, temperature, humidity, `condition`, `condition_vi`, wind_speed, created_at) 
            VALUES (:city, :date, :temperature, :humidity, :condition, :condition_vi, :wind_speed, NOW()) 
            ON DUPLICATE KEY UPDATE 
                temperature = :temperature_update, 
                humidity = :humidity_update, 
                `condition` = :condition_update, 
                `condition_vi` = :condition_vi_update, 
                wind_speed = :wind_speed_update, 
                created_at = NOW()"
        );

        $cityPredictions = [];
        foreach ($flaskResponse['data'] as $predictedData) {
            $predictedTemp = intval($predictedData['temperature']);
            $predictedHumidity = floatval($predictedData['humidity']);
            $predictedCondition = $predictedData['condition'] ?? 'broken';
            $predictedWindSpeed = floatval($predictedData['wind_speed']);
            $date = $predictedData['date'];

            // Chuẩn hóa định dạng ngày thành DD/MM/YYYY
            $formattedDate = (new DateTime($date))->format('d/m/Y');

            // Chuẩn hóa điều kiện thời tiết
            $normalizedCondition = $this->mapDescriptionToMain($predictedCondition);

            // Ánh xạ điều kiện sang tiếng Việt
            $predictedConditionVi = $weatherDescriptions[$normalizedCondition] ?? $weatherDescriptions['broken'];

            // Lưu vào bảng forecast
            $stmtInsert->bindValue(':city', $city, PDO::PARAM_STR);
            $stmtInsert->bindValue(':date', $date, PDO::PARAM_STR);
            $stmtInsert->bindValue(':temperature', $predictedTemp, PDO::PARAM_INT);
            $stmtInsert->bindValue(':humidity', $predictedHumidity, PDO::PARAM_STR);
            $stmtInsert->bindValue(':condition', $normalizedCondition, PDO::PARAM_STR);
            $stmtInsert->bindValue(':condition_vi', $predictedConditionVi, PDO::PARAM_STR);
            $stmtInsert->bindValue(':wind_speed', $predictedWindSpeed, PDO::PARAM_STR);
            $stmtInsert->bindValue(':temperature_update', $predictedTemp, PDO::PARAM_INT);
            $stmtInsert->bindValue(':humidity_update', $predictedHumidity, PDO::PARAM_STR);
            $stmtInsert->bindValue(':condition_update', $normalizedCondition, PDO::PARAM_STR);
            $stmtInsert->bindValue(':condition_vi_update', $predictedConditionVi, PDO::PARAM_STR);
            $stmtInsert->bindValue(':wind_speed_update', $predictedWindSpeed, PDO::PARAM_STR);
            $stmtInsert->execute();

            // Ghi log dự đoán
            $logMessage = sprintf(
                "city: %-15s | date: %-10s | temperature: %-5s | humidity: %-5s | condition: %-15s | condition_vi: %-15s | wind_speed: %-5s | created_at: %-19s\n",
                $city,
                $formattedDate,
                $predictedTemp,
                $predictedHumidity,
                $normalizedCondition,
                $predictedConditionVi,
                $predictedWindSpeed,
                date('Y-m-d H:i:s')
            );
            error_log($logMessage, 3, __DIR__ . '/../logs/predictions.log');

            $cityPredictions[] = [
                'date' => $formattedDate,
                'temperature' => $predictedTemp,
                'humidity' => $predictedHumidity,
                'condition' => $normalizedCondition,
                'condition_vi' => $predictedConditionVi,
                'wind_speed' => $predictedWindSpeed
            ];
        }

        return $cityPredictions;
    }
    //  Số ngày muốn dự báo
    public function predict($days_ahead = 7, $searchQuery = '') {
        try {
            // Xác định thành phố cần dự báo
            $cityToPredict = !empty($searchQuery) ? $searchQuery : 'Hà Nội';

            // Nếu có từ khóa tìm kiếm, kiểm tra xem thành phố có tồn tại trong cơ sở dữ liệu không
            if (!empty($searchQuery)) {
                $stmtCities = $this->db->prepare("SELECT DISTINCT city FROM weather_history WHERE city LIKE :searchQuery");
                $stmtCities->bindValue(':searchQuery', '%' . $searchQuery . '%', PDO::PARAM_STR);
                $stmtCities->execute();
                $searchedCities = $stmtCities->fetchAll(PDO::FETCH_COLUMN);

                if (empty($searchedCities)) {
                    $_SESSION['error'] = 'Không tìm thấy thành phố phù hợp với từ khóa: ' . htmlspecialchars($searchQuery);
                    error_log("Không tìm thấy thành phố phù hợp với từ khóa: $searchQuery", 3, __DIR__ . '/../logs/errors.log');
                    return [];
                }

                // Lấy thành phố đầu tiên khớp với từ khóa tìm kiếm
                $cityToPredict = $searchedCities[0];
            }

            // Dự báo chỉ cho thành phố được chọn
            $predictions = [];
            $cityPredictions = $this->predictForCity($cityToPredict, $days_ahead);
            if (!empty($cityPredictions)) {
                $predictions[$cityToPredict] = $cityPredictions;
            }

            return $predictions;
        } catch (PDOException $e) {
            $_SESSION['error'] = 'Lỗi khi dự báo thời tiết: ' . $e->getMessage();
            error_log("Lỗi khi dự báo thời tiết: " . $e->getMessage(), 3, __DIR__ . '/../logs/errors.log');
            return [];
        }
    }
}