<?php
session_start();
include 'db_connection.php';

$user_id = $_SESSION['user_id'] ?? 0;
$page = basename($_SERVER['SCRIPT_NAME']);

// Allow access to sales_products_table.php for all logged-in users
if ($page === 'sales_products_table.php') {
    return;
}

if ($user_id == 0) {
    header("Location: login.php");
    exit();
}

// Check if user is admin - admins have access to all pages
if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin') {
    return;
}

// Check specific page permissions
$check = mysqli_query($conn, "SELECT * FROM user_permissions WHERE user_id = $user_id AND page = '$page' AND can_access = 1");
if (mysqli_num_rows($check) == 0) {
    // توجيه المستخدم تلقائيًا لصفحة الكاشير إذا لم يملك صلاحية الصفحة
    header("Location: sales.php");
    exit();
}

app_log('REQUEST: ' . $_SERVER['REQUEST_METHOD'] . ' ' . $_SERVER['REQUEST_URI'] . ' | GET: ' . json_encode($_GET) . ' | POST: ' . json_encode($_POST) . ' | SESSION: ' . json_encode($_SESSION));
