<?php
// حماية الصفحة بناءً على الصلاحيات + السيشن ستارت
include 'page_protect.php';

include 'header.php';
include 'db_connection.php';

// Get current user's branch
$branch_id = $_SESSION['branch_id'];

$per_page = isset($_COOKIE['per_page']) ? (int)$_COOKIE['per_page'] : 12;
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
$start = ($page - 1) * $per_page;

// Server-side search
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$where = '';
if ($search !== '') {
    $search_escaped = mysqli_real_escape_string($conn, $search);
    $where = "WHERE name LIKE '%$search_escaped%'";
}

// Get total product count
$total_result = mysqli_query($conn, "SELECT COUNT(*) as total FROM products $where");
$total_row = mysqli_fetch_assoc($total_result);
$total_products = $total_row['total'];
$total_pages = ceil($total_products / $per_page);

// Fetch only products for this page
$products = mysqli_query($conn, "
    SELECT p.*, COALESCE(MAX(ps.quantity), 0) as stock, COALESCE(SUM(s.quantity), 0) AS total_sold
    FROM products p
    LEFT JOIN product_stock ps ON ps.product_id = p.id AND ps.branch_id = {$branch_id}
    LEFT JOIN sales s ON s.product_id = p.id AND s.branch_id = {$branch_id}
    $where
    GROUP BY p.id
    ORDER BY total_sold DESC
    LIMIT $start, $per_page
");
?>

<style>
    .products-wrapper {
        min-height: 1200px;
    }

    @media screen and (min-width:600px) {
        .cart {
            position: fixed;
            top: 100px;
            left: 0;
        }
    }
</style>

<div class="container-fluid py-4">
    <div class="row align-items-start position-relative">

        <!-- ✅ المنتجات -->
        <div class="col-md-10 col-sm-12 mb-4 products-wrapper">
            <h4 class="text-center mb-4">🧾 الكاشير (نقطة البيع)</h4>

            <!-- ✅ مربع البحث -->
            <form method="get" class="mb-3" id="searchForm">
                <input type="text" id="searchInput" name="search" class="form-control" placeholder="🔍 ابحث عن منتج..." value="<?= htmlspecialchars($search) ?>">
                <?php if (isset($_GET['page'])): ?>
                    <input type="hidden" name="page" value="<?= (int)$_GET['page'] ?>">
                <?php endif; ?>
            </form>

            <div class="row g-3" id="productsGrid">
                <?php while ($row = mysqli_fetch_assoc($products)) : ?>
                    <?php
                    $discounted_price = $row['sale_price'];
                    $stock = $row['stock'] ?? 0;
                    $discount_label = '';
                    if ($row['discount_type'] == 'percent') {
                        $discounted_price = $row['sale_price'] * (1 - $row['discount_value'] / 100);
                        $discount_label = "خصم {$row['discount_value']}%";
                    } elseif ($row['discount_type'] == 'amount') {
                        $discounted_price = $row['sale_price'] - $row['discount_value'];
                        $discount_label = "خصم " . number_format($row['discount_value'], 2) . " جنيه";
                    }
                    ?>
                    <div class="col-lg-3 col-md-4 col-sm-6  product-card" data-name="<?= $row['name']; ?>">
                        <div class="card h-100 text-center shadow-sm p-2">
                            <img src="uploads/<?= $row['image']; ?>" alt="صورة المنتج"
                                class="card-img-top"
                                style="height: 200px; object-fit: cover; border-radius: 0.5rem;">

                            <div class="card-body p-2">
                                <h6 class="fw-bold"><?= $row['name']; ?></h6>
                                <div class="d-flex justify-content-center align-items-center mb-2">
                                    <?php if ($discounted_price < $row['sale_price']): ?>
                                        <span style="color:#d32f2f;text-decoration:line-through;font-size:0.9em;">
                                            <?= number_format($row['sale_price'], 2) ?> ج.م
                                        </span>
                                        <span style="color:#888;font-weight:bold;font-size:1.1em;">
                                            <?= number_format($discounted_price, 2) ?> ج.م
                                        </span>
                                    <?php else: ?>
                                        <span style="font-weight:bold;" class="text-success"><?= number_format($row['sale_price'], 2) ?> ج.م</span>
                                    <?php endif; ?>
                                </div>
                                <span class="badge bg-info" id="stock_<?= $row['id']; ?>" data-stock="<?= $row['stock'] ?>">المتبقي بالمخزون: <?= $row['stock'] ?></span>

                                <div class="d-flex justify-content-center align-items-center mb-2">
                                    <button type="button" class="btn btn-sm btn-danger px-2 me-1"
                                        onclick="decrement(<?= $row['id']; ?>)">−</button>

                                    <input type="number" id="qty_<?= $row['id']; ?>"
                                        value="0" min="0"
                                        class="form-control text-center"
                                        style="width: 70px;">

                                    <button type="button" class="btn btn-sm btn-success px-2 ms-1"
                                        onclick="increment(<?= $row['id']; ?>)">+</button>
                                </div>

                                <button type="button"
                                    class="btn btn-warning btn-sm w-100 fw-bold"
                                    onclick="addToCart(
                                        <?= $row['id'] ?>,
                                        '<?= htmlspecialchars($row['name'], ENT_QUOTES) ?>',
                                        <?= $row['sale_price'] ?>,
                                        '<?= $row['discount_type'] ?>',
                                        <?= $row['discount_value'] ?>
                                    )">
                                    🛒 إضافة للسلة
                                </button>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
            <div class="d-flex justify-content-center my-4" id="pagination-container">
                <nav>
                    <ul class="pagination">
                        <?php
                        $max_visible = 5;
                        $per_page_param = isset($_COOKIE['per_page']) ? (int)$_COOKIE['per_page'] : 12;
                        $start_page = max(1, $page - intval($max_visible / 2));
                        $end_page = $start_page + $max_visible - 1;
                        if ($end_page > $total_pages) {
                            $end_page = $total_pages;
                            $start_page = max(1, $end_page - $max_visible + 1);
                        }
                        ?>
                        <?php if ($page > 1): ?>
                            <li class="page-item">
                                <a class="page-link" href="#" data-page="1" aria-label="First">&laquo;&laquo;</a>
                            </li>
                            <li class="page-item">
                                <a class="page-link" href="#" data-page="<?= $page - 1 ?>" aria-label="Previous">&laquo;</a>
                            </li>
                        <?php endif; ?>
                        <?php for ($i = $start_page; $i <= $end_page; $i++): ?>
                            <li class="page-item <?= $i == $page ? 'active' : '' ?>">
                                <a class="page-link" href="#" data-page="<?= $i ?>"><?= $i ?></a>
                            </li>
                        <?php endfor; ?>
                        <?php if ($page < $total_pages): ?>
                            <li class="page-item">
                                <a class="page-link" href="#" data-page="<?= $page + 1 ?>" aria-label="Next">&raquo;</a>
                            </li>
                            <li class="page-item">
                                <a class="page-link" href="#" data-page="<?= $total_pages ?>" aria-label="Last">&raquo;&raquo;</a>
                            </li>
                        <?php endif; ?>
                    </ul>
                </nav>
            </div>
        </div>

        <!-- ✅ السلة الثابتة -->
        <div class="col-md-2 col-sm-12 mb-5 cart">
            <form method="POST" action="sales_process.php" onsubmit="return prepareFormData()">
                <div class="card shadow-sm" style="position: sticky; top: 85px; z-index: 100;">
                    <div class="card-header bg-dark text-white text-center py-2">
                        🛍️ <strong>السلة</strong>
                    </div>
                    <div class="card-body p-3" id="cart-items" style="font-size: 0.9rem; max-height: 500px; overflow-y: auto;">
                        <p class="text-muted text-center">لم يتم إضافة منتجات بعد</p>
                    </div>
                    <div class="card-footer bg-light text-center py-3">
                        <div id="cart-total" class="fw-bold text-success mb-2" style="font-size: 1rem;">
                            الاجمالي: 0 ج.م
                        </div>
                        <input type="hidden" name="cart_data" id="cart_data">
                        <button type="submit" class="btn btn-success btn-sm w-100 fw-bold">
                            💵 تنفيذ البيع وطباعة الفاتورة
                        </button>
                    </div>
                </div>
            </form>
        </div>

    </div>
