<?php
// حماية الصلاحية للصفحة
include 'page_protect.php';

session_start();
include 'db_connection.php';
include 'header.php';

// Fetch branches for dropdowns
$branches = [];
$branch_q = mysqli_query($conn, "SELECT id, name FROM branches ORDER BY name");
while ($br = mysqli_fetch_assoc($branch_q)) {
    $branches[] = $br;
}
?>

<div class="container mt-5">
    <h3 class="mb-4">تحويل مخزون بين الفروع</h3>

    <!-- إشعارات -->
    <?php if (isset($_GET['success']) && $_GET['success'] == 1): ?>
        <div class="alert alert-success text-center fw-bold">
            تم تحويل المخزون بنجاح.
        </div>
    <?php elseif (isset($_GET['error'])): ?>
        <div class="alert alert-danger text-center fw-bold">
            ❌ <?php
                switch ($_GET['error']) {
                    case 'missing':
                        echo 'يرجى ملء جميع الحقول المطلوبة.';
                        break;
                    case 'insufficient':
                        echo 'الكمية غير كافية في المخزن المصدر.';
                        break;
                    case 'invalid':
                        echo 'بيانات غير صالحة.';
                        break;
                    default:
                        echo 'حدث خطأ غير متوقع.';
                        break;
                }
                ?>
        </div>
    <?php endif; ?>

    <form action="distribute_process.php" method="POST" onsubmit="return validateForm();">
        <div class="mb-3">
            <label for="product_input" class="form-label">اختر المنتج:</label>
            <input list="products_list" id="product_input" class="form-control" placeholder="ابحث أو اختر المنتج..." required>
            <datalist id="products_list">
                <?php
                $products_q = mysqli_query($conn, "SELECT id, name FROM products");
                while ($row = mysqli_fetch_assoc($products_q)) {
                    echo "<option value='" . htmlspecialchars($row['name']) . "' data-id='" . $row['id'] . "'></option>";
                }
                ?>
            </datalist>
            <input type="hidden" name="product_id" id="product_id_hidden" required>
        </div>

        <div class="row">
            <div class="col-md-6 mb-3">
                <label for="from_branch_id" class="form-label">من مخزن/فرع:</label>
                <select name="from_branch_id" id="from_branch_id" class="form-select" required>
                    <option value="">-- اختر المصدر --</option>
                    <option value="main">المخزن الرئيسي</option>
                    <?php foreach ($branches as $branch): ?>
                        <option value="<?= $branch['id'] ?>"><?= htmlspecialchars($branch['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-6 mb-3">
                <label for="to_branch_id" class="form-label">إلى مخزن/فرع:</label>
                <select name="to_branch_id" id="to_branch_id" class="form-select" required>
                    <option value="">-- اختر الوجهة --</option>
                    <option value="main">المخزن الرئيسي</option>
                    <?php foreach ($branches as $branch): ?>
                        <option value="<?= $branch['id'] ?>"><?= htmlspecialchars($branch['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>

        <div class="mb-3">
            <label for="quantity" class="form-label">الكمية المراد تحويلها:</label>
            <input type="number" name="quantity" class="form-control" min="1" required>
        </div>

        <button type="submit" class="btn btn-primary">تحويل المخزون</button>
    </form>
</div>

<?php include 'footer.php'; ?>

<script>
    function validateForm() {
        // Validate product selection
        var input = document.getElementById('product_input').value;
        var options = document.getElementById('products_list').options;
        var productFound = false;
        for (var i = 0; i < options.length; i++) {
            if (options[i].value === input) {
                document.getElementById('product_id_hidden').value = options[i].getAttribute('data-id');
                productFound = true;
                break;
            }
        }
        if (!productFound) {
            document.getElementById('product_id_hidden').value = '';
            alert('يرجى اختيار منتج صحيح من القائمة.');
            return false;
        }

        // Validate branches
        var fromBranch = document.getElementById('from_branch_id').value;
        var toBranch = document.getElementById('to_branch_id').value;

        if (fromBranch && toBranch && fromBranch === toBranch) {
            alert('لا يمكن تحويل المخزون إلى نفس الفرع المصدر.');
            return false;
        }

        return true;
    }
</script>