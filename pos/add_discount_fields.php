<?php
include 'db_connection.php';

// Add discount fields to products table
$alter_queries = [
    "ALTER TABLE products ADD COLUMN discount_type ENUM('none', 'percentage', 'amount') DEFAULT 'none'",
    "ALTER TABLE products ADD COLUMN discount_value DECIMAL(10,2) DEFAULT 0.00",
    "ALTER TABLE products ADD COLUMN final_price DECIMAL(10,2) DEFAULT 0.00"
];

$success = true;
foreach ($alter_queries as $query) {
    if (!mysqli_query($conn, $query)) {
        echo "❌ Error executing: $query<br>";
        echo "Error: " . mysqli_error($conn) . "<br>";
        $success = false;
    }
}

if ($success) {
    echo "✅ تم إضافة حقول الخصم بنجاح!<br>";

    // Update existing products to calculate final_price
    $update_query = "UPDATE products SET final_price = sale_price WHERE discount_type = 'none' OR discount_type IS NULL";
    if (mysqli_query($conn, $update_query)) {
        echo "✅ تم تحديث الأسعار النهائية للمنتجات الموجودة<br>";
    }

    echo "✅ تم إعداد نظام الخصم بنجاح!";
} else {
    echo "❌ حدث خطأ أثناء إعداد نظام الخصم";
}
