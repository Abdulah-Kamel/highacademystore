<?php
include 'db_connection.php';

// Create user_permissions table if it doesn't exist
$create_table = "
CREATE TABLE IF NOT EXISTS user_permissions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    page VARCHAR(255) NOT NULL,
    can_access TINYINT(1) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY unique_user_page (user_id, page),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
)";

if (mysqli_query($conn, $create_table)) {
    echo "✅ تم إنشاء جدول الصلاحيات بنجاح<br>";
} else {
    echo "❌ خطأ في إنشاء جدول الصلاحيات: " . mysqli_error($conn) . "<br>";
}

// Add default permissions for existing users if they don't have any
$users_without_permissions = mysqli_query($conn, "
    SELECT u.id FROM users u 
    LEFT JOIN user_permissions up ON u.id = up.user_id 
    WHERE up.user_id IS NULL
");

if (mysqli_num_rows($users_without_permissions) > 0) {
    $available_pages = [
        'dashboard.php'      => 'لوحة التحكم',
        'sales_report.php'   => 'تقرير المبيعات',
        'view_products.php'  => 'قائمة المنتجات',
        'add_products.php'   => 'إضافة منتج',
        'branch_stock_report.php' => 'تقرير المخزون',
        'manage_users.php'   => 'إدارة الموظفين',
        'sales.php'          => 'الكاشير (نقطة البيع)',
        'returns.php'        => 'المراجعات (المرتجعات)',
        'add_purchase_invoice.php' => 'إضافة فاتورة شراء',
        'suppliers.php'      => 'الموردين',
        'price_compare.php'  => 'مقارنة الأسعار'
    ];

    while ($user = mysqli_fetch_assoc($users_without_permissions)) {
        foreach ($available_pages as $page => $title) {
            // Give basic access to sales.php for all users
            $can_access = ($page === 'sales.php') ? 1 : 0;
            mysqli_query($conn, "INSERT INTO user_permissions (user_id, page, can_access) VALUES ({$user['id']}, '$page', $can_access)");
        }
    }
    echo "✅ تم إضافة الصلاحيات الافتراضية للمستخدمين الموجودين<br>";
}

echo "✅ تم إعداد نظام الصلاحيات بنجاح!";
