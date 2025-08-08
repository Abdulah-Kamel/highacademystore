<?php
include 'db_connection.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST["username"];
    $password = password_hash($_POST["password"], PASSWORD_DEFAULT);

    // التأكد من أن اسم المستخدم غير مكرر
    $check = $conn->prepare("SELECT * FROM users WHERE username = ?");
    $check->bind_param("s", $username);
    $check->execute();
    $result = $check->get_result();

    if ($result->num_rows > 0) {
        echo "اسم المستخدم مستخدم من قبل.";
    } else {
        $stmt = $conn->prepare("INSERT INTO users (username, password) VALUES (?, ?)");
        $stmt->bind_param("ss", $username, $password);
        if ($stmt->execute()) {
            echo "تم إنشاء الحساب بنجاح. <a href='login.php'>تسجيل الدخول الآن</a>";
        } else {
            echo "حدث خطأ أثناء إنشاء الحساب.";
        }
        $stmt->close();
    }
    $check->close();
    $conn->close();
}
?>