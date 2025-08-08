<?php
// حماية الصلاحية للصفحة
include 'page_protect.php';

session_start();
include 'db_connection.php';

if (
    $_SERVER['REQUEST_METHOD'] == 'POST' &&
    isset($_POST['products_data']) &&
    isset($_POST['supplier_id'])
) {
    $supplier_id = mysqli_real_escape_string($conn, $_POST['supplier_id']);
    $branch_id = isset($_POST['branch_id']) ? intval($_POST['branch_id']) : 0;
    $items = json_decode($_POST['products_data'], true);

    if (!$items || !is_array($items) || !$supplier_id) {
        echo "<div class='alert alert-danger text-center mt-5'>❌ بيانات غير مكتملة!</div>";
        exit;
    }

    // 1. Calculate total amount
    $total_amount = 0;
    foreach ($items as $item) {
        $total_amount += intval($item['qty']) * floatval($item['price']);
    }

    // 2. Insert into purchases
    $now = date('Y-m-d H:i:s');
    $purchase_sql = "INSERT INTO purchases (supplier_id, purchase_date, total_amount) VALUES ($supplier_id, '$now', $total_amount)";
    if (!mysqli_query($conn, $purchase_sql)) {
        echo "<div class='alert alert-danger text-center mt-5'>❌ فشل في إضافة الفاتورة: " . mysqli_error($conn) . "</div>";
        exit;
    }
    $purchase_id = mysqli_insert_id($conn);

    $errors = [];
    $success_count = 0;

    foreach ($items as $item) {
        $product_name = mysqli_real_escape_string($conn, trim($item['name']));
        $qty = intval($item['qty']);
        $price = floatval($item['price']);

        // تحقق من وجود المنتج
        $product_q = mysqli_query($conn, "SELECT id FROM products WHERE name = '$product_name' LIMIT 1");
        if (mysqli_num_rows($product_q) == 0) {
            $errors[] = "المنتج ($product_name) غير موجود في قاعدة البيانات!";
            continue;
        }
        $product = mysqli_fetch_assoc($product_q);
        $product_id = $product['id'];

        // 3. Insert into purchase_items
        $item_sql = "INSERT INTO purchase_items (purchase_id, product_id, quantity, unit_price) VALUES ($purchase_id, $product_id, $qty, $price)";
        if (!mysqli_query($conn, $item_sql)) {
            $errors[] = "فشل في إضافة المنتج ($product_name) للفاتورة: " . mysqli_error($conn);
            continue;
        }

        // تحديث المخزون حسب الفرع
        if ($branch_id > 0) {
            // تحديث أو إدخال في جدول product_stock
            $check_stock = mysqli_query($conn, "SELECT quantity FROM product_stock WHERE product_id = $product_id AND branch_id = $branch_id");
            if (mysqli_num_rows($check_stock) > 0) {
                mysqli_query($conn, "UPDATE product_stock SET quantity = quantity + $qty WHERE product_id = $product_id AND branch_id = $branch_id");
            } else {
                mysqli_query($conn, "INSERT INTO product_stock (product_id, branch_id, quantity) VALUES ($product_id, $branch_id, $qty)");
            }
        } else {
            // لو مفيش فرع (المخزن الرئيسي)
            mysqli_query($conn, "UPDATE products SET stock = stock + $qty WHERE id = $product_id");
        }

        // تحديث سعر الشراء لو عايز
        mysqli_query($conn, "UPDATE products SET purchase_price = $price WHERE id = $product_id");

        $success_count++;
    }

    // عرض النتيجة
    if ($success_count > 0) {
        echo "<div class='alert alert-success text-center mt-5'>✅ تم إضافة $success_count منتج بنجاح للمخزن المحدد والفاتورة محفوظة.</div>";
    }
    if ($errors) {
        foreach ($errors as $err) {
            echo "<div class='alert alert-danger text-center mt-2'>❌ $err</div>";
        }
    }

    echo '<div class="text-center mt-4"><a href="add_purchase_invoice.php" class="btn btn-dark">إضافة فاتورة جديدة</a> | <a href="index.php" class="btn btn-secondary">العودة للرئيسية</a></div>';
    exit;
} else {
    echo "<div class='alert alert-danger text-center mt-5'>❌ وصول غير مسموح.</div>";
    exit;
}
