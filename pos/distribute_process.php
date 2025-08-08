<?php
// حماية الصلاحية للصفحة
include 'page_protect.php';

session_start();
include 'db_connection.php';

function redirect_with_error($error)
{
    header("Location: distribute_stock.php?error=$error");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: distribute_stock.php');
    exit();
}

$product_id = isset($_POST['product_id']) ? intval($_POST['product_id']) : 0;
$from_branch = isset($_POST['from_branch_id']) ? $_POST['from_branch_id'] : '';
$to_branch = isset($_POST['to_branch_id']) ? $_POST['to_branch_id'] : '';
$quantity = isset($_POST['quantity']) ? intval($_POST['quantity']) : 0;

if ($product_id <= 0 || $quantity <= 0 || empty($from_branch) || empty($to_branch)) {
    redirect_with_error('missing');
}

if ($from_branch === $to_branch) {
    redirect_with_error('invalid'); // Cannot transfer to the same branch
}

// Start transaction
mysqli_begin_transaction($conn);

try {
    // Decrement from source
    if ($from_branch === 'main') {
        $check_sql = "SELECT stock FROM products WHERE id = $product_id FOR UPDATE";
        $result = mysqli_query($conn, $check_sql);
        $product = mysqli_fetch_assoc($result);
        if (!$product || $product['stock'] < $quantity) {
            throw new Exception('insufficient');
        }
        mysqli_query($conn, "UPDATE products SET stock = stock - $quantity WHERE id = $product_id");
    } else {
        $from_branch_id = intval($from_branch);
        $check_sql = "SELECT quantity FROM product_stock WHERE product_id = $product_id AND branch_id = $from_branch_id FOR UPDATE";
        $result = mysqli_query($conn, $check_sql);
        $stock = mysqli_fetch_assoc($result);
        if (!$stock || $stock['quantity'] < $quantity) {
            throw new Exception('insufficient');
        }
        mysqli_query($conn, "UPDATE product_stock SET quantity = quantity - $quantity WHERE product_id = $product_id AND branch_id = $from_branch_id");
    }

    // Increment at destination
    if ($to_branch === 'main') {
        mysqli_query($conn, "UPDATE products SET stock = stock + $quantity WHERE id = $product_id");
    } else {
        $to_branch_id = intval($to_branch);
        $check_sql = "SELECT id FROM product_stock WHERE product_id = $product_id AND branch_id = $to_branch_id";
        $result = mysqli_query($conn, $check_sql);
        if (mysqli_num_rows($result) > 0) {
            mysqli_query($conn, "UPDATE product_stock SET quantity = quantity + $quantity WHERE product_id = $product_id AND branch_id = $to_branch_id");
        } else {
            mysqli_query($conn, "INSERT INTO product_stock (product_id, branch_id, quantity) VALUES ($product_id, $to_branch_id, $quantity)");
        }
    }

    // If all queries succeed, commit the transaction
    mysqli_commit($conn);
    header("Location: distribute_stock.php?success=1");
    exit();
} catch (Exception $e) {
    // If any query fails, roll back the transaction
    mysqli_rollback($conn);
    redirect_with_error($e->getMessage());
}
