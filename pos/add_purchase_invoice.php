<?php
include 'page_protect.php'; // ✅ حماية الصفحة
include 'db_connection.php';
include 'header.php';

// جلب الفروع
$branches = [];
$branch_q = mysqli_query($conn, "SELECT id, name FROM branches ORDER BY name");
while ($br = mysqli_fetch_assoc($branch_q)) {
    $branches[] = $br;
}

// جلب الموردين
$suppliers = [];
$sup_query = mysqli_query($conn, "SELECT id, name FROM suppliers ORDER BY name");
while ($sup = mysqli_fetch_assoc($sup_query)) {
    $suppliers[] = $sup;
}

// جلب المنتجات
$products = [];
$prod_query = mysqli_query($conn, "SELECT id, name FROM products");
while ($prod = mysqli_fetch_assoc($prod_query)) {
    $products[] = $prod;
}
?>

<div class="container my-5">
    <h4 class="mb-4 text-center">📝 فاتورة شراء (إضافة منتجات لأي مخزن/فرع)</h4>

    <!-- نموذج الفاتورة -->
    <form id="invoiceForm" method="POST" action="process_purchase_invoice.php">
        <div class="row g-3 align-items-end">

            <!-- اختيار الفرع -->
            <div class="col-md-4">
                <label class="form-label">اختر الفرع أو المخزن الرئيسي</label>
                <select name="branch_id" class="form-control" required>
                    <option value="main">المخزن الرئيسي</option>
                    <?php foreach ($branches as $branch): ?>
                        <option value="<?= $branch['id'] ?>"><?= htmlspecialchars($branch['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- اختيار المورد مع بحث -->
            <div class="col-md-4">
                <label class="form-label">المورد</label>
                <input list="suppliers_list" id="supplier_input" class="form-control" placeholder="ابحث أو اختر المورد" autocomplete="off">
                <datalist id="suppliers_list">
                    <?php foreach ($suppliers as $sup): ?>
                        <option value="<?= htmlspecialchars($sup['name']) ?>" data-id="<?= $sup['id'] ?>"></option>
                    <?php endforeach; ?>
                </datalist>
            </div>

            <!-- اختيار المنتج مع بحث -->
            <div class="col-md-3">
                <label class="form-label">المنتج</label>
                <input list="products_list" id="product_select" class="form-control" placeholder="ابحث أو اختر المنتج" autocomplete="off">
                <datalist id="products_list">
                    <?php foreach ($products as $prod): ?>
                        <option value="<?= htmlspecialchars($prod['name']) ?>"></option>
                    <?php endforeach; ?>
                </datalist>
            </div>

            <div class="col-md-2">
                <label class="form-label">الكمية</label>
                <input type="number" id="qty_input" class="form-control" min="1">
            </div>
            <div class="col-md-2">
                <label class="form-label">سعر الشراء</label>
                <input type="number" id="price_input" class="form-control" min="0.01" step="0.01">
            </div>
            <div class="col-md-1 d-grid">
                <button type="button" onclick="addProductRow()" class="btn btn-success mt-2">➕</button>
            </div>
        </div>

        <div class="mt-4">
            <h6>المنتجات المضافة للفاتورة:</h6>
            <div class="table-responsive">
                <table class="table table-bordered text-center align-middle" id="invoiceTable">
                    <thead>
                        <tr>
                            <th>المنتج</th>
                            <th>الكمية</th>
                            <th>سعر الشراء</th>
                            <th>الإجمالي</th>
                            <th>حذف</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- الصفوف ستضاف ديناميكياً -->
                    </tbody>
                </table>
            </div>
        </div>

        <!-- هنعرف الـ supplier_id هنا -->
        <input type="hidden" name="supplier_id" id="supplier_id_hidden">
        <input type="hidden" name="products_data" id="products_data">

        <div class="d-grid mt-4">
            <button type="submit" class="btn btn-primary btn-lg">💾 حفظ الفاتورة وإضافة للمخزن</button>
        </div>
    </form>
</div>

<script>
    let invoiceItems = [];

    function addProductRow() {
        let prodName = document.getElementById('product_select').value.trim();
        let qty = parseInt(document.getElementById('qty_input').value);
        let price = parseFloat(document.getElementById('price_input').value);

        if (!prodName || !qty || !price) {
            alert("يرجى ملء جميع الحقول للمنتج!");
            return;
        }

        // لو المنتج مكرر؟ زود الكمية فقط
        let found = invoiceItems.findIndex(item => item.name === prodName);
        if (found > -1) {
            invoiceItems[found].qty += qty;
            invoiceItems[found].price = price; // لو السعر اتغير
        } else {
            invoiceItems.push({
                name: prodName,
                qty: qty,
                price: price
            });
        }

        renderInvoiceTable();
        // امسح الحقول
        document.getElementById('product_select').value = '';
        document.getElementById('qty_input').value = '';
        document.getElementById('price_input').value = '';
    }

    function removeRow(idx) {
        invoiceItems.splice(idx, 1);
        renderInvoiceTable();
    }

    function renderInvoiceTable() {
        let tbody = document.querySelector('#invoiceTable tbody');
        tbody.innerHTML = '';
        let total = 0;
        invoiceItems.forEach((item, idx) => {
            let row = document.createElement('tr');
            row.innerHTML = `
                <td>${item.name}</td>
                <td>${item.qty}</td>
                <td>${item.price.toFixed(2)}</td>
                <td>${(item.qty * item.price).toFixed(2)}</td>
                <td><button type="button" onclick="removeRow(${idx})" class="btn btn-sm btn-danger">🗑️</button></td>
            `;
            tbody.appendChild(row);
            total += item.qty * item.price;
        });

        // الإجمالي الكلي تحت الجدول
        if (invoiceItems.length > 0) {
            let row = document.createElement('tr');
            row.innerHTML = `<td colspan="3" class="fw-bold">الإجمالي الكلي</td><td class="fw-bold" colspan="2">${total.toFixed(2)}</td>`;
            tbody.appendChild(row);
        }
    }

    // حفظ الـ supplier_id الحقيقي لما تختار من datalist
    document.getElementById('supplier_input').addEventListener('input', function() {
        let inputValue = this.value;
        let options = document.getElementById('suppliers_list').options;
        let foundId = '';
        for (let i = 0; i < options.length; i++) {
            if (options[i].value === inputValue) {
                foundId = options[i].getAttribute('data-id');
                break;
            }
        }
        document.getElementById('supplier_id_hidden').value = foundId;
    });

    // قبل إرسال الفورم: حول الجدول لـ JSON وخزنه في input hidden
    document.getElementById('invoiceForm').onsubmit = function() {
        // لازم تحدد المورد من اللي موجودين فقط
        if (!document.getElementById('supplier_id_hidden').value) {
            alert('اختر مورد صحيح من القائمة.');
            return false;
        }
        if (invoiceItems.length === 0) {
            alert('يجب إضافة منتج واحد على الأقل.');
            return false;
        }
        document.getElementById('products_data').value = JSON.stringify(invoiceItems);
        return true;
    }
</script>

<?php

// --- Purchase History ---

// Filters
$filter_supplier = isset($_GET['supplier']) ? $_GET['supplier'] : '';
$filter_start_date = isset($_GET['start_date']) ? $_GET['start_date'] : '';
$filter_end_date = isset($_GET['end_date']) ? $_GET['end_date'] : '';

// Pagination
$limit = 10; // Records per page
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

// Build Query
$where_clauses = [];
if ($filter_supplier) {
    $where_clauses[] = "s.id = '" . mysqli_real_escape_string($conn, $filter_supplier) . "'";
}
if ($filter_start_date) {
    $where_clauses[] = "pi.purchase_date >= '" . mysqli_real_escape_string($conn, $filter_start_date) . "'";
}
if ($filter_end_date) {
    $where_clauses[] = "pi.purchase_date <= '" . mysqli_real_escape_string($conn, $filter_end_date) . "'";
}

$where_sql = count($where_clauses) > 0 ? 'WHERE ' . implode(' AND ', $where_clauses) : '';

// Get total records for pagination
$total_q = mysqli_query($conn, "SELECT COUNT(pit.id) as total FROM purchases pi JOIN suppliers s ON pi.supplier_id = s.id JOIN purchase_items pit ON pi.id = pit.purchase_id JOIN products p ON pit.product_id = p.id $where_sql");
$total_records = mysqli_fetch_assoc($total_q)['total'];
$total_pages = ceil($total_records / $limit);

// Fetch purchase history
$history_query = "
    SELECT 
        pi.id as purchase_id, 
        pi.purchase_date, 
        s.name as supplier_name, 
        p.name as product_name, 
        pit.quantity, 
        pit.unit_price, 
        (pit.quantity * pit.unit_price) as item_total
    FROM purchases pi
    JOIN suppliers s ON pi.supplier_id = s.id
    JOIN purchase_items pit ON pi.id = pit.purchase_id
    JOIN products p ON pit.product_id = p.id
    $where_sql
    ORDER BY pi.purchase_date DESC, pi.id DESC
    LIMIT $limit OFFSET $offset
";

$history_result = mysqli_query($conn, $history_query);

?>

<div class="container my-5">
    <h4 class="mb-4 text-center">📜 سجل فواتير الشراء</h4>

    <!-- Filter Form -->
    <form method="GET" action="" class="mb-4 p-3 bg-light rounded">
        <div class="row g-3">
            <div class="col-md-4">
                <label class="form-label">فلترة حسب المورد</label>
                <select name="supplier" class="form-control">
                    <option value="">كل الموردين</option>
                    <?php foreach ($suppliers as $sup): ?>
                        <option value="<?= $sup['id'] ?>" <?= ($filter_supplier == $sup['id']) ? 'selected' : '' ?>><?= htmlspecialchars($sup['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">من تاريخ</label>
                <input type="date" name="start_date" class="form-control" value="<?= htmlspecialchars($filter_start_date) ?>">
            </div>
            <div class="col-md-3">
                <label class="form-label">إلى تاريخ</label>
                <input type="date" name="end_date" class="form-control" value="<?= htmlspecialchars($filter_end_date) ?>">
            </div>
            <div class="col-md-2 d-flex align-items-end">
                <div class="btn-group w-100" role="group">
                    <button type="submit" class="btn btn-info">بحث</button>
                    <a href="add_purchase_invoice.php" class="btn btn-danger">مسح</a>
                </div>
            </div>
        </div>
    </form>

    <!-- History Table -->
    <div class="table-responsive">
        <table class="table table-bordered table-striped text-center">
            <thead class="table-dark">
                <tr>
                    <th>رقم الفاتورة</th>
                    <th>التاريخ</th>
                    <th>المورد</th>
                    <th>المنتج</th>
                    <th>الكمية</th>
                    <th>سعر الوحدة</th>
                    <th>الإجمالي</th>
                </tr>
            </thead>
            <tbody>
                <?php if (mysqli_num_rows($history_result) > 0): ?>
                    <?php while ($row = mysqli_fetch_assoc($history_result)): ?>
                        <tr>
                            <td><?= $row['purchase_id'] ?></td>
                            <td><?= $row['purchase_date'] ?></td>
                            <td><?= htmlspecialchars($row['supplier_name']) ?></td>
                            <td><?= htmlspecialchars($row['product_name']) ?></td>
                            <td><?= $row['quantity'] ?></td>
                            <td><?= number_format($row['unit_price'], 2) ?></td>
                            <td><?= number_format($row['item_total'], 2) ?></td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="7">لا توجد فواتير لعرضها.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <nav>
        <ul class="pagination justify-content-center">
            <?php
            if ($total_pages > 1) {
                $build_url = function ($page_num) use ($filter_supplier, $filter_start_date, $filter_end_date) {
                    $params = http_build_query([
                        'page' => $page_num,
                        'supplier' => $filter_supplier,
                        'start_date' => $filter_start_date,
                        'end_date' => $filter_end_date
                    ]);
                    return '?' . $params;
                };

                $max_visible = 5;
                $start_page = max(1, $page - intval($max_visible / 2));
                $end_page = $start_page + $max_visible - 1;
                if ($end_page > $total_pages) {
                    $end_page = $total_pages;
                    $start_page = max(1, $end_page - $max_visible + 1);
                }

                if ($page > 1) {
                    echo "<li class='page-item'><a class='page-link' href='" . $build_url(1) . "' aria-label='First'>&laquo;&laquo;</a></li>";
                    echo "<li class='page-item'><a class='page-link' href='" . $build_url($page - 1) . "' aria-label='Previous'>&laquo;</a></li>";
                }

                for ($i = $start_page; $i <= $end_page; $i++) {
                    $active_class = ($i == $page) ? ' active' : '';
                    echo "<li class='page-item{$active_class}'><a class='page-link' href='" . $build_url($i) . "'>{$i}</a></li>";
                }

                if ($page < $total_pages) {
                    echo "<li class='page-item'><a class='page-link' href='" . $build_url($page + 1) . "' aria-label='Next'>&raquo;</a></li>";
                    echo "<li class='page-item'><a class='page-link' href='" . $build_url($total_pages) . "' aria-label='Last'>&raquo;&raquo;</a></li>";
                }
            }
            ?>
        </ul>
    </nav>
</div>

<?php include 'footer.php'; ?>