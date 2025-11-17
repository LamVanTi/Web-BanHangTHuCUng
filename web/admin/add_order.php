<?php
include_once(__DIR__ . '/../../config/database.php');

$customers = $conn->query("SELECT * FROM customers ORDER BY name")->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $customer_id = $_POST['customer_id'];
    $total_price = $_POST['total_price'];
    $status = $_POST['status'];

    $stmt = $conn->prepare("INSERT INTO orders (customer_id, total_price, status, created_at) VALUES (?, ?, ?, NOW())");
    $stmt->execute([$customer_id, $total_price, $status]);

    header("Location: orders.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <title>➕ Thêm đơn hàng | PetShop Admin</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 text-gray-800">
  <div class="max-w-lg mx-auto mt-10 bg-white p-6 rounded-lg shadow">
    <h2 class="text-2xl font-bold mb-6">➕ Thêm đơn hàng mới</h2>
    <form method="POST" class="space-y-4">
      <div>
        <label class="block mb-1 font-medium">Khách hàng</label>
        <select name="customer_id" class="w-full border p-2 rounded">
          <option value="">-- Chọn khách hàng --</option>
          <?php foreach ($customers as $c): ?>
            <option value="<?= $c['id'] ?>"><?= htmlspecialchars($c['name']) ?></option>
          <?php endforeach; ?>
        </select>
      </div>

      <div>
        <label class="block mb-1 font-medium">Tổng tiền (VNĐ)</label>
        <input type="number" name="total_price" required class="w-full border p-2 rounded">
      </div>

      <div>
        <label class="block mb-1 font-medium">Trạng thái</label>
        <select name="status" class="w-full border p-2 rounded">
          <option value="Đang xử lý">Đang xử lý</option>
          <option value="Hoàn tất">Hoàn tất</option>
          <option value="Hủy">Hủy</option>
        </select>
      </div>

      <button type="submit" class="bg-blue-600 text-white px-5 py-2 rounded hover:bg-blue-700">💾 Lưu</button>
      <a href="orders.php" class="ml-3 text-gray-600 hover:underline">← Quay lại</a>
    </form>
  </div>
</body>
</html>
