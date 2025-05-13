<?php
require_once __DIR__ . '/../models/WeatherModel.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../controllers/WeatherPredictor.php';

class WeatherController {
    private $model;
    private $db;
    private $predictor;

    public function __construct() {
        $this->model = new WeatherModel();
        $database = new Database();
        $this->db = $database->getConnection();
        $this->predictor = new WeatherPredictor();

        if (!$this->db) {
            $_SESSION['error'] = 'Không thể kết nối đến cơ sở dữ liệu.';
            header('Location: /weather_forecast/auth/login');
            exit;
        }
    }

    public function index() {
        require_once __DIR__ . '/../views/layouts/main.php';
    }

    public function forecast() {
        $content = __DIR__ . '/../views/product/forecast.php';
        require_once __DIR__ . '/../views/layouts/secondary.php';
    }

    public function advancedForecast() {
        $content = __DIR__ . '/../views/product/advanced_forecast.php';
        require_once __DIR__ . '/../views/layouts/secondary.php';
    }

    public function chart() {
        $content = __DIR__ . '/../views/product/chart.php';
        require_once __DIR__ . '/../views/layouts/secondary.php';
    }

    public function news() {
        $content = __DIR__ . '/../views/product/news.php';
        require_once __DIR__ . '/../views/layouts/secondary.php';
    }

    public function author() {
        $content = __DIR__ . '/../views/product/author.php';
        require_once __DIR__ . '/../views/layouts/secondary.php';
    }

    public function introduce() {
        $content = __DIR__ . '/../views/product/introduce.php';
        require_once __DIR__ . '/../views/layouts/secondary.php';
    }

    public function login() {
        $content = __DIR__ . '/../views/auth/login.php';
        require_once __DIR__ . '/../views/layouts/secondary.php';
    }

    public function feedback() {
        $content = __DIR__ . '/../views/product/feedback.php';
        require_once __DIR__ . '/../views/layouts/secondary.php';
    }

    public function admin() {
        if (!isset($_SESSION['loggedInUser']) || $_SESSION['loggedInUser']['role'] !== 'admin') {
            header('Location: /weather_forecast/auth/login');
            exit;
        }
        $content = __DIR__ . '/../views/admin/admin.php';
        require_once __DIR__ . '/../views/layouts/secondary.php';
    }

