<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <title>Đăng ký - PetShop 🐾</title>
  <style>
    body {
      margin: 0;
      font-family: 'Poppins', sans-serif;
      background: linear-gradient(135deg, #d7f0ff, #ffe6f7);
      display: flex; justify-content: center; align-items: center;
      height: 100vh; overflow: hidden;
    }
    .container {
      background: white; padding: 40px; border-radius: 20px;
      box-shadow: 0 10px 25px rgba(0,0,0,0.1);
      text-align: center; width: 340px;
    }
    h2 { color: #66b3ff; margin-bottom: 20px; }
    input {
      width: 90%; padding: 12px; margin: 10px 0;
      border: none; border-radius: 10px; background: #f2f2f2;
    }
    button {
      background: linear-gradient(45deg, #99ccff, #ffb3e6);
      color: white; border: none; padding: 12px 25px;
      border-radius: 12px; cursor: pointer;
    }
    button:hover { transform: scale(1.05); }
    a { text-decoration: none; color: #888; font-size: 14px; }
  </style>
</head>
<body>
  <div class="container">
    <h2>🐾 Đăng ký PetShop</h2>
    <form id="registerForm">
      <input type="text" name="username" placeholder="Tên đăng nhập" required>
      <input type="password" name="password" placeholder="Mật khẩu" required>
      <input type="email" name="email" placeholder="Email" required>
      <button type="submit">Đăng ký</button>
    </form>
    <p><a href="login.php">Đã có tài khoản? Đăng nhập</a></p>
    <p id="msg"></p>
  </div>

<script>
document.getElementById("registerForm").onsubmit = async (e) => {
  e.preventDefault();
  const formData = new FormData(e.target);
  const res = await fetch("../api/user_api.php?action=register", {
    method: "POST",
    body: formData
  });
  const data = await res.json();
  document.getElementById("msg").innerText = data.success || data.error;
  if (data.success) setTimeout(() => location.href = "login.php", 1000);
};
</script>
</body>
</html>
