<?php

session_start();

require_once("auth.php");
requireLogin();
requireRole("admin");

require_once("config/database.php");

if ($_SESSION['role'] != "admin")
{
    header("Location: login.php");
    exit;
}

$title = "لوحة التحكم";

$query = "SELECT COUNT(*) AS total_patients FROM patients";
$result = mysqli_query($conn, $query);
$row = mysqli_fetch_assoc($result);
$total_patients = (int)$row['total_patients'];

$query = "SELECT COUNT(*) AS total_doctors FROM doctors";
$result = mysqli_query($conn, $query);
$row = mysqli_fetch_assoc($result);
$total_doctors = (int)$row['total_doctors'];

$query = "SELECT COUNT(*) AS total_centers FROM centers";
$result = mysqli_query($conn, $query);
$row = mysqli_fetch_assoc($result);
$total_centers = (int)$row['total_centers'];

$query = "SELECT COUNT(*) AS total_machines FROM machines";
$result = mysqli_query($conn, $query);
$row = mysqli_fetch_assoc($result);
$total_machines = (int)$row['total_machines'];

$query = "SELECT COUNT(*) AS total_appointments
FROM appointments
WHERE appointment_date = CURDATE()";

$result = mysqli_query($conn, $query);
$row = mysqli_fetch_assoc($result);
$total_appointments = (int)$row['total_appointments'];

$query = "SELECT COUNT(*) AS available_machines
FROM machines
WHERE status = 'available'";

$result = mysqli_query($conn, $query);
$row = mysqli_fetch_assoc($result);
$available_machines = (int)$row['available_machines'];

$query = "SELECT COUNT(*) AS busy_machines
FROM machines
WHERE status = 'busy'";

$result = mysqli_query($conn, $query);
$row = mysqli_fetch_assoc($result);
$busy_machines = (int)$row['busy_machines'];

$query = "SELECT COUNT(*) AS maintenance_machines
FROM machines
WHERE status = 'maintenance'";

$result = mysqli_query($conn, $query);
$row = mysqli_fetch_assoc($result);
$maintenance_machines = (int)$row['maintenance_machines'];

$query = "SELECT COUNT(*) AS waiting
FROM appointments
WHERE status = 'pending'";

$result = mysqli_query($conn, $query);
$row = mysqli_fetch_assoc($result);
$waiting = (int)$row['waiting'];

$query = "SELECT
activity_logs.created_at,
activity_logs.action,
activity_logs.details,
users.username

FROM activity_logs

LEFT JOIN users
ON activity_logs.user_id = users.id

ORDER BY activity_logs.created_at DESC

LIMIT 5";

$activity_result = mysqli_query($conn, $query);

?>

<?php include("includes/head.php"); ?>

<body>

<div class="container-fluid">

<div class="row">

<?php include("includes/sidebar.php"); ?>

<main class="col-md-9 col-lg-10 p-0">

<?php include("includes/topbar.php"); ?>

<div class="p-4">

<div class="row g-3 mb-4">

<div class="col-md-3">

<div class="card p-3">

<div class="text-muted small">
المرضى
</div>

<div class="fs-3 fw-bold text-primary">
<?= $total_patients; ?>
</div>

</div>

</div>

<div class="col-md-3">

<div class="card p-3">

<div class="text-muted small">
الأطباء
</div>

<div class="fs-3 fw-bold text-primary">
<?= $total_doctors; ?>
</div>

</div>

</div>

<div class="col-md-3">

<div class="card p-3">

<div class="text-muted small">
المراكز
</div>

<div class="fs-3 fw-bold text-primary">
<?= $total_centers; ?>
</div>

</div>

</div>

<div class="col-md-3">

<div class="card p-3">

<div class="text-muted small">
إجمالي الأجهزة
</div>

<div class="fs-3 fw-bold text-primary">
<?= $total_machines; ?>
</div>

</div>

</div>

<div class="col-md-3">

<div class="card p-3">

<div class="text-muted small">
مواعيد اليوم
</div>

<div class="fs-3 fw-bold text-primary">
<?= $total_appointments; ?>
</div>

</div>

</div>

<div class="col-md-3">

<div class="card p-3">

<div class="text-muted small">
أجهزة متاحة
</div>

<div class="fs-4 fw-bold text-success">
<?= $available_machines; ?>
</div>

</div>

</div>

<div class="col-md-3">

<div class="card p-3">

<div class="text-muted small">
أجهزة مشغولة
</div>

<div class="fs-4 fw-bold text-warning">
<?= $busy_machines; ?>
</div>

</div>

</div>

<div class="col-md-3">

<div class="card p-3">

<div class="text-muted small">
تحت الصيانة
</div>

<div class="fs-4 fw-bold text-danger">
<?= $maintenance_machines; ?>
</div>

</div>

</div>

<div class="col-md-3">

<div class="card p-3">

<div class="text-muted small">
قائمة الانتظار
</div>

<div class="fs-4 fw-bold">
<?= $waiting; ?>
</div>

</div>

</div>

</div>

<div class="row g-3">

<div class="col-lg-8">

<div class="card p-3">

<div class="d-flex justify-content-between align-items-center mb-3">

<h2 class="h6 mb-0">
أحدث النشاط
</h2>

<a href="admin-activity.php" class="small">
عرض الكل
</a>

</div>

<div class="table-responsive">

<table class="table table-sm align-middle mb-0">

<thead>

<tr>

<th>الوقت</th>
<th>المستخدم</th>
<th>الإجراء</th>
<th>التفاصيل</th>

</tr>

</thead>

<tbody>

<?php

if (mysqli_num_rows($activity_result) > 0)
{

while ($row = mysqli_fetch_assoc($activity_result))
{

?>

<tr>

<td>
<?= htmlspecialchars($row['created_at']); ?>
</td>

<td>
<?= htmlspecialchars($row['username'] ?? '—'); ?>
</td>

<td>
<?= htmlspecialchars($row['action'] ?? '—'); ?>
</td>

<td>
<?= htmlspecialchars($row['details'] ?? '—'); ?>
</td>

</tr>

<?php

}

}
else
{

?>

<tr>

<td colspan="4" class="text-center p-4">
لا توجد سجلات نشاط.
</td>

</tr>

<?php

}

?>

</tbody>

</table>

</div>

</div>

</div>

<div class="col-lg-4">

<div class="card p-3">

<h2 class="h6 mb-3">
اختصارات سريعة
</h2>

<div class="d-grid gap-2">

<a
class="btn btn-outline-primary btn-sm"
href="admin-appointments.php">

إدارة المواعيد

</a>

<a
class="btn btn-outline-primary btn-sm"
href="admin-patients.php">

إدارة المرضى

</a>

<a
class="btn btn-outline-primary btn-sm"
href="admin-doctors.php">

إدارة الأطباء

</a>

<a
class="btn btn-outline-primary btn-sm"
href="admin-machines.php">

إدارة الأجهزة

</a>

<a
class="btn btn-outline-primary btn-sm"
href="admin-centers.php">

إدارة المراكز

</a>

</div>

<p class="small text-muted mt-3 mb-0">

المواعيد المعلّقة:

<strong>
<?= $waiting; ?>
</strong>

</p>

</div>

</div>

</div>

</div>

</main>

</div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>