<?php
// حماية الصفحة بالصلاحية الموحدة
include 'page_protect.php';
include 'header.php';
?>

<div class="container mt-5 mb-5"> <!-- زودت المسافة السفلية -->
    <h2 class="text-center mb-5">لوحة التحكم</h2>

    <div class="row justify-content-center g-4">
        <!-- زر فاتورة الشراء -->
        <div class="col-md-4">
            <a href="add_purchase_invoice.php" class="btn btn-warning w-100 py-4 fs-5">
                🧾 فاتورة شراء (إدخال للمخزن الرئيسي)
            </a>
        </div>
        <div class="col-md-4">
            <a href="add_products.php" class="btn btn-success w-100 py-4 fs-5">➕ إضافة منتج</a>
        </div>
        <div class="col-md-4">
            <a href="sales.php" class="btn btn-info w-100 py-4 fs-5">🧾 الكاشير (نقطة البيع)</a>
        </div>
        <div class="col-md-4">
            <a href="sales_report.php" class="btn btn-warning w-100 py-4 fs-5">📊 تقرير المبيعات</a>
        </div>
        <div class="col-md-4">
            <a href="view_products.php" class="btn btn-primary w-100 py-4 fs-5">📁 عرض المنتجات</a>
        </div>

        <div class="col-md-4">
            <a href="suppliers.php" class="btn btn-primary w-100 py-4 fs-5">🏬 إدارة الموردين</a>
        </div>

        <!-- زر مقارنة الأسعار -->
        <div class="col-md-4">
            <a href="price_compare.php" class="btn btn-success w-100 py-4 fs-5">
                🏷️ مقارنة أسعار الموردين
            </a>
        </div>

        <div class="col-md-4">
            <a href="branch_stock_report.php" class="btn btn-primary w-100 py-4 fs-5">📁 عرض مخزون الفروع</a>
        </div>
        <div class="col-md-4">
            <a href="distribute_stock.php" class="btn btn-dark w-100 py-4 fs-5">🚩 توزيع المخزون على الفروع</a>
        </div>
        <div class="col-md-4">
            <a href="returns.php" class="btn btn-danger w-100 py-4 fs-5">📥 إدارة المرتجعات</a>
        </div>
        <div class="col-md-4">
            <a href="add_branch.php" class="btn btn-secondary w-100 py-4 fs-5">🏬 إضافة فرع</a>
        </div>
        <div class="col-md-4">
            <a href="manage_users.php" class="btn btn-secondary w-100 py-4 fs-5">👥 إدارة الموظفين</a>
        </div>

    </div>

    <!-- مسافة إضافية تحت الزرائر -->
    <div style="height: 100px;"></div>
</div>

<?php include 'footer.php'; ?>