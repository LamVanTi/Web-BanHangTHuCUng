<?php
include_once(__DIR__ . '/../../config/database.php');

$stmt = $conn->query("SELECT * FROM users ORDER BY id DESC");
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <title>👥 Quản lý người dùng | PetShop Admin</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 text-gray-800">

  <header class="bg-white shadow p-4 flex justify-between items-center">
    <h1 class="text-2xl font-bold">👥 Quản lý người dùng</h1>
    <a href="index.php" class="text-blue-600 hover:underline">← Quay lại Dashboard</a>
  </header>

  <main class="p-6">
    <div class="bg-white p-6 rounded-lg shadow">
      <div class="flex justify-between mb-4">
        <h2 class="text-xl font-semibold">Danh sách người dùng</h2>
        <a href="add_user.php" class="bg-green-500 text-white px-4 py-2 rounded hover:bg-green-600">➕ Thêm người dùng</a>
      </div>

      <div class="overflow-x-auto">
        <table class="min-w-full border border-gray-300 text-sm">
          <thead class="bg-gray-100">
            <tr>
              <th class="p-3 border">#</th>
              <th class="p-3 border text-left">Tên đăng nhập</th>
              <th class="p-3 border text-left">Mật khẩu</th>
              <th class="p-3 border text-left">Email</th>
              <th class="p-3 border text-center">Ngày tạo</th>
              <th class="p-3 border text-center">Hành động</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($users as $index => $u): ?>
            <tr class="hover:bg-gray-50">
              <td class="p-3 border text-center"><?= $index + 1 ?></td>
              <td class="p-3 border"><?= htmlspecialchars($u['username']) ?></td>
              <td class="p-3 border"><?= htmlspecialchars($u['password']) ?></td>
              <td class="p-3 border"><?= htmlspecialchars($u['email']) ?></td>
              <td class="p-3 border text-center"><?= date('d/m/Y', strtotime($u['created_at'])) ?></td>
              <td class="p-3 border text-center">
                <a href="edit_user.php?id=<?= $u['id'] ?>" class="bg-yellow-400 text-white px-3 py-1 rounded hover:bg-yellow-500">✏️</a>
                <a href="delete_user.php?id=<?= $u['id'] ?>" onclick="return confirm('Bạn có chắc muốn xóa người dùng này?')" class="bg-red-500 text-white px-3 py-1 rounded hover:bg-red-600">🗑️</a>
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
