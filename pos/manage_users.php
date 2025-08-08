<?php
// حماية الصلاحية للصفحة
include 'page_protect.php';

include 'db_connection.php';
include 'header.php';

// ✅ حذف المستخدم بأمان
if (isset($_GET['delete'])) {
    $delete_id = (int)$_GET['delete'];
    $stmt = mysqli_prepare($conn, "DELETE FROM users WHERE id = ?");
    mysqli_stmt_bind_param($stmt, "i", $delete_id);
    mysqli_stmt_execute($stmt);
    header("Location: manage_users.php");
    exit();
}

// ✅ إضافة مستخدم جديد بأمان مع الصلاحيات
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["add_user"])) {
    $username = $_POST["username"];
    $raw_password = $_POST["password"];
    $hashed_password = password_hash($raw_password, PASSWORD_DEFAULT);
    $branch_id = $_POST["branch_id"];
    $role = $_POST["role"];
    $status = $_POST["status"];
    $permissions = $_POST["permissions"] ?? [];

    // تحقق من وجود اسم المستخدم مسبقاً باستخدام prepared statement
    $check_stmt = mysqli_prepare($conn, "SELECT id FROM users WHERE username = ?");
    mysqli_stmt_bind_param($check_stmt, "s", $username);
    mysqli_stmt_execute($check_stmt);
    $result = mysqli_stmt_get_result($check_stmt);

    if (mysqli_num_rows($result) > 0) {
        echo "<script>alert('⚠️ اسم المستخدم موجود بالفعل');</script>";
    } else {
        $insert_stmt = mysqli_prepare($conn, "INSERT INTO users (username, password, branch_id, role, status) VALUES (?, ?, ?, ?, ?)");
        mysqli_stmt_bind_param($insert_stmt, "ssiss", $username, $hashed_password, $branch_id, $role, $status);
        if (mysqli_stmt_execute($insert_stmt)) {
            $new_user_id = mysqli_insert_id($conn);

            // ✅ إضافة الصلاحيات للمستخدم الجديد
            $available_pages = [
                'dashboard.php'      => 'لوحة التحكم',
                'sales_report.php'   => 'تقرير المبيعات',
                'view_products.php'  => 'قائمة المنتجات',
                'add_products.php'   => 'إضافة منتج',
                'edit_product.php'   => 'تعديل منتج',
                'delete_product.php' => 'حذف منتج',
                'branch_stock_report.php' => 'تقرير المخزون',
                'manage_users.php'   => 'إدارة الموظفين',
                'sales.php'          => 'الكاشير (نقطة البيع)',
                'sales_process.php'  => 'معالجة المبيعات',
                'returns.php'        => 'المراجعات (المرتجعات)',
                'return_process.php' => 'معالجة المرتجعات',
                'add_purchase_invoice.php' => 'إضافة فاتورة شراء',
                'process_purchase_invoice.php' => 'معالجة فاتورة الشراء',
                'suppliers.php'      => 'الموردين',
                'price_compare.php'  => 'مقارنة الأسعار',
                'distribute_stock.php' => 'تحويل المخزون',
                'distribute_process.php' => 'معالجة تحويل المخزون',
                'export_branch_stock_pdf.php' => 'تصدير تقرير المخزون PDF',
                'export_sales_summary_pdf.php' => 'تصدير تقرير المبيعات PDF'
            ];

            foreach ($available_pages as $page => $title) {
                $can_access = in_array($page, $permissions) ? 1 : 0;
                mysqli_query($conn, "INSERT INTO user_permissions (user_id, page, can_access) VALUES ($new_user_id, '$page', $can_access)");
            }

            echo "<script>alert('✅ تم إضافة المستخدم والصلاحيات بنجاح');</script>";
        } else {
            echo "خطأ في الإضافة: " . mysqli_error($conn);
        }
    }
}

// ✅ جلب الفروع
$branches = mysqli_query($conn, "SELECT * FROM branches");

// ✅ جلب المستخدمين
$users = mysqli_query($conn, "SELECT users.*, branches.name AS branch_name FROM users LEFT JOIN branches ON users.branch_id = branches.id");

// ✅ قائمة الصفحات المتاحة للصلاحيات
$available_pages = [
    'dashboard.php'      => 'لوحة التحكم',
    'sales_report.php'   => 'تقرير المبيعات',
    'view_products.php'  => 'قائمة المنتجات',
    'add_products.php'   => 'إضافة منتج',
    'edit_product.php'   => 'تعديل منتج',
    'delete_product.php' => 'حذف منتج',
    'branch_stock_report.php' => 'تقرير المخزون',
    'manage_users.php'   => 'إدارة الموظفين',
    'sales.php'          => 'الكاشير (نقطة البيع)',
    'sales_process.php'  => 'معالجة المبيعات',
    'returns.php'        => 'المراجعات (المرتجعات)',
    'return_process.php' => 'معالجة المرتجعات',
    'add_purchase_invoice.php' => 'إضافة فاتورة شراء',
    'process_purchase_invoice.php' => 'معالجة فاتورة الشراء',
    'suppliers.php'      => 'الموردين',
    'price_compare.php'  => 'مقارنة الأسعار',
    'distribute_stock.php' => 'تحويل المخزون',
    'distribute_process.php' => 'معالجة تحويل المخزون',
    'export_branch_stock_pdf.php' => 'تصدير تقرير المخزون PDF',
    'export_sales_summary_pdf.php' => 'تصدير تقرير المبيعات PDF'
];
?>

