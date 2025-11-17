<?php
include_once(__DIR__ . '/../../config/database.php');
$stmt = $conn->query("SELECT * FROM categories ORDER BY id DESC");
$categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <title>📁 Quản lý danh mục | PetShop Admin</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 text-gray-800">
  <header class="bg-white shadow p-4 flex justify-between items-center">
    <h1 class="text-2xl font-bold">📁 Quản lý danh mục</h1>
    <a href="index.php" class="text-blue-600 hover:underline">← Trở về Dashboard</a>
  </header>

  <main class="p-6">
    <div class="bg-white p-6 rounded-lg shadow-lg">
      <div class="flex justify-between mb-4">
        <h2 class="text-xl font-semibold">Danh sách danh mục</h2>
        <a href="category-add.php" class="bg-green-500 text-white px-4 py-2 rounded hover:bg-green-600">➕ Thêm danh mục</a>
      </div>

      <div class="overflow-x-auto">
        <table class="min-w-full border border-gray-200 text-sm">
          <thead class="bg-gray-100">
            <tr>
              <th class="p-3 border w-12">#</th>
              <th class="p-3 border text-left">Tên danh mục</th>
              <th class="p-3 border text-center w-40">Ngày tạo</th>
              <th class="p-3 border text-center w-40">Hành động</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($categories as $index => $c): ?>
              <tr class="hover:bg-gray-50">
                <td class="p-3 border text-center"><?= $index + 1 ?></td>
                <td class="p-3 border"><?= htmlspecialchars($c['name']) ?></td>
                <td class="p-3 border text-center"><?= date('d/m/Y', strtotime($c['created_at'])) ?></td>
                <td class="p-3 border text-center">
                  <a href="category-edit.php?id=<?= $c['id'] ?>" class="bg-yellow-400 text-white px-3 py-1 rounded hover:bg-yellow-300">✏️ Sửa</a>
                  <a href="category-delete.php?id=<?= $c['id'] ?>" onclick="return confirm('Bạn có chắc muốn xóa danh mục này?')" class="bg-red-500 text-white px-3 py-1 rounded hover:bg-red-300">🗑️ Xóa</a>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>
  </main>
</body>
</html>
