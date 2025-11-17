<?php
include_once(__DIR__ . '/../../config/database.php');

if (isset($_GET['id'])) {
    $id = (int) $_GET['id'];

    // Lấy trạng thái hiện tại
    $stmt = $conn->prepare("SELECT status FROM promotions WHERE id = ?");
    $stmt->execute([$id]);
    $promo = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($promo) {
        $newStatus = $promo['status'] == 1 ? 0 : 1;

        $update = $conn->prepare("UPDATE promotions SET status = ? WHERE id = ?");
        $update->execute([$newStatus, $id]);
    }
}

// Quay lại trang danh sách
header("Location: promotions.php");
exit;
?>
