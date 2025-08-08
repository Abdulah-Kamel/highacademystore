<?php
require_once('vendor/tcpdf/tcpdf.php');
require_once('db_connection.php');

// استقبال الفلاتر
$start = $_GET['start_date'] ?? date("Y-m-d");
$end = $_GET['end_date'] ?? date("Y-m-d");
$branch_filter = $_GET['branch_id'] ?? 'all';

// دالة ترجمة اسم اليوم
function arabicDayName($date)
{
    $days = [
        'Saturday'   => 'السبت',
        'Sunday'     => 'الأحد',
        'Monday'     => 'الإثنين',
        'Tuesday'    => 'الثلاثاء',
        'Wednesday'  => 'الأربعاء',
        'Thursday'   => 'الخميس',
        'Friday'     => 'الجمعة'
    ];
    $enDay = date('l', strtotime($date));
    return $days[$enDay] ?? $enDay;
}

// إعداد PDF
$pdf = new TCPDF('P', 'mm', 'A4', true, 'UTF-8', false);
$pdf->SetCreator('POS System');
$pdf->SetAuthor('POS Report');
$pdf->SetTitle('ملخص المبيعات');
$pdf->setRTL(true);
$pdf->SetFont('aealarabiya', '', 12);
$pdf->SetMargins(10, 10, 10);
$pdf->AddPage();

// إعداد سطر التاريخ
$startDay = arabicDayName($start);
$endDay   = arabicDayName($end);

$title_html = '
    <table style="width:100%;">
        <tr>
            <td style="text-align:center;font-weight:bold;color:#1565c0;font-size:18px;">
                ملخص المبيعات
            </td>
        </tr>
        <tr>
            <td style="text-align:center;font-size:13px;color:#1565c0;">
                الفترة من: ' . $start . ' (' . $startDay . ') إلى: ' . $end . ' (' . $endDay . ')
            </td>
        </tr>
    </table>
    <br>
';
$pdf->writeHTML($title_html, true, false, true, false, '');

// جلب الفروع
$branches = ($branch_filter === 'all') ?
    mysqli_query($conn, "SELECT * FROM branches") :
    mysqli_query($conn, "SELECT * FROM branches WHERE id = " . intval($branch_filter));

// طباعة ملخص كل فرع
while ($branch = mysqli_fetch_assoc($branches)) {
    $branch_id = $branch['id'];
    $branch_name = $branch['name'];

    $query = "
        SELECT 
            p.name AS product_name,
            SUM(s.quantity) AS total_qty,
            SUM(s.total_price) AS total_sales,
            SUM(s.total_price - (s.quantity * p.purchase_price)) AS total_profit,
            MAX(ps.quantity) AS remaining_stock
        FROM sales s
        JOIN products p ON s.product_id = p.id
        LEFT JOIN product_stock ps ON ps.product_id = p.id AND ps.branch_id = $branch_id
        WHERE s.branch_id = $branch_id AND s.sale_date BETWEEN '$start 00:00:00' AND '$end 23:59:59'
        GROUP BY s.product_id
    ";
    $result = mysqli_query($conn, $query);

    $html = '
        <table border="1" cellpadding="4" style="width:100%;margin-bottom:10px;">
            <tr style="background-color:#e3f2fd;">
                <td colspan="5" style="text-align:center;color:#1565c0;font-weight:bold;">
                    ملخص مبيعات فرع ' . $branch_name . '
                </td>
            </tr>
            <tr style="background-color:#f5f5f5;">
                <th>المنتج</th>
                <th>الكمية المباعة</th>
                <th>المتبقي بالمخزون</th>
                <th>الربح</th>
                <th>إجمالي البيع</th>
            </tr>
    ';
    $total_sales = 0;
    $total_profit = 0;
    while ($row = mysqli_fetch_assoc($result)) {
        $html .= '<tr>
            <td>' . $row['product_name'] . '</td>
            <td>' . $row['total_qty'] . '</td>
            <td>' . ($row['remaining_stock'] ?? 0) . '</td>
            <td>' . number_format((float)$row['total_profit'], 0) . ' ج.م</td>
            <td>' . number_format((float)$row['total_sales'], 2) . ' ج.م</td>
        </tr>';
        $total_sales += $row['total_sales'];
        $total_profit += $row['total_profit'];
    }
    // سطر الاجماليات: كلمة "الإجماليات:" تغطي أول 3 خلايا (colspan="3")
    $html .= '
            <tr style="background-color:#c8e6c9;">
                <td colspan="3" style="font-weight:bold;text-align:center;">الإجماليات:</td>
                <td style="font-weight:bold;">' . number_format((float)$total_profit, 0) . ' ج.م</td>
                <td style="font-weight:bold;">' . number_format((float)$total_sales, 2) . ' ج.م</td>
            </tr>
        </table>
    ';

    $pdf->writeHTML($html, true, false, true, false, '');
}

// إخراج الملف
$pdf->Output('summary_report.pdf', 'I');
