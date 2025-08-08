<?php
// حماية الصلاحية للصفحة
include 'page_protect.php';

include 'db_connection.php';
include 'header.php';

// ✅ تحديث الصلاحيات
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["update_permissions"])) {
    $user_id = (int)$_POST["user_id"];
    $permissions = $_POST["permissions"] ?? [];

    // حذف الصلاحيات الحالية
    mysqli_query($conn, "DELETE FROM user_permissions WHERE user_id = $user_id");

    // إضافة الصلاحيات الجديدة
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

    foreach ($available_pages as $page => $title) {
        $can_access = in_array($page, $permissions) ? 1 : 0;
        mysqli_query($conn, "INSERT INTO user_permissions (user_id, page, can_access) VALUES ($user_id, '$page', $can_access)");
    }

    echo "<script>alert('✅ تم تحديث الصلاحيات بنجاح');</script>";
}

// ✅ جلب المستخدمين
$users = mysqli_query($conn, "SELECT users.*, branches.name AS branch_name FROM users LEFT JOIN branches ON users.branch_id = branches.id");

// ✅ قائمة الصفحات المتاحة
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

// ✅ جلب صلاحيات مستخدم محدد
$selected_user_permissions = [];
if (isset($_GET['user_id'])) {
    $user_id = (int)$_GET['user_id'];
    $perm_query = mysqli_query($conn, "SELECT page FROM user_permissions WHERE user_id = $user_id AND can_access = 1");
    while ($row = mysqli_fetch_assoc($perm_query)) {
        $selected_user_permissions[] = $row['page'];
    }
}
?>

<div class="container">
    <h2 class="text-center mt-4 mb-4">إدارة صلاحيات الموظفين</h2>

    <div class="row">
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">اختر الموظف</h5>
                </div>
                <div class="card-body">
                    <form method="GET">
                        <div class="form-group">
                            <select name="user_id" class="form-control" onchange="this.form.submit()">
                                <option value="">اختر موظف...</option>
                                <?php
                                $users_display = mysqli_query($conn, "SELECT users.*, branches.name AS branch_name FROM users LEFT JOIN branches ON users.branch_id = branches.id");
                                while ($user = mysqli_fetch_assoc($users_display)) {
                                    $selected = (isset($_GET['user_id']) && $_GET['user_id'] == $user['id']) ? 'selected' : '';
                                ?>
                                    <option value="<?= $user['id'] ?>" <?= $selected ?>>
                                        <?= htmlspecialchars($user['username']) ?> - <?= htmlspecialchars($user['branch_name']) ?>
                                    </option>
                                <?php } ?>
                            </select>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-8">
            <?php if (isset($_GET['user_id']) && !empty($_GET['user_id'])): ?>
                <?php
                $selected_user_id = (int)$_GET['user_id'];
                $user_query = mysqli_query($conn, "SELECT users.*, branches.name AS branch_name FROM users LEFT JOIN branches ON users.branch_id = branches.id WHERE users.id = $selected_user_id");
                $selected_user = mysqli_fetch_assoc($user_query);
                ?>

                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">صلاحيات: <?= htmlspecialchars($selected_user['username']) ?></h5>
                        <small class="text-muted">الفرع: <?= htmlspecialchars($selected_user['branch_name']) ?> | الدور: <?= htmlspecialchars($selected_user['role']) ?></small>
                    </div>
                    <div class="card-body">
                        <form method="POST">
                            <input type="hidden" name="user_id" value="<?= $selected_user_id ?>">

                            <div class="row">
                                <?php foreach ($available_pages as $page => $title): ?>
                                    <div class="col-md-6 mb-2">
                                        <div class="form-check">
                                            <input type="checkbox" name="permissions[]" value="<?= $page ?>"
                                                class="form-check-input" id="perm_<?= $page ?>"
                                                <?= in_array($page, $selected_user_permissions) ? 'checked' : '' ?>>
                                            <label class="form-check-label" for="perm_<?= $page ?>">
                                                <?= $title ?>
                                            </label>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>

                            <button type="submit" name="update_permissions" class="btn btn-primary btn-block mt-3">
                                حفظ الصلاحيات
                            </button>
                        </form>
                    </div>
                </div>
            <?php else: ?>
                <div class="card">
                    <div class="card-body text-center">
                        <h5 class="text-muted">اختر موظف من القائمة لعرض وتعديل صلاحياته</h5>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <div class="mt-4">
        <h4>ملخص الصلاحيات</h4>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>الموظف</th>
                    <th>الفرع</th>
                    <th>الدور</th>
                    <th>عدد الصفحات المسموح بها</th>
                    <th>إجراءات</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $users_summary = mysqli_query($conn, "SELECT users.*, branches.name AS branch_name FROM users LEFT JOIN branches ON users.branch_id = branches.id");
                while ($user = mysqli_fetch_assoc($users_summary)) {
                    $perm_count_query = mysqli_query($conn, "SELECT COUNT(*) as count FROM user_permissions WHERE user_id = {$user['id']} AND can_access = 1");
                    $perm_count = mysqli_fetch_assoc($perm_count_query)['count'];
                ?>
                    <tr>
                        <td><?= htmlspecialchars($user['username']) ?></td>
                        <td><?= htmlspecialchars($user['branch_name']) ?></td>
                        <td><?= htmlspecialchars($user['role']) ?></td>
                        <td>
                            <span class="badge badge-info"><?= $perm_count ?> صفحة</span>
                        </td>
                        <td>
                            <a href="manage_permissions.php?user_id=<?= $user['id'] ?>" class="btn btn-sm btn-primary">تعديل الصلاحيات</a>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
</div>

<?php include 'footer.php'; ?>