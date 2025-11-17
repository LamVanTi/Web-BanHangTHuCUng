<?php
session_start();
include_once(__DIR__ . '/../config/database.php');

// 🐾 Lấy ID sản phẩm
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($id <= 0) {
  header("Location: index.php");
  exit;
}

// 🛍️ Lấy chi tiết sản phẩm + khuyến mãi
$sql = "
  SELECT p.*, 
         c.name AS category_name, 
         s.name AS supplier_name,
         pr.code AS promo_code,
         pr.discount AS promo_discount,
         pr.expiry_date AS promo_expiry
  FROM products p
  LEFT JOIN categories c ON p.category_id = c.id
  LEFT JOIN suppliers s ON p.supplier_id = s.id
  LEFT JOIN promotions pr ON p.id = pr.product_id AND pr.expiry_date >= CURDATE()
  WHERE p.id = :id
";
$stmt = $conn->prepare($sql);
$stmt->execute([':id' => $id]);
$product = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$product) {
  echo "<h2 class='text-center mt-20 text-gray-500'>❌ Sản phẩm không tồn tại.</h2>";
  exit;
}

// 💰 Tính giá sau khuyến mãi (nếu hợp lệ)
$discounted_price = $product['price'];
$isPromoActive = false;

if (!empty($product['promo_discount']) && strtotime($product['promo_expiry']) >= time()) {
  $discounted_price = $product['price'] * (1 - $product['promo_discount'] / 100);
  $isPromoActive = true;
}

// 🛒 Thêm vào giỏ hàng
if (isset($_POST['add_to_cart'])) {
  $qty = max(1, intval($_POST['quantity']));
  $_SESSION['cart'][$id] = [
    'name' => $product['name'],
    'price' => $discounted_price,
    'image' => $product['image'],
    'qty' => ($_SESSION['cart'][$id]['qty'] ?? 0) + $qty
  ];
  header("Location: cart.php");
  exit;
}

// ⚡ Mua ngay
if (isset($_POST['buy_now'])) {
  $qty = max(1, intval($_POST['quantity']));
  $_SESSION['cart'][$id] = [
    'name' => $product['name'],
    'price' => $discounted_price,
    'image' => $product['image'],
    'qty' => ($_SESSION['cart'][$id]['qty'] ?? 0) + $qty
  ];
  header("Location: checkout.php");
  exit;
}

// 💬 Gửi đánh giá sản phẩm
if (isset($_POST['submit_feedback'])) {
  if (isset($_SESSION['user_id'])) {
    $rating = intval($_POST['rating']);
    $content = trim($_POST['content']);
    if ($rating >= 1 && $rating <= 5 && $content != '') {
      $fb_sql = "INSERT INTO feedback (user_id, product_id, rating, content, created_at)
                 VALUES (:uid, :pid, :rating, :content, NOW())";
      $fb_stmt = $conn->prepare($fb_sql);
      $fb_stmt->execute([
        ':uid' => $_SESSION['user_id'],
        ':pid' => $id,
        ':rating' => $rating,
        ':content' => $content
      ]);
      header("Location: product_detail.php?id=$id");
      exit;
    } else {
      $error_msg = "Vui lòng chọn số sao và nhập nội dung đánh giá.";
    }
  } else {
    $error_msg = "Bạn cần đăng nhập để gửi đánh giá.";
  }
}

// 🔄 Lấy sản phẩm tương tự
$related_sql = "
  SELECT id, name, image, price 
  FROM products 
  WHERE category_id = :cat AND id != :id 
  ORDER BY RAND() 
  LIMIT 4
";
$rel_stmt = $conn->prepare($related_sql);
$rel_stmt->execute([':cat' => $product['category_id'], ':id' => $id]);
$related = $rel_stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= htmlspecialchars($product['name']) ?> | PetShop</title>
  <link rel="icon" type="image/png" href="../assets/logo.jpg">
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="assets/css/style.css">
</head>
<body class="bg-gray-50 text-gray-800">

<?php include_once(__DIR__ . '/includes/header.php'); ?>

