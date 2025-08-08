<?php
require_once('vendor/tcpdf/tcpdf.php');
require_once('db_connection.php');

// حماية الصلاحية للصفحة
include 'page_protect.php';

session_start();
include 'db_connection.php';

// جلب الفروع
$branch_query = "SELECT id, name FROM branches";
$branch_result = mysqli_query($conn, $branch_query);
$branches = [];
while ($b = mysqli_fetch_assoc($branch_result)) {
    $branches[] = $b;
}

// جلب كل المنتجات (بدون LIMIT)
$product_query = "
    SELECT DISTINCT p.id, p.name, p.stock, p.min_stock_main, p.min_stock_shebin, p.min_stock_qwisna, p.purchase_price
    FROM products p
    LEFT JOIN product_stock ps ON ps.product_id = p.id
";
$product_result = mysqli_query($conn, $product_query);
$all_products = [];
while ($product = mysqli_fetch_assoc($product_result)) {
    $all_products[] = $product;
}

// حساب اجمالي سعر المخزون لكل فرع (لكل المنتجات)
$main_total_price = 0;
$branch_total_prices = [];
foreach ($branches as $branch) {
    $branch_total_prices[$branch['id']] = 0;
}
$grand_total_price = 0;

foreach ($all_products as $product) {
    $product_id = $product['id'];
    $main_qty = $product['stock'];
    $main_price = (float)($product['purchase_price'] ?? 0);
    $main_total_price += $main_qty * $main_price;
    $row_prices = $main_qty * $main_price;
    foreach ($branches as $branch) {
        $bid = $branch['id'];
        $q = "SELECT quantity FROM product_stock WHERE product_id = $product_id AND branch_id = $bid";
        $res = mysqli_query($conn, $q);
        $row = mysqli_fetch_assoc($res);
        $qty = $row ? $row['quantity'] : 0;
        $branch_total_prices[$bid] += $qty * $main_price;
        $row_prices += $qty * $main_price;
    }
    $grand_total_price += $row_prices;
}

// إعداد PDF
$pdf = new TCPDF('L', 'mm', 'A4', true, 'UTF-8', false);
$pdf->SetCreator('POS System');
$pdf->SetAuthor('POS Report');
$pdf->SetTitle('تقرير توزيع المخزون على الفروع');
$pdf->setRTL(true);
$pdf->SetFont('aealarabiya', '', 13);
$pdf->SetMargins(10, 10, 10);
$pdf->AddPage();

$report_date = date('Y-m-d H:i');
$header_html = '
    <table style=" margin-bottom:10px;">
        <tr>
            <td style="text-align:center; font-size:22px; font-weight:bold; color:#1565c0;">
                تقرير توزيع المخزون على الفروع
            </td>
            <td style=" text-align:center; font-size:18px; color:#333;">
                التاريخ: ' . $report_date . '
            </td>
        </tr>
    </table>
    <hr style="border:1px solid #1565c0; margin-bottom:12px;">
';
$pdf->writeHTML($header_html, true, false, true, false, '');

// جدول المخزون
$html = '<table border="1" cellpadding="4" style=" font-size:12px; border-collapse:collapse; margin-left:auto; width:900px; margin-right:auto; text-align:center;">
    <thead style="background-color:#e3f2fd;">
        <tr style="background-color:#1565c0; color:#fff;">
            <th style="width:32px;">#</th>
            <th style=";">المنتج</th>
            <th style="">المخزن الرئيسي</th>';
foreach ($branches as $branch) {
    $html .= '<th style="">' . htmlspecialchars($branch['name']) . '</th>';
}
$html .= '<th style="">الإجمالي الكلي</th></tr></thead><tbody>';
$serial = 1;
$row_alt = false;
foreach ($all_products as $product) {
    $row_bg = $row_alt ? ' style="background-color:#f9f9f9;"' : '';
    $html .= '<tr' . $row_bg . '>';
    $html .= '<td style="width:32px;">' . $serial++ . '</td>';
    $html .= '<td style="font-weight:bold;;">' . htmlspecialchars($product['name']) . '</td>';
    $product_id = $product['id'];
    $main_qty = $product['stock'];
    $main_min = (int)($product['min_stock_main'] ?? 0);
    $main_style = ($main_qty < $main_min) ? ' style="background-color:#ffb3b3;"' : '';
    $html .= '<td style=""' . $main_style . '>' . $main_qty . '</td>';
    $total = $main_qty;
    foreach ($branches as $branch) {
        $bid = $branch['id'];
        $q = "SELECT quantity FROM product_stock WHERE product_id = $product_id AND branch_id = $bid";
        $res = mysqli_query($conn, $q);
        $row = mysqli_fetch_assoc($res);
        $qty = $row ? $row['quantity'] : 0;
        $total += $qty;
        $branch_min = 0;
        if (strpos($branch['name'], 'شبين الكوم') !== false) {
            $branch_min = (int)($product['min_stock_shebin'] ?? 0);
        } elseif (strpos($branch['name'], 'قويسنا') !== false) {
            $branch_min = (int)($product['min_stock_qwisna'] ?? 0);
        }
        $branch_style = ($qty < $branch_min) ? ' style="background-color:#ffb3b3; "' : '';
        $html .= '<td style=""' . $branch_style . '>' . $qty . '</td>';
    }
    $html .= '<td style="">' . $total . '</td>';
    $html .= '</tr>';
    $row_alt = !$row_alt;
}
$html .= '</tbody></table>';
$pdf->writeHTML($html, true, false, true, false, '');

// جدول الاجماليات (للأدمن فقط)
if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin') {
    $html2 = '<br><table border="1" cellpadding="4" style=" font-size:13px; border-collapse:collapse;">
        <thead style="background-color:#e3f2fd;">
            <tr style="background-color:#1565c0; color:#fff;">
                <th>سعر مخزون المخزن الرئيسي</th>';
    foreach ($branches as $branch) {
        $html2 .= '<th>سعر مخزون ' . htmlspecialchars($branch['name']) . '</th>';
    }
    $html2 .= '<th>إجمالي كل المخزون</th></tr></thead><tbody><tr>';
    $html2 .= '<td style="font-weight:bold;">' . number_format($main_total_price, 2) . ' ج.م</td>';
    foreach ($branches as $branch) {
        $html2 .= '<td style="font-weight:bold;">' . number_format($branch_total_prices[$branch['id']], 2) . ' ج.م</td>';
    }
    $html2 .= '<td style="font-weight:bold;">' . number_format($grand_total_price, 2) . ' ج.م</td>';
    $html2 .= '</tr></tbody></table>';
    $pdf->writeHTML($html2, true, false, true, false, '');
}

$pdf->Output('branch_stock_report.pdf', 'D');
