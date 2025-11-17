<?php
include_once(__DIR__ . '/../../config/database.php');

if (!isset($_GET['id'])) {
    die("Thiếu ID đơn hàng");
}

$order_id = $_GET['id'];

// Lấy thông tin đơn hàng
$stmt = $conn->prepare("SELECT total_price, status FROM orders WHERE id = ?");
$stmt->execute([$order_id]);
$order = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$order) {
    die("Không tìm thấy đơn hàng");
}

// Nếu đã hoàn tất thì không cộng doanh thu lại
if ($order['status'] == 'Hoàn tất') {
    header("Location: orders.php");
    exit;
}

$total = $order['total_price'];

// Cập nhật trạng thái đơn hàng → Hoàn tất
$update = $conn->prepare("UPDATE orders SET status = 'Hoàn tất' WHERE id = ?");
$update->execute([$order_id]);

// Cộng doanh thu ngay
$insert = $conn->prepare("
    INSERT INTO revenue (amount, created_at)
    VALUES (?, NOW())
");

$insert->execute([$total]);

header("Location: orders.php");
exit;
?>
