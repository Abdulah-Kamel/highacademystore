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
$page = strtolower($page); // Convert to lowercase for case-insensitive comparison
$check = mysqli_prepare($conn, "SELECT 1 FROM user_permissions WHERE user_id = ? AND LOWER(page) = ? AND can_access = 1");
mysqli_stmt_bind_param($check, "is", $user_id, $page);
mysqli_stmt_execute($check);
$result = mysqli_stmt_get_result($check);

if (mysqli_num_rows($result) == 0) {
    // Log the permission denied attempt
    error_log("Permission denied for user $user_id trying to access $page");
    
    // Redirect to sales page with an error message
    $_SESSION['error'] = "ليس لديك صلاحية للوصول إلى هذه الصفحة";
    if ($page !== 'sales.php') {
        header("Location: sales.php");
        exit();
    }
    // If already on sales.php but no permission, show an error
    if (!isset($_SESSION['error_shown'])) {
        $_SESSION['error_shown'] = true;
        echo "<script>alert('ليس لديك صلاحية للوصول إلى هذه الصفحة');</script>";
    }
}

app_log('REQUEST: ' . $_SERVER['REQUEST_METHOD'] . ' ' . $_SERVER['REQUEST_URI'] . ' | GET: ' . json_encode($_GET) . ' | POST: ' . json_encode($_POST) . ' | SESSION: ' . json_encode($_SESSION));
