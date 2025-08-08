<?php
// حماية الصلاحية للصفحة
include 'page_protect.php';

session_start();
include 'db_connection.php';
include 'header.php';

// جلب المنتجات
$products = mysqli_query($conn, "SELECT id, name FROM products");

// جلب الفروع
$branches = mysqli_query($conn, "SELECT id, name FROM branches");
?>

<div class="container mt-5">
    <h3>إرجاع منتج</h3>
    <form method="POST" action="return_process.php">
        <div class="mb-3">
            <label>اختر المنتج:</label>
            <select name="product_id" class="form-control" required>
                <option value="">-- اختر المنتج --</option>
                <?php while ($p = mysqli_fetch_assoc($products)): ?>
                    <option value="<?php echo $p['id']; ?>"><?php echo $p['name']; ?></option>
                <?php endwhile; ?>
            </select>
        </div>

        <div class="mb-3">
            <label>اختر الفرع:</label>
            <select name="branch_id" class="form-control" required>
                <option value="">-- اختر الفرع --</option>
                <?php while ($b = mysqli_fetch_assoc($branches)): ?>
                    <option value="<?php echo $b['id']; ?>"><?php echo $b['name']; ?></option>
                <?php endwhile; ?>
            </select>
        </div>

        <div class="mb-3">
            <label>الكمية المراد إرجاعها:</label>
            <input type="number" name="quantity" min="1" required class="form-control">
        </div>

        <div class="mb-3">
            <label>سبب المرتجع (اختياري):</label>
            <textarea name="reason" class="form-control"></textarea>
        </div>

        <button type="submit" class="btn btn-danger">تنفيذ المرتجع</button>
    </form>
</div>

<?php include 'footer.php'; ?>