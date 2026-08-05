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

$title = "إدارة الأطباء";

$search = "";

if (isset($_GET['search']))
{
    $search = trim($_GET['search']);
}

$search_safe = mysqli_real_escape_string($conn, $search);

$query = "SELECT
doctors.id,
doctors.full_name,
doctors.specialty,
doctors.phone,
doctors.status,
users.email,
centers.name AS center_name

FROM doctors

LEFT JOIN users
ON doctors.user_id = users.id

LEFT JOIN centers
ON doctors.center_id = centers.id

WHERE
doctors.full_name LIKE '%$search_safe%'
OR doctors.specialty LIKE '%$search_safe%'
OR users.email LIKE '%$search_safe%'

ORDER BY doctors.id DESC";

$result = mysqli_query($conn, $query);

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

<form action="" method="get" class="d-flex gap-2">

<input
type="search"
name="search"
value="<?= htmlspecialchars($search); ?>"
class="form-control"
placeholder="بحث بالاسم / التخصص / البريد">

<button
type="submit"
class="btn btn-outline-primary">

بحث

</button>

</form>

<a
href="admin-doctor-form.php"
class="btn btn-primary">

إضافة طبيب

</a>

</div>

<div class="card">

<div class="table-responsive">

<table class="table table-hover align-middle mb-0">

<thead>

<tr>

<th>#</th>
<th>الاسم</th>
<th>التخصص</th>
<th>المركز</th>
<th>البريد</th>
<th>الهاتف</th>
<th>الحالة</th>
<th>الإجراءات</th>

</tr>

</thead>

<tbody>

<?php

if (mysqli_num_rows($result) > 0)
{

while ($row = mysqli_fetch_assoc($result))
{

?>

<tr>

<td>
<?= $row['id']; ?>
</td>

<td>
<?= htmlspecialchars($row['full_name']); ?>
</td>

<td>
<?= htmlspecialchars($row['specialty'] ?? '—'); ?>
</td>

<td class="small">
<?= htmlspecialchars($row['center_name'] ?? '—'); ?>
</td>

<td class="small">
<?= htmlspecialchars($row['email'] ?? '—'); ?>
</td>

<td>
<?= htmlspecialchars($row['phone'] ?? '—'); ?>
</td>

<td>

<?php

if ($row['status'] == "active")
{
    echo '<span class="badge text-bg-success">نشط</span>';
}
else
{
    echo '<span class="badge text-bg-secondary">غير نشط</span>';
}

?>

</td>

<td class="text-nowrap">

<a
href="admin-doctor-form.php?id=<?= $row['id']; ?>"
class="btn btn-sm btn-outline-primary">

تعديل

</a>

<?php

if ($row['status'] == "active")
{

?>

<a
href="admin-doctor-delete.php?id=<?= $row['id']; ?>"
class="btn btn-sm btn-outline-danger"
onclick="return confirm('هل أنت متأكد من إيقاف هذا الطبيب؟');">

حذف

</a>

<?php

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

<td colspan="8" class="text-center p-4">

لا توجد نتائج.

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