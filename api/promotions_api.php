<?php
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

include_once(__DIR__ . "/../config/database.php");

$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
  // 📘 Lấy danh sách hoặc chi tiết khuyến mãi
  case 'GET':
    if (isset($_GET['id'])) {
      $stmt = $conn->prepare("SELECT * FROM promotions WHERE id = ?");
      $stmt->execute([$_GET['id']]);
      echo json_encode($stmt->fetch(PDO::FETCH_ASSOC));
    } else {
      $stmt = $conn->query("SELECT * FROM promotions ORDER BY id DESC");
      echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
    }
    break;

  // ➕ Thêm khuyến mãi
  case 'POST':
    $data = json_decode(file_get_contents("php://input"));
    $stmt = $conn->prepare("INSERT INTO promotions (code, discount, expiry_date) VALUES (?, ?, ?)");
    $stmt->execute([$data->code, $data->discount, $data->expiry_date]);
    echo json_encode(["message" => "Thêm khuyến mãi thành công"]);
    break;

  // ✏️ Cập nhật khuyến mãi
  case 'PUT':
    $data = json_decode(file_get_contents("php://input"));
    $stmt = $conn->prepare("UPDATE promotions SET code=?, discount=?, expiry_date=? WHERE id=?");
    $stmt->execute([$data->code, $data->discount, $data->expiry_date, $data->id]);
    echo json_encode(["message" => "Cập nhật khuyến mãi thành công"]);
    break;

  // ❌ Xóa khuyến mãi
  case 'DELETE':
    if (isset($_GET['id'])) {
      $stmt = $conn->prepare("DELETE FROM promotions WHERE id=?");
      $stmt->execute([$_GET['id']]);
      echo json_encode(["message" => "Đã xóa khuyến mãi"]);
    }
    break;

  default:
    http_response_code(405);
    echo json_encode(["error" => "Phương thức không hợp lệ"]);
}
?>
