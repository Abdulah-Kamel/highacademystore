<?php
include 'page_protect.php';
include 'db_connection.php';

// Get current user's branch
$branch_id = $_SESSION['branch_id'];

$per_page = isset($_COOKIE['per_page']) ? (int)$_COOKIE['per_page'] : 12;
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
$start = ($page - 1) * $per_page;

$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$where = '';
if ($search !== '') {
    $search_escaped = mysqli_real_escape_string($conn, $search);
    $where = "WHERE name LIKE '%$search_escaped%'";
}

$total_result = mysqli_query($conn, "SELECT COUNT(*) as total FROM products $where");
$total_row = mysqli_fetch_assoc($total_result);
$total_products = $total_row['total'];
$total_pages = ceil($total_products / $per_page);

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

// Output product cards grid
echo '<div class="row g-3" id="productsGrid">';
if ($products && mysqli_num_rows($products) > 0) {
    while ($row = mysqli_fetch_assoc($products)) {
        $js_name = htmlspecialchars(addslashes($row['name']), ENT_QUOTES);

        // Calculate discount
        $discounted_price = $row['sale_price'];
        $discount_label = '';
        if ($row['discount_type'] == 'percent') {
            $discounted_price = $row['sale_price'] * (1 - $row['discount_value'] / 100);
            $discount_label = "خصم {$row['discount_value']}%";
        } elseif ($row['discount_type'] == 'amount') {
            $discounted_price = $row['sale_price'] - $row['discount_value'];
            $discount_label = "خصم " . number_format($row['discount_value'], 2) . " جنيه";
        }

        echo '<div class="col-lg-3 col-md-4 col-sm-6  product-card" data-name="' . htmlspecialchars($row['name']) . '">';
        echo '    <div class="card h-100 text-center shadow-sm p-2">';
        echo '        <img src="uploads/' . htmlspecialchars($row['image']) . '" alt="صورة المنتج" class="card-img-top" style="height: 200px; object-fit: cover; border-radius: 0.5rem;">';
        echo '        <div class="card-body p-2">';
        echo '            <h6 class="fw-bold">' . htmlspecialchars($row['name']) . '</h6>';

        // Display price with discount
        echo '            <div class="d-flex justify-content-center align-items-center mb-2">';
        if ($discounted_price < $row['sale_price']) {
            echo '                <span style="color:#d32f2f;text-decoration:line-through;font-size:0.9em;">' . number_format($row['sale_price'], 2) . ' ج.م</span>';
            echo '                <span style="font-weight:bold;" class="text-success ms-2">' . number_format($discounted_price, 2) . ' ج.م</span>';
        } else {
            echo '                <span style="font-weight:bold;" class="text-success">' . number_format($row['sale_price'], 2) . ' ج.م</span>';
        }

        if ($discount_label) {
            echo '                <span class="bg-success text-white rounded px-2 ms-2">' . $discount_label . '</span>';
        }
        echo '            </div>';
        echo '            <span class="badge bg-info" id="stock_' . $row['id'] . '" data-stock="' . $row['stock'] . '">المتبقي بالمخزون: ' . $row['stock'] . '</span>';
        echo '            <div class="d-flex justify-content-center align-items-center mb-2">';
        echo '                <button type="button" class="btn btn-sm btn-danger px-2 me-1" onclick="decrement(' . $row['id'] . ')">−</button>';
        echo '                <input type="number" id="qty_' . $row['id'] . '" value="0" min="0" class="form-control text-center" style="width: 70px;">';
        echo '                <button type="button" class="btn btn-sm btn-success px-2 ms-1" onclick="increment(' . $row['id'] . ')">+</button>';
        echo '            </div>
        
            ';


        echo '            <button type="button" class="btn btn-warning btn-sm w-100 fw-bold" onclick="addToCart(' . $row['id'] . ', \'' . $js_name . '\', ' . $row['sale_price'] . ', \'' . $row['discount_type'] . '\', ' . $row['discount_value'] . ')">🛒 إضافة للسلة</button>';
        echo '        </div>';
        echo '    </div>';
        echo '</div>';
    }
} else {
    echo '<div class="col-12 text-center text-muted">لا توجد منتجات.</div>';
}
echo '</div>';

// Output pagination
$max_visible = 5;
$per_page_param = $per_page;
$start_page = max(1, $page - intval($max_visible / 2));
$end_page = $start_page + $max_visible - 1;
if ($end_page > $total_pages) {
    $end_page = $total_pages;
    $start_page = max(1, $end_page - $max_visible + 1);
}
echo '<div class="d-flex justify-content-center my-4" id="pagination-container"><nav><ul class="pagination">';
if ($page > 1) {
    echo '<li class="page-item"><a class="page-link" href="#" data-page="1" aria-label="First">&laquo;&laquo;</a></li>';
    echo '<li class="page-item"><a class="page-link" href="#" data-page="' . ($page - 1) . '" aria-label="Previous">&laquo;</a></li>';
}
for ($i = $start_page; $i <= $end_page; $i++) {
    $active = $i == $page ? 'active' : '';
    echo '<li class="page-item ' . $active . '"><a class="page-link" href="#" data-page="' . $i . '">' . $i . '</a></li>';
}
if ($page < $total_pages) {
    echo '<li class="page-item"><a class="page-link" href="#" data-page="' . ($page + 1) . '" aria-label="Next">&raquo;</a></li>';
    echo '<li class="page-item"><a class="page-link" href="#" data-page="' . $total_pages . '" aria-label="Last">&raquo;&raquo;</a></li>';
}
echo '</ul></nav></div>';
