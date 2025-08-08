<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
  <meta charset="UTF-8">
  <title>مكتبات يسر</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
  <style>
    body {
      padding-top: 70px;
    }
  </style>
</head>

<body>

  <?php
  if (session_status() === PHP_SESSION_NONE) {
    session_start();
  }
  ?>

  <nav class="navbar navbar-expand-lg navbar-dark bg-dark fixed-top shadow-sm">
    <div class="container-fluid">
      <a class="navbar-brand fw-bold" href="index.php">📚 مكتبات يسر</a>

      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
        <span class="navbar-toggler-icon"></span>
      </button>

      <div class="collapse navbar-collapse justify-content-between" id="navbarNav">
        <ul class="navbar-nav me-auto mb-2 mb-lg-0">
          <li class="nav-item"><a class="nav-link" href="add_products.php">➕ إضافة منتج</a></li>
          <li class="nav-item"><a class="nav-link" href="view_products.php">📁 عرض المنتجات</a></li>
          <li class="nav-item"><a class="nav-link" href="sales.php">🧾 الكاشير</a></li>
          <li class="nav-item"><a class="nav-link" href="sales_report.php">📊 تقارير المبيعات</a></li>
          <li class="nav-item"><a class="nav-link" href="branch_stock_report.php">🏬 مخزون الفروع</a></li>
          <li class="nav-item"><a class="nav-link" href="add_purchase_invoice.php">فاتورة شراء 🛒</a></li>
        </ul>

        <ul class="navbar-nav">
          <?php if (isset($_SESSION['username'])): ?>
            <li class="nav-item">
              <span class="navbar-text text-white me-3">
                مرحبًا، <?= htmlspecialchars($_SESSION['username']) ?>
              </span>
            </li>
          <?php endif; ?>
          <li class="nav-item">
            <a href="logout.php" class="btn btn-outline-light btn-sm">🔓 تسجيل الخروج</a>
          </li>
        </ul>
      </div>
    </div>
  </nav>