<?php
// حماية الصلاحية للصفحة
include 'page_protect.php';

include 'header.php';

// تأكيد إضافة الفرع
$message = "";
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $branch_name = mysqli_real_escape_string($conn, $_POST['branch_name']);

    if (!empty($branch_name)) {
        // التحقق من تكرار الاسم
        $check = mysqli_query($conn, "SELECT * FROM branches WHERE name = '$branch_name'");
        if (mysqli_num_rows($check) > 0) {
            $message = "<p style='color:red;'>⚠️ الفرع موجود بالفعل!</p>";
        } else {
            $insert = mysqli_query($conn, "INSERT INTO branches (name) VALUES ('$branch_name')");
            if ($insert) {
                $message = "<p style='color:green;'>✅ تم إضافة الفرع بنجاح.</p>";
            } else {
                $message = "<p style='color:red;'>❌ حدث خطأ أثناء الإضافة.</p>";
            }
        }
    } else {
        $message = "<p style='color:red;'>⚠️ يرجى إدخال اسم الفرع.</p>";
    }
}
?>

<div class="container mt-5">
    <h2>إضافة فرع جديد</h2>
    <?php echo $message; ?>
    <form method="POST">
        <div class="mb-3">
            <label for="branch_name" class="form-label">اسم الفرع:</label>
            <input type="text" name="branch_name" class="form-control" required>
        </div>
        <button type="submit" class="btn btn-primary">إضافة الفرع</button>
    </form>
</div>

<?php include 'footer.php'; ?>