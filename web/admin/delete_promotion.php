<?php
include_once(__DIR__ . '/../../config/database.php');

$id = $_GET['id'] ?? null;

if ($id) {
  $stmt = $conn->prepare("DELETE FROM promotions WHERE id = ?");
  $stmt->execute([$id]);
}

header("Location: promotions.php");
exit;
?>
