<?php
include 'page_protect.php';
include 'db_connection.php';
include 'header.php';

// Handle add supplier
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_supplier'])) {
    $name = trim($_POST['name']);
    $phone = trim($_POST['phone']);
    $email = trim($_POST['email']);
    $address = trim($_POST['address']);
    if ($name !== '') {
        $stmt = $conn->prepare("INSERT INTO suppliers (name, phone, email, address) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $name, $phone, $email, $address);
        $stmt->execute();
        $stmt->close();
        header('Location: suppliers.php?success=1');
        exit();
    }
}

// Handle edit supplier
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_supplier'])) {
    $id = intval($_POST['id']);
    $name = trim($_POST['name']);
    $phone = trim($_POST['phone']);
    $email = trim($_POST['email']);
    $address = trim($_POST['address']);
    if ($name !== '') {
        $stmt = $conn->prepare("UPDATE suppliers SET name=?, phone=?, email=?, address=? WHERE id=?");
        $stmt->bind_param("ssssi", $name, $phone, $email, $address, $id);
        $stmt->execute();
        $stmt->close();
        header('Location: suppliers.php?updated=1');
        exit();
    }
}

// Handle delete supplier (يحذف المورد فقط)
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    // Before deleting supplier
    $check = $conn->query("SELECT COUNT(*) as cnt FROM purchases WHERE supplier_id = $id");
    $row = $check->fetch_assoc();
    if ($row['cnt'] > 0) {
        // Show error and do not delete
        header('Location: suppliers.php?error=linked');
        exit();
    }
    $stmt = $conn->prepare("DELETE FROM suppliers WHERE id=?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();
    header('Location: suppliers.php?deleted=1');
    exit();
}

// Fetch all suppliers
$suppliers = mysqli_query($conn, "SELECT * FROM suppliers ORDER BY id DESC");
?>
<div class="container my-5">
    <h2 class="mb-4 text-center">إدارة الموردين</h2>
    <?php if (isset($_GET['success'])): ?>
        <div class="alert alert-success text-center">تم إضافة المورد بنجاح!</div>
    <?php elseif (isset($_GET['updated'])): ?>
        <div class="alert alert-info text-center">تم تحديث بيانات المورد!</div>
    <?php elseif (isset($_GET['deleted'])): ?>
        <div class="alert alert-danger text-center">تم حذف المورد!</div>
    <?php endif; ?>
    <?php if (isset($_GET['error']) && $_GET['error'] == 'linked'): ?>
        <div class="alert alert-danger text-center">لا يمكن حذف المورد لأنه مرتبط بفواتير شراء.</div>
    <?php endif; ?>
    <div class="row">
        <div class="col-md-5">
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-primary text-white">إضافة مورد جديد</div>
                <div class="card-body">
                    <form method="POST">
                        <input type="hidden" name="add_supplier" value="1">
                        <div class="mb-3">
                            <label class="form-label">اسم المورد</label>
                            <input type="text" name="name" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">رقم الهاتف</label>
                            <input type="text" name="phone" class="form-control">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">البريد الإلكتروني</label>
                            <input type="email" name="email" class="form-control">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">العنوان</label>
                            <input type="text" name="address" class="form-control">
                        </div>
                        <button type="submit" class="btn btn-success w-100">إضافة المورد</button>
                    </form>
                </div>
            </div>
        </div>
        <div class="col-md-7">
            <div class="card shadow-sm">
                <div class="card-header bg-secondary text-white">قائمة الموردين</div>
                <div class="card-body p-0">
                    <table class="table table-bordered table-hover mb-0 text-center">
                        <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th>الاسم</th>
                                <th>الهاتف</th>
                                <th>البريد الإلكتروني</th>
                                <th>العنوان</th>
                                <th>إجراءات</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($row = mysqli_fetch_assoc($suppliers)): ?>
                                <tr>
                                    <td><?= $row['id'] ?></td>
                                    <td><?= htmlspecialchars($row['name']) ?></td>
                                    <td><?= htmlspecialchars($row['phone']) ?></td>
                                    <td><?= htmlspecialchars($row['email']) ?></td>
                                    <td><?= htmlspecialchars($row['address']) ?></td>
                                    <td>
                                        <button class="btn btn-sm btn-info" data-bs-toggle="modal" data-bs-target="#editModal<?= $row['id'] ?>">تعديل</button>
                                        <a href="suppliers.php?delete=<?= $row['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('هل أنت متأكد من حذف هذا المورد؟');">حذف</a>
                                    </td>
                                </tr>
                                <!-- Edit Modal -->
                                <div class="modal fade" id="editModal<?= $row['id'] ?>" tabindex="-1" aria-labelledby="editModalLabel<?= $row['id'] ?>" aria-hidden="true">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <form method="POST">
                                                <input type="hidden" name="edit_supplier" value="1">
                                                <input type="hidden" name="id" value="<?= $row['id'] ?>">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="editModalLabel<?= $row['id'] ?>">تعديل بيانات المورد</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <div class="mb-3">
                                                        <label class="form-label">اسم المورد</label>
                                                        <input type="text" name="name" class="form-control" required value="<?= htmlspecialchars($row['name']) ?>">
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="form-label">رقم الهاتف</label>
                                                        <input type="text" name="phone" class="form-control" value="<?= htmlspecialchars($row['phone']) ?>">
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="form-label">البريد الإلكتروني</label>
                                                        <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($row['email']) ?>">
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="form-label">العنوان</label>
                                                        <input type="text" name="address" class="form-control" value="<?= htmlspecialchars($row['address']) ?>">
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                                                    <button type="submit" class="btn btn-primary">حفظ التغييرات</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<?php include 'footer.php'; ?>
<!-- Bootstrap JS (for modals) -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>