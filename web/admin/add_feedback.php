<?php
include_once(__DIR__ . '/../../config/database.php');

if ($_SERVER["REQUEST_METHOD"] === "POST") {
  $user_id = $_POST['user_id'];
  $product_id = $_POST['product_id'];
  $content = $_POST['content'];
  $rating = $_POST['rating'];

  $stmt = $conn->prepare("INSERT INTO feedback (user_id, product_id, content, rating) VALUES (?, ?, ?, ?)");
  $stmt->execute([$user_id, $product_id, $content, $rating]);

  header("Location: feedback.php");
  exit;
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <title>➕ Thêm phản hồi | PetShop Admin</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 text-gray-800">

  <div class="max-w-lg mx-auto mt-10 bg-white p-6 rounded-lg shadow">
    <h2 class="text-2xl font-bold mb-6">➕ Thêm phản hồi</h2>
    <form method="POST" class="space-y-4">
      <input type="number" name="user_id" placeholder="ID người dùng" required class="w-full border p-2 rounded">
      <input type="number" name="product_id" placeholder="ID sản phẩm" required class="w-full border p-2 rounded">
      <textarea name="content" placeholder="Nội dung phản hồi" required class="w-full border p-2 rounded"></textarea>
      <input type="number" name="rating" placeholder="Đánh giá (1-5)" min="1" max="5" required class="w-full border p-2 rounded">
      <button type="submit" class="bg-blue-600 text-white px-5 py-2 rounded hover:bg-blue-700">💾 Lưu</button>
      <a href="feedback.php" class="ml-3 text-gray-600 hover:underline">← Quay lại</a>
    </form>
  </div>
</body>
</html>