    public function saveWeather() {
        $apiKey = API_KEY;
        $provinces = [
            "Hà Nội", "Thành phố Hồ Chí Minh", "Đà Nẵng", "Hải Phòng", "Cần Thơ", "An Giang", "Vũng Tàu", "Bắc Giang", 
            "Bắc Kạn", "Bạc Liêu", "Bắc Ninh", "Bến Tre", "Bình Định", "Bình Dương", "Bình Phước", "Bình Thuận", 
            "Cà Mau", "Cao Bằng", "Đắk Lắk", "Đắk Nông", "Điện Biên", "Đồng Nai", "Đồng Tháp", "Gia Lai", "Hà Giang", 
            "Hà Nam", "Hà Tĩnh", "Hải Dương", "Hậu Giang", "Hòa Bình", "Hưng Yên", "Khánh Hòa", "Kiên Giang", "Kon Tum", 
            "Lai Châu", "Lâm Đồng", "Lạng Sơn", "Lào Cai", "Long An", "Nam Định", "Nghệ An", "Ninh Bình", "Ninh Thuận", 
            "Phú Thọ", "Quảng Bình", "Quảng Nam", "Quảng Ngãi", "Quảng Ninh", "Quảng Trị", "Sóc Trăng", "Sơn La", 
            "Tây Ninh", "Thái Bình", "Thái Nguyên", "Thanh Hóa", "Thừa Thiên Huế", "Tiền Giang", "Trà Vinh", "Tuyên Quang", 
            "Vĩnh Long", "Vĩnh Phúc", "Yên Bái", "Phú Yên"
        ];
        // Chia tỉnh thành thành các nhóm nhỏ
        $chunkedProvinces = array_chunk($provinces, 10); // Ví dụ chia mỗi nhóm 10 tỉnh
        try {
            $stmtCache = $this->db->prepare(
                "INSERT INTO weather_cache (city, weather_data, cached_at) 
                VALUES (:city, :weather_data, NOW()) 
                ON DUPLICATE KEY UPDATE 
                    weather_data = :weather_data_update, 
                    cached_at = NOW()"
            );

            $stmtHistory = $this->db->prepare(
                "INSERT INTO weather_history (city, date, temperature, humidity, `condition`, wind_speed, recorded_at, action_date) 
                VALUES (:city, :date, :temperature, :humidity, :condition, :wind_speed, NOW(), :action_date) 
                ON DUPLICATE KEY UPDATE 
                    temperature = :temperature_update, 
                    humidity = :humidity_update, 
                    `condition` = :condition_update, 
                    wind_speed = :wind_speed_update, 
                    recorded_at = NOW(), 
                    action_date = :action_date_update"
            );

            $successCount = 0;
            $actionDate = date('Y-m-d'); // Ngày thực hiện hành động lưu

            foreach ($provinces as $city) {
                $url = API_URL_CURRENT . "?q=" . urlencode($city) . ",VN&appid=$apiKey&units=metric";
                $response = @file_get_contents($url);
                if ($response === false) {
                    error_log("Không thể lấy dữ liệu thời tiết cho thành phố $city", 3, __DIR__ . '/../logs/errors.log');
                    continue;
                }

                $weatherData = json_decode($response, true);
                if (!isset($weatherData['cod']) || $weatherData['cod'] != 200 || !isset($weatherData['main'])) {
                    error_log("Dữ liệu thời tiết không hợp lệ cho thành phố $city", 3, __DIR__ . '/../logs/errors.log');
                    continue;
                }

                $weatherDataJson = json_encode($weatherData);
                // Lưu vào weather_cache
                $stmtCache->bindValue(':city', $city, PDO::PARAM_STR);
                $stmtCache->bindValue(':weather_data', $weatherDataJson, PDO::PARAM_STR);
                $stmtCache->bindValue(':weather_data_update', $weatherDataJson, PDO::PARAM_STR);
                $stmtCache->execute();

                // Lưu vào weather_history
                $date = date('Y-m-d'); // Ngày dữ liệu thời tiết đại diện
                $temperature = $weatherData['main']['temp'] ?? 0;
                $humidity = $weatherData['main']['humidity'] ?? 0;
                $condition = $weatherData['weather'][0]['description'] ?? 'Unknown';
                $windSpeed = $weatherData['wind']['speed'] ?? 0;

                $stmtHistory->bindValue(':city', $city, PDO::PARAM_STR);
                $stmtHistory->bindValue(':date', $date, PDO::PARAM_STR);
                $stmtHistory->bindValue(':temperature', $temperature, PDO::PARAM_STR);
                $stmtHistory->bindValue(':humidity', $humidity, PDO::PARAM_STR);
                $stmtHistory->bindValue(':condition', $condition, PDO::PARAM_STR);
                $stmtHistory->bindValue(':wind_speed', $windSpeed, PDO::PARAM_STR);
                $stmtHistory->bindValue(':action_date', $actionDate, PDO::PARAM_STR);
                $stmtHistory->bindValue(':temperature_update', $temperature, PDO::PARAM_STR);
                $stmtHistory->bindValue(':humidity_update', $humidity, PDO::PARAM_STR);
                $stmtHistory->bindValue(':condition_update', $condition, PDO::PARAM_STR);
                $stmtHistory->bindValue(':wind_speed_update', $windSpeed, PDO::PARAM_STR);
                $stmtHistory->bindValue(':action_date_update', $actionDate, PDO::PARAM_STR);
                $stmtHistory->execute();

                $successCount++;
            }

            $_SESSION['success'] = "Đã cập nhật dữ liệu thời tiết cho $successCount tỉnh/thành phố.";
        } catch (PDOException $e) {
            $_SESSION['error'] = 'Lỗi khi lưu dữ liệu thời tiết: ' . $e->getMessage();
            error_log("Lỗi khi lưu dữ liệu thời tiết: " . $e->getMessage(), 3, __DIR__ . '/../logs/errors.log');
        }
    }

