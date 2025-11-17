<?php
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

include_once(__DIR__ . "/../config/database.php");

$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
  // 📘 Lấy danh sách hoặc chi tiết nhà cung cấp
  case 'GET':
    if (isset($_GET['id'])) {
      $stmt = $conn->prepare("SELECT * FROM suppliers WHERE id = ?");
      $stmt->execute([$_GET['id']]);
      $supplier = $stmt->fetch(PDO::FETCH_ASSOC);
      echo json_encode($supplier ?: ["message" => "Không tìm thấy nhà cung cấp"]);
    } else {
      $stmt = $conn->query("SELECT * FROM suppliers ORDER BY id DESC");
      echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
    }
    break;

  // ➕ Thêm nhà cung cấp mới
  case 'POST':
    $data = json_decode(file_get_contents("php://input"));
    if (!isset($data->name) || !isset($data->phone) || !isset($data->address)) {
      http_response_code(400);
      echo json_encode(["error" => "Thiếu dữ liệu (name, phone, address)"]);
      exit;
    }

    $stmt = $conn->prepare("INSERT INTO suppliers (name, phone, address) VALUES (?, ?, ?)");
    $stmt->execute([$data->name, $data->phone, $data->address]);
    echo json_encode(["message" => "Thêm nhà cung cấp thành công"]);
    break;

  // ✏️ Sửa thông tin nhà cung cấp
  case 'PUT':
    $data = json_decode(file_get_contents("php://input"));
    if (!isset($data->id)) {
      http_response_code(400);
      echo json_encode(["error" => "Thiếu ID nhà cung cấp"]);
      exit;
    }

    $stmt = $conn->prepare("UPDATE suppliers SET name=?, phone=?, address=? WHERE id=?");
    $stmt->execute([$data->name, $data->phone, $data->address, $data->id]);
    echo json_encode(["message" => "Cập nhật nhà cung cấp thành công"]);
    break;

  // ❌ Xóa nhà cung cấp
  case 'DELETE':
    if (!isset($_GET['id'])) {
      http_response_code(400);
      echo json_encode(["error" => "Thiếu ID nhà cung cấp"]);
      exit;
    }

    $stmt = $conn->prepare("DELETE FROM suppliers WHERE id = ?");
    $stmt->execute([$_GET['id']]);
    echo json_encode(["message" => "Đã xóa nhà cung cấp"]);
    break;

  default:
    http_response_code(405);
    echo json_encode(["error" => "Phương thức không hợp lệ"]);
}
?>
