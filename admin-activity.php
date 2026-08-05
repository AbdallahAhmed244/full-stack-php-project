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

require_once("config/database.php");

$title = "سجل النشاط";

$search = "";

if (isset($_GET['search']))
{
    $search = trim($_GET['search']);
}

$search_safe = mysqli_real_escape_string($conn, $search);

$query = "SELECT
activity_logs.id,
activity_logs.action,
activity_logs.details,
activity_logs.created_at,
users.email

FROM activity_logs

LEFT JOIN users
ON activity_logs.user_id = users.id

WHERE
activity_logs.action LIKE '%$search_safe%'
OR activity_logs.details LIKE '%$search_safe%'
OR users.email LIKE '%$search_safe%'

ORDER BY activity_logs.id DESC";

$result = mysqli_query($conn, $query);

?>

<?php include "includes/head.php"; ?>

<body>

<div class="container-fluid">

<div class="row">

<?php include("includes/sidebar.php"); ?>

<main class="col-md-9 col-lg-10 p-0">

<?php
$title = "سجل النشاط";
include("includes/topbar.php");
?>

<div class="p-4">

<div class="mb-3">

<form action="" method="get" class="d-flex gap-2">

<input
type="search"
name="search"
value="<?= htmlspecialchars($search); ?>"
class="form-control"
placeholder="بحث في الإجراءات أو التفاصيل أو البريد">

<button
type="submit"
class="btn btn-outline-primary">

بحث

</button>

</form>

</div>

<div class="card">

<div class="table-responsive">

<table class="table table-sm table-hover align-middle mb-0">

<thead>

<tr>

<th>#</th>

<th>الوقت</th>

<th>المستخدم</th>

<th>الإجراء</th>

<th>التفاصيل</th>

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

<td class="small text-nowrap">
<?= htmlspecialchars($row['created_at']); ?>
</td>

<td class="small">
<?= htmlspecialchars($row['email'] ?? '—'); ?>
</td>

<td>
<code class="small">
<?= htmlspecialchars($row['action'] ?? '—'); ?>
</code>
</td>

<td class="small">
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

<td colspan="5" class="text-center p-4">

لا توجد سجلات.

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