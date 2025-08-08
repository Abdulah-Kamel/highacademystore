<?php
// حماية الصلاحية
include 'page_protect.php';
include 'header.php';
// الاتصال بقاعدة البيانات
include 'db_connection.php';

// Pagination setup
$per_page = isset($_GET['per_page']) && (in_array((int)$_GET['per_page'], [0, 10, 20, 50, 100])) ? (int)$_GET['per_page'] : 10;
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
$start = ($page - 1) * ($per_page > 0 ? $per_page : 1);

// Server-side search
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$where = '';
if ($search !== '') {
    $search_escaped = $conn->real_escape_string($search);
    $where = "WHERE name LIKE '%$search_escaped%'";
}

// Get total product count
$total_result = $conn->query("SELECT COUNT(*) as total FROM products $where");
$total_row = $total_result->fetch_assoc();
$total_products = $total_row['total'];
$total_pages = $per_page > 0 ? ceil($total_products / $per_page) : 1;

// جلب المنتجات مع حدود الصفحة
if ($per_page > 0) {
    $sql = "SELECT * FROM products $where LIMIT $start, $per_page";
} else {
    $sql = "SELECT * FROM products $where";
}
$result = $conn->query($sql);
?>

<div class="container my-5">
    <div class="card shadow-sm">
        <div class="card-header bg-primary text-white text-center py-3">
            <h4 class="mb-0">📦 قائمة المنتجات</h4>
        </div>

        <div class="card-body table-responsive">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <!-- مربع البحث الجديد -->
                <form method="get" class="mb-0" id="searchForm" style="max-width:400px;">
                    <input type="text" id="searchInput" name="search" class="form-control" placeholder="🔍 ابحث عن منتج أو مورد..." value="<?= htmlspecialchars($search) ?>">
                    <?php if (isset($_GET['per_page'])): ?>
                        <input type="hidden" name="per_page" value="<?= (int)$_GET['per_page'] ?>">
                    <?php endif; ?>
                </form>
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
                    <?php if ($search !== ''): ?><input type="hidden" name="search" value="<?= htmlspecialchars($search) ?>"><?php endif; ?>
                </form>
            </div>

            <table class="table table-bordered text-center align-middle">
                <thead class="table-success">
                    <tr>
                        <th>الصورة</th>
                        <th>اسم المنتج</th>
                        <th>سعر الشراء</th>
                        <th>سعر البيع</th>
                        <th>الكمية</th>
                        <th>الحد الأدنى (المخزن الرئيسي)</th>
                        <th>الحد الأدنى (شبين الكوم)</th>
                        <th>الحد الأدنى (قويسنا)</th>
                        <th>تعديل</th>
                        <th>حذف</th>
                    </tr>
                </thead>
                <tbody id="productsTable">
                    <?php if ($result->num_rows > 0): ?>
                        <?php while ($row = $result->fetch_assoc()): ?>
                            <tr class="product-row">
                                <td>
                                    <?php if (!empty($row['image'])): ?>
                                        <img src="uploads/<?= $row['image']; ?>" class="img-thumbnail" style="width: 80px; height: 80px; object-fit: cover;">
                                    <?php else: ?>
                                        <span class="text-muted">لا يوجد</span>
                                    <?php endif; ?>
                                </td>
                                <td><?= $row['name']; ?></td>
                                <td><?= number_format($row['purchase_price'], 0); ?> ج.م</td>
                                <td><?= number_format($row['sale_price'], 0); ?> ج.م</td>
                                <td><?= $row['stock']; ?></td>
                                <td><?= $row['min_stock_main'] ?? '-'; ?></td>
                                <td><?= $row['min_stock_shebin'] ?? '-'; ?></td>
                                <td><?= $row['min_stock_qwisna'] ?? '-'; ?></td>
                                <td>
                                    <a href="edit_product.php?id=<?= $row['id']; ?>" class="btn btn-sm btn-outline-primary">✏️ تعديل</a>
                                </td>
                                <td>
                                    <a href="delete_product.php?id=<?= $row['id']; ?>" class="btn btn-sm btn-outline-danger"
                                        onclick="return confirm('⚠️ هل أنت متأكد من حذف المنتج؟')">🗑️ حذف</a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="11" class="text-muted">لا توجد منتجات.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>

            <!-- Pagination controls -->
            <?php if ($per_page > 0): ?>
                <div class="d-flex justify-content-center my-4" id="pagination-container">
                    <nav>
                        <ul class="pagination">
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
            <?php endif; ?>
        </div>
    </div>
</div>

<?php
$conn->close();
include 'footer.php';
?>

<script>
    const searchInput = document.getElementById('searchInput');
    const perPageSelect = document.getElementById('per_page');
    const tableBody = document.getElementById('productsTable');
    const paginationContainer = document.getElementById('pagination-container');

    let typingTimer;

    function fetchTable(page = 1) {
        const search = searchInput.value;
        const per_page = perPageSelect.value;
        const params = new URLSearchParams({
            search,
            per_page,
            page
        });
        fetch('view_products_table.php?' + params)
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
    searchInput.addEventListener('input', function() {
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