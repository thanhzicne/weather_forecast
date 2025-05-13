<?php
// app/config/database.php

define('ENV', 'development'); 

define('DB_HOST', 'localhost');
define('DB_NAME', 'weather_db');
define('DB_USER', 'root');
define('DB_PASS', '');

define('API_KEY', 'e2c222797835642c23ca9f8d6fda7d2b');
define('API_URL_CURRENT', 'http://api.openweathermap.org/data/2.5/weather');
define('API_URL_FORECAST', 'http://api.openweathermap.org/data/2.5/forecast');

class Database
{
    private static $conn = null;

    public function getConnection() {
        if (self::$conn === null) {
            try {
                self::$conn = new PDO(
                    "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4",
                    DB_USER,
                    DB_PASS
                );
                self::$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                self::$conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
            } catch (PDOException $e) {
                // Ghi log lỗi chi tiết
                error_log("Lỗi kết nối cơ sở dữ liệu: " . $e->getMessage(), 3, __DIR__ . '/../logs/errors.log');
                if (defined('ENV') && ENV === 'production') {
                    echo json_encode(['status' => 'error', 'message' => 'Không thể kết nối đến cơ sở dữ liệu. Vui lòng thử lại sau.']);
                    die();
                } else {
                    echo json_encode(['status' => 'error', 'message' => 'Lỗi kết nối cơ sở dữ liệu: ' . $e->getMessage()]);
                    die();
                }
            }
        }
        return self::$conn;
    }

    public function testConnection()
    {
        $conn = $this->getConnection();
        if ($conn) {
            return json_encode(['status' => 'success', 'message' => 'Kết nối cơ sở dữ liệu thành công!']);
        }
        return json_encode(['status' => 'error', 'message' => 'Không thể kết nối đến cơ sở dữ liệu.']);
    }
}
?>