<?php
require_once __DIR__ . '/../config/database.php';

class AlertController {
    private $db;

    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
    }

    // Lấy tất cả thông báo
    public function getAll() {
        try {
            $stmt = $this->db->prepare("SELECT a.*, u.username FROM alerts a LEFT JOIN users u ON a.created_by = u.id ORDER BY a.created_at DESC");
            $stmt->execute();
            $alerts = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return json_encode($alerts);
        } catch (PDOException $e) {
            return json_encode(['status' => 'error', 'message' => 'Lỗi khi lấy dữ liệu: ' . $e->getMessage()]);
        }
    }

    // Xóa thông báo
    public function delete() {
        session_start();
        if (!isset($_SESSION['loggedInUser']) || $_SESSION['loggedInUser']['role'] !== 'admin') {
            http_response_code(403);  // Forbidden
            return json_encode(['status' => 'error', 'message' => 'Không có quyền truy cập.']);
        }

        if ($_SERVER["REQUEST_METHOD"] === "POST") {
            $id = $_POST['id'] ?? '';
            if (empty($id)) {
                http_response_code(400);  // Bad Request
                return json_encode(['status' => 'error', 'message' => 'Vui lòng cung cấp ID bài báo.']);
            }

            try {
                $stmt = $this->db->prepare("DELETE FROM alerts WHERE id = :id");
                $stmt->bindValue(':id', $id, PDO::PARAM_INT);

                if ($stmt->execute()) {
                    http_response_code(200);  
                    return json_encode(['status' => 'success', 'message' => 'Xóa bài báo thành công.']);
                } else {
                    http_response_code(500); 
                    return json_encode(['status' => 'error', 'message' => 'Có lỗi xảy ra khi xóa.']);
                }
            } catch (PDOException $e) {
                http_response_code(500);  
                return json_encode(['status' => 'error', 'message' => 'Lỗi khi xóa bài báo: ' . $e->getMessage()]);
            }
        }
        http_response_code(405); 
        error_log("Invalid request method or missing ID for delete.");
        return;  
    }
}

error_log("Received request: " . print_r($_GET, true));

$controller = new AlertController();
$action = isset($_GET['action']) ? $_GET['action'] : '';

error_log("Action received: " . $action); 

switch ($action) {
    case 'getAll':
        echo $controller->getAll();
        break;
    case 'delete':
        echo $controller->delete();
        break;
    default:

        error_log("Invalid action: " . $action);
        break;
}
?>
