<?php

session_start();

require_once("auth.php");
requireLogin();
requireRole("admin");

require_once("config/database.php");

$title = "إدارة المواعيد";

$search = trim($_GET['search'] ?? "");
$status = trim($_GET['status'] ?? "");

$search_safe = mysqli_real_escape_string($conn, $search);
$status_safe = mysqli_real_escape_string($conn, $status);

$allowed_statuses = [
    "pending",
    "approved",
    "completed",
    "cancelled"
];

$query = "SELECT
appointments.id,
appointments.appointment_date,
appointments.appointment_time,
appointments.status,
patients.full_name AS patient_name,
doctors.full_name AS doctor_name,
centers.name AS center_name

FROM appointments

LEFT JOIN patients
ON appointments.patient_id = patients.id

LEFT JOIN doctors
ON appointments.doctor_id = doctors.id

LEFT JOIN centers
ON appointments.center_id = centers.id

WHERE
(
patients.full_name LIKE '%$search_safe%'
OR doctors.full_name LIKE '%$search_safe%'
OR centers.name LIKE '%$search_safe%'
)";

if (!empty($status) && in_array($status, $allowed_statuses))
{
    $query .= " AND appointments.status='$status_safe'";
}

$query .= " ORDER BY
appointments.appointment_date DESC,
appointments.appointment_time DESC";

$result = mysqli_query($conn, $query);

if (!$result)
{
    die(mysqli_error($conn));
}

?>

<?php include "includes/head.php"; ?>

<body>

<div class="container-fluid">

<div class="row">

<?php include("includes/sidebar.php"); ?>

<main class="col-md-9 col-lg-10 p-0">

<?php include("includes/topbar.php"); ?>

<div class="p-4">

<div class="d-flex flex-wrap gap-2 justify-content-between align-items-center mb-3">

<form
action=""
method="get"
class="d-flex flex-wrap gap-2">

<input
type="search"
name="search"
value="<?= htmlspecialchars($search); ?>"
class="form-control"
placeholder="بحث باسم المريض / الطبيب / المركز">

<select
name="status"
class="form-select"
style="max-width:12rem">

<option value="">
كل الحالات
</option>

<option
value="pending"
<?= $status == "pending" ? "selected" : ""; ?>>

بانتظار التأكيد

</option>

<option
value="approved"
<?= $status == "approved" ? "selected" : ""; ?>>

مؤكد

</option>

<option
value="completed"
<?= $status == "completed" ? "selected" : ""; ?>>

تمت

</option>

<option
value="cancelled"
<?= $status == "cancelled" ? "selected" : ""; ?>>

ملغي

</option>

</select>

<button
type="submit"
class="btn btn-outline-primary">

تصفية

</button>

<a
href="admin-appointments.php"
class="btn btn-outline-secondary">

إلغاء التصفية

</a>

</form>

<a
href="admin-appointment-form.php"
class="btn btn-primary">

إضافة موعد

</a>

</div>

<div class="card">

<div class="table-responsive">

<table class="table table-hover align-middle mb-0">

<thead>

<tr>

<th>#</th>
<th>التاريخ</th>
<th>الوقت</th>
<th>المريض</th>
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

<?= (int)$row['id']; ?>

</td>

<td>

<?= htmlspecialchars($row['appointment_date'] ?? '—'); ?>

</td>

<td>

<?= htmlspecialchars($row['appointment_time'] ?? '—'); ?>

</td>

<td>

<?= htmlspecialchars($row['patient_name'] ?? '—'); ?>

</td>

<td>

<?= htmlspecialchars($row['doctor_name'] ?? '—'); ?>

</td>

<td>

<?= htmlspecialchars($row['center_name'] ?? '—'); ?>

</td>

<td>

<?php if ($row['status'] == "pending"): ?>

<span class="badge text-bg-warning">

بانتظار التأكيد

</span>

<?php elseif ($row['status'] == "approved"): ?>

<span class="badge text-bg-primary">

مؤكد

</span>

<?php elseif ($row['status'] == "completed"): ?>

<span class="badge text-bg-success">

تمت

</span>

<?php elseif ($row['status'] == "cancelled"): ?>

<span class="badge text-bg-danger">

ملغي

</span>

<?php else: ?>

<span class="badge text-bg-secondary">

<?= htmlspecialchars($row['status'] ?? '—'); ?>

</span>

<?php endif; ?>

</td>

<td class="text-nowrap">

<a
href="admin-appointment-form.php?id=<?= (int)$row['id']; ?>"
class="btn btn-sm btn-outline-primary">

تعديل

</a>

<a
href="admin-appointment-delete.php?id=<?= (int)$row['id']; ?>"
class="btn btn-sm btn-outline-danger"
onclick="return confirm('هل أنت متأكد من حذف هذا الموعد؟');">

حذف

</a>

</td>

</tr>

<?php endwhile; ?>

<?php else: ?>

<tr>

<td
colspan="8"
class="text-center p-4 text-muted">

لا توجد مواعيد.

</td>

</tr>

<?php endif; ?>

</tbody>

</table>

</div>

</div>

<p class="small text-muted mt-2">

إجمالي المواعيد:

<strong>

<?= mysqli_num_rows($result); ?>

</strong>

</p>

</div>

</main>

</div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>