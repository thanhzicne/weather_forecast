<?php
require_once __DIR__ . '/../config/database.php';

class FeedbackController {
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

    public function submit() {
        if ($_SERVER["REQUEST_METHOD"] === "POST") {
            header('Content-Type: application/json; charset=UTF-8');

            $full_name = trim($_POST['full_name'] ?? '');
            $email = trim($_POST['email'] ?? '');
            $content = trim($_POST['content'] ?? '');

            if (empty($full_name) || empty($email) || empty($content)) {
                echo json_encode(['status' => 'error', 'message' => 'Vui lòng nhập đầy đủ thông tin.']);
                return;
            }

            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                echo json_encode(['status' => 'error', 'message' => 'Email không hợp lệ.']);
                return;
            }

            if (strlen($content) < 10) {
                echo json_encode(['status' => 'error', 'message' => 'Nội dung phải dài ít nhất 10 ký tự.']);
                return;
            }

            try {
                $stmt = $this->db->prepare(
                    "INSERT INTO feedback (full_name, email, content, created_at) 
                    VALUES (:full_name, :email, :content, NOW())"
                );
                $stmt->bindValue(':full_name', htmlspecialchars($full_name), PDO::PARAM_STR);
                $stmt->bindValue(':email', $email, PDO::PARAM_STR);
                $stmt->bindValue(':content', htmlspecialchars($content), PDO::PARAM_STR);

                if ($stmt->execute()) {
                    echo json_encode(['status' => 'success', 'message' => 'Gửi phản hồi thành công.']);
                } else {
                    error_log("Không thể lưu phản hồi vào cơ sở dữ liệu.", 3, __DIR__ . '/../logs/errors.log');
                    echo json_encode(['status' => 'error', 'message' => 'Không thể lưu phản hồi vào cơ sở dữ liệu.']);
                }
            } catch (PDOException $e) {
                error_log("Lỗi khi gửi phản hồi: " . $e->getMessage(), 3, __DIR__ . '/../logs/errors.log');
                echo json_encode(['status' => 'error', 'message' => 'Lỗi khi gửi phản hồi: ' . $e->getMessage()]);
            }
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Phương thức không được hỗ trợ.']);
        }
    }

    public function getAll() {
        try {
            $stmt = $this->db->prepare("SELECT * FROM feedback ORDER BY created_at DESC");
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Lỗi khi lấy dữ liệu phản hồi: " . $e->getMessage(), 3, __DIR__ . '/../logs/errors.log');
            session_start();
            $_SESSION['error'] = 'Lỗi khi lấy dữ liệu: ' . $e->getMessage();
            return [];
        }
    }
}
?>