<?php
include 'db_connection.php';

// Get all cashier users
$cashiers = mysqli_query($conn, "SELECT id FROM users WHERE role = 'cashier'");

// Required pages for cashiers
$required_pages = [
    'sales.php',
    'sales_process.php',
    'sales_products_table.php'
];

while ($cashier = mysqli_fetch_assoc($cashier)) {
    $user_id = $cashier['id'];
    
    foreach ($required_pages as $page) {
        // Check if permission already exists
        $check = mysqli_query($conn, "SELECT 1 FROM user_permissions WHERE user_id = $user_id AND page = '$page'");
        
        if (mysqli_num_rows($check) == 0) {
            // Add permission
            mysqli_query($conn, "INSERT INTO user_permissions (user_id, page, can_access) VALUES ($user_id, '$page', 1)");
            echo "Added permission for page $page to user $user_id<br>";
        } else {
            // Update existing permission
            mysqli_query($conn, "UPDATE user_permissions SET can_access = 1 WHERE user_id = $user_id AND page = '$page'");
            echo "Updated permission for page $page to user $user_id<br>";
        }
    }
}

echo "<br>All cashier permissions have been updated. <a href='manage_users.php'>Return to user management</a>";
?>
