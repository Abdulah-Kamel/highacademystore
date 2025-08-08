<?php
// حماية الصلاحية للصفحة
include 'page_protect.php';

session_start();
include 'db_connection.php';

// التحقق من وجود البيانات
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $product_id = intval($_POST['product_id']);
    $branch_id = intval($_POST['branch_id']);
    $quantity = intval($_POST['quantity']);
    $reason = mysqli_real_escape_string($conn, $_POST['reason']);
    $return_date = date("Y-m-d H:i:s");

    // 1. تسجيل المرتجع في جدول returns
    $insert = mysqli_query($conn, "INSERT INTO returns (prouduct_id	, branch_id, quantity, reason, return_date)
                                   VALUES ($product_id, $branch_id, $quantity, '$reason', '$return_date')");

    // 2. تعديل الكمية في جدول product_stock
    $check = mysqli_query($conn, "SELECT * FROM product_stock WHERE product_id = $product_id AND branch_id = $branch_id");

    if (mysqli_num_rows($check) > 0) {
        mysqli_query($conn, "UPDATE product_stock SET quantity = quantity + $quantity WHERE product_id = $product_id AND branch_id = $branch_id");
    } else {
        mysqli_query($conn, "INSERT INTO product_stock (product_id, branch_id, quantity) VALUES ($product_id, $branch_id, $quantity)");
    }

    // 3. إعادة التوجيه
    header("Location: returns.php?success=1");
    exit;
} else {
    echo "طلب غير صالح.";
}
