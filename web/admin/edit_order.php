<?php
include_once(__DIR__ . '/../../config/database.php');

$id = $_GET['id'] ?? null;
$stmt = $conn->prepare("SELECT * FROM orders WHERE id = ?");
$stmt->execute([$id]);
$order = $stmt->fetch(PDO::FETCH_ASSOC);

$customers = $conn->query("SELECT * FROM customers ORDER BY name")->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $customer_id = $_POST['customer_id'];
    $total_price = $_POST['total_price'];
    $status = $_POST['status'];

    $stmt = $conn->prepare("UPDATE orders SET customer_id=?, total_price=?, status=? WHERE id=?");
    $stmt->execute([$customer_id, $total_price, $status, $id]);

    header("Location: orders.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <title>✏️ Sửa đơn hàng | PetShop Admin</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 text-gray-800">
  <div class="max-w-lg mx-auto mt-10 bg-white p-6 rounded-lg shadow">
    <h2 class="text-2xl font-bold mb-6">✏️ Sửa đơn hàng</h2>
    <form method="POST" class="space-y-4">
      <div>
        <label class="block mb-1 font-medium">Khách hàng</label>
        <select name="customer_id" class="w-full border p-2 rounded">
          <?php foreach ($customers as $c): ?>
            <option value="<?= $c['id'] ?>" <?= $order['customer_id'] == $c['id'] ? 'selected' : '' ?>>
              <?= htmlspecialchars($c['name']) ?>
            </option>
          <?php endforeach; ?>
        </select>
      </div>

      <div>
        <label class="block mb-1 font-medium">Tổng tiền (VNĐ)</label>
        <input type="number" name="total_price" value="<?= htmlspecialchars($order['total_price']) ?>" required class="w-full border p-2 rounded">
      </div>

      <div>
        <label class="block mb-1 font-medium">Trạng thái</label>
        <select name="status" class="w-full border p-2 rounded">
          <?php foreach (['Đang xử lý', 'Hoàn tất', 'Hủy'] as $status): ?>
            <option value="<?= $status ?>" <?= $order['status'] == $status ? 'selected' : '' ?>><?= $status ?></option>
          <?php endforeach; ?>
        </select>
      </div>

      <button type="submit" class="bg-blue-600 text-white px-5 py-2 rounded hover:bg-blue-700">💾 Cập nhật</button>
      <a href="orders.php" class="ml-3 text-gray-600 hover:underline">← Quay lại</a>
    </form>
  </div>
</body>
</html>
