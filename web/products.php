<?php include_once(__DIR__ . '/../config/database.php'); ?>
<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Sản phẩm | PetShop</title>
  <link rel="icon" type="image/png" href="../assets/logo.jpg">
  <script src="https://cdn.tailwindcss.com"></script>
  <style>
    /* Thêm hiệu ứng fade-in cho các card */
    .fade-in { 
      animation: fadeIn 0.5s ease-in-out;
    }
    @keyframes fadeIn {
      from {opacity: 0; transform: translateY(10px);}
      to {opacity: 1; transform: translateY(0);}
    }
  </style>
</head>
<body class="bg-purple-50 text-gray-800">

  <?php include_once(__DIR__ . '/includes/header.php'); ?>

  <!-- 🌸 Section sản phẩm -->
  <section class="container mx-auto p-6">
    <h1 class="text-3xl font-bold mb-6 text-purple-700">Danh sách sản phẩm 🐾</h1>

    <!-- Bộ lọc -->
    <div class="grid grid-cols-2 md:grid-cols-6 gap-4 mb-8">
      <input type="text" id="search" placeholder="Tìm sản phẩm..." 
             class="border p-2 rounded focus:ring-2 focus:ring-pink-300 transition">
      <select id="category" class="border p-2 rounded focus:ring-2 focus:ring-pink-300 transition">
        <option value="">--Danh mục--</option>
        <?php
        $cats = $conn->query("SELECT * FROM categories")->fetchAll(PDO::FETCH_ASSOC);
        foreach ($cats as $c) echo "<option value='{$c['id']}'>{$c['name']}</option>";
        ?>
      </select>
      <select id="supplier" class="border p-2 rounded focus:ring-2 focus:ring-pink-300 transition">
        <option value="">--Nhà cung cấp--</option>
        <?php
        $suppliers = $conn->query("SELECT * FROM suppliers")->fetchAll(PDO::FETCH_ASSOC);
        foreach ($suppliers as $s) echo "<option value='{$s['id']}'>{$s['name']}</option>";
        ?>
      </select>
      <select id="price_range" class="border p-2 rounded focus:ring-2 focus:ring-pink-300 transition">
        <option value="">--Khoảng giá--</option>
        <option value="0-50000">Dưới 50.000₫</option>
        <option value="50000-100000">50.000₫ - 100.000₫</option>
        <option value="100000-300000">100.000₫ - 300.000₫</option>
        <option value="300000-500000">300.000₫ - 500.000₫</option>
        <option value="500000-9999999">Trên 500.000₫</option>
      </select>
      <button id="btnFilter" class="bg-pink-300 text-purple-800 px-4 py-2 rounded hover:bg-pink-400 transition-colors col-span-2 md:col-span-1">
        Lọc sản phẩm
      </button>
    </div>

    <!-- Danh sách sản phẩm -->
    <div id="productList" class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-6"></div>
  </section>

  <?php include_once(__DIR__ . '/includes/footer.php'); ?>

  <script>
    const API_URL = "../api/filter_products.php";
    const listEl = document.getElementById("productList");
    const searchEl = document.getElementById("search");
    const btnFilter = document.getElementById("btnFilter");

    async function loadProducts() {
      try {
        const priceValue = document.getElementById("price_range").value;
        let [min, max] = priceValue ? priceValue.split("-") : ["", ""];
        const params = new URLSearchParams({
          search: searchEl.value,
          category: document.getElementById("category").value,
          supplier: document.getElementById("supplier").value,
          min_price: min,
          max_price: max
        });

        listEl.innerHTML = `<p class="col-span-full text-center text-gray-400">⏳ Đang tải...</p>`;

        const res = await fetch(`${API_URL}?${params.toString()}`);
        if (!res.ok) throw new Error("HTTP " + res.status);
        const data = await res.json();

        if (!Array.isArray(data)) throw new Error("Dữ liệu không hợp lệ!");

        if (!data.length) {
          listEl.innerHTML = `<p class="col-span-full text-center text-gray-400 italic">Không tìm thấy sản phẩm phù hợp.</p>`;
          return;
        }

        // Hiển thị sản phẩm
        listEl.innerHTML = data.map(p => {
          const hasPromo = p.promo_discount && p.promo_discount > 0;
          const discountPrice = hasPromo ? p.price * (1 - p.promo_discount / 100) : p.price;

          return `
            <div class="relative bg-white rounded-xl shadow hover:shadow-lg transition overflow-hidden fade-in">
              ${hasPromo ? `
                <div class="absolute top-2 left-2 bg-pink-400 text-white text-xs font-bold px-2 py-1 rounded animate-pulse">
                  🎁 -${p.promo_discount}%
                </div>
              ` : ''}
              <img src="../uploads/${p.image || 'noimage.jpg'}" alt="${p.name}" class="w-full h-48 object-cover">
              <div class="p-4">
                <h3 class="font-semibold text-lg mb-1 truncate">${p.name}</h3>
                <p class="text-gray-500 text-sm mb-2">${p.category_name || '-'}</p>
                ${hasPromo ? `
                  <p class="text-gray-400 line-through text-sm">${Number(p.price).toLocaleString()}₫</p>
                  <p class="text-red-600 font-bold text-lg mb-3">${Number(discountPrice).toLocaleString()}₫</p>
                ` : `
                  <p class="text-blue-600 font-bold text-lg mb-3">${Number(p.price).toLocaleString()}₫</p>
                `}
                <a href="product_detail.php?id=${p.id}" class="block text-center bg-pink-300 text-purple-800 py-2 rounded-lg hover:bg-pink-400 transition">
                  Xem chi tiết
                </a>
              </div>
            </div>
          `;
        }).join('');
      } catch (e) {
        console.error(e);
        listEl.innerHTML = `<p class="col-span-full text-center text-red-500">⚠️ Lỗi tải dữ liệu!</p>`;
      }
    }

    btnFilter.addEventListener("click", loadProducts);
    let typingTimer;
    searchEl.addEventListener("input", () => {
      clearTimeout(typingTimer);
      typingTimer = setTimeout(loadProducts, 500);
    });

    loadProducts();
  </script>
</body>
</html>
