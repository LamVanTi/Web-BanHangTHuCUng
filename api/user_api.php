<?php
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Origin: *");

include_once(__DIR__ . "/../config/database.php");

$action = $_GET['action'] ?? '';

switch ($action) {
  case 'register':
    if (empty($_POST['username']) || empty($_POST['password']) || empty($_POST['email'])) {
      echo json_encode(["error" => "Thiếu dữ liệu (username, password, email)"]);
      exit;
    }

    $username = $_POST['username'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $email = $_POST['email'];

    $check = $conn->prepare("SELECT * FROM users WHERE username = ?");
    $check->execute([$username]);
    if ($check->rowCount() > 0) {
      echo json_encode(["error" => "Tên đăng nhập đã tồn tại!"]);
      exit;
    }

    $stmt = $conn->prepare("INSERT INTO users (username, password, email) VALUES (?, ?, ?)");
    $stmt->execute([$username, $password, $email]);
    echo json_encode(["success" => "Đăng ký thành công!"]);
    break;

  case 'login':
    if (empty($_POST['username']) || empty($_POST['password'])) {
      echo json_encode(["error" => "Thiếu tên đăng nhập hoặc mật khẩu"]);
      exit;
    }

    $username = $_POST['username'];
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($password, $user['password'])) {
      session_start();
      $_SESSION['user'] = $user;
      echo json_encode(["success" => "Đăng nhập thành công!"]);
    } else {
      echo json_encode(["error" => "Sai tài khoản hoặc mật khẩu"]);
    }
    break;

  default:
    echo json_encode(["error" => "Thiếu action"]);
}
?>
