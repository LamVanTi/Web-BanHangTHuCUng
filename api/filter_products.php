<?php
header('Content-Type: application/json');
include_once(__DIR__ . '/../config/database.php');

$search = $_GET['search'] ?? '';
$category = $_GET['category'] ?? '';
$supplier = $_GET['supplier'] ?? '';
$min_price = $_GET['min_price'] ?? '';
$max_price = $_GET['max_price'] ?? '';

$sql = "
  SELECT p.*, c.name AS category_name 
  FROM products p 
  LEFT JOIN categories c ON p.category_id = c.id 
  WHERE 1
";

$params = [];

// Tìm kiếm theo tên
if ($search !== '') {
  $sql .= " AND p.name LIKE :search";
  $params[':search'] = "%$search%";
}

// Lọc theo danh mục
if ($category !== '') {
  $sql .= " AND p.category_id = :category";
  $params[':category'] = $category;
}

// Lọc theo nhà cung cấp
if ($supplier !== '') {
  $sql .= " AND p.supplier_id = :supplier";
  $params[':supplier'] = $supplier;
}

// Lọc theo khoảng giá
if ($min_price !== '') {
  $sql .= " AND p.price >= :min_price";
  $params[':min_price'] = $min_price;
}
if ($max_price !== '') {
  $sql .= " AND p.price <= :max_price";
  $params[':max_price'] = $max_price;
}

$sql .= " ORDER BY p.created_at DESC";

$stmt = $conn->prepare($sql);
$stmt->execute($params);
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode($products);

