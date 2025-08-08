<?php
// حماية الصلاحية للصفحة
include 'page_protect.php';

include 'db_connection.php';
include 'header.php';

// جلب الفروع
$branch_query = "SELECT id, name FROM branches";
$branch_result = mysqli_query($conn, $branch_query);
$branches = [];
while ($b = mysqli_fetch_assoc($branch_result)) {
    $branches[] = $b;
}

// Pagination setup
$per_page = isset($_GET['per_page']) && in_array((int)$_GET['per_page'], [0, 10, 20, 50, 100]) ? (int)$_GET['per_page'] : 10;
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
$start = ($page - 1) * ($per_page > 0 ? $per_page : 1);

// Get total product count
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$where = '';
if ($search !== '') {
    $search_escaped = $conn->real_escape_string($search);
    $where = "WHERE p.name LIKE '%$search_escaped%'";
}
$total_result = $conn->query("SELECT COUNT(*) as total FROM products p $where");
$total_row = $total_result->fetch_assoc();
$total_products = $total_row['total'];
$total_pages = $per_page > 0 ? ceil($total_products / $per_page) : 1;

// جلب المنتجات مع حدود الصفحة (للعرض فقط)
if ($per_page > 0) {
    $product_query = "
        SELECT DISTINCT p.id, p.name, p.stock, p.min_stock_main, p.min_stock_shebin, p.min_stock_qwisna, p.purchase_price
        FROM products p
        LEFT JOIN product_stock ps ON ps.product_id = p.id
        $where
        LIMIT $start, $per_page
    ";
} else {
    $product_query = "
        SELECT DISTINCT p.id, p.name, p.stock, p.min_stock_main, p.min_stock_shebin, p.min_stock_qwisna, p.purchase_price
        FROM products p
        LEFT JOIN product_stock ps ON ps.product_id = p.id
        $where
    ";
}
$product_result = mysqli_query($conn, $product_query);
$all_products = [];
while ($product = mysqli_fetch_assoc($product_result)) {
    $all_products[] = $product;
}

// جلب كل المنتجات (بدون LIMIT) لحساب الاجماليات
$total_products_query = "
    SELECT DISTINCT p.id, p.name, p.stock, p.min_stock_main, p.min_stock_shebin, p.min_stock_qwisna, p.purchase_price
    FROM products p
    LEFT JOIN product_stock ps ON ps.product_id = p.id
";
$total_products_result = mysqli_query($conn, $total_products_query);

// حساب اجمالي سعر المخزون لكل فرع (لكل المنتجات)
$main_total_price = 0;
$branch_total_prices = [];
foreach ($branches as $branch) {
    $branch_total_prices[$branch['id']] = 0;
}
$grand_total_price = 0;

while ($product = mysqli_fetch_assoc($total_products_result)) {
    $product_id = $product['id'];
    $main_qty = $product['stock'];
    $main_price = (float)($product['purchase_price'] ?? 0);
    $main_total_price += $main_qty * $main_price;
    $row_prices = $main_qty * $main_price;
    foreach ($branches as $branch) {
        $bid = $branch['id'];
        $q = "SELECT quantity FROM product_stock WHERE product_id = $product_id AND branch_id = $bid";
        $res = mysqli_query($conn, $q);
        $row = mysqli_fetch_assoc($res);
        $qty = $row ? $row['quantity'] : 0;
        $branch_total_prices[$bid] += $qty * $main_price;
        $row_prices += $qty * $main_price;
    }
    $grand_total_price += $row_prices;
}
?>

<!DOCTYPE html>
<html lang="ar">

<head>
    <meta charset="UTF-8">
    <title>تقرير توزيع المخزون على الفروع</title>
    <style>
        html,
        body {
            height: 100%;
        }

        body {
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }

        main {
            flex: 1;
        }

        table {
            border: 1px solid #ccc;
        }

        th,
        td {
            vertical-align: middle !important;
        }

        .low-stock {
            background-color: #ffb3b3 !important;
        }

        .search-bar {
            margin-bottom: 20px;
        }

        .table-summary td,
        .table-summary th {
            font-weight: bold;
        }

        /* Responsive pagination */
        .pagination {
            flex-wrap: wrap;
        }

        @media (max-width: 600px) {
            .pagination {
                flex-wrap: nowrap;
                overflow-x: auto;
                -webkit-overflow-scrolling: touch;
                white-space: nowrap;
                padding-bottom: 4px;
            }

            .pagination .page-item {
                flex: 0 0 auto;
            }
        }
    </style>