<!-- 🌟 Chi tiết sản phẩm -->
<main class="container mx-auto py-10 px-4 grid grid-cols-1 md:grid-cols-2 gap-10">
  <!-- Ảnh sản phẩm -->
  <div class="bg-white rounded-xl shadow p-4 relative">
    <?php if ($isPromoActive): ?>
      <span class="absolute top-4 left-4 bg-red-600 text-white px-3 py-1 rounded-lg font-semibold text-sm">
        🔖 Giảm <?= $product['promo_discount'] ?>%
      </span>
    <?php endif; ?>
    <img src="../uploads/<?= htmlspecialchars($product['image'] ?? 'noimage.jpg') ?>" 
         alt="<?= htmlspecialchars($product['name']) ?>" 
         class="max-w-full max-h-[450px] object-contain mx-auto rounded-lg">
  </div>

  <!-- Thông tin sản phẩm -->
  <div class="bg-white rounded-xl shadow p-6 flex flex-col justify-between">
    <div>
      <h1 class="text-3xl font-bold mb-2"><?= htmlspecialchars($product['name']) ?></h1>
      <p class="text-gray-500 mb-3"><?= htmlspecialchars($product['category_name'] ?? 'Không có danh mục') ?></p>

      <!-- Hiển thị giá khuyến mãi -->
      <?php if ($isPromoActive): ?>
        <div class="mb-4">
          <p class="text-gray-400 line-through text-lg">
            <?= number_format($product['price'], 0, ',', '.') ?>₫
          </p>
          <p class="text-3xl text-red-500 font-semibold">
            <?= number_format($discounted_price, 0, ',', '.') ?>₫
          </p>
          <p class="text-sm text-green-600">
            Áp dụng mã: <strong><?= htmlspecialchars($product['promo_code']) ?></strong>
            (HSD: <?= date('d/m/Y', strtotime($product['promo_expiry'])) ?>)
          </p>
        </div>
      <?php else: ?>
        <p class="text-3xl text-red-500 font-semibold mb-4">
          <?= number_format($product['price'], 0, ',', '.') ?>₫
        </p>
      <?php endif; ?>

      <p class="text-gray-700 leading-relaxed mb-6">
        <?= nl2br(htmlspecialchars($product['description'] ?? 'Chưa có mô tả cho sản phẩm này.')) ?>
      </p>

      <form method="post" class="flex items-center gap-3 mb-6">
        <label for="quantity" class="text-gray-700">Số lượng:</label>
        <input type="number" name="quantity" id="quantity" value="1" min="1" class="border rounded-lg px-3 py-1 w-20">
      </form>

      <p class="text-sm text-gray-500 mb-2">🏭 Nhà cung cấp: 
        <span class="font-medium"><?= htmlspecialchars($product['supplier_name'] ?? 'Không rõ') ?></span>
      </p>
      <p class="text-sm text-gray-500 mb-4">🕓 Ngày đăng: 
        <?= date('d/m/Y', strtotime($product['created_at'])) ?>
      </p>
    </div>

    <!-- Nút mua hàng -->
    <form method="post" class="flex items-center gap-4 mt-6">
      <button name="add_to_cart" class="flex-1 bg-blue-600 text-white py-3 rounded-lg font-semibold hover:bg-blue-700">
        🛒 Thêm vào giỏ hàng
      </button>
      <button name="buy_now" class="flex-1 bg-orange-500 text-white py-3 rounded-lg font-semibold hover:bg-orange-600">
        ⚡ Mua ngay
      </button>
    </form>
  </div>
</main>

<!-- 💬 Đánh giá sản phẩm -->
<section class="container mx-auto px-4 mt-12 mb-16">
  <h2 class="text-2xl font-bold mb-6 text-gray-800">💬 Đánh giá sản phẩm</h2>

  <?php
  $fb_sql = "SELECT f.*, u.username 
              FROM feedback f
              LEFT JOIN users u ON f.user_id = u.id
              WHERE f.product_id = :pid
              ORDER BY f.created_at DESC";
  $fb_stmt = $conn->prepare($fb_sql);
  $fb_stmt->execute([':pid' => $id]);
  $feedbacks = $fb_stmt->fetchAll(PDO::FETCH_ASSOC);
  ?>

  <?php if (!empty($feedbacks)): ?>
    <div class="space-y-4 mb-8">
      <?php foreach ($feedbacks as $fb): ?>
        <div class="bg-white p-5 rounded-lg shadow hover:shadow-md transition">
          <div class="flex justify-between items-center mb-1">
            <p class="font-semibold text-blue-700">
              <?= htmlspecialchars($fb['username'] ?? 'Người dùng ẩn danh') ?>
            </p>
            <p class="text-sm text-gray-400">
              <?= date('d/m/Y H:i', strtotime($fb['created_at'])) ?>
            </p>
          </div>
          <p class="text-yellow-500 mb-1">
            <?php for ($i = 1; $i <= 5; $i++): ?>
              <?= $i <= intval($fb['rating']) ? '⭐' : '☆' ?>
            <?php endfor; ?>
          </p>
          <p class="text-gray-700"><?= htmlspecialchars($fb['content']) ?></p>
        </div>
      <?php endforeach; ?>
    </div>
  <?php else: ?>
    <p class="text-gray-500 italic mb-8">Chưa có đánh giá nào cho sản phẩm này.</p>
  <?php endif; ?>

  <!-- 📝 Form gửi đánh giá -->
  <div class="bg-white p-6 rounded-lg shadow-md">
    <h3 class="text-xl font-semibold mb-4 text-gray-700">Gửi đánh giá của bạn</h3>

    <?php if (!empty($error_msg)): ?>
      <p class="text-red-600 mb-3"><?= htmlspecialchars($error_msg) ?></p>
    <?php endif; ?>

    <form method="post" class="space-y-4">
      <div>
        <label class="block text-gray-700 mb-1">Đánh giá (sao):</label>
        <select name="rating" class="border rounded-lg px-3 py-2 w-32">
          <option value="">-- Chọn --</option>
          <option value="5">⭐⭐⭐⭐⭐ - Tuyệt vời</option>
          <option value="4">⭐⭐⭐⭐ - Tốt</option>
          <option value="3">⭐⭐⭐ - Bình thường</option>
          <option value="2">⭐⭐ - Kém</option>
          <option value="1">⭐ - Tệ</option>
        </select>
      </div>

      <div>
        <label class="block text-gray-700 mb-1">Nội dung:</label>
        <textarea name="content" rows="4" class="w-full border rounded-lg px-3 py-2" placeholder="Viết đánh giá của bạn..."></textarea>
      </div>

      <button name="submit_feedback" class="bg-green-600 text-white px-5 py-2 rounded-lg font-semibold hover:bg-green-700">
        Gửi đánh giá
      </button>
    </form>
  </div>
</section>

<?php include_once(__DIR__ . '/includes/footer.php'); ?>
</body>
</html>
