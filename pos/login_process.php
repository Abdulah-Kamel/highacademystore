<?php
session_start();
include 'db_connection.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = $_POST['password'];

    // جلب بيانات المستخدم من قاعدة البيانات
    $query = "SELECT * FROM users WHERE username = '$username' LIMIT 1";
    $result = mysqli_query($conn, $query);

    if (mysqli_num_rows($result) == 1) {
        $row = mysqli_fetch_assoc($result);

        // لو كلمة السر مشفرة (يفضل بشدة دائماً)
        if (password_verify($password, $row['password'])) {
            $_SESSION['user_id']   = $row['id'];
            $_SESSION['username']  = $row['username'];
            $_SESSION['role']      = strtolower($row['role']); // حفظ الدور
            $_SESSION['branch_id'] = $row['branch_id'];

            header("Location: index.php");
            exit();
        } else {
            echo "<script>alert('كلمة المرور غير صحيحة'); window.location='login.php';</script>";
            exit();
        }
    } else {
        echo "<script>alert('اسم المستخدم غير موجود'); window.location='login.php';</script>";
        exit();
    }
} else {
    header("Location: login.php");
    exit();
}
?>