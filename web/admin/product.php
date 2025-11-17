<?php
include_once(__DIR__ . '/../../config/database.php');

$query = "
    SELECT p.*, c.name AS category_name, s.name AS supplier_name
    FROM products p
    LEFT JOIN categories c ON p.category_id = c.id
    LEFT JOIN suppliers s ON p.supplier_id = s.id
    ORDER BY p.id DESC
";
$stmt = $conn->query($query);
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <title>🐾 Quản lý sản phẩm | PetShop Admin</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 text-gray-800">
  <header class="bg-white shadow p-4 flex justify-between items-center">
    <h1 class="text-2xl font-bold">📦 Quản lý sản phẩm</h1>
    <a href="index.php" class="text-blue-600 hover:underline">← Trở về Dashboard</a>
  </header>

  <main class="p-6">
    <div class="bg-white p-6 rounded-lg shadow-lg">
      <div class="flex justify-between mb-4">
        <h2 class="text-xl font-semibold">Danh sách sản phẩm</h2>
        <a href="product-add.php" class="bg-green-500 text-white px-4 py-2 rounded hover:bg-green-600">➕ Thêm sản phẩm</a>
      </div>

      <div class="overflow-x-auto">
        <table class="min-w-full border border-gray-200 text-sm">
          <thead class="bg-gray-100">
            <tr>
              <th class="p-3 border">#</th>
              <th class="p-3 border">Hình ảnh</th>
              <th class="p-3 border text-left">Tên sản phẩm</th>
              <th class="p-3 border text-right">Giá (VNĐ)</th>
              <th class="p-3 border text-left">Danh mục</th>
              <th class="p-3 border text-left">Nhà cung cấp</th>
              <th class="p-3 border">Ngày tạo</th>
              <th class="p-3 border text-center">Hành động</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($products as $index => $p): ?>
              <tr class="hover:bg-gray-50">
                <td class="p-3 border text-center"><?= $index + 1 ?></td>
                <td class="p-3 border text-center">
                  <img src="../../uploads/<?= htmlspecialchars($p['image'] ?? 'noimage.jpg') ?>"
                       class="w-12 h-12 object-cover rounded">
                </td>
                <td class="p-3 border"><?= htmlspecialchars($p['name']) ?></td>
                <td class="p-3 border text-right text-green-600 font-semibold">
                  <?= number_format($p['price'], 0, ',', '.') ?>
                </td>
                <td class="p-3 border"><?= htmlspecialchars($p['category_name'] ?? '-') ?></td>
                <td class="p-3 border"><?= htmlspecialchars($p['supplier_name'] ?? '-') ?></td>
                <td class="p-3 border text-center"><?= date('d/m/Y', strtotime($p['created_at'])) ?></td>
                <td class="p-3 border text-center">
                  <a href="product-edit.php?id=<?= $p['id'] ?>"
                     class="bg-yellow-400 text-white px-3 py-1 rounded hover:bg-yellow-500">✏️ Sửa</a>
                  <a href="product-delete.php?id=<?= $p['id'] ?>"
                     onclick="return confirm('Bạn có chắc muốn xóa sản phẩm này?')"
                     class="bg-red-500 text-white px-3 py-1 rounded hover:bg-red-600">🗑️ Xóa</a>
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
