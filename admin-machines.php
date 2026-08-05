<?php

session_start();

require_once("auth.php");
requireLogin();
requireRole("admin");

require_once("config/database.php");


$title = "إدارة الأجهزة";

$search = "";
$status_filter = "";

if (isset($_GET['search'])) {
    $search = trim($_GET['search']);
}

if (isset($_GET['status'])) {
    $status_filter = trim($_GET['status']);
}

$search_safe = mysqli_real_escape_string($conn, $search);
$status_safe = mysqli_real_escape_string($conn, $status_filter);

$query = "SELECT
machines.id,
machines.machine_number,
machines.status,
machines.last_maintenance,
centers.name AS center_name

FROM machines

LEFT JOIN centers
ON machines.center_id = centers.id

WHERE
(
machines.machine_number LIKE '%$search_safe%'
OR centers.name LIKE '%$search_safe%'
)";

if (!empty($status_filter)) {
    $query .= " AND machines.status='$status_safe'";
}

$query .= " ORDER BY machines.id DESC";

$result = mysqli_query($conn, $query);

if (!$result) {
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

<form action="" method="get" class="d-flex flex-wrap gap-2">

<input
type="search"
name="search"
value="<?= htmlspecialchars($search); ?>"
class="form-control"
placeholder="بحث برقم الجهاز / المركز">

<select
name="status"
class="form-select"
style="max-width:10rem">

<option value="">كل الحالات</option>

<option
value="available"
<?= $status_filter == "available" ? "selected" : ""; ?>>

متاح

</option>

<option
value="busy"
<?= $status_filter == "busy" ? "selected" : ""; ?>>

مشغول

</option>

<option
value="maintenance"
<?= $status_filter == "maintenance" ? "selected" : ""; ?>>

صيانة

</option>

</select>

<button
type="submit"
class="btn btn-outline-primary">

تصفية

</button>

</form>

<a
href="admin-machine-form.php"
class="btn btn-primary">

إضافة جهاز

</a>

</div>

<div class="card">

<div class="table-responsive">

<table class="table table-hover align-middle mb-0">

<thead>

<tr>

<th>#</th>
<th>الرقم</th>
<th>المركز</th>
<th>الحالة</th>
<th>المريض الحالي</th>
<th>آخر صيانة</th>
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
<code><?= htmlspecialchars($row['machine_number']); ?></code>
</td>

<td class="small">
<?= htmlspecialchars($row['center_name'] ?? '—'); ?>
</td>

<td>

<?php if ($row['status'] == "available"): ?>

<span class="badge text-bg-success">
متاح
</span>

<?php elseif ($row['status'] == "busy"): ?>

<span class="badge text-bg-warning">
مشغول
</span>

<?php else: ?>

<span class="badge text-bg-danger">
صيانة
</span>

<?php endif; ?>

</td>

<td class="small">
—
</td>

<td class="small">

<?= !empty($row['last_maintenance'])
    ? htmlspecialchars($row['last_maintenance'])
    : '—'; ?>

</td>

<td class="text-nowrap">

<a
href="admin-machine-form.php?id=<?= $row['id']; ?>"
class="btn btn-sm btn-outline-primary">

تعديل

</a>

<a
href="admin-machine-delete.php?id=<?= $row['id']; ?>"
class="btn btn-sm btn-outline-danger"
onclick="return confirm('هل أنت متأكد من حذف الجهاز؟');">

حذف

</a>

</td>

</tr>

<?php endwhile; ?>

<?php else: ?>

<tr>

<td
colspan="7"
class="text-center p-4">

لا توجد نتائج.

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