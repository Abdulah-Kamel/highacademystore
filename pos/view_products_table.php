<?php
include 'page_protect.php';
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

// Fetch products for this page
if ($per_page > 0) {
    $sql = "SELECT * FROM products $where LIMIT $start, $per_page";
} else {
    $sql = "SELECT * FROM products $where";
}
$result = $conn->query($sql);

// Output table body

echo '<tbody>';
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        echo '<tr class="product-row">';
        echo '<td>';
        if (!empty($row['image'])) {
            echo '<img src="uploads/' . htmlspecialchars($row['image']) . '" class="img-thumbnail" style="width: 80px; height: 80px; object-fit: cover;">';
        } else {
            echo '<span class="text-muted">لا يوجد</span>';
        }
        echo '</td>';
        echo '<td>' . htmlspecialchars($row['name']) . '</td>';
        echo '<td>' . number_format($row['purchase_price'], 0) . ' ج.م</td>';
        echo '<td>' . number_format($row['sale_price'], 0) . ' ج.م</td>';
        echo '<td>' . $row['stock'] . '</td>';
        echo '<td>' . ($row['min_stock_main'] ?? '-') . '</td>';
        echo '<td>' . ($row['min_stock_shebin'] ?? '-') . '</td>';
        echo '<td>' . ($row['min_stock_qwisna'] ?? '-') . '</td>';
        echo '<td><a href="edit_product.php?id=' . $row['id'] . '" class="btn btn-sm btn-outline-primary">✏️ تعديل</a></td>';
        echo '<td><a href="delete_product.php?id=' . $row['id'] . '" class="btn btn-sm btn-outline-danger" onclick="return confirm(\'⚠️ هل أنت متأكد من حذف المنتج؟\')">🗑️ حذف</a></td>';
        echo '</tr>';
    }
} else {
    echo '<tr><td colspan="11" class="text-muted">لا توجد منتجات.</td></tr>';
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
