<?php
include_once(__DIR__ . '/../../config/database.php');
$sql = "
  SELECT f.*, u.username AS user_name, p.name AS product_name
  FROM feedback f
  LEFT JOIN users u ON f.user_id = u.id
  LEFT JOIN products p ON f.product_id = p.id
  ORDER BY f.id DESC
";
$stmt = $conn->query($sql);
$feedbacks = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <title>💬 Quản lý phản hồi | PetShop Admin</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 text-gray-800">

  <header class="bg-white shadow p-4 flex justify-between items-center">
    <h1 class="text-2xl font-bold">💬 Quản lý phản hồi</h1>
    <a href="index.php" class="text-blue-600 hover:underline">← Quay lại Dashboard</a>
  </header>

  <main class="p-6">
    <div class="bg-white p-6 rounded-lg shadow">
      <div class="flex justify-between mb-4">
        <h2 class="text-xl font-semibold">Danh sách phản hồi</h2>
        <a href="add_feedback.php" class="bg-green-500 text-white px-4 py-2 rounded hover:bg-green-600">➕ Thêm phản hồi</a>
      </div>

      <table class="min-w-full border border-gray-300 text-sm">
        <thead class="bg-gray-100">
          <tr>
            <th class="p-3 border">#</th>
            <th class="p-3 border">Người dùng</th>
            <th class="p-3 border">Sản phẩm</th>
            <th class="p-3 border">Nội dung</th>
            <th class="p-3 border text-center">Đánh giá</th>
            <th class="p-3 border text-center">Ngày tạo</th>
            <th class="p-3 border text-center">Hành động</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($feedbacks as $index => $f): ?>
          <tr class="hover:bg-gray-50">
            <td class="p-3 border text-center"><?= $index+1 ?></td>
            <td class="p-3 border"><?= htmlspecialchars($f['user_name'] ?? 'Ẩn danh') ?></td>
            <td class="p-3 border"><?= htmlspecialchars($f['product_name'] ?? '-') ?></td>
            <td class="p-3 border"><?= htmlspecialchars($f['content']) ?></td>
            <td class="p-3 border text-center text-yellow-600 font-semibold"><?= $f['rating'] ?>/5</td>
            <td class="p-3 border text-center"><?= date('d/m/Y', strtotime($f['created_at'])) ?></td>
            <td class="p-3 border text-center">
              <a href="edit_feedback.php?id=<?= $f['id'] ?>" class="bg-yellow-400 text-white px-3 py-1 rounded hover:bg-yellow-500">✏️</a>
              <a href="delete_feedback.php?id=<?= $f['id'] ?>" onclick="return confirm('Xóa phản hồi này?')" class="bg-red-500 text-white px-3 py-1 rounded hover:bg-red-600">🗑️</a>
            </td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </main>
</body>
</html>
