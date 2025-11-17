<?php
include_once(__DIR__ . '/../../config/database.php');

// Lấy số liệu thống kê
$totalProducts = $conn->query("SELECT COUNT(*) FROM products")->fetchColumn();
$totalCategories = $conn->query("SELECT COUNT(*) FROM categories")->fetchColumn();
$totalUsers = $conn->query("SELECT COUNT(*) FROM users")->fetchColumn();
$totalSuppliers = $conn->query("SELECT COUNT(*) FROM suppliers")->fetchColumn();
$totalOrders = $conn->query("SELECT COUNT(*) FROM orders")->fetchColumn();
$totalFeedback = $conn->query("SELECT COUNT(*) FROM feedback")->fetchColumn();
$totalPromotions = $conn->query("SELECT COUNT(*) FROM promotions")->fetchColumn();

$totalRevenue = $conn->query("
    SELECT SUM(amount) 
    FROM revenue
")->fetchColumn() ?? 0;


// Doanh thu 7 ngày gần nhất (giả sử bảng orders có created_at và total_amount)
// Doanh thu 7 tuần gần nhất
$weeklyLabels = [];
$weeklyData = [];

$stmt = $conn->query("
    SELECT YEAR(created_at) AS year, WEEK(created_at, 1) AS week, SUM(total_price) AS revenue
    FROM orders
    WHERE status = 'Hoàn tất'
    GROUP BY year, week
    ORDER BY year DESC, week DESC
    LIMIT 7
");

while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $weeklyLabels[] = "Tuần " . $row['week'] . "/" . $row['year'];
    $weeklyData[] = (float)$row['revenue'];
}

// đảo lại để hiển thị tuần cũ → mới
$weeklyLabels = array_reverse($weeklyLabels);
$weeklyData = array_reverse($weeklyData);

?>
<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <title>🐾 Admin Dashboard | PetShop</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body class="bg-gray-100 text-gray-800">
  <div class="flex h-screen">
    <!-- Sidebar -->
    <aside class="w-64 bg-white shadow-md">
      <div class="p-6 text-center border-b">
        <img src="assets/logo.jpg" alt="PetShop Logo" class="mx-auto w-16">
        <h1 class="text-xl font-bold mt-2">PetShop Admin</h1>
      </div>
      <nav class="mt-6">
        <a href="index.php" class="block px-6 py-3 bg-gray-200 font-semibold">📊 Dashboard</a>
        <a href="product.php" class="block px-6 py-3 hover:bg-gray-100">🛍️ Sản phẩm</a>
        <a href="category.php" class="block px-6 py-3 hover:bg-gray-100">📁 Danh mục</a>
        <a href="orders.php" class="block px-6 py-3 hover:bg-gray-100">📦 Đơn hàng</a>
        <a href="users.php" class="block px-6 py-3 hover:bg-gray-100">👥 Người dùng</a>
        <a href="suppliers.php" class="block px-6 py-3 hover:bg-gray-100">🏭 Nhà cung cấp</a>
        <a href="promotions.php" class="block px-6 py-3 hover:bg-gray-100">🎁 Khuyến mãi</a>
        <a href="feedback.php" class="block px-6 py-3 hover:bg-gray-100">💬 Phản hồi</a>
      </nav>
    </aside>

    <!-- Main content -->
    <main class="flex-1 p-6 overflow-auto">
      <h2 class="text-2xl font-bold mb-4">📊 Tổng quan hoạt động</h2>

      <!-- Cards -->
      <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-4 gap-6 mb-6">
        <div class="bg-white p-6 rounded-lg shadow">
          <h3 class="text-gray-600">🛍️ Sản phẩm</h3>
          <p class="text-3xl font-bold text-blue-500"><?= $totalProducts ?></p>
        </div>
        <div class="bg-white p-6 rounded-lg shadow">
          <h3 class="text-gray-600">📦 Đơn hàng</h3>
          <p class="text-3xl font-bold text-green-500"><?= $totalOrders ?></p>
        </div>
        <div class="bg-white p-6 rounded-lg shadow">
          <h3 class="text-gray-600">👥 Người dùng</h3>
          <p class="text-3xl font-bold text-purple-500"><?= $totalUsers ?></p>
        </div>
        <div class="bg-white p-6 rounded-lg shadow">
          <h3 class="text-gray-600">🏭 Nhà cung cấp</h3>
          <p class="text-3xl font-bold text-yellow-500"><?= $totalSuppliers ?></p>
        </div>
      </div>

      <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
        <div class="bg-white p-6 rounded-lg shadow">
          <h3 class="text-gray-600">🎁 Khuyến mãi</h3>
          <p class="text-3xl font-bold text-pink-500"><?= $totalPromotions ?></p>
        </div>
        <div class="bg-white p-6 rounded-lg shadow">
          <h3 class="text-gray-600">💬 Phản hồi</h3>
          <p class="text-3xl font-bold text-teal-500"><?= $totalFeedback ?></p>
        </div>
        <div class="bg-white p-6 rounded-lg shadow col-span-2">
          <h3 class="text-gray-600">💰 Tổng doanh thu</h3>
          <p class="text-3xl font-bold text-red-500">₫<?= number_format($totalRevenue, 0, ',', '.') ?></p>
        </div>
      </div>

      <!-- Chart -->
      <div class="bg-white p-6 rounded-lg shadow">
        <h3 class="text-lg font-semibold mb-4">📈 Doanh thu 7 ngày qua</h3>
        <canvas id="salesChart"></canvas>
      </div>
    </main>
  </div>

  <script>
    const ctx = document.getElementById('salesChart');
    new Chart(ctx, {
      type: 'line',
      data: {
        labels: <?= json_encode($labels) ?>,
        datasets: [{
          label: 'Doanh thu (VNĐ)',
          data: <?= json_encode($salesData) ?>,
          borderColor: 'rgb(34, 197, 94)',
          backgroundColor: 'rgba(34, 197, 94, 0.2)',
          tension: 0.3,
          fill: true
        }]
      }
    });
  </script>
</body>
</html>