</head>

<body>

    <main class="container py-4 mb-5">
        <h3 class="text-center mb-4">📦 تقرير توزيع المخزون على الفروع</h3>

        <div class="mb-3 d-flex justify-content-between align-items-center">
            <a href="export_branch_stock_pdf.php" class="btn btn-danger btn-sm" target="_blank">🖨️ تصدير PDF</a>
            <form method="get" class="d-flex align-items-center mb-0" style="gap:8px;">
                <label for="per_page" class="mb-0">عدد المنتجات في الصفحة:</label>
                <select name="per_page" id="per_page" class="form-select form-select-sm" style="width:auto;" onchange="this.form.submit()">
                    <option value="10" <?= $per_page == 10 ? 'selected' : '' ?>>10</option>
                    <option value="20" <?= $per_page == 20 ? 'selected' : '' ?>>20</option>
                    <option value="50" <?= $per_page == 50 ? 'selected' : '' ?>>50</option>
                    <option value="100" <?= $per_page == 100 ? 'selected' : '' ?>>100</option>
                    <option value="0" <?= $per_page == 0 ? 'selected' : '' ?>>الكل</option>
                </select>
                <?php if ($page > 1): ?><input type="hidden" name="page" value="<?= $page ?>"><?php endif; ?>
            </form>
        </div>

        <!-- شريط البحث -->
        <form class="search-bar mb-3" method="get" id="searchForm">
            <div class="input-group">
                <input type="text" id="search-input" name="search" class="form-control" placeholder="ابحث باسم المنتج..." value="<?= htmlspecialchars($_GET['search'] ?? '') ?>">
                <?php if (isset($_GET['per_page'])): ?>
                    <input type="hidden" name="per_page" value="<?= (int)$_GET['per_page'] ?>">
                <?php endif; ?>
            </div>
        </form>

        <div class="table-responsive">
            <table id="stock-table" class="table table-bordered text-center align-middle">
                <thead class="table-success">
                    <tr>
                        <th>#</th>
                        <th>المنتج</th>
                        <th>المخزن الرئيسي</th>
                        <?php foreach ($branches as $branch): ?>
                            <th><?= $branch['name']; ?></th>
                        <?php endforeach; ?>
                        <th>الإجمالي الكلي</th>
                    </tr>
                </thead>
                <tbody id="stock-table-body">
                    <?php $serial = ($page - 1) * $per_page + 1; ?>
                    <?php foreach ($all_products as $product): ?>
                        <tr>
                            <td><?= $serial++; ?></td>
                            <td><?= htmlspecialchars($product['name']); ?></td>
                            <?php
                            $product_id = $product['id'];
                            $main_qty = $product['stock'];
                            $main_min = (int)($product['min_stock_main'] ?? 0);
                            $main_price = (float)($product['purchase_price'] ?? 0);
                            $main_style = ($main_qty < $main_min) ? 'class="low-stock"' : '';
                            echo "<td $main_style>$main_qty</td>";
                            $total = $main_qty;
                            // فروع
                            foreach ($branches as $branch) {
                                $bid = $branch['id'];
                                $q = "SELECT quantity FROM product_stock WHERE product_id = $product_id AND branch_id = $bid";
                                $res = mysqli_query($conn, $q);
                                $row = mysqli_fetch_assoc($res);
                                $qty = $row ? $row['quantity'] : 0;
                                $total += $qty;
                                // الحد الأدنى للفرع المناسب
                                $branch_min = 0;
                                if (strpos($branch['name'], 'شبين الكوم') !== false) {
                                    $branch_min = (int)($product['min_stock_shebin'] ?? 0);
                                } elseif (strpos($branch['name'], 'قويسنا') !== false) {
                                    $branch_min = (int)($product['min_stock_qwisna'] ?? 0);
                                }
                                $branch_style = ($qty < $branch_min) ? 'class="low-stock"' : '';
                                echo "<td $branch_style>$qty</td>";
                            }
                            echo "<td>$total</td>";
                            ?>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <!-- Pagination controls -->
        <?php if ($per_page > 0): ?>
            <div class="d-flex justify-content-center my-4">
                <nav>
                    <ul class="pagination" id="pagination-container">
                        <?php
                        $max_visible = 5;
                        $start_page = max(1, $page - intval($max_visible / 2));
                        $end_page = $start_page + $max_visible - 1;
                        if ($end_page > $total_pages) {
                            $end_page = $total_pages;
                            $start_page = max(1, $end_page - $max_visible + 1);
                        }
                        ?>
                        <?php if ($page > 1): ?>
                            <li class="page-item">
                                <a class="page-link" href="?page=1&per_page=<?= $per_page ?>" aria-label="First">&laquo;&laquo;</a>
                            </li>
                            <li class="page-item">
                                <a class="page-link" href="?page=<?= $page - 1 ?>&per_page=<?= $per_page ?>" aria-label="Previous">&laquo;</a>
                            </li>
                        <?php endif; ?>
                        <?php for ($i = $start_page; $i <= $end_page; $i++): ?>
                            <li class="page-item <?= $i == $page ? 'active' : '' ?>">
                                <a class="page-link" href="?page=<?= $i ?>&per_page=<?= $per_page ?>" data-page="<?= $i ?>"><?= $i ?></a>
                            </li>
                        <?php endfor; ?>
                        <?php if ($page < $total_pages): ?>
                            <li class="page-item">
                                <a class="page-link" href="?page=<?= $page + 1 ?>&per_page=<?= $per_page ?>" aria-label="Next">&raquo;</a>
                            </li>
                            <li class="page-item">
                                <a class="page-link" href="?page=<?= $total_pages ?>&per_page=<?= $per_page ?>" aria-label="Last">&raquo;&raquo;</a>
                            </li>
                        <?php endif; ?>
                    </ul>
                </nav>
            </div>
        <?php endif; ?>

        <!-- جدول إجمالي الأسعار (سعر المخزون) يظهر للأدمن فقط -->
        <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
            <div class="table-responsive">
                <table class="table table-bordered table-summary text-center align-middle" style="width:100%;">
                    <thead class="table-info">
                        <tr>
                            <th>سعر مخزون المخزن الرئيسي</th>
                            <?php foreach ($branches as $branch): ?>
                                <th>سعر مخزون <?= $branch['name']; ?></th>
                            <?php endforeach; ?>
                            <th>إجمالي كل المخزون</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><?= number_format($main_total_price, 2) ?> ج.م</td>
                            <?php foreach ($branches as $branch): ?>
                                <td><?= number_format($branch_total_prices[$branch['id']], 2) ?> ج.م</td>
                            <?php endforeach; ?>
                            <td><?= number_format($grand_total_price, 2) ?> ج.م</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </main>

    <script>
        const searchInput = document.getElementById('search-input');
        const perPageSelect = document.getElementById('per_page');
        const tableBody = document.getElementById('stock-table-body');
        const paginationContainer = document.getElementById('pagination-container');

        let typingTimer;
        let lastSearch = searchInput.value;

        function fetchTable(page = 1) {
            const search = searchInput.value;
            const per_page = perPageSelect.value;
            const params = new URLSearchParams({
                search,
                per_page,
                page
            });
            fetch('branch_stock_report_table.php?' + params)
                .then(res => res.text())
                .then(html => {
                    // Expecting the response to be: <tbody>...</tbody><div>pagination</div>
                    const parser = new DOMParser();
                    const doc = parser.parseFromString('<table>' + html + '</table>', 'text/html');
                    const newTbody = doc.querySelector('tbody');
                    const newPagination = doc.querySelector('div');
                    if (newTbody) tableBody.innerHTML = newTbody.innerHTML;
                    if (newPagination) paginationContainer.innerHTML = newPagination.innerHTML;
                });
        }

        // Live search
        searchInput.addEventListener('keyup', function() {
            console.log('searching...');    
            
            clearTimeout(typingTimer);
            typingTimer = setTimeout(() => {
                fetchTable(1);
            }, 300); // 300ms debounce
        });

        // Per page change
        perPageSelect.addEventListener('change', function() {
            fetchTable(1);
        });

        // Pagination click (event delegation)
        paginationContainer.addEventListener('click', function(e) {
            if (e.target.classList.contains('page-link')) {
                e.preventDefault();
                const page = e.target.dataset.page;
                if (page) fetchTable(page);
            }
        });
    </script>

    <?php include 'footer.php'; ?>
</body>

</html>