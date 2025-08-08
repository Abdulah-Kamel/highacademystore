<?php
include 'page_protect.php';
include 'db_connection.php';

// Get branches
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

// Fetch products for this page
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

// Output table body
$serial = ($page - 1) * ($per_page > 0 ? $per_page : 1) + 1;
echo '<tbody>';
foreach ($all_products as $product) {
    echo '<tr>';
    echo '<td>' . $serial++ . '</td>';
    echo '<td>' . htmlspecialchars($product['name']) . '</td>';
    $product_id = $product['id'];
    $main_qty = $product['stock'];
    $main_min = (int)($product['min_stock_main'] ?? 0);
    $main_price = (float)($product['purchase_price'] ?? 0);
    $main_style = ($main_qty < $main_min) ? 'class="low-stock"' : '';
    echo "<td $main_style>$main_qty</td>";
    $total = $main_qty;
    // Branches
    foreach ($branches as $branch) {
        $bid = $branch['id'];
        $q = "SELECT quantity FROM product_stock WHERE product_id = $product_id AND branch_id = $bid";
        $res = mysqli_query($conn, $q);
        $row = mysqli_fetch_assoc($res);
        $qty = $row ? $row['quantity'] : 0;
        $total += $qty;
        // Branch min
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
    echo '</tr>';
}
echo '</tbody>';

// Output pagination
if ($per_page > 0) {
    $max_visible = 5;
    $start_page = max(1, $page - intval($max_visible / 2));
    $end_page = $start_page + $max_visible - 1;
    if ($end_page > $total_pages) {
        $end_page = $total_pages;
        $start_page = max(1, $end_page - $max_visible + 1);
    }
    echo '<div class="d-flex justify-content-center my-4"><nav><ul class="pagination">';
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
}
