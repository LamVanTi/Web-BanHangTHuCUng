<?php
session_start();
include_once(__DIR__ . '/../config/database.php');

// Nếu chưa đăng nhập thì chuyển về login
if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}

$userId = $_SESSION['user']['id'];

// Lấy thông tin user từ database
$stmt = $conn->prepare("SELECT id, username, email, created_at FROM users WHERE id = ?");
$stmt->execute([$userId]);  // Sử dụng đúng biến
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    die("Không tìm thấy thông tin người dùng!");
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Hồ sơ người dùng</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gradient-to-br from-purple-100 to-pink-100 min-h-screen">

    <div class="container mx-auto mt-10 p-6 max-w-lg bg-white shadow-xl rounded-2xl">

        <h2 class="text-3xl font-bold text-purple-700 text-center mb-6">
            👤 Hồ sơ khách hàng
        </h2>

        <div class="space-y-4 text-gray-700 text-lg">

            <div class="flex justify-between">
                <span class="font-semibold">ID:</span>
                <span><?= $user['id'] ?></span>
            </div>

            <div class="flex justify-between">
                <span class="font-semibold">Tên đăng nhập:</span>
                <span><?= htmlspecialchars($user['username']) ?></span>
            </div>

            <div class="flex justify-between">
                <span class="font-semibold">Email:</span>
                <span><?= htmlspecialchars($user['email']) ?></span>
            </div>

            <div class="flex justify-between">
                <span class="font-semibold">Ngày tạo tài khoản:</span>
                <span><?= $user['created_at'] ?></span>
            </div>

        </div>

        <div class="mt-8 flex justify-between">
            <a href="index.php" 
               class="px-4 py-2 bg-purple-500 hover:bg-purple-600 text-white rounded-lg transition">
                ⬅ Về trang chủ
            </a>

            <a href="logout.php" 
               class="px-4 py-2 bg-red-500 hover:bg-red-600 text-white rounded-lg transition">
                🚪 Đăng xuất
            </a>
        </div>

    </div>

</body>
</html>
