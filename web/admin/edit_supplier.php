<?php
include_once(__DIR__ . '/../../config/database.php');

$id = $_GET['id'] ?? null;
$stmt = $conn->prepare("SELECT * FROM suppliers WHERE id = ?");
$stmt->execute([$id]);
$supplier = $stmt->fetch(PDO::FETCH_ASSOC);

if ($_SERVER["REQUEST_METHOD"] === "POST") {
  $name = $_POST['name'];
  $phone = $_POST['phone'];
  $address = $_POST['address'];

  $stmt = $conn->prepare("UPDATE suppliers SET name=?, phone=?, address=? WHERE id=?");
  $stmt->execute([$name, $phone, $address, $id]);

  header("Location: suppliers.php");
  exit;
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <title>✏️ Sửa nhà cung cấp</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 text-gray-800">
  <div class="max-w-lg mx-auto mt-10 bg-white p-6 rounded-lg shadow">
    <h2 class="text-2xl font-bold mb-6">✏️ Sửa nhà cung cấp</h2>
    <form method="POST" class="space-y-4">
      <input type="text" name="name" value="<?= htmlspecialchars($supplier['name']) ?>" required class="w-full border p-2 rounded">
      <input type="text" name="phone" value="<?= htmlspecialchars($supplier['phone']) ?>" required class="w-full border p-2 rounded">
      <input type="text" name="address" value="<?= htmlspecialchars($supplier['address']) ?>" required class="w-full border p-2 rounded">

      <button type="submit" class="bg-blue-600 text-white px-5 py-2 rounded hover:bg-blue-700">💾 Cập nhật</button>
      <a href="suppliers.php" class="ml-3 text-gray-600 hover:underline">← Quay lại</a>
    </form>
  </div>
</body>
</html>
