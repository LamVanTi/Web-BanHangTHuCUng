<?php
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE");
header("Access-Control-Allow-Headers: Content-Type");

include_once(__DIR__ . "/../config/database.php");


// Lấy method yêu cầu
$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'GET':
        // Nếu có id thì lấy 1 sản phẩm, nếu không lấy tất cả
        if (isset($_GET['id'])) {
            $id = intval($_GET['id']);
            $stmt = $conn->prepare("SELECT * FROM products WHERE id = ?");
            $stmt->execute([$id]);
            $product = $stmt->fetch(PDO::FETCH_ASSOC);
            echo json_encode($product ?: ["message" => "Không tìm thấy sản phẩm."]);
        } else {
            $stmt = $conn->query("
                SELECT p.*, c.name AS category_name, s.name AS supplier_name
                FROM products p
                LEFT JOIN categories c ON p.category_id = c.id
                LEFT JOIN suppliers s ON p.supplier_id = s.id
                ORDER BY p.id DESC
            ");
            $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
            echo json_encode($products);
        }
        break;

    case 'POST':
        // Nhận dữ liệu JSON từ client
        $data = json_decode(file_get_contents("php://input"));
        if (!isset($data->name) || !isset($data->price)) {
            echo json_encode(["error" => "Thiếu thông tin sản phẩm."]);
            exit;
        }

        $stmt = $conn->prepare("
            INSERT INTO products (name, price, image, category_id, supplier_id, description)
            VALUES (?, ?, ?, ?, ?, ?)
        ");
        $stmt->execute([
            $data->name,
            $data->price,
            $data->image ?? null,
            $data->category_id ?? null,
            $data->supplier_id ?? null,
            $data->description ?? null
        ]);
        echo json_encode(["message" => "Thêm sản phẩm thành công!"]);
        break;

    case 'PUT':
        $data = json_decode(file_get_contents("php://input"));
        if (!isset($data->id)) {
            echo json_encode(["error" => "Thiếu ID sản phẩm."]);
            exit;
        }

        $stmt = $conn->prepare("
            UPDATE products
            SET name=?, price=?, image=?, category_id=?, supplier_id=?, description=?
            WHERE id=?
        ");
        $stmt->execute([
            $data->name,
            $data->price,
            $data->image,
            $data->category_id,
            $data->supplier_id,
            $data->description,
            $data->id
        ]);
        echo json_encode(["message" => "Cập nhật sản phẩm thành công!"]);
        break;

    case 'DELETE':
        if (!isset($_GET['id'])) {
            echo json_encode(["error" => "Thiếu ID sản phẩm cần xóa."]);
            exit;
        }

        $id = intval($_GET['id']);
        $stmt = $conn->prepare("DELETE FROM products WHERE id = ?");
        $stmt->execute([$id]);
        echo json_encode(["message" => "Đã xóa sản phẩm ID $id"]);
        break;

    default:
        http_response_code(405);
        echo json_encode(["error" => "Phương thức không được hỗ trợ."]);
}
?>
