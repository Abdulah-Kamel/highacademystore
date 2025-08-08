<?php
include 'page_protect.php';
include 'db_connection.php';

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

// Create withdrawals table if it doesn't exist
$create_table_query = "
CREATE TABLE IF NOT EXISTS withdrawals (
    id INT AUTO_INCREMENT PRIMARY KEY,
    branch_id INT NOT NULL,
    amount DECIMAL(10,2) NOT NULL,
    reason VARCHAR(255),
    withdrawal_date DATETIME NOT NULL,
    withdrawn_by VARCHAR(100) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (branch_id) REFERENCES branches(id)
)";
mysqli_query($conn, $create_table_query);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_withdrawal'])) {
    $branch_id = intval($_POST['branch_id']);
    $amount = floatval($_POST['amount']);
    $reason = mysqli_real_escape_string($conn, $_POST['reason']);
    $withdrawn_by = $_SESSION['username'];
    $withdrawal_date = date('Y-m-d H:i:s');

    // Get date range from form
    $start = $_POST['start_date'] ?? date("Y-m-d");
    $end = $_POST['end_date'] ?? date("Y-m-d");

    // Validate amount
    if ($amount <= 0) {
        $_SESSION['error_message'] = "المبلغ يجب أن يكون أكبر من صفر";
        header("Location: sales_report.php?" . $_SERVER['QUERY_STRING']);
        exit();
    }

    // Get total sales for this branch in the specified period
    $sales_where = "s.sale_date BETWEEN '$start 00:00:00' AND '$end 23:59:59' AND s.branch_id = $branch_id";
    $sales_query = "SELECT SUM(s.total_price) as total_sales FROM sales s WHERE $sales_where";
    $sales_result = mysqli_query($conn, $sales_query);
    $total_sales = mysqli_fetch_assoc($sales_result)['total_sales'] ?? 0;

    // Get existing withdrawals for this branch in the same period
    $withdrawal_where = "withdrawal_date BETWEEN '$start 00:00:00' AND '$end 23:59:59' AND branch_id = $branch_id";
    $existing_withdrawals_query = "SELECT SUM(amount) as total_withdrawals FROM withdrawals WHERE $withdrawal_where";
    $existing_withdrawals_result = mysqli_query($conn, $existing_withdrawals_query);
    $existing_withdrawals = mysqli_fetch_assoc($existing_withdrawals_result)['total_withdrawals'] ?? 0;

    // Calculate remaining available amount
    $available_amount = $total_sales - $existing_withdrawals;

    // Check if withdrawal amount exceeds available amount
    if ($amount > $available_amount) {
        if ($total_sales == 0) {
            $_SESSION['error_message'] = "لا يمكن السحب - لا توجد مبيعات في هذه الفترة للفرع المحدد";
        } else {
            $_SESSION['error_message'] = "المبلغ المطلوب سحبه (" . number_format($amount, 2) . " ج.م) يتجاوز المبلغ المتاح للسحب (" . number_format($available_amount, 2) . " ج.م)";
        }

        // Redirect back to sales report with same parameters
        $redirect_params = [];
        if (isset($_POST['start_date'])) $redirect_params['start_date'] = $_POST['start_date'];
        if (isset($_POST['end_date'])) $redirect_params['end_date'] = $_POST['end_date'];
        if (isset($_POST['original_branch_filter'])) $redirect_params['branch_id'] = $_POST['original_branch_filter'];

        $query_string = http_build_query($redirect_params);
        header("Location: sales_report.php?" . $query_string);
        exit();
    }

    // Insert withdrawal record
    $insert_query = "INSERT INTO withdrawals (branch_id, amount, reason, withdrawal_date, withdrawn_by) 
                     VALUES ($branch_id, $amount, '$reason', '$withdrawal_date', '$withdrawn_by')";

    if (mysqli_query($conn, $insert_query)) {
        $_SESSION['success_message'] = "تم تسجيل السحب بنجاح - المبلغ: " . number_format($amount, 2) . " ج.م";
    } else {
        $_SESSION['error_message'] = "حدث خطأ أثناء تسجيل السحب: " . mysqli_error($conn);
    }

    // Redirect back to sales report with same parameters
    $redirect_params = [];
    if (isset($_POST['start_date'])) $redirect_params['start_date'] = $_POST['start_date'];
    if (isset($_POST['end_date'])) $redirect_params['end_date'] = $_POST['end_date'];
    if (isset($_POST['original_branch_filter'])) $redirect_params['branch_id'] = $_POST['original_branch_filter'];

    $query_string = http_build_query($redirect_params);
    header("Location: sales_report.php?" . $query_string);
    exit();
}

// If accessed directly, redirect to sales report
header("Location: sales_report.php");
exit();
