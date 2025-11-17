<?php
include_once(__DIR__ . '/../../config/database.php');

$id = $_GET['id'] ?? null;
if ($id) {
    $stmt = $conn->prepare("DELETE FROM products WHERE id = ?");
    $stmt->execute([$id]);
}
header("Location: product.php");
exit;
?>
