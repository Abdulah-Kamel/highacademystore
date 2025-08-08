<?php
include 'page_protect.php';
include 'db_connection.php';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: view_products.php?msg=invalid");
    exit();
}

$id = intval($_GET['id']);

// حذف كل ما يتعلق بالمنتج من جداول مرتبطة
$conn->query("DELETE FROM purchase_items WHERE product_id = $id");
// أضف هنا أي جداول أخرى فيها product_id كمفتاح أجنبي
// $conn->query("DELETE FROM sales WHERE product_id = $id");
// $conn->query("DELETE FROM returns WHERE product_id = $id");

// حذف صورة المنتج إن وجدت
$sql_img = "SELECT image FROM products WHERE id=$id";
$result_img = $conn->query($sql_img);
if ($result_img && $row_img = $result_img->fetch_assoc()) {
    if (!empty($row_img['image']) && file_exists("uploads/" . $row_img['image'])) {
        unlink("uploads/" . $row_img['image']);
    }
}

// حذف المنتج من قاعدة البيانات
$sql = "DELETE FROM products WHERE id=$id";
if ($conn->query($sql) === TRUE) {
    header("Location: view_products.php?msg=deleted");
    exit();
} else {
    header("Location: view_products.php?msg=error");
    exit();
}
?>