<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

define('BASE_URL', '/weather_forecast/');

// Không cần autoloader vì không dùng namespace
// spl_autoload_register(function ($class) { ... });

$logDir = __DIR__ . '/logs';
$logFile = $logDir . '/errors.log';
if (!is_dir($logDir)) {
    mkdir($logDir, 0755, true);
}

$layoutFile = __DIR__ . '/views/layouts/main.php';

// Phân tích URL
$url = $_GET['url'] ?? '';
$url = rtrim($url, '/');
$url = filter_var($url, FILTER_SANITIZE_URL);
$url = explode('/', $url);

$controllerName = isset($url[0]) && $url[0] != '' ? ucfirst($url[0]) . 'Controller' : 'WeatherController';

$allowedControllers = ['WeatherController', 'ForecastController', 'ChartController', 'NewsController', 'AuthorController', 'IntroduceController', 'AuthController', 'AlertController', 'FeedbackController'];
if (!in_array($controllerName, $allowedControllers) || !preg_match('/^[a-zA-Z0-9]+$/', $url[0])) {
    header('HTTP/1.0 404 Not Found');
    $content = __DIR__ . '/views/errors/404.php';
    if (file_exists($layoutFile)) {
        require_once $layoutFile;
    } else {
        echo '<div style="text-align: center; margin-top: 50px; font-family: Arial, sans-serif;">
                <h1>Lỗi hệ thống</h1>
                <p>Không tìm thấy file layout chính (views/layouts/main.php). Vui lòng kiểm tra cấu trúc dự án.</p>
            </div>';
    }
    error_log("Invalid controller: $controllerName, URL: {$_SERVER['REQUEST_URI']}", 3, $logFile);
    exit;
}

if (!file_exists("controllers/$controllerName.php")) {
    header('HTTP/1.0 404 Not Found');
    $content = __DIR__ . '/views/errors/404.php';
    if (file_exists($layoutFile)) {
        require_once $layoutFile;
    } else {
        echo '<div style="text-align: center; margin-top: 50px; font-family: Arial, sans-serif;">
                <h1>Lỗi hệ thống</h1>
                    <p>Không tìm thấy file layout chính (views/layouts/main.php). Vui lòng kiểm tra cấu trúc dự án.</p>
            </div>';
    }
    error_log("Controller file not found: $controllerName", 3, $logFile);
    exit;
}

require_once "controllers/$controllerName.php";

try {
    $controller = new $controllerName();
} catch (Exception $e) {
    header('HTTP/1.0 500 Internal Server Error');
    $content = __DIR__ . '/views/errors/500.php';
    if (file_exists($layoutFile)) {
        require_once $layoutFile;
    } else {
        echo '<div style="text-align: center; margin-top: 50px; font-family: Arial, sans-serif;">
                <h1>Lỗi hệ thống</h1>
                <p>Không tìm thấy file layout chính (views/layouts/main.php). Vui lòng kiểm tra cấu trúc dự án.</p>
            </div>';
    }
    error_log("Error initializing controller: $controllerName - " . $e->getMessage(), 3, $logFile);
    exit;
}

$action = isset($url[1]) && $url[1] != '' ? $url[1] : 'index';

if (!method_exists($controller, $action) || !preg_match('/^[a-zA-Z0-9]+$/', $action)) {
    header('HTTP/1.0 404 Not Found');
    $content = __DIR__ . '/views/errors/404.php';
    if (file_exists($layoutFile)) {
        require_once $layoutFile;
    } else {
        echo '<div style="text-align: center; margin-top: 50px; font-family: Arial, sans-serif;">
                <h1>Lỗi hệ thống</h1>
                <p>Không tìm thấy file layout chính (views/layouts/main.php). Vui lòng kiểm tra cấu trúc dự án.</p>
                </div>';
    }
    error_log("Action not found: $action in $controllerName", 3, $logFile);
    exit;
}

try {
    call_user_func_array([$controller, $action], array_slice($url, 2));
} catch (Exception $e) {
    header('HTTP/1.0 500 Internal Server Error');
    $content = __DIR__ . '/views/errors/500.php';
    if (file_exists($layoutFile)) {
        require_once $layoutFile;
    } else {
        echo '<div style="text-align: center; margin-top: 50px; font-family: Arial, sans-serif;">
                <h1>Lỗi hệ thống</h1>
                <p>Không tìm thấy file layout chính (views/layouts/main.php). Vui lòng kiểm tra cấu trúc dự án.</p>
                </div>';
    }
    error_log("Error executing action: $action in $controllerName - " . $e->getMessage(), 3, $logFile);
    exit;
}
?>