<?php
session_start();
include_once(__DIR__ . '/../config/database.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_SESSION['cart'])) {
  $fullname = trim($_POST['fullname']);
  $phone = trim($_POST['phone']);
  $address = trim($_POST['address']);
  $payment = $_POST['payment'];

  $total = 0;
  foreach ($_SESSION['cart'] as $item) {
    $total += $item['price'] * $item['qty'];
  }

  // Thêm vào bảng orders
  $stmt = $conn->prepare("
    INSERT INTO orders (customer_name, phone, address, payment_method, total_price, created_at)
    VALUES (:name, :phone, :address, :payment, :total, NOW())
  ");
  $stmt->execute([
    ':name' => $fullname,
    ':phone' => $phone,
    ':address' => $address,
    ':payment' => $payment,
    ':total' => $total
  ]);
  $order_id = $conn->lastInsertId();

  // Thêm chi tiết đơn hàng
  $detail_stmt = $conn->prepare("
    INSERT INTO order_details (order_id, product_id, quantity, price)
    VALUES (:order_id, :pid, :qty, :price)
  ");

  foreach ($_SESSION['cart'] as $pid => $item) {
    $detail_stmt->execute([
      ':order_id' => $order_id,
      ':pid' => $pid,
      ':qty' => $item['qty'],
      ':price' => $item['price']
    ]);
  }

  // Xóa giỏ hàng
  $_SESSION['cart'] = [];
} else {
  header("Location: cart.php");
  exit;
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <title>✅ Đặt hàng thành công | PetShop</title>
  <link rel="icon" href="../assets/logo.jpg">
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 text-gray-800">
  <div class="container mx-auto text-center py-20">
    <img src="https://cdn-icons-png.flaticon.com/512/845/845646.png" alt="success" class="mx-auto w-24 mb-6">
    <h1 class="text-3xl font-bold text-green-600 mb-3">Đặt hàng thành công 🎉</h1>
    <p class="text-gray-600 mb-6">Cảm ơn bạn <strong><?= htmlspecialchars($fullname) ?></strong>!  
      Đơn hàng của bạn (#<?= $order_id ?>) đã được ghi nhận.</p>
    <a href="index.php" class="bg-blue-500 text-white px-6 py-3 rounded-lg hover:bg-blue-600">Về trang chủ</a>
  </div>
</body>
</html>
