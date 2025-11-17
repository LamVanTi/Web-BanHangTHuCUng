<?php
include_once(__DIR__ . '/../../config/database.php');

$sql = "
    SELECT o.*, c.name AS customer_name
    FROM orders o
    LEFT JOIN customers c ON o.customer_id = c.id
    ORDER BY o.id DESC
";
$stmt = $conn->query($sql);
$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <title>🧾 Quản lý đơn hàng | PetShop Admin</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 text-gray-800">
  <header class="bg-white shadow p-4 flex justify-between items-center">
    <h1 class="text-2xl font-bold">🧾 Quản lý đơn hàng</h1>
    <a href="index.php" class="text-blue-600 hover:underline">← Trở về Dashboard</a>
  </header>

  <main class="p-6">
    <div class="bg-white p-6 rounded-lg shadow-lg">
      <div class="flex justify-between mb-4">
        <h2 class="text-xl font-semibold">Danh sách đơn hàng</h2>
        <a href="add_order.php" class="bg-green-500 text-white px-4 py-2 rounded hover:bg-green-600">➕ Thêm đơn hàng</a>
      </div>

      <div class="overflow-x-auto">
        <table class="min-w-full border border-gray-200 text-sm">
          <thead class="bg-gray-100">
            <tr>
              <th class="p-3 border">#</th>
              <th class="p-3 border">Khách hàng</th>
              <th class="p-3 border text-right">Tổng tiền (VNĐ)</th>
              <th class="p-3 border text-center">Trạng thái</th>
              <th class="p-3 border text-center">Ngày tạo</th>
              <th class="p-3 border text-center">Hành động</th>
            </tr>
          </thead>

          <tbody>
            <?php foreach ($orders as $i => $o): ?>
              <tr class="hover:bg-gray-50">
                <td class="p-3 border text-center"><?= $i + 1 ?></td>

                <td class="p-3 border">
                  <?= htmlspecialchars($o['customer_name'] ?? 'Khách lẻ') ?>
                </td>

                <td class="p-3 border text-right text-green-600 font-semibold">
                  <?= number_format($o['total_price'], 0, ',', '.') ?>
                </td>

                <td class="p-3 border text-center">
                  <span class="px-2 py-1 rounded text-white
                    <?= $o['status'] == 'Hoàn tất' ? 'bg-green-500' :
                        ($o['status'] == 'Đang xử lý' ? 'bg-yellow-500' : 'bg-gray-400') ?>">
                    <?= htmlspecialchars($o['status']) ?>
                  </span>
                </td>

                <td class="p-3 border text-center">
                  <?= date('d/m/Y', strtotime($o['created_at'])) ?>
                </td>

                <td class="p-3 border text-center space-x-1">

                  <?php if ($o['status'] != 'Hoàn tất'): ?>
                    <a href="confirm_order.php?id=<?= $o['id'] ?>"
                       onclick="return confirm('Xác nhận đơn hàng này?')"
                       class="bg-blue-500 text-white px-3 py-1 rounded hover:bg-blue-600">
                       ✔ Xác nhận
                    </a>
                  <?php endif; ?>

                  <a href="edit_order.php?id=<?= $o['id'] ?>"
                     class="bg-yellow-400 text-white px-3 py-1 rounded hover:bg-yellow-500">
                     ✏️ Sửa
                  </a>

                  <a href="delete_order.php?id=<?= $o['id'] ?>"
                     onclick="return confirm('Xóa đơn hàng này?')"
                     class="bg-red-500 text-white px-3 py-1 rounded hover:bg-red-600">
                     🗑️ Xóa
                  </a>

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
