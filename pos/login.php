<?php
session_start();
include 'db_connection.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    $stmt = $conn->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();

    $result = $stmt->get_result();
    if ($row = $result->fetch_assoc()) {
        if ($password === $row['password']) {
            $_SESSION['user_id']   = $row['id'];
            $_SESSION['username']  = $row['username'];
            $_SESSION['branch_id'] = $row['branch_id'];
            $_SESSION['role']      = strtolower($row['role']);
            header("Location: index.php");
            exit();
        } else {
            $error = "كلمة المرور غير صحيحة";
        }
    } else {
        $error = "اسم المستخدم غير موجود";
    }
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
    <meta charset="UTF-8">
    <title>تسجيل الدخول</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;700&display=swap" rel="stylesheet">
    <style>
        body {
            background: #f2f2f2;
            font-family: 'Tajawal', sans-serif;
            min-height: 100vh;
        }

        .login-container {
            max-width: 900px;
            margin: 100px auto;
            background: #fff;
            border-radius: 22px;
            box-shadow: 0 4px 32px 0 rgba(0, 0, 0, 0.10), 0 1.5px 4px 0 rgba(0, 0, 0, 0.08);
            overflow: hidden;
            display: flex;
            flex-direction: row;
        }

        .login-image {
            flex: 1 1 0;
            background: #eee;
            min-height: 480px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .login-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            border-radius: 22px 0 0 22px;
        }

        .login-form-area {
            flex: 1 1 0;
            padding: 48px 36px 36px 36px;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .login-form-area h2 {
            color: #f44336;
            font-weight: bold;
            margin-bottom: 0.5rem;
        }

        .login-form-area .subheader {
            color: #222;
            font-size: 1.1rem;
            margin-bottom: 1.5rem;
        }

        .form-label {
            font-weight: 500;
        }

        .required-star {
            color: #f44336;
            margin-right: 2px;
        }

        .form-link {
            color: #f44336;
            font-size: 0.95rem;
            text-decoration: none;
            margin-right: 8px;
        }

        .form-link:hover {
            text-decoration: underline;
        }

        .remember-me {
            font-size: 0.97rem;
        }

        .btn-signin {
            background: #f44336;
            color: #fff;
            border: none;
            border-radius: 2rem;
            font-size: 1.2rem;
            font-weight: bold;
            padding: 0.7rem 0;
            margin-top: 1.2rem;
            margin-bottom: 1.2rem;
            transition: background 0.2s;
        }

        .btn-signin:hover {
            background: #d32f2f;
        }

        .demo-btns .btn {
            border-radius: 1.5rem;
            font-weight: bold;
            font-size: 1rem;
            margin: 0 0.3rem 0.5rem 0.3rem;
            min-width: 120px;
        }

        .btn-admin {
            background: #ff9800;
            color: #fff;
        }

        .btn-admin:hover {
            background: #e65100;
            color: #fff;
        }

        .btn-manager {
            background: #2196f3;
            color: #fff;
        }

        .btn-manager:hover {
            background: #1565c0;
            color: #fff;
        }

        .btn-pos {
            background: #a259e6;
            color: #fff;
        }

        .btn-pos:hover {
            background: #6c2eb7;
            color: #fff;
        }

        @media screen and (max-width: 600px) {
            .login-container {
                flex-direction: column;
                max-width: 80%;
            }

            .login-image {
                order: 1;
            }

            .login-form-area {
                order: 2;
            }
        }
    </style>
</head>

<body>

    <div class="login-container">
        <div class="login-form-area">
            <h2 class="text-center mb-2">تسجيل الدخول</h2>
            <div class="subheader text-center mb-4">تسجيل الدخول للمتابعة</div>
            <?php if ($error): ?>
                <div class="alert alert-danger text-center mb-3"><?= $error ?></div>
            <?php endif; ?>
            <form method="POST" autocomplete="off">
                <div class="mb-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <label class="form-label mb-0">اسم المستخدم <span class="required-star">*</span></label>
                    </div>
                    <input type="text" name="username" class="form-control form-control-lg" required autofocus placeholder="أدخل اسم المستخدم">
                </div>
                <div class="mb-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <label class="form-label mb-0">كلمة المرور <span class="required-star">*</span></label>
                    </div>
                    <input type="password" name="password" id="password" class="form-control form-control-lg" required placeholder="أدخل كلمة المرور">
                </div>
                <button type="submit" class="btn btn-signin w-100">تسجيل الدخول</button>
            </form>
        </div>
        <div class="login-image">
            <img src="https://images.unsplash.com/photo-1519389950473-47ba0277781c?auto=format&fit=crop&w=600&q=80" alt="POS System on Computer">
        </div>
    </div>

</body>

</html>