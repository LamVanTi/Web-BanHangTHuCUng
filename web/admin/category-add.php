<?php
include_once(__DIR__ . '/../../config/database.php');

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $name = trim($_POST['name']);
    if ($name !== '') {
        $stmt = $conn->prepare("INSERT INTO categories (name) VALUES (?)");
        $stmt->execute([$name]);
    }
    header("Location: category.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <title>➕ Thêm danh mục | PetShop Admin</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 text-gray-800">
  <div class="max-w-lg mx-auto mt-10 bg-white p-6 rounded-lg shadow">
    <h2 class="text-2xl font-bold mb-6">➕ Thêm danh mục mới</h2>
    <form method="POST" class="space-y-4">
      <div>
        <label class="block mb-1 font-medium">Tên danh mục</label>
        <input type="text" name="name" required class="w-full border p-2 rounded" placeholder="Nhập tên danh mục...">
      </div>
      <button type="submit" class="bg-blue-600 text-white px-5 py-2 rounded hover:bg-blue-700">💾 Lưu</button>
      <a href="category.php" class="ml-3 text-gray-600 hover:underline">← Quay lại</a>
    </form>
  </div>
</body>
</html>
