<?php
include 'page_protect.php';
include 'header.php';
include 'db_connection.php';

// جلب كل المنتجات مع الموردين
$query = "
    SELECT 
        p.name AS product_name,
        s.name AS supplier_name,
        pi.unit_price AS purchase_price
    FROM purchase_items pi
    JOIN purchases pu ON pi.purchase_id = pu.id
    JOIN suppliers s ON pu.supplier_id = s.id
    JOIN products p ON pi.product_id = p.id
    ORDER BY p.name ASC, pi.unit_price ASC
";
$result = mysqli_query($conn, $query);

// إعادة تنظيم النتائج: كل منتج => array من الموردين
$products = [];
while ($row = mysqli_fetch_assoc($result)) {
    $products[$row['product_name']][] = [
        'supplier' => $row['supplier_name'],
        'price' => floatval($row['purchase_price'])
    ];
}
?>

<div class="container my-5">
    <div class="card shadow-sm">
        <div class="card-header bg-info text-white text-center py-3">
            <h4 class="mb-0">🔎 مقارنة أسعار الموردين لكل منتج</h4>
        </div>

        <div class="card-body">
            <!-- مربع البحث -->
            <input type="text" id="searchInput" class="form-control mb-3" placeholder="ابحث باسم المنتج أو المورد..." onkeyup="filterTable()">

            <div class="table-responsive">
                <table class="table table-bordered text-center align-middle" id="compareTable">
                    <thead class="table-secondary">
                        <tr>
                            <th>اسم المنتج</th>
                            <th>المورد</th>
                            <th>سعر الشراء</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($products as $product_name => $suppliers):
                            // هات أقل سعر شراء للمنتج ده
                            $min_price = min(array_column($suppliers, 'price'));
                            $rowspan = count($suppliers);
                            foreach ($suppliers as $i => $row): ?>
                                <tr>
                                    <?php if ($i === 0): ?>
                                        <td rowspan="<?= $rowspan; ?>" style="vertical-align: middle;"><?= htmlspecialchars($product_name); ?></td>
                                    <?php endif; ?>
                                    <td><?= htmlspecialchars($row['supplier']); ?></td>
                                    <td>
                                        <?php if ($row['price'] == $min_price): ?>
                                            <span class="badge bg-success" style="font-size: 1rem;">
                                                <?= number_format($row['price'], 2); ?> ج.م ✔️
                                            </span>
                                        <?php else: ?>
                                            <span class="badge bg-secondary" style="font-size: 1rem;">
                                                <?= number_format($row['price'], 2); ?> ج.م
                                            </span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
    function filterTable() {
        var input = document.getElementById("searchInput").value.toLowerCase();
        var rows = document.querySelectorAll("#compareTable tbody tr");

        rows.forEach(function(row) {
            var txt = row.textContent.toLowerCase();
            row.style.display = txt.includes(input) ? '' : 'none';
        });
    }
</script>

<?php include 'footer.php'; ?>