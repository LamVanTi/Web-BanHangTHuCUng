const API_URL = "../api/filter_products.php";
const listEl = document.getElementById("productList");
const searchEl = document.getElementById("search");
const btnFilter = document.getElementById("btnFilter");

async function loadProducts() {
  const params = new URLSearchParams({
    search: searchEl.value,
    category: document.getElementById("category").value,
    supplier: document.getElementById("supplier").value,
    min_price: document.getElementById("min_price").value,
    max_price: document.getElementById("max_price").value
  });

  listEl.innerHTML = `<p class="col-span-full text-center text-gray-400">⏳ Đang tải...</p>`;

  const res = await fetch(`${API_URL}?${params.toString()}`);
  const data = await res.json();

  if (!data.length) {
    listEl.innerHTML = `<p class="col-span-full text-center text-gray-400 italic">Không tìm thấy sản phẩm phù hợp.</p>`;
    return;
  }

  listEl.innerHTML = data.map(p => `
    <div class="bg-white rounded-xl shadow hover:shadow-lg transition overflow-hidden fade-in">
      <img src="../uploads/${p.image || 'noimage.jpg'}" alt="${p.name}" class="w-full h-48 object-cover">
      <div class="p-4">
        <h3 class="font-semibold text-lg mb-1 truncate">${p.name}</h3>
        <p class="text-gray-500 text-sm mb-2">${p.category_name || '-'}</p>
        <p class="text-red-500 font-bold text-lg mb-3">${Number(p.price).toLocaleString()}₫</p>
        <a href="product_detail.php?id=${p.id}" class="block text-center bg-blue-500 text-white py-2 rounded-lg hover:bg-blue-600">
          Xem chi tiết
        </a>
      </div>
    </div>
  `).join('');
}

// Gọi lọc khi nhấn nút
btnFilter.addEventListener("click", loadProducts);

// Tìm kiếm động sau 0.5s dừng gõ
let typingTimer;
searchEl.addEventListener("input", () => {
  clearTimeout(typingTimer);
  typingTimer = setTimeout(loadProducts, 500);
});

// Tải lần đầu
loadProducts();


