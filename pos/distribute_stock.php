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

// جلب المنتجات
$products = [];
$prod_query = mysqli_query($conn, "SELECT id, name FROM products");
while ($prod = mysqli_fetch_assoc($prod_query)) {
    $products[] = $prod;
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
                    case 'no_products':
                        echo 'يجب إضافة منتج واحد على الأقل للتحويل.';
                        break;
                    default:
                        echo 'حدث خطأ غير متوقع.';
                        break;
                }
                ?>
        </div>
    <?php endif; ?>

    <form id="transferForm" action="distribute_process.php" method="POST">
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

        <div class="row g-3 align-items-end mb-4">
            <!-- اختيار المنتج مع بحث -->
            <div class="col-md-8">
                <label class="form-label">اختر المنتج:</label>
                <input list="products_list" id="product_input" class="form-control" placeholder="ابحث أو اختر المنتج..." autocomplete="off">
                <datalist id="products_list">
                    <?php foreach ($products as $prod): ?>
                        <option value="<?= htmlspecialchars($prod['name']) ?>" data-id="<?= $prod['id'] ?>"></option>
                    <?php endforeach; ?>
                </datalist>
                <input type="hidden" id="product_id_hidden">
            </div>

            <div class="col-md-3">
                <label class="form-label">الكمية:</label>
                <input type="number" id="quantity_input" class="form-control" min="1">
            </div>

            <div class="col-md-1 d-grid">
                <button type="button" onclick="addProductRow()" class="btn btn-success">➕</button>
            </div>
        </div>

        <div class="mt-4">
            <h6>المنتجات المراد تحويلها:</h6>
            <div class="table-responsive">
                <table class="table table-bordered text-center align-middle" id="transferTable">
                    <thead>
                        <tr>
                            <th>المنتج</th>
                            <th>الكمية</th>
                            <th>حذف</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- الصفوف ستضاف ديناميكياً -->
                    </tbody>
                </table>
            </div>
        </div>

        <input type="hidden" name="products_data" id="products_data">

        <button type="submit" class="btn btn-primary">تحويل المخزون</button>
    </form>
</div>

<?php include 'footer.php'; ?>

<script>
    let transferItems = [];

    document.getElementById('product_input').addEventListener('input', function() {
        let inputValue = this.value;
        let options = document.getElementById('products_list').options;
        let foundId = '';
        for (let i = 0; i < options.length; i++) {
            if (options[i].value === inputValue) {
                foundId = options[i].getAttribute('data-id');
                break;
            }
        }
        document.getElementById('product_id_hidden').value = foundId;
    });

    function addProductRow() {
        let prodName = document.getElementById('product_input').value.trim();
        let qty = parseInt(document.getElementById('quantity_input').value);
        let productId = document.getElementById('product_id_hidden').value;

        if (!prodName || !qty) {
            alert("يرجى ملء جميع الحقول للمنتج!");
            return;
        }

        // التحقق من أن المنتج موجود في القائمة
        if (!productId) {
            alert('يرجى اختيار منتج صحيح من القائمة.');
            return;
        }

        // لو المنتج مكرر؟ زود الكمية فقط
        let found = transferItems.findIndex(item => item.id === productId);
        if (found > -1) {
            transferItems[found].qty += qty;
        } else {
            transferItems.push({
                id: productId,
                name: prodName,
                qty: qty
            });
        }

        renderTransferTable();
        // امسح الحقول
        document.getElementById('product_input').value = '';
        document.getElementById('quantity_input').value = '';
    }

    function removeRow(idx) {
        transferItems.splice(idx, 1);
        renderTransferTable();
    }

    function renderTransferTable() {
        let tbody = document.querySelector('#transferTable tbody');
        tbody.innerHTML = '';

        transferItems.forEach((item, idx) => {
            let row = document.createElement('tr');
            row.innerHTML = `
                <td>${item.name}</td>
                <td>${item.qty}</td>
                <td><button type="button" onclick="removeRow(${idx})" class="btn btn-sm btn-danger">🗑️</button></td>
            `;
            tbody.appendChild(row);
        });
    }

    // قبل إرسال الفورم: التحقق من صحة البيانات
    document.getElementById('transferForm').onsubmit = function() {
        // التحقق من الفروع
        var fromBranch = document.getElementById('from_branch_id').value;
        var toBranch = document.getElementById('to_branch_id').value;

        if (!fromBranch || !toBranch) {
            alert('يرجى اختيار الفرع المصدر والوجهة.');
            return false;
        }

        if (fromBranch === toBranch) {
            alert('لا يمكن تحويل المخزون إلى نفس الفرع المصدر.');
            return false;
        }

        if (transferItems.length === 0) {
            alert('يجب إضافة منتج واحد على الأقل للتحويل.');
            return false;
        }

        document.getElementById('products_data').value = JSON.stringify(transferItems);
        return true;
    }
</script>