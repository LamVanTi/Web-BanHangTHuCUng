<?php
include_once(__DIR__ . '/../../config/database.php');

if ($_SERVER["REQUEST_METHOD"] === "POST") {
  $name = $_POST['name'];
  $phone = $_POST['phone'];
  $address = $_POST['address'];

  $stmt = $conn->prepare("INSERT INTO suppliers (name, phone, address) VALUES (?, ?, ?)");
  $stmt->execute([$name, $phone, $address]);

  header("Location: suppliers.php");
  exit;
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <title>➕ Thêm nhà cung cấp</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 text-gray-800">
  <div class="max-w-lg mx-auto mt-10 bg-white p-6 rounded-lg shadow">
    <h2 class="text-2xl font-bold mb-6">➕ Thêm nhà cung cấp</h2>
    <form method="POST" class="space-y-4">
      <input type="text" name="name" placeholder="Tên nhà cung cấp" required class="w-full border p-2 rounded">
      <input type="text" name="phone" placeholder="Số điện thoại" required class="w-full border p-2 rounded">
      <input type="text" name="address" placeholder="Địa chỉ" required class="w-full border p-2 rounded">

      <button type="submit" class="bg-blue-600 text-white px-5 py-2 rounded hover:bg-blue-700">💾 Lưu</button>
      <a href="suppliers.php" class="ml-3 text-gray-600 hover:underline">← Quay lại</a>
    </form>
  </div>
</body>
</html>
