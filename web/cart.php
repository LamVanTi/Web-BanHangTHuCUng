<?php
session_start();
include_once(__DIR__ . '/../config/database.php');
include_once(__DIR__ . '/includes/header.php');

if (!isset($_SESSION['cart'])) $_SESSION['cart'] = [];

if (isset($_GET['add'])) {
    $id = intval($_GET['add']);
    $qty = $_GET['qty'] ? intval($_GET['qty']) : 1;
    $stmt = $conn->prepare("SELECT id, name, price, image FROM products WHERE id = :id");
    $stmt->execute([':id' => $id]);
    $product = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($product) {
        if (isset($_SESSION['cart'][$id])) $_SESSION['cart'][$id]['qty'] += $qty;
        else $_SESSION['cart'][$id] = [
            'name' => $product['name'],
            'price' => $product['price'],
            'image' => $product['image'],
            'qty' => $qty
        ];
    }
    header("Location: cart.php"); exit;
}

if (isset($_GET['remove'])) {
    $id = intval($_GET['remove']);
    unset($_SESSION['cart'][$id]);
    header("Location: cart.php"); exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_cart'])) {
    foreach ($_POST['qty'] as $id => $qty) $_SESSION['cart'][$id]['qty'] = max(1, intval($qty));
    header("Location: cart.php"); exit;
}

$total = 0;
foreach ($_SESSION['cart'] as $item) $total += $item['price'] * $item['qty'];
?>

<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>🛒 Giỏ hàng | PetShop</title>
<link rel="icon" type="image/png" href="../assets/logo.jpg">
<script src="https://cdn.tailwindcss.com"></script>
<link rel="stylesheet" href="assets/css/style.css">
<style>
/* Giỏ hàng style nâng cao */
.product-img { @apply w-20 h-20 object-cover rounded-lg border; }
.table-hover tbody tr:hover { @apply bg-gray-50; }
.qty-input { @apply w-16 border rounded text-center focus:ring-2 focus:ring-blue-300; }

/* Nút nổi bật */
.button-primary {
  padding: 0.75rem 2rem;
  border-radius: 1rem;
  font-weight: 700;
  color: #fff;
  text-align: center;
  cursor: pointer;
  transition: all 0.3s ease;
  box-shadow: 0 4px 12px rgba(0,0,0,0.2);
}

/* Nút Cập nhật */
.button-update {
  background: linear-gradient(45deg, #6B7280, #4B5563);
}
.button-update:hover {
  background: linear-gradient(45deg, #4B5563, #374151);
  transform: scale(1.08);
  box-shadow: 0 8px 16px rgba(0,0,0,0.3);
}

/* Nút Tiếp tục mua hàng */
.button-continue {
  background: linear-gradient(45deg, #3B82F6, #2563EB);
}
.button-continue:hover {
  background: linear-gradient(45deg, #2563EB, #1D4ED8);
  transform: scale(1.08);
  box-shadow: 0 8px 16px rgba(0,0,0,0.3);
}

/* Nút Thanh toán */
.button-checkout {
  background: linear-gradient(45deg, #10B981, #059669);
}
.button-checkout:hover {
  background: linear-gradient(45deg, #059669, #047857);
  transform: scale(1.08);
  box-shadow: 0 8px 16px rgba(0,0,0,0.3);
}

.total-box { @apply text-xl font-bold text-gray-700; }
</style>
</head>
<body class="bg-gray-50 text-gray-800">

<section class="text-center py-12 bg-gradient-to-r from-blue-500 to-indigo-600 text-white shadow-md">
  <h1 class="text-3xl font-extrabold">🛍️ Giỏ hàng của bạn</h1>
  <p class="mt-2 text-lg">Kiểm tra sản phẩm trước khi thanh toán</p>
</section>

<main class="container mx-auto p-6">
<?php if (empty($_SESSION['cart'])): ?>
  <div class="text-center text-gray-500 py-32">
    <p class="text-2xl">🛒 Giỏ hàng của bạn đang trống.</p>
    <a href="index.php" class="mt-6 inline-block button-primary button-continue">
      Tiếp tục mua hàng
    </a>
  </div>
<?php else: ?>
<form method="post">
  <div class="overflow-x-auto bg-white shadow-lg rounded-xl border border-gray-200 table-hover">
    <table class="min-w-full text-sm text-gray-700">
      <thead class="bg-gray-100 text-gray-600 uppercase">
        <tr>
          <th class="p-3 text-left">Sản phẩm</th>
          <th class="p-3 text-center">Giá</th>
          <th class="p-3 text-center">Số lượng</th>
          <th class="p-3 text-center">Thành tiền</th>
          <th class="p-3 text-center">Thao tác</th>
        </tr>
      </thead>
      <tbody>
    <?php foreach ($_SESSION['cart'] as $id => $item): ?>
    <tr class="border-t transition hover:bg-blue-50">
      <td class="flex items-center gap-4 p-3">
        <img src="../uploads/<?= htmlspecialchars($item['image'] ?? 'noimage.jpg') ?>"
             class="w-16 h-16 object-cover rounded shadow">
        <span class="font-semibold text-gray-800">
            <?= htmlspecialchars($item['name']) ?>
        </span>
      </td>

      <td class="text-center text-red-500 font-semibold">
        <?= number_format($item['price'],0,',','.') ?>₫
      </td>

      <td class="text-center">
        <input type="number" name="qty[<?= $id ?>]" value="<?= $item['qty'] ?>"
               min="1" class="qty-input border rounded w-16 text-center">
      </td>

      <td class="text-center font-bold text-gray-800">
        <?= number_format($item['price'] * $item['qty'],0,',','.') ?>₫
      </td>

      <td class="text-center">
        <a href="?remove=<?= $id ?>" class="text-red-500 hover:text-red-700 font-semibold">
          🗑️ Xóa
        </a>
      </td>
    </tr>
    <?php endforeach; ?>
</tbody>
    </table>
  </div>

  <div class="flex flex-col md:flex-row justify-between items-center mt-6 bg-white shadow-lg p-6 rounded-xl border border-gray-200">
    <div class="total-box">
      Tổng tiền: <span class="text-red-500"><?= number_format($total,0,',','.') ?>₫</span>
    </div>
    <div class="flex flex-wrap gap-3 mt-4 md:mt-0">
      <button type="submit" name="update_cart" class="button-primary button-update">
       Cập nhật giỏ hàng
      </button>
      <a href="index.php" class="button-primary button-continue">
       Tiếp tục mua hàng
      </a>
      <a href="checkout.php" class="button-primary button-checkout">
       Thanh toán
      </a>
    </div>
  </div>
</form>
<?php endif; ?>
</main>

<?php include_once(__DIR__ . '/includes/footer.php'); ?>
<script src="assets/js/main.js"></script>
</body>
</html> 