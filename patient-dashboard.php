<?php

session_start();

require_once("auth.php");
requireLogin();
requireRole("patient");

require_once("config/database.php");

if ($_SESSION['role'] != "patient")
{
    header("Location: login.php");
    exit;
}

$title = "لوحة تحكم المريض";

$user_id = $_SESSION['user_id'];

$query = "SELECT *
FROM patients
WHERE user_id='$user_id'";

$result = mysqli_query($conn, $query);

$patient = mysqli_fetch_assoc($result);

if (!$patient)
{
    die("Patient not found.");
}

$query = "SELECT
appointments.*,
doctors.full_name AS doctor_name,
centers.name AS center_name

FROM appointments

LEFT JOIN doctors
ON appointments.doctor_id = doctors.id

LEFT JOIN centers
ON appointments.center_id = centers.id

WHERE appointments.patient_id='{$patient['id']}'

ORDER BY appointment_date DESC,
appointment_time DESC";

$appointments = mysqli_query($conn, $query);

$appointments_count = mysqli_num_rows($appointments);

?>

<?php include("includes/head.php"); ?>

<body>

<div class="container-fluid">

<div class="row">

<?php include("includes/sidebar.php"); ?>

<main class="col-md-9 col-lg-10 p-0">

<?php include("includes/topbar.php"); ?>

<div class="p-4">

<div class="alert alert-success">

مرحباً

<strong>
<?= htmlspecialchars($patient['full_name']); ?>
</strong>

، يمكنك حجز جلسة غسيل جديدة ومتابعة جميع مواعيدك.

</div>

<div class="row g-3 mb-4">

<div class="col-md-6">

<div class="card p-3">

<div class="text-muted small">
عدد المواعيد
</div>

<div class="fs-5 fw-bold text-primary">

<?= $appointments_count; ?>

موعد

</div>

</div>

</div>

<div class="col-md-6">

<div class="card p-3">

<div class="text-muted small">
المريض
</div>

<div class="fs-5 fw-bold text-primary">

<?= htmlspecialchars($patient['full_name']); ?>

</div>

</div>

</div>

</div>

<div class="card p-3">

<div class="d-flex justify-content-between align-items-center mb-3">

<h2 class="h6 mb-0">
مواعيدي
</h2>

<a
href="patient-book-appointment.php"
class="btn btn-primary btn-sm">

حجز موعد جديد

</a>

</div>

<div class="table-responsive">

<table class="table table-hover align-middle">

<thead>

<tr>

<th>التاريخ</th>

<th>الوقت</th>

<th>الطبيب</th>

<th>المركز</th>

<th>الحالة</th>

</tr>

</thead>

<tbody>

<?php

if ($appointments_count > 0)
{

while ($row = mysqli_fetch_assoc($appointments))
{

?>

<tr>

<td>
<?= htmlspecialchars($row['appointment_date'] ?? '—'); ?>
</td>

<td>
<?= htmlspecialchars($row['appointment_time'] ?? '—'); ?>
</td>

<td>
<?= htmlspecialchars($row['doctor_name'] ?? '—'); ?>
</td>

<td>
<?= htmlspecialchars($row['center_name'] ?? '—'); ?>
</td>

<td>

<?php

if ($row['status'] == "pending")
{
    echo '<span class="badge text-bg-warning">بانتظار التأكيد</span>';
}
elseif ($row['status'] == "approved")
{
    echo '<span class="badge text-bg-primary">مؤكد</span>';
}
elseif ($row['status'] == "completed")
{
    echo '<span class="badge text-bg-success">تمت</span>';
}
elseif ($row['status'] == "cancelled")
{
    echo '<span class="badge text-bg-danger">ملغي</span>';
}
else
{
    echo '<span class="badge text-bg-secondary">' . htmlspecialchars($row['status']) . '</span>';
}

?>

</td>

</tr>

<?php

}

}
else
{

?>

<tr>

<td colspan="5" class="text-center p-4">

لا توجد مواعيد حتى الآن.

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

</main>

</div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>