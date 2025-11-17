<?php
include_once(__DIR__ . '/../../config/database.php');

// Lấy ID khuyến mãi từ URL
$id = $_GET['id'] ?? null;
$stmt = $conn->prepare("SELECT * FROM promotions WHERE id = ?");
$stmt->execute([$id]);
$promo = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$promo) {
  echo "<p class='text-center text-red-600 mt-10'>❌ Không tìm thấy khuyến mãi.</p>";
  exit;
}

// Lấy danh sách sản phẩm để hiển thị trong dropdown
$productStmt = $conn->query("SELECT id, name FROM products ORDER BY name ASC");
$products = $productStmt->fetchAll(PDO::FETCH_ASSOC);

// Cập nhật khuyến mãi
if ($_SERVER["REQUEST_METHOD"] === "POST") {
  $code = $_POST['code'];
  $discount = $_POST['discount'];
  $expiry_date = $_POST['expiry_date'];
  $product_id = $_POST['product_id'];
  $status = $_POST['status'];

  $stmt = $conn->prepare("UPDATE promotions 
                          SET code=?, discount=?, expiry_date=?, product_id=?, status=? 
                          WHERE id=?");
  $stmt->execute([$code, $discount, $expiry_date, $product_id, $status, $id]);

  header("Location: promotions.php");
  exit;
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <title>✏️ Sửa khuyến mãi | PetShop Admin</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 text-gray-800">

  <div class="max-w-2xl mx-auto mt-10 bg-white p-8 rounded-xl shadow-lg">
    <h2 class="text-3xl font-bold mb-6 text-blue-700">✏️ Sửa khuyến mãi</h2>

    <form method="POST" class="space-y-6">
      <!-- Mã khuyến mãi -->
      <div>
        <label class="block mb-2 font-semibold text-gray-700">Mã khuyến mãi</label>
        <input type="text" name="code" value="<?= htmlspecialchars($promo['code']) ?>" 
               required class="w-full border border-gray-300 p-2 rounded-lg focus:ring-2 focus:ring-blue-400">
      </div>

      <!-- Phần trăm giảm giá -->
      <div>
        <label class="block mb-2 font-semibold text-gray-700">Giảm giá (%)</label>
        <input type="number" name="discount" value="<?= htmlspecialchars($promo['discount']) ?>" 
               min="1" max="100" required 
               class="w-full border border-gray-300 p-2 rounded-lg focus:ring-2 focus:ring-blue-400">
      </div>

      <!-- Ngày hết hạn -->
      <div>
        <label class="block mb-2 font-semibold text-gray-700">Ngày hết hạn</label>
        <input type="date" name="expiry_date" value="<?= htmlspecialchars($promo['expiry_date']) ?>" 
               required class="w-full border border-gray-300 p-2 rounded-lg focus:ring-2 focus:ring-blue-400">
      </div>

      <!-- Chọn sản phẩm -->
      <div>
        <label class="block mb-2 font-semibold text-gray-700">Sản phẩm áp dụng</label>
        <select name="product_id" required 
                class="w-full border border-gray-300 p-2 rounded-lg focus:ring-2 focus:ring-blue-400">
          <option value="">-- Chọn sản phẩm --</option>
          <?php foreach ($products as $p): ?>
            <option value="<?= $p['id'] ?>" <?= $p['id'] == $promo['product_id'] ? 'selected' : '' ?>>
              <?= htmlspecialchars($p['name']) ?>
            </option>
          <?php endforeach; ?>
        </select>
      </div>

      <!-- Trạng thái -->
      <div>
        <label class="block mb-2 font-semibold text-gray-700">Trạng thái</label>
        <select name="status" class="w-full border border-gray-300 p-2 rounded-lg focus:ring-2 focus:ring-blue-400">
          <option value="1" <?= $promo['status'] == 1 ? 'selected' : '' ?>>Hoạt động ✅</option>
          <option value="0" <?= $promo['status'] == 0 ? 'selected' : '' ?>>Tạm dừng ⏸️</option>
        </select>
      </div>

      <!-- Nút hành động -->
      <div class="pt-4 flex justify-between items-center">
        <a href="promotions.php" class="text-gray-600 hover:text-blue-600">← Quay lại danh sách</a>
        <button type="submit" class="bg-blue-600 text-white px-5 py-2 rounded-lg font-semibold hover:bg-blue-700 transition">
          💾 Cập nhật khuyến mãi
        </button>
      </div>
    </form>
  </div>

</body>
</html>
