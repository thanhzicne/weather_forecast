<?php
require_once __DIR__ . '/../config/database.php';

class AuthController {
    private $db;

    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();

        if (!$this->db) {
            session_start();
            $_SESSION['error'] = 'Không thể kết nối đến cơ sở dữ liệu.';
            header('Location: /weather_forecast/auth/login');
            exit;
        }
    }

    public function showLogin() {
        require_once __DIR__ . '/../views/auth/login.php';
    }

    public function login() {
        session_start();

        if ($_SERVER["REQUEST_METHOD"] === "POST") {
            $username = $_POST['username'] ?? '';
            $password = $_POST['password'] ?? '';

            if (empty($username) || empty($password)) {
                $_SESSION['error'] = 'Vui lòng nhập đầy đủ thông tin.';
                header('Location: /weather_forecast/auth/login');
                exit;
            }

            try {
                $stmt = $this->db->prepare("SELECT * FROM users WHERE username = :username");
                $stmt->bindValue(':username', $username, PDO::PARAM_STR);
                $stmt->execute();
                $user = $stmt->fetch(PDO::FETCH_ASSOC);

                // So sánh mật khẩu trực tiếp (không mã hóa)
                if ($user && $password === $user['password']) {
                    $_SESSION['loggedInUser'] = [
                        'id' => $user['id'],
                        'username' => $user['username'],
                        'role' => $user['role']
                    ];
                    header('Location: /weather_forecast/views/admin/admin.php');
                    exit;
                } else {
                    $_SESSION['error'] = 'Sai tài khoản hoặc mật khẩu.';
                    header('Location: /weather_forecast/auth/login');
                    exit;
                }
            } catch (PDOException $e) {
                $_SESSION['error'] = 'Lỗi khi đăng nhập: ' . $e->getMessage();
                header('Location: /weather_forecast/auth/login');
                exit;
            }
        } else {
            $this->showLogin();
        }
    }

    public function logout() {
        session_start();
        session_unset();
        session_destroy();
        header('Location: /weather_forecast/auth/login');
        exit;
    }
}
?>