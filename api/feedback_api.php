<?php
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

include_once(__DIR__ . "/../config/database.php");

$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
  case 'GET':
    if (isset($_GET['id'])) {
      $stmt = $conn->prepare("
        SELECT f.*, u.username AS user_name, p.name AS product_name
        FROM feedback f
        LEFT JOIN users u ON f.user_id = u.id
        LEFT JOIN products p ON f.product_id = p.id
        WHERE f.id = ?
      ");
      $stmt->execute([$_GET['id']]);
      echo json_encode($stmt->fetch(PDO::FETCH_ASSOC));
    } else {
      $sql = "
        SELECT f.*, u.username AS user_name, p.name AS product_name
        FROM feedback f
        LEFT JOIN users u ON f.user_id = u.id
        LEFT JOIN products p ON f.product_id = p.id
        ORDER BY f.id DESC
      ";
      $stmt = $conn->query($sql);
      echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
    }
    break;

  case 'POST':
    $data = json_decode(file_get_contents("php://input"));
    $stmt = $conn->prepare("INSERT INTO feedback (user_id, product_id, content, rating) VALUES (?, ?, ?, ?)");
    $stmt->execute([$data->user_id, $data->product_id, $data->content, $data->rating]);
    echo json_encode(["message" => "Thêm phản hồi thành công"]);
    break;

  case 'PUT':
    $data = json_decode(file_get_contents("php://input"));
    $stmt = $conn->prepare("UPDATE feedback SET user_id=?, product_id=?, content=?, rating=? WHERE id=?");
    $stmt->execute([$data->user_id, $data->product_id, $data->content, $data->rating, $data->id]);
    echo json_encode(["message" => "Cập nhật phản hồi thành công"]);
    break;

  case 'DELETE':
    if (isset($_GET['id'])) {
      $stmt = $conn->prepare("DELETE FROM feedback WHERE id=?");
      $stmt->execute([$_GET['id']]);
      echo json_encode(["message" => "Đã xóa phản hồi"]);
    }
    break;

  default:
    http_response_code(405);
    echo json_encode(["error" => "Phương thức không hợp lệ"]);
}
?>
