<?php
include_once(__DIR__ . '/../../config/database.php');

// Lấy danh mục và nhà cung cấp để chọn trong form
$categories = $conn->query("SELECT * FROM categories ORDER BY name")->fetchAll(PDO::FETCH_ASSOC);
$suppliers = $conn->query("SELECT * FROM suppliers ORDER BY name")->fetchAll(PDO::FETCH_ASSOC);

// Khi người dùng bấm nút “Lưu sản phẩm mới”
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $name = $_POST['name'];
    $price = $_POST['price'];
    $category_id = $_POST['category_id'];
    $supplier_id = $_POST['supplier_id'];
    $description = $_POST['description'];

    // Xử lý upload ảnh
    $image = null;
    if (!empty($_FILES['image']['tmp_name'])) {
        $fileName = time() . "_" . basename($_FILES['image']['name']); // đặt tên ảnh tránh trùng
        $targetPath = "../../uploads/" . $fileName;
        if (move_uploaded_file($_FILES['image']['tmp_name'], $targetPath)) {
            $image = $fileName;
        }
    }

    // Thêm vào database
    $sql = "INSERT INTO products (name, price, image, category_id, supplier_id, description)
            VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$name, $price, $image, $category_id, $supplier_id, $description]);

    // Quay về danh sách sản phẩm
    header("Location: product.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <title>➕ Thêm sản phẩm mới | PetShop Admin</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 text-gray-800">
  <div class="max-w-3xl mx-auto mt-10 bg-white p-6 rounded-lg shadow">
    <h2 class="text-2xl font-bold mb-6">➕ Thêm sản phẩm mới</h2>

    <form method="POST" enctype="multipart/form-data" class="space-y-4">
      <div>
        <label class="block mb-1 font-medium">Tên sản phẩm</label>
        <input type="text" name="name" required class="w-full border p-2 rounded" placeholder="Nhập tên sản phẩm...">
      </div>

      <div>
        <label class="block mb-1 font-medium">Giá (VNĐ)</label>
        <input type="number" name="price" required class="w-full border p-2 rounded" placeholder="Ví dụ: 120000">
      </div>

      <div>
        <label class="block mb-1 font-medium">Danh mục</label>
        <select name="category_id" required class="w-full border p-2 rounded">
          <option value="">-- Chọn danh mục --</option>
          <?php foreach ($categories as $c): ?>
            <option value="<?= $c['id'] ?>"><?= htmlspecialchars($c['name']) ?></option>
          <?php endforeach; ?>
        </select>
      </div>

      <div>
        <label class="block mb-1 font-medium">Nhà cung cấp</label>
        <select name="supplier_id" required class="w-full border p-2 rounded">
          <option value="">-- Chọn nhà cung cấp --</option>
          <?php foreach ($suppliers as $s): ?>
            <option value="<?= $s['id'] ?>"><?= htmlspecialchars($s['name']) ?></option>
          <?php endforeach; ?>
        </select>
      </div>

      <div>
        <label class="block mb-1 font-medium">Mô tả</label>
        <textarea name="description" rows="3" class="w-full border p-2 rounded" placeholder="Mô tả sản phẩm..."></textarea>
      </div>

      <div>
        <label class="block mb-1 font-medium">Hình ảnh</label>
        <input type="file" name="image" accept="image/*" class="w-full border p-2 rounded">
      </div>

      <div class="flex justify-between mt-6">
        <button type="submit" class="bg-blue-600 text-white px-5 py-2 rounded hover:bg-blue-700">
          💾 Lưu sản phẩm
        </button>
        <a href="product.php" class="text-gray-600 hover:underline">← Quay lại danh sách</a>
      </div>
    </form>
  </div>
</body>
</html>