</div>

<script>
    (function() {
        function getPerPage() {
            return window.innerWidth <= 600 ? 10 : 12;
        }

        function getCookie(name) {
            let match = document.cookie.match(new RegExp('(^| )' + name + '=([^;]+)'));
            return match ? parseInt(match[2]) : null;
        }

        function setPerPageAndReloadIfNeeded() {
            var perPage = getPerPage();
            var current = getCookie('per_page');
            if (current !== perPage) {
                document.cookie = "per_page=" + perPage + "; path=/";
                window.location.reload();
            }
        }
        // Check on load
        setPerPageAndReloadIfNeeded();
        // Check on resize (debounced)
        let resizeTimeout;
        window.addEventListener('resize', function() {
            clearTimeout(resizeTimeout);
            resizeTimeout = setTimeout(setPerPageAndReloadIfNeeded, 200);
        });
    })();
</script>

<script>
    let cart = JSON.parse(localStorage.getItem('cart')) || {};

    function renderCart() {
        const container = document.getElementById('cart-items');
        const totalBox = document.getElementById('cart-total');
        container.innerHTML = '';

        const keys = Object.keys(cart);
        let total = 0;

        if (keys.length === 0) {
            container.innerHTML = '<p class="text-muted text-center">لم يتم إضافة منتجات بعد</p>';
            totalBox.textContent = 'الاجمالي: 0 ج.م';
            return;
        }

        keys.forEach(id => {
            const item = cart[id];
            console.log('Cart item:', item); // Debug log

            let discountLabel = '';
            let discountedPrice = parseFloat(item.price);

            // Convert discountValue to number and handle null/undefined
            const discountValue = parseFloat(item.discountValue) || 0;
            const discountType = item.discountType || '';

            if (discountType === 'percent' && discountValue > 0) {
                discountedPrice = item.price * (1 - discountValue / 100);
                discountLabel = `خصم ${discountValue}%`;
            } else if (discountType === 'amount' && discountValue > 0) {
                discountedPrice = item.price - discountValue;
                discountLabel = `خصم ${discountValue.toFixed(2)} جنيه`;
            }

            let itemTotal = discountedPrice * item.qty;
            total += itemTotal;

            const div = document.createElement('div');
            div.className = 'card mb-2 shadow-sm';
            div.innerHTML = `
                <div class="card-body p-2">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <strong class="d-block">${item.name}</strong>
                            <small class="text-muted">
                                ${discountedPrice.toFixed(2)} ج.م × ${item.qty} = 
                                <span class="text-success">${itemTotal.toFixed(2)} ج.م</span>
                                ${discountLabel ? `<span class="bg-success  text-white rounded px-2 ms-2">${discountLabel}</span>` : ''}
                            </small>
                        </div>
                        <div class="text-end">
                            <div class="btn-group btn-group-sm" role="group">
                                <button onclick="changeQty(${id}, -1)" class="btn btn-outline-secondary">−</button>
                                <button class="btn btn-light disabled">${item.qty}</button>
                                <button onclick="changeQty(${id}, 1)" class="btn btn-outline-secondary">+</button>
                            </div>
                            <button onclick="removeFromCart(${id})" class="btn btn-sm btn-outline-danger mt-1">❌</button>
                        </div>
                    </div>
                </div>
            `;
            container.appendChild(div);
        });

        totalBox.textContent = 'الاجمالي: ' + total.toFixed(2) + ' ج.م';
    }

    function updateStockDisplay(id) {
        const qtyInput = document.getElementById('qty_' + id);
        const stockSpan = document.getElementById('stock_' + id);
        if (!stockSpan.dataset.stock) return; // In case it's not set
        const stock = parseInt(stockSpan.dataset.stock, 10);
        const qty = parseInt(qtyInput.value || 0, 10);
        stockSpan.textContent = `المتبقي بالمخزون: ${stock - qty}`;
    }

    function increment(id) {
        const qtyInput = document.getElementById('qty_' + id);
        const stockSpan = document.getElementById('stock_' + id);
        const stock = parseInt(stockSpan.dataset.stock, 10);
        let qty = parseInt(qtyInput.value || 0, 10);

        const cartQty = cart[id] ? cart[id].qty : 0;
        const availableStock = stock - cartQty;

        if (qty >= availableStock) {
            alert(`لا يمكن طلب كمية أكبر من المتوفر بالمخزون (${availableStock})`);
            return;
        }
        qtyInput.value = qty + 1;
        updateStockDisplay(id);
    }

    function decrement(id) {
        const qtyInput = document.getElementById('qty_' + id);
        let qty = parseInt(qtyInput.value || 0, 10);
        if (qty > 0) {
            qtyInput.value = qty - 1;
            updateStockDisplay(id);
        }
    }

    function addToCart(id, name, price, discountType, discountValue) {
        const qtyInput = document.getElementById('qty_' + id);
        const qty = parseInt(qtyInput.value || 0, 10);

        if (qty <= 0) {
            alert("يجب اختيار كمية أولاً");
            return;
        }

        const stockSpan = document.getElementById('stock_' + id);
        const stock = parseInt(stockSpan.dataset.stock, 10);
        const cartQty = cart[id] ? cart[id].qty : 0;
        const availableStock = stock - cartQty;

        if (qty > availableStock) {
            alert(`الكمية المطلوبة غير متوفرة. المتبقي: ${availableStock}`);
            return;
        }

        // Convert values to proper types
        const priceNum = parseFloat(price);
        const discountValueNum = parseFloat(discountValue) || 0;
        const discountTypeStr = discountType || '';

        if (cart[id]) {
            cart[id].qty += qty;
        } else {
            cart[id] = {
                name,
                price: priceNum,
                qty,
                discountType: discountTypeStr,
                discountValue: discountValueNum
            };
        }

        console.log('Adding to cart:', cart[id]); // Debug log
        localStorage.setItem('cart', JSON.stringify(cart));
        renderCart();
        qtyInput.value = 0;
        updateStockDisplay(id); // Reset stock display
    }

    function removeFromCart(id) {
        delete cart[id];
        localStorage.setItem('cart', JSON.stringify(cart));
        renderCart();
    }

    function changeQty(id, delta) {
        if (cart[id]) {
            cart[id].qty += delta;
            if (cart[id].qty <= 0) {
                delete cart[id];
            }
            localStorage.setItem('cart', JSON.stringify(cart));
            renderCart();
        }
    }

    function prepareFormData() {
        if (Object.keys(cart).length === 0) {
            alert("السلة فارغة، لا يمكن تنفيذ البيع.");
            return false;
        }

        const confirmSale = confirm("هل أنت متأكد من تنفيذ البيع وطباعة الفاتورة؟");
        if (!confirmSale) return false;

        document.getElementById('cart_data').value = JSON.stringify(cart);
        localStorage.removeItem('cart');
        return true;
    }

    window.onload = function() {
        renderCart();
    };
