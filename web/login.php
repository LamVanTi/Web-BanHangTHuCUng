<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <title>Đăng nhập - PetShop 🐾</title>
  <style>
    body {
      margin: 0;
      font-family: 'Poppins', sans-serif;
      background: linear-gradient(135deg, #ffe6f7, #d7f0ff);
      display: flex; justify-content: center; align-items: center;
      height: 100vh;
    }
    .container {
      background: white; padding: 40px; border-radius: 20px;
      box-shadow: 0 10px 25px rgba(0,0,0,0.1);
      text-align: center; width: 340px;
    }
    h2 { color: #ff99cc; margin-bottom: 20px; }
    input {
      width: 90%; padding: 12px; margin: 10px 0;
      border: none; border-radius: 10px; background: #f2f2f2;
    }
    button {
      background: linear-gradient(45deg, #ffb3e6, #99ccff);
      color: white; border: none; padding: 12px 25px;
      border-radius: 12px; cursor: pointer;
      transition: transform 0.2s;
    }
    button:hover { transform: scale(1.05); }
    a { text-decoration: none; color: #888; font-size: 14px; }
    #msg { margin-top: 15px; color: red; font-weight: 500; }
    #msg.success { color: green; }
  </style>
</head>
<body>
  <div class="container">
    <h2>🐾 Đăng nhập PetShop</h2>
    <form id="loginForm">
      <input type="text" name="username" placeholder="Tên đăng nhập" required>
      <input type="password" name="password" placeholder="Mật khẩu" required>
      <button type="submit">Đăng nhập</button>
    </form>
    <p><a href="register.php">Chưa có tài khoản? Đăng ký</a></p>
    <p id="msg"></p>
  </div>

<script>
document.getElementById("loginForm").onsubmit = async (e) => {
  e.preventDefault();
  const formData = new FormData(e.target);

  try {
    const res = await fetch("../api/user_api.php?action=login", {
      method: "POST",
      body: formData
    });

    const data = await res.json();
    const msgEl = document.getElementById("msg");

    if (data.success) {
      msgEl.innerText = data.success;
      msgEl.classList.add("success");
      // Redirect sau 1 giây
      setTimeout(() => {
        // Nếu API trả về redirect URL
        if (data.redirect) location.href = data.redirect;
        else location.href = "index.php";
      }, 1000);
    } else if (data.error) {
      msgEl.innerText = data.error;
      msgEl.classList.remove("success");
    } else {
      msgEl.innerText = "Có lỗi xảy ra, vui lòng thử lại!";
      msgEl.classList.remove("success");
    }
  } catch (err) {
    document.getElementById("msg").innerText = "Không kết nối được server!";
    console.error(err);
  }
};
</script>
</body>
</html>
