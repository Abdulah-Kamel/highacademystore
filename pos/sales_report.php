<?php
// حماية الصفحة بناءً على الصلاحيات
include 'page_protect.php'; // السيشن ستارت هنا

include 'db_connection.php';
include 'header.php';

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

// Function to format date and time in Arabic
function formatArabicDateTime($datetime, $format = 'full')
{
    $timestamp = strtotime($datetime);

    // Arabic day names
    $arabic_days = [
        'Sunday' => 'الأحد',
        'Monday' => 'الإثنين',
        'Tuesday' => 'الثلاثاء',
        'Wednesday' => 'الأربعاء',
        'Thursday' => 'الخميس',
        'Friday' => 'الجمعة',
        'Saturday' => 'السبت'
    ];

    $day = date('j', $timestamp);
    $month = date('n', $timestamp); // Month number instead of name
    $year = date('Y', $timestamp);
    $day_name = $arabic_days[date('l', $timestamp)];

    // Convert numbers to Arabic numerals
    $arabic_numerals = ['٠', '١', '٢', '٣', '٤', '٥', '٦', '٧', '٨', '٩'];
    $english_numerals = ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9'];

    if ($format == 'time_only') {
        $time = date('g:i A', $timestamp);
        $time = str_replace(['AM', 'PM'], ['ص', 'م'], $time);
        return str_replace($english_numerals, $arabic_numerals, $time);
    } elseif ($format == 'date_only') {
        $date_str = "$day/$month/$year";
        return str_replace($english_numerals, $arabic_numerals, $date_str);
    } elseif ($format == 'short') {
        $time = date('g:i A', $timestamp);
        $time = str_replace(['AM', 'PM'], ['ص', 'م'], $time);
        $date_str = "$day/$month/$year - $time";
        return str_replace($english_numerals, $arabic_numerals, $date_str);
    } else {
        // Full format
        $time = date('g:i A', $timestamp);
        $time = str_replace(['AM', 'PM'], ['ص', 'م'], $time);
        $date_str = "$day_name, $day/$month/$year - $time";
        return str_replace($english_numerals, $arabic_numerals, $date_str);
    }
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

// تحديد بيانات المستخدم
$user_role = $_SESSION['role'] ?? '';
$user_branch_id = $_SESSION['branch_id'] ?? null;

// تحديد فلتـر الفرع حسب نوع المستخدم
if ($user_role == 'admin') {
    $branch_filter = $_GET['branch_id'] ?? 'all';
} else {
    $branch_filter = $user_branch_id;
}

$start = $_GET['start_date'] ?? date("Y-m-d");
$end = $_GET['end_date'] ?? date("Y-m-d");

$where = "s.sale_date BETWEEN '$start 00:00:00' AND '$end 23:59:59'";
if ($branch_filter !== 'all') {
    $where .= " AND s.branch_id = " . intval($branch_filter);
}

// جلب الفروع
$branches = mysqli_query($conn, "SELECT * FROM branches");
?>

<div class="container-fluid mt-5">
    <div class="row">

        <!-- Main Content -->
        <div class="col-md-9">
            <h2 class="mb-4 text-center">📊 تقرير المبيعات حسب الفروع</h2>

            <!-- Display success/error messages -->
            <?php if (isset($_SESSION['success_message'])): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <?= $_SESSION['success_message'] ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
                <?php unset($_SESSION['success_message']); ?>
            <?php endif; ?>

            <?php if (isset($_SESSION['error_message'])): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <?= $_SESSION['error_message'] ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
                <?php unset($_SESSION['error_message']); ?>
            <?php endif; ?>

            <!-- ✅ الفلاتر -->
            <form method="GET" class="row g-3 mb-4 align-items-end">
                <div class="col-md-3">
                    <label>من تاريخ:</label>
                    <input type="date" name="start_date" class="form-control" value="<?= $start ?>">
                </div>
                <div class="col-md-3">
                    <label>إلى تاريخ:</label>
                    <input type="date" name="end_date" class="form-control" value="<?= $end ?>">
                </div>
                <div class="col-md-3">
                    <label>الفرع:</label>
                    <?php if ($user_role == 'admin'): ?>
                        <select name="branch_id" class="form-select">
                            <option value="all" <?= ($branch_filter == 'all') ? 'selected' : '' ?>>كل الفروع</option>
                            <?php
                            mysqli_data_seek($branches, 0);
                            while ($b = mysqli_fetch_assoc($branches)) : ?>
                                <option value="<?= $b['id'] ?>" <?= ($branch_filter == $b['id']) ? 'selected' : '' ?>>
                                    <?= $b['name'] ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                    <?php else: ?>
                        <input type="hidden" name="branch_id" value="<?= $user_branch_id ?>">
                        <input type="text" class="form-control" value="<?php
                                                                        mysqli_data_seek($branches, 0);
                                                                        while ($b = mysqli_fetch_assoc($branches)) {
                                                                            if ($b['id'] == $user_branch_id) echo $b['name'];
                                                                        }
                                                                        ?>" readonly>
                    <?php endif; ?>
                </div>
                <div class="col-md-3 d-flex gap-2">
                    <button type="submit" class="btn btn-primary w-50">عرض التقرير</button>
                    <a href="export_sales_summary_pdf.php?start_date=<?= $start ?>&end_date=<?= $end ?>&branch_id=<?= $branch_filter ?>"
                        class="btn btn-danger w-50" target="_blank">🖨️ تصدير PDF (الملخص فقط)</a>
                </div>
            </form>

            <?php
            // Prepare data for withdrawal sidebar
            $total_sales_for_sidebar = 0;
            $total_withdrawals_for_sidebar = 0;
            $branch_name_for_sidebar = '';
            $available_for_withdrawal_sidebar = 0;
            $withdrawal_history_sidebar = [];

            if ($branch_filter !== 'all' && is_numeric($branch_filter)) {
                $branch_id = intval($branch_filter);

                // Get branch name
                $branch_q = mysqli_query($conn, "SELECT name FROM branches WHERE id = $branch_id");
                if ($branch_row = mysqli_fetch_assoc($branch_q)) {
                    $branch_name_for_sidebar = $branch_row['name'];
                }

                // Calculate total sales for the period
                $sales_query = "SELECT SUM(total_price) as total FROM sales WHERE branch_id = $branch_id AND sale_date BETWEEN '$start 00:00:00' AND '$end 23:59:59'";
                $sales_result = mysqli_query($conn, $sales_query);
                if ($sales_row = mysqli_fetch_assoc($sales_result)) {
                    $total_sales_for_sidebar = $sales_row['total'] ?? 0;
                }

                // Calculate total withdrawals for the period
                $withdrawal_where_sidebar = "withdrawal_date BETWEEN '$start 00:00:00' AND '$end 23:59:59' AND branch_id = $branch_id";
                $withdrawal_query = "SELECT SUM(amount) as total FROM withdrawals WHERE $withdrawal_where_sidebar";
                $withdrawal_result = mysqli_query($conn, $withdrawal_query);
                if ($withdrawal_row = mysqli_fetch_assoc($withdrawal_result)) {
                    $total_withdrawals_for_sidebar = $withdrawal_row['total'] ?? 0;
                }

                $available_for_withdrawal_sidebar = $total_sales_for_sidebar - $total_withdrawals_for_sidebar;

                // Get withdrawal history
                $history_query_sidebar = "SELECT * FROM withdrawals WHERE branch_id = $branch_id AND $withdrawal_where_sidebar ORDER BY withdrawal_date DESC";
                $history_result_sidebar = mysqli_query($conn, $history_query_sidebar);
                while ($row = mysqli_fetch_assoc($history_result_sidebar)) {
                    $withdrawal_history_sidebar[] = $row;
                }
            }
            ?>

            <!-- ✅ ملخص الفروع أولا (لأدمن فقط) -->
            <?php if ($user_role == 'admin'): ?>
                <?php
                if ($branch_filter === 'all' && $user_role == 'admin') {
                    $branches_data = mysqli_query($conn, "SELECT * FROM branches");
                } else {
                    $branches_data = mysqli_query($conn, "SELECT * FROM branches WHERE id = " . intval($branch_filter));
                }

                mysqli_data_seek($branches_data, 0);
                while ($branch = mysqli_fetch_assoc($branches_data)) :
                    $branch_id = $branch['id'];
                    $branch_name = $branch['name'];

                    $summary_query = "
                SELECT p.name AS product_name, 
                       SUM(s.quantity) AS total_qty,
                       SUM(s.total_price - (s.quantity * p.purchase_price)) AS total_profit,
                       SUM(s.total_price) AS total_sales,
                       MAX(ps.quantity) AS remaining_stock
                FROM sales s
                JOIN products p ON s.product_id = p.id
                LEFT JOIN product_stock ps ON ps.product_id = p.id AND ps.branch_id = $branch_id
                WHERE s.branch_id = $branch_id AND $where
                GROUP BY s.product_id
            ";
                    $summary_result = mysqli_query($conn, $summary_query);

                    // Get total withdrawals for this branch in the same period
                    $withdrawal_where = "withdrawal_date BETWEEN '$start 00:00:00' AND '$end 23:59:59' AND branch_id = $branch_id";
                    $withdrawal_query = "SELECT SUM(amount) as total_withdrawals FROM withdrawals WHERE $withdrawal_where";
                    $withdrawal_result = mysqli_query($conn, $withdrawal_query);
                    $total_withdrawals = mysqli_fetch_assoc($withdrawal_result)['total_withdrawals'] ?? 0;

                    $total_profit = 0;
                    $total_sales = 0;
                    if (mysqli_num_rows($summary_result) > 0): ?>
                        <div class="mb-4">
                            <h5 class="text-info">📦 ملخص مبيعات فرع <?= $branch_name ?> خلال الفترة من <?= formatArabicDateTime($start, 'date_only') ?> إلى <?= formatArabicDateTime($end, 'date_only') ?></h5>
                            <table class="table table-bordered table-striped table-sm text-center shadow">
                                <thead style="background: #6db2ff; color:#fff;">
                                    <tr>
                                        <th>المنتج</th>
                                        <th>الكمية المباعة</th>
                                        <th style="background:#d1ffd6; color:#222;">المتبقي بالمخزون</th>
                                        <th style="background:#d9faff;">الربح</th>
                                        <th style="background:#ffe8c2;">إجمالي البيع</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php while ($sum = mysqli_fetch_assoc($summary_result)) :
                                        $total_profit += $sum['total_profit'];
                                        $total_sales += $sum['total_sales'];
                                    ?>
                                        <tr>
                                            <td><?= $sum['product_name'] ?></td>
                                            <td><?= $sum['total_qty'] ?></td>
                                            <td><?= $sum['remaining_stock'] ?? 0 ?></td>
                                            <td><?= number_format($sum['total_profit'], 2) ?> ج.م</td>
                                            <td><?= number_format($sum['total_sales'], 2) ?> ج.م</td>
                                        </tr>
                                    <?php endwhile; ?>
                                </tbody>
                                <tfoot>
                                    <tr style="background:#e0f2fa;">
                                        <td colspan="3"><strong>إجمالي الربح</strong></td>
                                        <td colspan="2"><strong><?= number_format($total_profit, 2) ?> ج.م</strong></td>
                                    </tr>
                                    <tr style="background:#fff1b6;">
                                        <td colspan="3"><strong>إجمالي المبيعات</strong></td>
                                        <td colspan="2"><strong><?= number_format($total_sales, 2) ?> ج.م</strong></td>
                                    </tr>
                                    <?php if ($total_withdrawals > 0): ?>
                                        <tr style="background:#ffebee;">
                                            <td colspan="3"><strong>إجمالي المسحوبات</strong></td>
                                            <td colspan="2"><strong style="color: #d32f2f;">-<?= number_format($total_withdrawals, 2) ?> ج.م</strong></td>
                                        </tr>
                                        <tr style="background:#e8f5e8;">
                                            <td colspan="3"><strong>صافي المبيعات (بعد المسحوبات)</strong></td>
                                            <td colspan="2"><strong style="color: #2e7d32;"><?= number_format($total_sales - $total_withdrawals, 2) ?> ج.م</strong></td>
                                        </tr>
                                    <?php endif; ?>
                                </tfoot>
                            </table>

                        </div>
                <?php endif;
                endwhile; ?>
            <?php endif; ?>

            <!-- تفاصيل المبيعات بعد الملخصات (تظهر لكل يوزر فرعه فقط أو لكل الفروع للأدمن) -->
            <?php
            // أعد جلب الفروع لنفس المنطق
            if ($branch_filter === 'all' && $user_role == 'admin') {
                $branches_data2 = mysqli_query($conn, "SELECT * FROM branches");
            } else {
                $branches_data2 = mysqli_query($conn, "SELECT * FROM branches WHERE id = " . intval($branch_filter));
            }

            while ($branch = mysqli_fetch_assoc($branches_data2)) :
                $branch_id = $branch['id'];
                $branch_name = $branch['name'];

                $query = "
            SELECT s.*, p.name AS product_name, p.purchase_price
            FROM sales s
            JOIN products p ON s.product_id = p.id
            WHERE s.branch_id = $branch_id AND $where
            ORDER BY s.sale_date DESC
        ";

                $result = mysqli_query($conn, $query);
                if (mysqli_num_rows($result) > 0):
                    $remaining_stock_map = [];

                    // Get withdrawals for this branch
                    $withdrawal_where = "withdrawal_date BETWEEN '$start 00:00:00' AND '$end 23:59:59' AND branch_id = $branch_id";
                    $withdrawal_query = "SELECT SUM(amount) as total_withdrawals FROM withdrawals WHERE $withdrawal_where";
                    $withdrawal_result = mysqli_query($conn, $withdrawal_query);
                    $branch_withdrawals = mysqli_fetch_assoc($withdrawal_result)['total_withdrawals'] ?? 0;
            ?>
                    <div class="mb-5">
                        <h4 class="mb-3 text-success">🏬 <?= $branch_name ?></h4>
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover text-center shadow">
                                <thead style="background: #183153; color:#fff;">
                                    <tr>
                                        <th>اسم المنتج</th>
                                        <th style="background:#d9faff; color:#333;">الكمية</th>
                                        <th style="background:#d1ffd6; color:#222;">الباقي بالمخزون</th>
                                        <th style="background:#ffe8c2;">الإجمالي</th>
                                        <th>الوقت / التاريخ</th>
                                        <th>تم بواسطة</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $total = 0;
                                    while ($row = mysqli_fetch_assoc($result)) :
                                        $total += $row['total_price'];
                                        $product_id = $row['product_id'];

                                        if (!isset($remaining_stock_map[$product_id])) {
                                            $stock_q = mysqli_query($conn, "SELECT quantity FROM product_stock WHERE product_id = $product_id AND branch_id = $branch_id");
                                            $stock = mysqli_fetch_assoc($stock_q);
                                            $remaining_stock_map[$product_id] = $stock['quantity'] ?? 0;
                                        }

                                        $current_remaining = $remaining_stock_map[$product_id];
                                    ?>
                                        <tr>
                                            <td><?= $row['product_name'] ?></td>
                                            <td><?= $row['quantity'] ?></td>
                                            <td><?= $current_remaining ?></td>
                                            <td><?= number_format($row['total_price'], 2) ?> ج.م</td>
                                            <td>
                                                <?= formatArabicDateTime($row['sale_date'], 'time_only') ?><br>
                                                <small><?= formatArabicDateTime($row['sale_date'], 'date_only') ?></small>
                                            </td>
                                            <td><?= $row['sold_by'] ?></td>
                                        </tr>
                                    <?php
                                        $remaining_stock_map[$product_id] += $row['quantity'];
                                    endwhile;
                                    ?>
                                </tbody>
                                <tfoot>
                                    <tr style="background:#e0f2fa;">
                                        <td colspan="3"><strong>إجمالي المبيعات</strong></td>
                                        <td colspan="3"><strong><?= number_format($total, 2) ?> ج.م</strong></td>
                                    </tr>
                                    <?php if ($branch_withdrawals > 0): ?>
                                        <tr style="background:#ffebee;">
                                            <td colspan="3"><strong>إجمالي المسحوبات</strong></td>
                                            <td colspan="3"><strong style="color: #d32f2f;">-<?= number_format($branch_withdrawals, 2) ?> ج.م</strong></td>
                                        </tr>
                                        <tr style="background:#e8f5e8;">
                                            <td colspan="3"><strong>صافي المبيعات (بعد المسحوبات)</strong></td>
                                            <td colspan="3"><strong style="color: #2e7d32;"><?= number_format($total - $branch_withdrawals, 2) ?> ج.م</strong></td>
                                        </tr>
                                    <?php endif; ?>
                                </tfoot>
                            </table>
                        </div>

                    </div>
            <?php endif;
            endwhile; ?>
        </div>


        <!-- Withdrawal Sidebar -->
        <div class="col-md-3">
            <div class="card shadow-sm" style="position: sticky; top: 85px; z-index: 100;">
                <div class="card-header bg-dark text-white text-center py-2">
                    💰 <strong>إدارة المسحوبات</strong>
                </div>
                <div class="card-body p-3" style="max-height: 80vh; overflow-y: auto;">
                    <?php if ($branch_filter !== 'all' && is_numeric($branch_filter)): ?>
                        <h5 class="text-center mb-3">فرع: <?= $branch_name_for_sidebar ?></h5>

                        <!-- Withdrawal Form -->
                        <div class="mb-4">
                            <h6>إضافة سحب جديد</h6>
                            <?php if ($available_for_withdrawal_sidebar > 0): ?>
                                <div class="alert alert-info p-2 small">
                                    متاح للسحب: <strong><?= number_format($available_for_withdrawal_sidebar, 2) ?> ج.م</strong>
                                </div>
                                <form method="POST" action="process_withdrawal.php">
                                    <input type="hidden" name="branch_id" value="<?= $branch_filter ?>">
                                    <input type="hidden" name="start_date" value="<?= $start ?>">
                                    <input type="hidden" name="end_date" value="<?= $end ?>">
                                    <input type="hidden" name="original_branch_filter" value="<?= $branch_filter ?>">
                                    <div class="mb-2">
                                        <label class="form-label small">المبلغ:</label>
                                        <input type="number" step="0.01" name="amount" class="form-control form-control-sm" max="<?= $available_for_withdrawal_sidebar ?>" required>
                                    </div>
                                    <div class="mb-2">
                                        <label class="form-label small">السبب (اختياري):</label>
                                        <input type="text" name="reason" class="form-control form-control-sm">
                                    </div>
                                    <button type="submit" name="add_withdrawal" class="btn btn-success btn-sm w-100">تنفيذ السحب</button>
                                </form>
                            <?php else: ?>
                                <div class="alert alert-warning p-2 small">
                                    لا يوجد مبلغ متاح للسحب في هذه الفترة.
                                </div>
                            <?php endif; ?>
                        </div>

                        <!-- Withdrawal History -->
                        <?php if (!empty($withdrawal_history_sidebar)): ?>
                            <hr>
                            <h6>تاريخ المسحوبات</h6>
                            <div class="table-responsive">
                                <table class="table table-sm table-bordered small">
                                    <thead class="table-light">
                                        <tr>
                                            <th>المبلغ</th>
                                            <th>السبب</th>
                                            <th>التاريخ</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($withdrawal_history_sidebar as $withdrawal): ?>
                                            <tr>
                                                <td class="text-danger"><?= number_format($withdrawal['amount'], 2) ?></td>
                                                <td><?= htmlspecialchars($withdrawal['reason'] ?: '-') ?></td>
                                                <td><?= formatArabicDateTime($withdrawal['withdrawal_date'], 'short') ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php else: ?>
                            <p class="text-muted text-center small">لا يوجد مسحوبات في هذه الفترة.</p>
                        <?php endif; ?>

                    <?php else: ?>
                        <div class="alert alert-primary text-center">
                            <i class="bi bi-info-circle fs-4"></i>
                            <p class="mt-2">الرجاء اختيار فرع محدد من الفلاتر لعرض وإدارة المسحوبات الخاصة به.</p>
                        </div>
                    <?php endif; ?>
                </div>
                <div class="card-footer text-center small">
                    صافي المبيعات:
                    <?php if ($branch_filter !== 'all' && is_numeric($branch_filter)): ?>
                        <strong class="text-success"><?= number_format($available_for_withdrawal_sidebar, 2) ?> ج.م</strong>
                    <?php else: ?>
                        <span>-</span>
                    <?php endif; ?>
                </div>
            </div>
        </div> <!-- /col-md-3 -->
    </div> <!-- /col-md-9 -->

</div> <!-- /row -->

<?php include 'footer.php'; ?>