</script>

<script>
    // Add AJAX live search and pagination for products
    const searchInput = document.getElementById('searchInput');
    const productsGrid = document.getElementById('productsGrid');
    const paginationContainer = document.getElementById('pagination-container');

    let typingTimer;

    function fetchProducts(page = 1) {
        const search = searchInput.value;
        const params = new URLSearchParams({
            search,
            page
        });
        fetch('sales_products_table.php?' + params)
            .then(res => res.text())
            .then(html => {
                // Expecting the response to be: <div>products</div><div>pagination</div>
                const parser = new DOMParser();
                const doc = parser.parseFromString('<div>' + html + '</div>', 'text/html');
                const newGrid = doc.querySelector('#productsGrid');
                const newPagination = doc.querySelector('#pagination-container');
                if (newGrid) productsGrid.innerHTML = newGrid.innerHTML;
                if (newPagination) paginationContainer.innerHTML = newPagination.innerHTML;
            });
    }

    // Live search
    searchInput.addEventListener('input', function() {
        clearTimeout(typingTimer);
        typingTimer = setTimeout(() => {
            fetchProducts(1);
        }, 300); // 300ms debounce
    });

    // Pagination click (event delegation)
    paginationContainer.addEventListener('click', function(e) {
        if (e.target.classList.contains('page-link')) {
            e.preventDefault();
            const page = e.target.dataset.page;
            if (page) fetchProducts(page);
        }
    });
</script>

<?php include 'footer.php'; ?>