    public function getWeatherData() {
        try {
            $stmt = $this->db->prepare("SELECT * FROM weather_cache ORDER BY cached_at DESC");
            $stmt->execute();
            $weatherData = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return array_map(function($row) {
                $data = json_decode($row['weather_data'], true);
                return [
                    'city' => $row['city'],
                    'temperature' => $data['main']['temp'] ?? 'N/A',
                    'humidity' => $data['main']['humidity'] ?? 'N/A',
                    'condition' => $data['weather'][0]['main'] ?? 'N/A',
                    'wind_speed' => $data['wind']['speed'] ?? 'N/A',
                    'cached_at' => $row['cached_at']
                ];
            }, $weatherData);
        } catch (PDOException $e) {
            $_SESSION['error'] = 'Lỗi khi lấy dữ liệu thời tiết: ' . $e->getMessage();
            error_log("Lỗi khi lấy dữ liệu thời tiết: " . $e->getMessage(), 3, __DIR__ . '/../logs/errors.log');
            return [];
        }
    }

    public function getWeatherHistory() {
        try {
            // Khởi tạo câu truy vấn cơ bản
            $query = "SELECT * FROM weather_history WHERE 1=1";
            $params = [];
    
            // Thêm điều kiện lọc theo thời gian nếu có
            if (isset($_GET['start_date']) && !empty($_GET['start_date'])) {
                $query .= " AND action_date >= ?";
                $params[] = $_GET['start_date'];
            }
            if (isset($_GET['end_date']) && !empty($_GET['end_date'])) {
                $query .= " AND action_date <= ?";
                $params[] = $_GET['end_date'];
            }
    
            // Sắp xếp theo action_date và recorded_at
            $query .= " ORDER BY action_date DESC, recorded_at DESC";
    
            // Chuẩn bị và thực thi câu truy vấn
            $stmt = $this->db->prepare($query);
            if (!empty($params)) {
                $stmt->execute($params);
            } else {
                $stmt->execute();
            }
    
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            $_SESSION['error'] = 'Lỗi khi lấy lịch sử thời tiết: ' . $e->getMessage();
            error_log("Lỗi khi lấy lịch sử thời tiết: " . $e->getMessage(), 3, __DIR__ . '/../logs/errors.log');
            return [];
        }
    }

    public function getLastUpdate() {
        try {
            $stmt = $this->db->prepare("SELECT MAX(cached_at) as last_update FROM weather_cache");
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result['last_update'] ?? null;
        } catch (PDOException $e) {
            $_SESSION['error'] = 'Lỗi khi kiểm tra thời gian cập nhật: ' . $e->getMessage();
            error_log("Lỗi khi kiểm tra thời gian cập nhật: " . $e->getMessage(), 3, __DIR__ . '/../logs/errors.log');
            return null;
        }
    }

    public function predictWeather() {
        try {
            // Sử dụng WeatherPredictor để dự báo
            $predictions = $this->predictor->predict();

            if (empty($predictions)) {
                $_SESSION['error'] = 'Không có dữ liệu để dự báo thời tiết.';
            } else {
                $_SESSION['success'] = 'Dự báo thời tiết đã được cập nhật thành công.';
            }
        } catch (Exception $e) {
            $_SESSION['error'] = 'Lỗi khi dự báo thời tiết: ' . $e->getMessage();
            error_log("Lỗi khi dự báo thời tiết: " . $e->getMessage(), 3, __DIR__ . '/../logs/errors.log');
        }
    }
}
?>