<div class="container py-5">
    <h2 class="text-center mt-4 mb-4 ">إدارة الموظفين</h2>

    <form method="POST" class="mb-5" style="max-width: 800px; margin: auto;">
        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label>اسم المستخدم</label>
                    <input type="text" name="username" class="form-control" required>
                </div>

                <div class="form-group">
                    <label>كلمة المرور</label>
                    <input type="password" name="password" class="form-control" required>
                </div>

                <div class="form-group">
                    <label>الفرع</label>
                    <select name="branch_id" class="form-control" required>
                        <?php while ($branch = mysqli_fetch_assoc($branches)) { ?>
                            <option value="<?= $branch['id'] ?>"><?= $branch['name'] ?></option>
                        <?php } ?>
                    </select>
                </div>

                <div class="form-group">
                    <label>الدور</label>
                    <select name="role" class="form-control" required>
                        <option value="admin">مدير (Admin)</option>
                        <option value="manager">مدير فرع (Manager)</option>
                        <option value="cashier">كاشير (Cashier)</option>
                    </select>
                </div>

                <div class="form-group">
                    <label>الحالة</label>
                    <select name="status" class="form-control" required>
                        <option value="active">نشط</option>
                        <option value="inactive">موقوف</option>
                    </select>
                </div>
            </div>

            <div class="col-md-6">
                <h5 class="mb-3 text-primary">صلاحيات الوصول للصفحات:</h5>
                <div class="permissions-container" style="max-height: 300px; overflow-y: auto;">
                    <?php foreach ($available_pages as $page => $title): ?>
                        <div class="form-check mb-2">
                            <input type="checkbox" name="permissions[]" value="<?= $page ?>"
                                class="form-check-input" id="perm_<?= $page ?>">
                            <label class="form-check-label" for="perm_<?= $page ?>">
                                <?= $title ?>
                            </label>
                        </div>
                    <?php endforeach; ?>
                </div>
                <small class="text-muted">اختر الصفحات التي يستطيع الموظف الوصول إليها</small>
            </div>
        </div>

        <button type="submit" name="add_user" class="btn btn-success btn-block mt-3">إضافة مستخدم</button>
    </form>

    <h4 class="mb-3 text-center">قائمة المستخدمين</h4>
    <table class="table table-bordered text-center">
        <thead>
            <tr>
                <th>الاسم</th>
                <th>الفرع</th>
                <th>الدور</th>
                <th>الحالة</th>
                <th>الصلاحيات</th>
                <th>تعديل</th>
                <th>حذف</th>
            </tr>
        </thead>
        <tbody>
            <?php
            // Reset the branches result set
            $branches = mysqli_query($conn, "SELECT * FROM branches");
            $branches_array = [];
            while ($branch = mysqli_fetch_assoc($branches)) {
                $branches_array[$branch['id']] = $branch['name'];
            }

            while ($user = mysqli_fetch_assoc($users)) {
                // Get user permissions count
                $perm_query = mysqli_query($conn, "SELECT COUNT(*) as perm_count FROM user_permissions WHERE user_id = {$user['id']} AND can_access = 1");
                $perm_count = mysqli_fetch_assoc($perm_query)['perm_count'];
            ?>
                <tr>
                    <td><?= htmlspecialchars($user['username']) ?></td>
                    <td><?= htmlspecialchars($user['branch_name']) ?></td>
                    <td><?= htmlspecialchars($user['role']) ?></td>
                    <td><?= $user['status'] == 'active' ? 'نشط' : 'موقوف' ?></td>
                    <td>
                        <span class="badge badge-info"><?= $perm_count ?> صفحة</span>
                        <a href="edit_user.php?id=<?= $user['id'] ?>" class="btn btn-sm btn-outline-info ml-1">تفاصيل</a>
                    </td>
                    <td><a href="edit_user.php?id=<?= $user['id'] ?>" class="btn btn-sm btn-primary">تعديل</a></td>
                    <td><a href="manage_users.php?delete=<?= $user['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('هل أنت متأكد من الحذف؟')">حذف</a></td>
                </tr>
            <?php } ?>
        </tbody>
    </table>
</div>

<?php include 'footer.php'; ?>