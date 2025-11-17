<?php
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE");
header("Access-Control-Allow-Headers: Content-Type");

include_once(__DIR__ . "/../config/database.php");

$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'GET':
        if (isset($_GET['id'])) {
            $stmt = $conn->prepare("SELECT * FROM orders WHERE id = ?");
            $stmt->execute([$_GET['id']]);
            echo json_encode($stmt->fetch(PDO::FETCH_ASSOC));
        } else {
            $sql = "
                SELECT o.*, c.name AS customer_name
                FROM orders o
                LEFT JOIN customers c ON o.customer_id = c.id
                ORDER BY o.id DESC
            ";
            $stmt = $conn->query($sql);
            echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
        }
        break;

    case 'POST':
        $data = json_decode(file_get_contents("php://input"));
        $stmt = $conn->prepare("INSERT INTO orders (customer_id, total_price, status, created_at) VALUES (?, ?, ?, NOW())");
        $stmt->execute([$data->customer_id, $data->total_price, $data->status]);
        echo json_encode(["message" => "Thêm đơn hàng thành công"]);
        break;

    case 'PUT':
        $data = json_decode(file_get_contents("php://input"));
        $stmt = $conn->prepare("UPDATE orders SET customer_id=?, total_price=?, status=? WHERE id=?");
        $stmt->execute([$data->customer_id, $data->total_price, $data->status, $data->id]);
        echo json_encode(["message" => "Cập nhật đơn hàng thành công"]);
        break;

    case 'DELETE':
        if (!isset($_GET['id'])) {
            echo json_encode(["error" => "Thiếu ID đơn hàng cần xóa"]);
            exit;
        }
        $stmt = $conn->prepare("DELETE FROM orders WHERE id = ?");
        $stmt->execute([$_GET['id']]);
        echo json_encode(["message" => "Đã xóa đơn hàng"]);
        break;

    default:
        http_response_code(405);
        echo json_encode(["error" => "Phương thức không hợp lệ"]);
}
?>
