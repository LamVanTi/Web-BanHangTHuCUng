<?php
if (session_status() === PHP_SESSION_NONE) session_start();

$cartCount = 0;
if (!empty($_SESSION['cart'])) {
  foreach ($_SESSION['cart'] as $item) $cartCount += $item['qty'];
}

// Kiểm tra đăng nhập
$isLoggedIn = isset($_SESSION['user']);

?>
<header class="bg-gradient-to-r from-blue-200 via-purple-200 to-pink-200 sticky top-0 z-50 shadow-md">
  <div class="container mx-auto flex items-center justify-between p-4">
    
    <!-- Logo -->
    <div class="flex items-center space-x-2">
      <a href="index.php" class="flex items-center space-x-2 group">
        <img src="../assets/logo.jpg" alt="PetShop Logo"
             class="w-12 h-12 rounded-full border border-gray-200 group-hover:scale-105 transition-transform duration-300">
        <span class="text-2xl font-bold text-purple-700 group-hover:text-pink-600 transition-colors duration-300">
          PetShop
        </span>
      </a>
    </div>

    <!-- Menu -->
    <nav class="flex items-center space-x-6 text-base font-medium text-gray-700">
      <a href="index.php" class="hover:text-purple-700 transition-colors">Trang chủ</a>
      <a href="products.php" class="hover:text-purple-700 transition-colors">Sản phẩm</a>
      <a href="about.php" class="hover:text-purple-700 transition-colors">Liên hệ</a>
    </nav>

    <!-- Cart & User -->
    <div class="flex items-center space-x-4">
      
      <!-- Cart -->
      <a href="cart.php" class="relative text-2xl group hover:text-pink-600 transition-colors">
        🛒
        <?php if ($cartCount > 0): ?>
          <span class="absolute -top-2 -right-3 bg-pink-400 text-white text-xs px-1.5 rounded-full group-hover:scale-110 transition-transform duration-300">
            <?= $cartCount ?>
          </span>
        <?php endif; ?>
      </a>

      <!-- User (Login / Profile + Logout) -->
      <?php if ($isLoggedIn): ?>
        <!-- Profile -->
        <a href="profile.php" 
           class="flex items-center space-x-1 text-gray-700 hover:text-purple-700 transition-colors">
          <span>👤</span>
         </span>
        </a>

        <!-- Logout -->
        <a href="logout.php" 
           class="text-red-600 hover:text-red-800 font-medium transition-colors">
          Đăng xuất
        </a>

      <?php else: ?>
        <!-- Login -->
        <a href="login.php" class="text-gray-700 hover:text-purple-700 transition-colors">
          👤 Đăng nhập
        </a>
      <?php endif; ?>

    </div>
  </div>
</header>
