<?php
// حماية الصلاحية للصفحة
include 'page_protect.php';

session_start();
if (!isset($_SESSION['username']) || !isset($_SESSION['branch_id'])) {
    header("Location: login.php");
    exit();
}

include 'db_connection.php';

app_log('REQUEST: ' . $_SERVER['REQUEST_METHOD'] . ' ' . $_SERVER['REQUEST_URI'] . ' | GET: ' . json_encode($_GET) . ' | POST: ' . json_encode($_POST) . ' | SESSION: ' . json_encode($_SESSION));

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['cart_data'])) {
    $username = $_SESSION['username'];
    $branch_id = $_SESSION['branch_id'];
    $now = date("Y-m-d H:i:s");

    $cart = json_decode($_POST['cart_data'], true);
    app_log('Cart: ' . print_r($cart, true));

    $errors = [];
    $success = false;

    foreach ($cart as $product_id => $item) {
        $product_id = intval($product_id);
        $quantity = intval($item['qty']);
        $unit_price = floatval($item['price']);
        $total_price = $unit_price * $quantity;

        if ($quantity <= 0) continue;

        // جلب الكمية من مخزون الفرع
        $stock_q = mysqli_query($conn, "SELECT quantity FROM product_stock WHERE product_id = $product_id AND branch_id = $branch_id");
        $stock_data = mysqli_fetch_assoc($stock_q);
        $branch_stock = $stock_data ? intval($stock_data['quantity']) : null;
        app_log("Stock for product_id $product_id in branch $branch_id: " . ($branch_stock === null ? 'NO ROW' : $branch_stock));

        if ($branch_stock === null) {
            // لا يوجد مخزون لهذا المنتج في هذا الفرع
            $errors[] = "لا يوجد مخزون لهذا المنتج (ID: $product_id) في هذا الفرع.";
            continue;
        }
        if ($branch_stock < $quantity) {
            app_log("Insufficient stock for product_id $product_id: requested $quantity, available $branch_stock");
            $errors[] = "الكمية غير كافية للمنتج (ID: $product_id). المتوفر: $branch_stock, المطلوب: $quantity.";
            continue;
        }

        // خصم الكمية من مخزون الفرع
        $new_stock = $branch_stock - $quantity;
        mysqli_query($conn, "UPDATE product_stock SET quantity = $new_stock WHERE product_id = $product_id AND branch_id = $branch_id");

        // تسجيل البيع
        // Fetch product info including discount
        $product_q = mysqli_query($conn, "SELECT sale_price, discount_type, discount_value FROM products WHERE id = $product_id");
        $product = mysqli_fetch_assoc($product_q);

        $discounted_price = $product['sale_price'];
        if ($product['discount_type'] == 'percent') {
            $discounted_price = $product['sale_price'] * (1 - $product['discount_value'] / 100);
        } elseif ($product['discount_type'] == 'amount') {
            $discounted_price = $product['sale_price'] - $product['discount_value'];
        }
        $total_price = $discounted_price * $quantity;

        $stmt = $conn->prepare("INSERT INTO sales (product_id, quantity, total_price, sale_date, sold_by, branch_id)
                                VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("iidssi", $product_id, $quantity, $total_price, $now, $username, $branch_id);
        if (!$stmt->execute()) {
            app_log('Sales insert error: ' . $stmt->error);
            $errors[] = "خطأ في تسجيل البيع للمنتج (ID: $product_id): " . $stmt->error;
        } else {
            $success = true;
        }
        $stmt->close();
    }

    if (!empty($errors)) {
        echo '<div class="container my-5">';
        foreach ($errors as $err) {
            echo '<div class="alert alert-danger text-center">❌ ' . htmlspecialchars($err) . '</div>';
        }
        if ($success) {
            echo '<div class="alert alert-success text-center">✅ تم تنفيذ البيع للمنتجات المتوفرة فقط.</div>';
        }
        echo '<div class="text-center mt-4"><a href="sales.php" class="btn btn-dark">العودة لنقطة البيع</a></div>';
        echo '</div>';
        exit();
    }

    header("Location: sales.php?success=1");
    exit();
} else {
    header("Location: sales.php");
    exit();
}
