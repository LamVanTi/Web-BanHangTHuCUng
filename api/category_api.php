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
            $stmt = $conn->prepare("SELECT * FROM categories WHERE id = ?");
            $stmt->execute([$_GET['id']]);
            $category = $stmt->fetch(PDO::FETCH_ASSOC);
            echo json_encode($category ?: ["message" => "Không tìm thấy danh mục."]);
        } else {
            $stmt = $conn->query("SELECT * FROM categories ORDER BY id DESC");
            echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
        }
        break;

    case 'POST':
        $data = json_decode(file_get_contents("php://input"));
        if (!isset($data->name)) {
            echo json_encode(["error" => "Thiếu tên danh mục"]);
            exit;
        }
        $stmt = $conn->prepare("INSERT INTO categories (name) VALUES (?)");
        $stmt->execute([$data->name]);
        echo json_encode(["message" => "Thêm danh mục thành công!"]);
        break;

    case 'PUT':
        $data = json_decode(file_get_contents("php://input"));
        if (!isset($data->id) || !isset($data->name)) {
            echo json_encode(["error" => "Thiếu ID hoặc tên danh mục"]);
            exit;
        }
        $stmt = $conn->prepare("UPDATE categories SET name = ? WHERE id = ?");
        $stmt->execute([$data->name, $data->id]);
        echo json_encode(["message" => "Cập nhật danh mục thành công!"]);
        break;

    case 'DELETE':
        if (!isset($_GET['id'])) {
            echo json_encode(["error" => "Thiếu ID danh mục cần xóa"]);
            exit;
        }
        $stmt = $conn->prepare("DELETE FROM categories WHERE id = ?");
        $stmt->execute([$_GET['id']]);
        echo json_encode(["message" => "Đã xóa danh mục ID " . $_GET['id']]);
        break;

    default:
        http_response_code(405);
        echo json_encode(["error" => "Phương thức không hợp lệ"]);
}
?>
