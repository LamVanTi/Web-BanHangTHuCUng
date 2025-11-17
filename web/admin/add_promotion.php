<?php
include_once(__DIR__ . '/../../config/database.php');

// Lấy danh sách sản phẩm để hiển thị trong <select>
$stmt = $conn->prepare("SELECT id, name FROM products");
$stmt->execute();
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER["REQUEST_METHOD"] === "POST") {
  $product_id = $_POST['product_id'];
  $code = $_POST['code'];
  $discount = $_POST['discount'];
  $expiry_date = $_POST['expiry_date'];

  $stmt = $conn->prepare("INSERT INTO promotions (product_id, code, discount, expiry_date) VALUES (?, ?, ?, ?)");
  $stmt->execute([$product_id, $code, $discount, $expiry_date]);

  header("Location: promotions.php");
  exit;
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <title>➕ Thêm khuyến mãi | PetShop Admin</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 text-gray-800">

  <div class="max-w-lg mx-auto mt-10 bg-white p-6 rounded-lg shadow">
    <h2 class="text-2xl font-bold mb-6">➕ Thêm khuyến mãi</h2>

    <form method="POST" class="space-y-4">
      <div>
        <label class="block mb-1 font-medium">Chọn sản phẩm</label>
        <select name="product_id" required class="w-full border p-2 rounded">
          <option value="">-- Chọn sản phẩm --</option>
          <?php foreach ($products as $p): ?>
            <option value="<?= $p['id'] ?>"><?= htmlspecialchars($p['name']) ?></option>
          <?php endforeach; ?>
        </select>
      </div>

      <div>
        <label class="block mb-1 font-medium">Mã khuyến mãi</label>
        <input type="text" name="code" required placeholder="VD: SALE50" class="w-full border p-2 rounded">
      </div>

      <div>
        <label class="block mb-1 font-medium">Giảm giá (%)</label>
        <input type="number" name="discount" required min="1" max="100" placeholder="VD: 10" class="w-full border p-2 rounded">
      </div>

      <div>
        <label class="block mb-1 font-medium">Ngày hết hạn</label>
        <input type="date" name="expiry_date" required class="w-full border p-2 rounded">
      </div>

      <div class="pt-4 flex justify-between items-center">
        <button type="submit" class="bg-blue-600 text-white px-5 py-2 rounded hover:bg-blue-700">💾 Lưu</button>
        <a href="promotions.php" class="text-gray-600 hover:underline">← Quay lại</a>
      </div>
    </form>
  </div>

</body>
</html>
