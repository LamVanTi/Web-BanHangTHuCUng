<?php
session_start();
include_once(__DIR__ . '/../config/database.php');
include_once(__DIR__ . '/includes/header.php');

if (empty($_SESSION['cart'])) {
  header("Location: cart.php");
  exit;
}

// Tổng tiền
$total = 0;
foreach ($_SESSION['cart'] as $item) {
  $total += $item['price'] * $item['qty'];
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>💳 Thanh toán | PetShop</title>
  <link rel="icon" href="../assets/logo.jpg">
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 text-gray-800">

<section class="text-center py-10 bg-gradient-to-r from-green-500 to-emerald-600 text-white">
  <h1 class="text-3xl font-bold">💳 Thanh toán</h1>
  <p>Hoàn tất đơn hàng của bạn trong vài bước</p>
</section>

<main class="container mx-auto grid md:grid-cols-2 gap-8 p-6">
  <!-- Thông tin khách hàng -->
  <div class="bg-white shadow rounded-lg p-6">
    <h2 class="text-xl font-semibold mb-4">📋 Thông tin giao hàng</h2>
    <form method="post" action="order_success.php">
      <input type="text" name="fullname" required placeholder="Họ và tên" class="w-full border rounded-lg p-2 mb-3">
      <input type="text" name="phone" required placeholder="Số điện thoại" class="w-full border rounded-lg p-2 mb-3">
      <input type="text" name="address" required placeholder="Địa chỉ giao hàng" class="w-full border rounded-lg p-2 mb-3">
      <select name="payment" class="w-full border rounded-lg p-2 mb-3">
        <option value="COD">Thanh toán khi nhận hàng (COD)</option>
        <option value="Bank">Chuyển khoản ngân hàng</option>
      </select>
      <button type="submit" class="w-full bg-green-600 text-white py-3 rounded-lg hover:bg-green-700">
        ✅ Xác nhận đặt hàng
      </button>
    </form>
  </div>

  <!-- Tổng đơn hàng -->
  <div class="bg-white shadow rounded-lg p-6">
    <h2 class="text-xl font-semibold mb-4">🧾 Đơn hàng của bạn</h2>
    <?php foreach ($_SESSION['cart'] as $item): ?>
      <div class="flex justify-between border-b py-2">
        <span><?= htmlspecialchars($item['name']) ?> x<?= $item['qty'] ?></span>
        <span><?= number_format($item['price'] * $item['qty'], 0, ',', '.') ?>₫</span>
      </div>
    <?php endforeach; ?>
    <div class="text-right mt-4 text-xl font-bold text-red-500">
      Tổng cộng: <?= number_format($total, 0, ',', '.') ?>₫
    </div>
  </div>
</main>

<?php include_once(__DIR__ . '/includes/footer.php'); ?>
</body>
</html>
