<?php
include 'db_connection.php';

if (!isset($_GET['id'])) {
    echo "معرف المستخدم غير موجود!";
    exit;
}

$id = intval($_GET['id']);

// جلب بيانات المستخدم
$query = "SELECT * FROM users WHERE id = $id";
$result = mysqli_query($conn, $query);
$user = mysqli_fetch_assoc($result);

// *** 1. جلب كل الصفحات الممكن التحكم فيها ***
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

// *** 2. جلب الصلاحيات الحالية ***
$permissions_query = mysqli_query($conn, "SELECT page FROM user_permissions WHERE user_id = $id AND can_access = 1");
$user_permissions = [];
while ($row = mysqli_fetch_assoc($permissions_query)) {
    $user_permissions[] = $row['page'];
}

// تعديل البيانات
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $branch_id = $_POST['branch_id'];
    $role = $_POST['role'];
    $status = $_POST['status'];
    $permissions = $_POST['permissions'] ?? [];

    // تعديل كلمة المرور فقط إذا تم إدخالها
    $update_password = "";
    if (!empty($password)) {
        $update_password = ", password = '$password'";
    }

    $update_query = "UPDATE users SET 
        username = '$username',
        branch_id = '$branch_id',
        role = '$role',
        status = '$status'
        $update_password
        WHERE id = $id";

    $success = true;

    if (mysqli_query($conn, $update_query)) {
        // *** 3. تحديث الصلاحيات ***
        mysqli_query($conn, "DELETE FROM user_permissions WHERE user_id = $id");
        foreach ($available_pages as $page => $page_title) {
            $can_access = in_array($page, $permissions) ? 1 : 0;
            mysqli_query($conn, "INSERT INTO user_permissions (user_id, page, can_access) VALUES ($id, '$page', $can_access)");
        }

        echo "<script>alert('تم تعديل بيانات المستخدم والصلاحيات بنجاح'); window.location.href='manage_users.php';</script>";
        exit;
    } else {
        echo "حدث خطأ أثناء التعديل: " . mysqli_error($conn);
        $success = false;
    }
}
?>

<?php include 'header.php'; ?>

<div class="container mt-5" style="max-width: 700px;">
    <h3 class="mb-4 text-center">تعديل بيانات الموظف</h3>
    <form method="POST">
        <div class="form-group">
            <label>اسم المستخدم:</label>
            <input type="text" name="username" class="form-control" value="<?= $user['username'] ?>" required>
        </div>

        <div class="form-group">
            <label>كلمة المرور (اتركها فارغة إن لم ترغب بتغييرها):</label>
            <input type="password" name="password" class="form-control">
        </div>

        <div class="form-group">
            <label>الفرع:</label>
            <select name="branch_id" class="form-control" required>
                <?php
                $branches = mysqli_query($conn, "SELECT * FROM branches");
                while ($branch = mysqli_fetch_assoc($branches)) {
                    $selected = ($user['branch_id'] == $branch['id']) ? "selected" : "";
                    echo "<option value='{$branch['id']}' $selected>{$branch['name']}</option>";
                }
                ?>
            </select>
        </div>

        <div class="form-group">
            <label>الدور:</label>
            <select name="role" class="form-control" required>
                <option value="admin" <?= $user['role'] == 'admin' ? 'selected' : '' ?>>مدير</option>
                <option value="manager" <?= $user['role'] == 'manager' ? 'selected' : '' ?>>مشرف</option>
                <option value="cashier" <?= $user['role'] == 'cashier' ? 'selected' : '' ?>>كاشير</option>
            </select>
        </div>

        <div class="form-group">
            <label>الحالة:</label>
            <select name="status" class="form-control" required>
                <option value="active" <?= $user['status'] == 'active' ? 'selected' : '' ?>>نشط</option>
                <option value="inactive" <?= $user['status'] == 'inactive' ? 'selected' : '' ?>>موقوف</option>
            </select>
        </div>

        <!-- ***** الصلاحيات ***** -->
        <div class="mt-4 mb-3">
            <h5 class="mb-2 text-primary">صلاحيات الوصول للصفحات:</h5>
            <div class="row">
                <?php foreach ($available_pages as $page => $title): ?>
                    <div class="col-6 col-md-4 mb-2">
                        <label>
                            <input type="checkbox" name="permissions[]" value="<?= $page ?>"
                                <?= in_array($page, $user_permissions) ? 'checked' : '' ?>>
                            <?= $title ?>
                        </label>
                    </div>
                <?php endforeach; ?>
            </div>
            <small class="text-muted">اختر الصفحات التي يستطيع الموظف الوصول إليها فقط.</small>
        </div>

        <button type="submit" class="btn btn-primary btn-block mt-3">حفظ التعديلات</button>
    </form>
</div>

<?php include 'footer.php'; ?>