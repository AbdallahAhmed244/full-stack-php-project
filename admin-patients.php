<?php

session_start();

require_once("auth.php");
requireLogin();
requireRole("admin");

require_once("config/database.php");
$title = "إدارة المرضى";
$search = "";

if (isset($_GET['search'])) {
    $search = mysqli_real_escape_string($conn, $_GET['search']);
}

$query = "SELECT
patients.*,
users.email,
doctors.full_name AS doctor_name,
centers.name AS center_name

FROM patients

INNER JOIN users
ON patients.user_id = users.id

LEFT JOIN appointments
ON patients.id = appointments.patient_id

LEFT JOIN doctors
ON appointments.doctor_id = doctors.id

LEFT JOIN centers
ON appointments.center_id = centers.id";

if ($search != "") {
    $query .= " WHERE
    patients.full_name LIKE '%$search%'
    OR patients.phone LIKE '%$search%'
    OR users.email LIKE '%$search%'";
}

$query .= " GROUP BY patients.id
ORDER BY patients.id DESC";

$result = mysqli_query($conn, $query);

if (!$result) {
    die(mysqli_error($conn));
}

?>

<?php include("includes/head.php"); ?>

<body>

<div class="container-fluid">

    <div class="row">

        <?php include("includes/sidebar.php"); ?>

        <main class="col-md-9 col-lg-10 p-0">

            <?php include("includes/topbar.php"); ?>

            <div class="p-4">

                <div class="d-flex flex-wrap gap-2 justify-content-between align-items-center mb-3">

                    <form method="get" class="d-flex gap-2">

                        <input
                            type="search"
                            name="search"
                            class="form-control"
                            placeholder="بحث بالاسم / الهاتف / البريد"
                            value="<?= htmlspecialchars($search); ?>">

                        <button
                            type="submit"
                            class="btn btn-outline-primary">

                            بحث

                        </button>

                    </form>

                    <a
                        href="admin-patient-form.php"
                        class="btn btn-primary">

                        إضافة مريض

                    </a>

                </div>

                <div class="card">

                    <div class="table-responsive">

                        <table class="table table-hover align-middle mb-0">

                            <thead>

                                <tr>

                                    <th>#</th>
                                    <th>الاسم</th>
                                    <th>البريد</th>
                                    <th>الهاتف</th>
                                    <th>الطبيب</th>
                                    <th>المركز</th>
                                    <th>الحالة</th>
                                    <th>الإجراءات</th>

                                </tr>

                            </thead>

                            <tbody>

                                <?php if (mysqli_num_rows($result) > 0): ?>

                                    <?php while ($row = mysqli_fetch_assoc($result)): ?>

                                        <tr>

                                            <td>
                                                <?= $row['id']; ?>
                                            </td>

                                            <td>
                                                <?= htmlspecialchars($row['full_name']); ?>
                                            </td>

                                            <td>
                                                <?= htmlspecialchars($row['email']); ?>
                                            </td>

                                            <td>
                                                <?= htmlspecialchars($row['phone']); ?>
                                            </td>

                                            <td>
                                                <?= htmlspecialchars($row['doctor_name'] ?? '—'); ?>
                                            </td>

                                            <td>
                                                <?= htmlspecialchars($row['center_name'] ?? '—'); ?>
                                            </td>

                                            <td>

                                                <?php if ($row['status'] == "active"): ?>

                                                    <span class="badge text-bg-success">
                                                        نشط
                                                    </span>

                                                <?php else: ?>

                                                    <span class="badge text-bg-secondary">
                                                        غير نشط
                                                    </span>

                                                <?php endif; ?>

                                            </td>

                                            <td>

                                                <a
                                                    href="admin-patient-form.php?id=<?= $row['id']; ?>"
                                                    class="btn btn-sm btn-outline-primary">

                                                    تعديل

                                                </a>

                                                <a
                                                    href="admin-patient-delete.php?id=<?= $row['id']; ?>"
                                                    class="btn btn-sm btn-outline-danger"
                                                    onclick="return confirm('هل أنت متأكد من حذف هذا المريض؟');">

                                                    حذف

                                                </a>

                                            </td>

                                        </tr>

                                    <?php endwhile; ?>

                                <?php else: ?>

                                    <tr>

                                        <td colspan="8" class="text-center text-muted py-4">

                                            لا يوجد مرضى

                                        </td>

                                    </tr>

                                <?php endif; ?>

                            </tbody>

                        </table>

                    </div>

                </div>

                <p class="small text-muted mt-2">

                    الإجمالي: <?= mysqli_num_rows($result); ?>

                </p>

            </div>

        </main>

    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>