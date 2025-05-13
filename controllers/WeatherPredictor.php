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

    private function calculateStats($data, $key) {
        if (empty($data)) {
            error_log("Không có dữ liệu lịch sử để tính thống kê cho $key", 3, __DIR__ . '/../logs/errors.log');
            return ['mean' => 0, 'stddev' => 0, 'trend' => 0];
        }

        $values = array_map(function($row) use ($key) {
            $value = floatval($row[$key]);
            error_log("Raw value - $key: $value", 3, __DIR__ . '/../logs/raw_values.log');
            if ($key === 'humidity' && ($value < 0 || $value > 100)) {
                error_log("Giá trị độ ẩm không hợp lý: $value cho $key", 3, __DIR__ . '/../logs/errors.log');
                return 0;
            }
            if ($key === 'temperature' && ($value < 10 || $value > 45)) {
                error_log("Giá trị nhiệt độ không hợp lý: $value cho $key", 3, __DIR__ . '/../logs/errors.log');
                return 0;
            }
            if ($key === 'wind_speed' && ($value < 0 || $value > 30)) {
                error_log("Giá trị tốc độ gió không hợp lý: $value cho $key", 3, __DIR__ . '/../logs/errors.log');
                return 0;
            }
            return $value;
        }, $data);

        $count = count($values);
        $mean = array_sum($values) / $count;
        error_log("Mean trước khi giới hạn - $key: $mean", 3, __DIR__ . '/../logs/predictions.log');

        $variance = array_sum(array_map(function($val) use ($mean) {
            return pow($val - $mean, 2);
        }, $values)) / $count;

        $stddev = sqrt($variance);

        $trend = 0;
        if ($count > 1) {
            $trend = ($values[0] - $values[1]) * 0.5;
            $trend = min(10, max(-10, $trend));
        } else {
            error_log("Không đủ dữ liệu để tính xu hướng cho $key: chỉ có $count ngày", 3, __DIR__ . '/../logs/warnings.log');
        }

        if ($key === 'humidity') {
            $mean = max(0, min(100, $mean));
        } elseif ($key === 'temperature') {
            $mean = max(10, min(45, $mean));
        } elseif ($key === 'wind_speed') {
            $mean = max(0, min(30, $mean));
        }

        return ['mean' => $mean, 'stddev' => $stddev, 'trend' => $trend];
    }

    private function getSeasonAdjustment($month) {
        if (in_array($month, [4, 5, 6, 7, 8])) {
            return 1.1;
        } elseif (in_array($month, [11, 12, 1, 2])) {
            return 0.9;
        } else {
            return 1.0;
        }
    }

    private function predictCondition($historicalConditions, $currentCondition, $predictedTemp, $currentTemp) {
        if (empty($historicalConditions)) {
            if ($predictedTemp > $currentTemp + 2) {
                return ($currentCondition === 'rain') ? 'clouds' : $currentCondition;
            } elseif ($predictedTemp < $currentTemp - 2) {
                return ($currentCondition === 'clear') ? 'clouds' : $currentCondition;
            }
            return $currentCondition;
        }

        $weightedCounts = [];
        $totalWeight = 0;
        $days = count($historicalConditions);
        foreach ($historicalConditions as $index => $condition) {
            $weight = 1 + ($days - $index - 1) * 0.2;
            $weightedCounts[$condition] = ($weightedCounts[$condition] ?? 0) + $weight;
            $totalWeight += $weight;
        }

        $probabilities = array_map(function($count) use ($totalWeight) {
            return $count / $totalWeight;
        }, $weightedCounts);

        if (isset($probabilities[$currentCondition]) && $probabilities[$currentCondition] > 0.5) {
            return $currentCondition;
        }

        if ($predictedTemp > $currentTemp + 2 && isset($probabilities['clear'])) {
            $probabilities['clear'] += 0.1;
        } elseif ($predictedTemp < $currentTemp - 2 && isset($probabilities['rain'])) {
            $probabilities['rain'] += 0.1;
        }

        arsort($probabilities);
        return key($probabilities);
    }

    function mapDescriptionToMain($description) {
        $description = strtolower($description);
        if (strpos($description, 'cloud') !== false) return 'clouds';
        if (strpos($description, 'rain') !== false) return 'rain';
        if (strpos($description, 'clear') !== false) return 'clear';
        if (strpos($description, 'snow') !== false) return 'snow';
        if (strpos($description, 'mist') !== false || strpos($description, 'fog') !== false) return 'mist';
        if (strpos($description, 'thunderstorm') !== false) return 'thunderstorm';
        return 'unknown';
    }

    private function getCurrentWeatherData($city) {
        $apiKey = 'e2c222797835642c23ca9f8d6fda7d2b';
        $url = "http://api.openweathermap.org/data/2.5/weather?q=" . urlencode($city) . "&appid=" . $apiKey . "&units=metric";
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $response = curl_exec($ch);
        curl_close($ch);

        $weatherData = json_decode($response, true);
        
        if (isset($weatherData['cod']) && $weatherData['cod'] !== 200) {
            error_log("Lỗi khi lấy dữ liệu thời tiết cho $city: " . ($weatherData['message'] ?? 'Unknown error'), 3, __DIR__ . '/../logs/errors.log');
            return [
                'main' => ['temp' => 0, 'humidity' => 0],
                'wind' => ['speed' => 0],
                'weather' => [['description' => 'Unknown']]
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
                ['description' => $weatherData['weather'][0]['description'] ?? 'Unknown']
            ]
        ];
    }

    public function predict() {
        $weatherDescriptions = [
            "clear" => "Trời quang",
            "clouds" => "Có mây",
            "few clouds" => "Ít mây",
            "scattered clouds" => "Mây rải rác",
            "broken clouds" => "Mây đứt đoạn",
            "overcast clouds" => "Nhiều mây",
            "light rain" => "Mưa nhẹ",
            "moderate rain" => "Mưa vừa",
            "heavy rain" => "Mưa to",
            "thunderstorm" => "Dông",
            "snow" => "Tuyết",
            "mist" => "Sương mù",
            "fog" => "Sương mù dày",
            "haze" => "Mù khô",
            "drizzle" => "Mưa phùn",
            "rain" => "Có mưa",
            "unknown" => "Không xác định"
        ];

        try {
            $stmtCities = $this->db->prepare("SELECT DISTINCT city FROM weather_history");
            $stmtCities->execute();
            $cities = $stmtCities->fetchAll(PDO::FETCH_COLUMN);

            if (empty($cities)) {
                $_SESSION['error'] = 'Không có dữ liệu thời tiết lịch sử để dự báo.';
                error_log("Không có dữ liệu thời tiết lịch sử để dự báo.", 3, __DIR__ . '/../logs/errors.log');
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

            $predictions = [];

            foreach ($cities as $city) {
                $stmtHistory = $this->db->prepare(
                    "SELECT temperature, humidity, `condition`, wind_speed 
                    FROM weather_history 
                    WHERE city = :city AND date <= CURDATE() 
                    ORDER BY date DESC 
                    LIMIT 7"
                );
                $stmtHistory->bindValue(':city', $city, PDO::PARAM_STR);
                $stmtHistory->execute();
                $historicalData = $stmtHistory->fetchAll(PDO::FETCH_ASSOC);

                if (empty($historicalData)) {
                    error_log("Không có dữ liệu lịch sử cho tỉnh $city.", 3, __DIR__ . '/../logs/errors.log');
                    continue;
                }

                // Lấy dữ liệu hiện tại từ API
                $weatherData = $this->getCurrentWeatherData($city);
                $currentTemp = floatval($weatherData['main']['temp'] ?? 0);
                $currentHumidity = floatval($weatherData['main']['humidity'] ?? 0);
                $currentCondition = $weatherData['weather'][0]['description'] ?? 'Unknown';
                $currentWindSpeed = floatval($weatherData['wind']['speed'] ?? 0);

                // Chuẩn hóa currentCondition từ API
                $normalizedCurrentCondition = $this->mapDescriptionToMain($currentCondition);

                // Chuẩn hóa historicalConditions từ dữ liệu cũ
                $historicalConditions = array_map(function($row) {
                    return $this->mapDescriptionToMain($row['condition']);
                }, $historicalData);

                // Tính thống kê từ dữ liệu lịch sử
                $tempStats = $this->calculateStats($historicalData, 'temperature');
                $humidityStats = $this->calculateStats($historicalData, 'humidity');
                $windSpeedStats = $this->calculateStats($historicalData, 'wind_speed');

                // Dự đoán nhiệt độ
                $month = (int)date('m');
                $seasonAdjustment = $this->getSeasonAdjustment($month);
                $predictedTemp = $tempStats['mean'] + $tempStats['trend'];
                $predictedTemp *= $seasonAdjustment;
                $predictedTemp = round(min(45, max(10, $predictedTemp)), 1);

                // Dự đoán độ ẩm
                $humidityAdjustment = ($predictedTemp > $currentTemp) ? -3 : 3;
                $predictedHumidity = $humidityStats['mean'] + $humidityStats['trend'] + $humidityAdjustment;
                error_log("Humidity Stats - $city: mean={$humidityStats['mean']}, trend={$humidityStats['trend']}, adjustment=$humidityAdjustment, predicted=$predictedHumidity", 3, __DIR__ . '/../logs/predictions.log');
                $predictedHumidity = max(0, min(100, $predictedHumidity));
                error_log("Sau khi giới hạn - $city: $predictedHumidity", 3, __DIR__ . '/../logs/predictions.log');
                $predictedHumidity = round($predictedHumidity);
                $predictedHumidity = (int)$predictedHumidity;
                error_log("Giá trị cuối cùng - $city: $predictedHumidity", 3, __DIR__ . '/../logs/predictions.log');

                // Dự đoán điều kiện
                $predictedCondition = $this->predictCondition($historicalConditions, $normalizedCurrentCondition, $predictedTemp, $currentTemp);
                if (empty($predictedCondition)) {
                    $predictedCondition = 'unknown';
                }

                // Chuẩn hóa và ánh xạ sang tiếng Việt
                $normalizedCondition = $this->mapDescriptionToMain($predictedCondition);
                $predictedConditionVi = $weatherDescriptions[$normalizedCondition] ?? $normalizedCondition;

                // Dự đoán tốc độ gió
                $windAdjustment = mt_rand((int)round(-$windSpeedStats['stddev'] * 100), (int)round($windSpeedStats['stddev'] * 100)) / 100;
                error_log("Wind Adjustment - $city: stddev={$windSpeedStats['stddev']}, adjustment=$windAdjustment", 3, __DIR__ . '/../logs/predictions.log');
                $predictedWindSpeed = $windSpeedStats['mean'] + $windSpeedStats['trend'] + $windAdjustment;
                $predictedWindSpeed = round(max(0, min(30, $predictedWindSpeed)), 1);
                error_log("Predicted Wind Speed - $city: $predictedWindSpeed", 3, __DIR__ . '/../logs/predictions.log');

                // Lưu dự đoán cho ngày mai
                $date = date('Y-m-d', strtotime('+1 day'));
                $stmtInsert->bindValue(':city', $city, PDO::PARAM_STR);
                $stmtInsert->bindValue(':date', $date, PDO::PARAM_STR);
                $stmtInsert->bindValue(':temperature', $predictedTemp, PDO::PARAM_STR);
                $stmtInsert->bindValue(':humidity', $predictedHumidity, PDO::PARAM_INT);
                $stmtInsert->bindValue(':condition', $normalizedCondition, PDO::PARAM_STR);
                $stmtInsert->bindValue(':condition_vi', $predictedConditionVi, PDO::PARAM_STR);
                $stmtInsert->bindValue(':wind_speed', $predictedWindSpeed, PDO::PARAM_STR);
                $stmtInsert->bindValue(':temperature_update', $predictedTemp, PDO::PARAM_STR);
                $stmtInsert->bindValue(':humidity_update', $predictedHumidity, PDO::PARAM_INT);
                $stmtInsert->bindValue(':condition_update', $normalizedCondition, PDO::PARAM_STR);
                $stmtInsert->bindValue(':condition_vi_update', $predictedConditionVi, PDO::PARAM_STR);
                $stmtInsert->bindValue(':wind_speed_update', $predictedWindSpeed, PDO::PARAM_STR);
                $stmtInsert->execute();

                $predictions[$city] = [
                    'temperature' => $predictedTemp,
                    'humidity' => $predictedHumidity,
                    'condition' => $normalizedCondition,
                    'condition_vi' => $predictedConditionVi,
                    'wind_speed' => $predictedWindSpeed
                ];
            }

            return $predictions;
        } catch (PDOException $e) {
            $_SESSION['error'] = 'Lỗi khi dự báo thời tiết: ' . $e->getMessage();
            error_log("Lỗi khi dự báo thời tiết: " . $e->getMessage(), 3, __DIR__ . '/../logs/errors.log');
            return [];
        }
    }
}
?>