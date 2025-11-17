<?php
include_once(__DIR__ . '/../../config/database.php');

$id = $_GET['id'] ?? null;
$product = [
    'name' => '',
    'price' => '',
    'image' => '',
    'category_id' => '',
    'supplier_id' => '',
    'description' => ''
];

if ($id) {
    $stmt = $conn->prepare("SELECT * FROM products WHERE id = ?");
    $stmt->execute([$id]);
    $product = $stmt->fetch(PDO::FETCH_ASSOC);
}

// Lấy danh sách danh mục và nhà cung cấp
$categories = $conn->query("SELECT * FROM categories ORDER BY name")->fetchAll(PDO::FETCH_ASSOC);
$suppliers = $conn->query("SELECT * FROM suppliers ORDER BY name")->fetchAll(PDO::FETCH_ASSOC);

// Khi submit form
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $name = $_POST['name'];
    $price = $_POST['price'];
    $category_id = $_POST['category_id'];
    $supplier_id = $_POST['supplier_id'];
    $description = $_POST['description'];
    $image = $_FILES['image']['name'] ?? $product['image'];

    // Upload ảnh nếu có
    if (!empty($_FILES['image']['tmp_name'])) {
        move_uploaded_file($_FILES['image']['tmp_name'], "../../uploads/" . basename($image));
    }

    if ($id) {
        $sql = "UPDATE products SET name=?, price=?, image=?, category_id=?, supplier_id=?, description=? WHERE id=?";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$name, $price, $image, $category_id, $supplier_id, $description, $id]);
    } else {
        $sql = "INSERT INTO products (name, price, image, category_id, supplier_id, description) VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$name, $price, $image, $category_id, $supplier_id, $description]);
    }

    header("Location: products.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <title><?= $id ? "Sửa sản phẩm" : "Thêm sản phẩm" ?> | PetShop Admin</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 text-gray-800">
  <div class="max-w-3xl mx-auto mt-10 bg-white p-6 rounded-lg shadow">
    <h2 class="text-2xl font-bold mb-6"><?= $id ? "✏️ Sửa sản phẩm" : "➕ Thêm sản phẩm" ?></h2>
    <form method="POST" enctype="multipart/form-data" class="space-y-4">
      <div>
        <label class="block mb-1 font-medium">Tên sản phẩm</label>
        <input type="text" name="name" value="<?= htmlspecialchars($product['name']) ?>" required class="w-full border p-2 rounded">
      </div>

      <div>
        <label class="block mb-1 font-medium">Giá (VNĐ)</label>
        <input type="number" name="price" value="<?= htmlspecialchars($product['price']) ?>" required class="w-full border p-2 rounded">
      </div>

      <div>
        <label class="block mb-1 font-medium">Danh mục</label>
        <select name="category_id" class="w-full border p-2 rounded">
          <?php foreach ($categories as $c): ?>
            <option value="<?= $c['id'] ?>" <?= $product['category_id'] == $c['id'] ? 'selected' : '' ?>>
              <?= htmlspecialchars($c['name']) ?>
            </option>
          <?php endforeach; ?>
        </select>
      </div>

      <div>
        <label class="block mb-1 font-medium">Nhà cung cấp</label>
        <select name="supplier_id" class="w-full border p-2 rounded">
          <?php foreach ($suppliers as $s): ?>
            <option value="<?= $s['id'] ?>" <?= $product['supplier_id'] == $s['id'] ? 'selected' : '' ?>>
              <?= htmlspecialchars($s['name']) ?>
            </option>
          <?php endforeach; ?>
        </select>
      </div>

      <div>
        <label class="block mb-1 font-medium">Mô tả</label>
        <textarea name="description" rows="3" class="w-full border p-2 rounded"><?= htmlspecialchars($product['description']) ?></textarea>
      </div>

      <div>
        <label class="block mb-1 font-medium">Hình ảnh</label>
        <?php if ($product['image']): ?>
          <img src="../../uploads/<?= htmlspecialchars($product['image']) ?>" class="w-24 h-24 object-cover rounded mb-2">
        <?php endif; ?>
        <input type="file" name="image" accept="image/*" class="w-full border p-2 rounded">
      </div>

      <button type="submit" class="bg-blue-600 text-white px-5 py-2 rounded hover:bg-blue-700">
        💾 Lưu
      </button>
      <a href="products.php" class="ml-3 text-gray-600 hover:underline">← Quay lại</a>
    </form>
  </div>
</body>
</html>
