<?php
include 'page_protect.php'; // ✅ حماية الصفحة
include 'header.php';
include 'db_connection.php';

$success = false;
$error = '';

// جلب كل الفروع (للاختيار)
$branches = [];
$res = $conn->query("SELECT id, name FROM branches");
while ($row = $res->fetch_assoc()) $branches[] = $row;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name            = $_POST['product_name'];
    $purchase_price  = $_POST['purchase_price'];
    $sale_price      = $_POST['sale_price'];
    $stock           = $_POST['stock'];
    $min_stock_main  = $_POST['min_stock_main'];
    $min_stock_shebin = $_POST['min_stock_shebin'];
    $min_stock_qwisna = $_POST['min_stock_qwisna'];
    $branch_id       = isset($_POST['branch_id']) ? intval($_POST['branch_id']) : 0; // 0 = مخزن رئيسي
    $discount_type   = $_POST['discount_type'] ?? '';
    $discount_value  = floatval($_POST['discount_value'] ?? 0);

    // Check for duplicate product by name only
    $check_stmt = $conn->prepare("SELECT id FROM products WHERE name = ? LIMIT 1");
    $check_stmt->bind_param("s", $name);
    $check_stmt->execute();
    $check_stmt->store_result();
    if ($check_stmt->num_rows > 0) {
        $error = '⚠️ هذا المنتج موجود بالفعل!';
        $check_stmt->close();
    } else {
        $check_stmt->close();
        // صورة المنتج
        $image_name = '';
        if (isset($_FILES['product_image']) && $_FILES['product_image']['error'] === 0) {
            $image_tmp = $_FILES['product_image']['tmp_name'];
            $image_name = time() . '_' . basename($_FILES['product_image']['name']);
            move_uploaded_file($image_tmp, 'uploads/' . $image_name);
        }

        // Determine stock allocation based on selected branch
        $main_stock = ($branch_id == 0) ? $stock : 0; // Only add to main if main branch is selected
        
        // Always use the same order and types as in edit
        $stmt = $conn->prepare("INSERT INTO products 
            (name, purchase_price, sale_price, stock, min_stock_main, min_stock_shebin, min_stock_qwisna, image, discount_type, discount_value) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param(
            "sddiiiissd",
            $name,
            $purchase_price,
            $sale_price,
            $main_stock, // Use calculated main stock
            $min_stock_main,
            $min_stock_shebin,
            $min_stock_qwisna,
            $image_name,
            $discount_type,
            $discount_value
        );
        error_log('Discount Type: ' . $discount_type);
        error_log('Discount Value: ' . $discount_value);
        if ($stmt->execute()) {
            $product_id = $conn->insert_id;
            // If a specific branch is selected (not main), add stock to product_stock
            if ($branch_id != 0) {
                $add_stock = $conn->prepare("INSERT INTO product_stock (product_id, branch_id, quantity) VALUES (?, ?, ?)");
                $add_stock->bind_param("iii", $product_id, $branch_id, $stock);
                $add_stock->execute();
                $add_stock->close();
            }
            $success = true;
        } else {
            $error = 'حدث خطأ أثناء حفظ المنتج: ' . $stmt->error;
        }
        $stmt->close();
    }
}
?>

<div class="container my-5">
    <div class="row justify-content-center">
        <div class="col-md-10 col-lg-8">
            <?php if ($success): ?>
                <div class="alert alert-success text-center">✅ تم إضافة المنتج بنجاح!</div>
            <?php elseif ($error): ?>
                <div class="alert alert-danger text-center"><?= $error ?></div>
            <?php endif; ?>

            <div class="card shadow border-0">
                <div class="card-header bg-primary text-white text-center py-3">
                    <h5 class="mb-0">🆕 إضافة منتج جديد</h5>
                </div>

                <div class="card-body">
                    <form method="POST" enctype="multipart/form-data">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">اسم المنتج</label>
                                <input type="text" name="product_name" class="form-control" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">سعر الشراء</label>
                                <input type="number" name="purchase_price" class="form-control" step="0.01" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">سعر البيع</label>
                                <input type="number" name="sale_price" class="form-control" step="0.01" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">الكمية</label>
                                <input type="number" name="stock" class="form-control" required>
                            </div>
                            <!-- اختيار الفرع -->
                            <div class="col-md-4">
                                <label class="form-label">الفرع (اختياري)</label>
                                <select name="branch_id" class="form-control">
                                    <option value="0">المخزن الرئيسي</option>
                                    <?php foreach ($branches as $branch): ?>
                                        <option value="<?= $branch['id'] ?>"><?= $branch['name'] ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">الحد الأدنى للمخزون (رئيسي)</label>
                                <input type="number" name="min_stock_main" class="form-control" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">الحد الأدنى للمخزون في شبين الكوم</label>
                                <input type="number" name="min_stock_shebin" class="form-control" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">الحد الأدنى للمخزون في قويسنا</label>
                                <input type="number" name="min_stock_qwisna" class="form-control" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">نوع الخصم</label>
                                <select name="discount_type" class="form-control">
                                    <option value="">بدون خصم</option>
                                    <option value="percent">نسبة مئوية (%)</option>
                                    <option value="amount">قيمة ثابتة</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">قيمة الخصم</label>
                                <input type="number" step="0.01" name="discount_value" class="form-control" value="0">
                            </div>
                            <div class="col-12">
                                <label class="form-label">صورة المنتج</label>
                                <input type="file" name="product_image" class="form-control">
                            </div>
                        </div>
                        <div class="d-grid mt-4">
                            <button type="submit" class="btn btn-success btn-lg">➕ إضافة المنتج</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>