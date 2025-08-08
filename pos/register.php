<?php
session_start();
include 'db_connection.php'; // الاتصال بقاعدة البيانات

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);

    // تحقق إذا كان المستخدم موجود بالفعل
    $check_user = mysqli_query($conn, "SELECT * FROM users WHERE username='$username'");
    if (mysqli_num_rows($check_user) > 0) {
        echo "اسم المستخدم مستخدم من قبل.";
    } else {
        // تشفير كلمة السر
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        $sql = "INSERT INTO users (username, password) VALUES ('$username', '$hashed_password')";
        if (mysqli_query($conn, $sql)) {
            echo "تم إنشاء الحساب بنجاح. <a href='login.php'>تسجيل الدخول الآن</a>";
        } else {
            echo "حدث خطأ أثناء إنشاء الحساب: " . mysqli_error($conn);
        }
    }
}
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>تسجيل مستخدم جديد</title>
    <style>
        body {
            font-family: Arial;
            background-color: #f0f2f5;
            text-align: center;
            padding-top: 100px;
        }
        form {
            background-color: #fff;
            display: inline-block;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0,0,0,0.2);
        }
        input {
            padding: 10px;
            margin: 10px 0;
            width: 100%;
        }
        button {
            padding: 10px 20px;
            background-color: purple;
            color: white;
            border: none;
            cursor: pointer;
        }
        a {
            display: block;
            margin-top: 10px;
            color: #333;
        }
    </style>
</head>
<body>

<form method="POST" action="">
    <h2>تسجيل حساب جديد</h2>
    <input type="text" name="username" placeholder="اسم المستخدم" required><br>
    <input type="password" name="password" placeholder="كلمة المرور" required><br>
    <button type="submit">تسجيل</button>
    <a href="login.php">العودة لتسجيل الدخول</a>
</form>

</body>
</html>