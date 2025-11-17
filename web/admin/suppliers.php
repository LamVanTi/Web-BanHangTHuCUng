<?php
include_once(__DIR__ . '/../../config/database.php');
$stmt = $conn->query("SELECT * FROM suppliers ORDER BY id DESC");
$suppliers = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <title>🏢 Quản lý nhà cung cấp | PetShop Admin</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 text-gray-800">
  <header class="bg-white shadow p-4 flex justify-between items-center">
    <h1 class="text-2xl font-bold">🏢 Quản lý nhà cung cấp</h1>
    <a href="index.php" class="text-blue-600 hover:underline">← Quay lại Dashboard</a>
  </header>

  <main class="p-6">
    <div class="bg-white p-6 rounded-lg shadow">
      <div class="flex justify-between mb-4">
        <h2 class="text-xl font-semibold">Danh sách nhà cung cấp</h2>
        <a href="add_supplier.php" class="bg-green-500 text-white px-4 py-2 rounded hover:bg-green-600">➕ Thêm nhà cung cấp</a>
      </div>

      <div class="overflow-x-auto">
        <table class="min-w-full border border-gray-300 text-sm">
          <thead class="bg-gray-100">
            <tr>
              <th class="p-3 border">#</th>
              <th class="p-3 border text-left">Tên nhà cung cấp</th>
              <th class="p-3 border text-left">Số điện thoại</th>
              <th class="p-3 border text-left">Địa chỉ</th>
              <th class="p-3 border text-center">Ngày tạo</th>
              <th class="p-3 border text-center">Hành động</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($suppliers as $index => $s): ?>
              <tr class="hover:bg-gray-50">
                <td class="p-3 border text-center"><?= $index + 1 ?></td>
                <td class="p-3 border"><?= htmlspecialchars($s['name']) ?></td>
                <td class="p-3 border"><?= htmlspecialchars($s['phone']) ?></td>
                <td class="p-3 border"><?= htmlspecialchars($s['address']) ?></td>
                <td class="p-3 border text-center"><?= date('d/m/Y', strtotime($s['created_at'])) ?></td>
                <td class="p-3 border text-center">
                  <a href="edit_supplier.php?id=<?= $s['id'] ?>" class="bg-yellow-400 text-white px-3 py-1 rounded hover:bg-yellow-500">✏️</a>
                  <a href="delete_supplier.php?id=<?= $s['id'] ?>" onclick="return confirm('Bạn có chắc muốn xóa nhà cung cấp này?')" class="bg-red-500 text-white px-3 py-1 rounded hover:bg-red-600">🗑️</a>
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
