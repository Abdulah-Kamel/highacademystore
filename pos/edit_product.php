<?php
// حماية الصلاحية للصفحة
include 'page_protect.php';

include 'header.php';
include 'db_connection.php';

// جلب بيانات المنتج الحالي
$id = intval($_GET['id'] ?? 0);
$product = null;

if ($id > 0) {
    $stmt = $conn->prepare("SELECT * FROM products WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $product = $result->fetch_assoc();
    $stmt->close();
}

$success = false;
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name            = $_POST['product_name'];
    $purchase_price  = $_POST['purchase_price'];
    $sale_price      = $_POST['sale_price'];
    $stock           = $_POST['stock'];
    $min_stock_main  = $_POST['min_stock_main'];
    $min_stock_shebin = $_POST['min_stock_shebin'];
    $min_stock_qwisna = $_POST['min_stock_qwisna'];

    // صورة جديدة؟
    $image_name = $product['image'];
    if (isset($_FILES['product_image']) && $_FILES['product_image']['error'] === 0) {
        $image_tmp = $_FILES['product_image']['tmp_name'];
        $image_name = time() . '_' . basename($_FILES['product_image']['name']);
        move_uploaded_file($image_tmp, 'uploads/' . $image_name);
    }

    // تحديث البيانات
    $discount_type = $_POST['discount_type'] ?? '';
    $discount_value = floatval($_POST['discount_value'] ?? 0);

    $stmt = $conn->prepare("UPDATE products SET name=?, purchase_price=?, sale_price=?, stock=?, min_stock_main=?, min_stock_shebin=?, min_stock_qwisna=?, image=?, discount_type=?, discount_value=? WHERE id=?");
    $stmt->bind_param("sddiiiissdi", $name, $purchase_price, $sale_price, $stock, $min_stock_main, $min_stock_shebin, $min_stock_qwisna, $image_name, $discount_type, $discount_value, $id);

    if ($stmt->execute()) {
        $success = true;
        // جلب البيانات من جديد
        $result = $conn->query("SELECT * FROM products WHERE id = $id");
        $product = $result->fetch_assoc();
    } else {
        $error = "حدث خطأ أثناء التحديث: " . $stmt->error;
    }
    $stmt->close();
}
?>

<div class="container my-5">
    <div class="row justify-content-center">
        <div class="col-md-10 col-lg-8">
            <?php if ($success): ?>
                <div class="alert alert-success text-center">✅ تم تحديث المنتج بنجاح!</div>
            <?php elseif ($error): ?>
                <div class="alert alert-danger text-center"><?= $error ?></div>
            <?php endif; ?>

            <div class="card shadow border-0">
                <div class="card-header bg-primary text-white text-center py-3">
                    <h5 class="mb-0">✏️ تعديل بيانات المنتج</h5>
                </div>
                <div class="card-body">
                    <?php if ($product): ?>
                        <form method="POST" enctype="multipart/form-data">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label">اسم المنتج</label>
                                    <input type="text" name="product_name" class="form-control" required value="<?= htmlspecialchars($product['name']) ?>">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">سعر الشراء</label>
                                    <input type="number" name="purchase_price" class="form-control" step="0.01" required value="<?= $product['purchase_price'] ?>">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">سعر البيع</label>
                                    <input type="number" name="sale_price" class="form-control" step="0.01" required value="<?= $product['sale_price'] ?>">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">الكمية</label>
                                    <input type="number" name="stock" class="form-control" required value="<?= $product['stock'] ?>">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">الحد الأدنى للمخزون (المخزن الرئيسي)</label>
                                    <input type="number" name="min_stock_main" class="form-control" value="<?= $product['min_stock_main'] ?>">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">الحد الأدنى للمخزون (شبين الكوم)</label>
                                    <input type="number" name="min_stock_shebin" class="form-control" value="<?= $product['min_stock_shebin'] ?>">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">الحد الأدنى للمخزون (قويسنا)</label>
                                    <input type="number" name="min_stock_qwisna" class="form-control" value="<?= $product['min_stock_qwisna'] ?>">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">نوع الخصم</label>
                                    <select name="discount_type" class="form-control">
                                        <option value="" <?= ($product['discount_type'] == '') ? 'selected' : '' ?>>بدون خصم</option>
                                        <option value="percent" <?= ($product['discount_type'] == 'percent') ? 'selected' : '' ?>>نسبة مئوية (%)</option>
                                        <option value="amount" <?= ($product['discount_type'] == 'amount') ? 'selected' : '' ?>>قيمة ثابتة</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">قيمة الخصم</label>
                                    <input type="number" step="0.01" name="discount_value" class="form-control" value="<?= htmlspecialchars($product['discount_value']) ?>">
                                </div>
                                <div class="col-12">
                                    <label class="form-label">صورة المنتج</label>
                                    <input type="file" name="product_image" class="form-control">
                                    <?php if (!empty($product['image'])): ?>
                                        <img src="uploads/<?= $product['image']; ?>" class="img-thumbnail mt-2" style="width:80px;">
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="d-grid mt-4">
                                <button type="submit" class="btn btn-success btn-lg">💾 حفظ التعديلات</button>
                            </div>
                        </form>
                    <?php else: ?>
                        <div class="alert alert-danger">لم يتم العثور على المنتج المطلوب.</div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>