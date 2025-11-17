<?php
include_once(__DIR__ . '/../../config/database.php');

// Lấy danh sách khuyến mãi kèm sản phẩm (nếu có liên kết)
$sql = "
  SELECT pr.id, pr.code, pr.discount, pr.expiry_date, pr.created_at, 
         p.name AS product_name
  FROM promotions pr
  LEFT JOIN products p ON pr.product_id = p.id
  ORDER BY pr.id DESC
";
$stmt = $conn->prepare($sql);
$stmt->execute();
$promos = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <title>🏷️ Quản lý khuyến mãi</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 text-gray-800 min-h-screen">
  <!-- Header -->
  <header class="bg-white shadow p-4 flex justify-between items-center sticky top-0 z-10">
    <h1 class="text-2xl font-bold text-blue-700 flex items-center gap-2">🏷️ Quản lý khuyến mãi</h1>
    <a href="index.php" class="text-blue-600 hover:underline">← Quay lại Dashboard</a>
  </header>

  <main class="p-6">
    <div class="bg-white p-6 rounded-lg shadow-lg">
      <div class="flex justify-between items-center mb-5">
        <h2 class="text-xl font-semibold">📋 Danh sách khuyến mãi</h2>
        <a href="add_promotion.php" 
           class="bg-green-500 text-white px-4 py-2 rounded-lg shadow hover:bg-green-600 transition">
          ➕ Thêm khuyến mãi
        </a>
      </div>

      <?php if (empty($promos)): ?>
        <p class="text-center text-gray-500 italic py-6">Hiện chưa có chương trình khuyến mãi nào.</p>
      <?php else: ?>
      <div class="overflow-x-auto">
        <table class="min-w-full border border-gray-300 text-sm rounded-lg overflow-hidden">
          <thead class="bg-blue-100 text-blue-800 uppercase">
            <tr>
              <th class="p-3 border">#</th>
              <th class="p-3 border text-left">Mã code</th>
              <th class="p-3 border">Giảm (%)</th>
              <th class="p-3 border text-left">Sản phẩm áp dụng</th>
              <th class="p-3 border">Ngày hết hạn</th>
              <th class="p-3 border">Ngày tạo</th>
              <th class="p-3 border">Trạng thái</th>
              <th class="p-3 border">Hành động</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($promos as $index => $p): ?>
              <?php 
                $isExpired = strtotime($p['expiry_date']) < time();
              ?>
              <tr class="hover:bg-gray-50 transition">
                <td class="p-3 border text-center font-medium"><?= $index + 1 ?></td>
                <td class="p-3 border font-semibold text-gray-700"><?= htmlspecialchars($p['code']) ?></td>
                <td class="p-3 border text-center text-red-600 font-bold"><?= $p['discount'] ?>%</td>
                <td class="p-3 border"><?= htmlspecialchars($p['product_name'] ?? '—') ?></td>
                <td class="p-3 border text-center"><?= htmlspecialchars($p['expiry_date']) ?></td>
                <td class="p-3 border text-center"><?= date('d/m/Y', strtotime($p['created_at'])) ?></td>
                <td class="p-3 border text-center">
                  <?php if ($isExpired): ?>
                    <span class="bg-red-100 text-red-600 px-2 py-1 rounded text-xs font-semibold">Hết hạn</span>
                  <?php else: ?>
                    <span class="bg-green-100 text-green-600 px-2 py-1 rounded text-xs font-semibold">Còn hiệu lực</span>
                  <?php endif; ?>
                </td>
                <td class="p-3 border text-center">
                  <a href="edit_promotion.php?id=<?= $p['id'] ?>" 
                     class="bg-yellow-400 text-white px-3 py-1 rounded hover:bg-yellow-500 transition">✏️</a>
                  <a href="delete_promotion.php?id=<?= $p['id'] ?>" 
                     onclick="return confirm('Bạn có chắc muốn xóa khuyến mãi này?')" 
                     class="bg-red-500 text-white px-3 py-1 rounded hover:bg-red-600 transition">🗑️</a>
                </td>
                
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
      <?php endif; ?>
    </div>
  </main>
</body>
</html>
