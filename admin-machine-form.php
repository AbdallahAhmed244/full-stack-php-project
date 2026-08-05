<?php

session_start();

require_once("auth.php");
requireLogin();
requireRole("admin");

require_once("config/database.php");

$error = "";
$machine = null;
$editing = false;
$id = "";

if (isset($_GET['id']) && !empty($_GET['id']))
{
    $id = mysqli_real_escape_string($conn, $_GET['id']);

    $query = "SELECT *
              FROM machines
              WHERE id='$id'";

    $result = mysqli_query($conn, $query);

    if (!$result)
    {
        die(mysqli_error($conn));
    }

    if (mysqli_num_rows($result) == 1)
    {
        $machine = mysqli_fetch_assoc($result);
        $editing = true;
    }
    else
    {
        header("Location: admin-machines.php");
        exit;
    }
}

if (isset($_POST['submit']))
{
    $machine_number = trim($_POST['machine_number'] ?? "");
    $center_id = $_POST['center_id'] ?? "";
    $status = $_POST['status'] ?? "available";
    $last_maintenance = $_POST['last_maintenance'] ?? "";

    $machine_number = mysqli_real_escape_string($conn, $machine_number);
    $center_id = mysqli_real_escape_string($conn, $center_id);
    $last_maintenance = mysqli_real_escape_string($conn, $last_maintenance);

    $allowed_statuses = ["available", "busy", "maintenance"];

    if (empty($machine_number))
    {
        $error = "من فضلك أدخل رقم الجهاز.";
    }
    elseif (!in_array($status, $allowed_statuses))
    {
        $error = "حالة الجهاز غير صحيحة.";
    }
    else
    {
        $check_query = "SELECT id
                         FROM machines
                         WHERE machine_number='$machine_number'";

        if ($editing)
        {
            $check_query .= " AND id!='$id'";
        }

        $check_result = mysqli_query($conn, $check_query);

        if (!$check_result)
        {
            $error = mysqli_error($conn);
        }
        elseif (mysqli_num_rows($check_result) > 0)
        {
            $error = "رقم الجهاز موجود بالفعل.";
        }
        else
        {
            $center_value = !empty($center_id)
                ? "'$center_id'"
                : "NULL";

            $maintenance_value = !empty($last_maintenance)
                ? "'$last_maintenance'"
                : "NULL";

            if ($editing)
            {
                $query = "UPDATE machines
                          SET machine_number='$machine_number',
                              center_id=$center_value,
                              status='$status',
                              last_maintenance=$maintenance_value
                          WHERE id='$id'";
            }
            else
            {
                $query = "INSERT INTO machines
                          (machine_number, center_id, status, last_maintenance)
                          VALUES
                          ('$machine_number',
                           $center_value,
                           '$status',
                           $maintenance_value)";
            }

            if (mysqli_query($conn, $query))
            {
                header("Location: admin-machines.php");
                exit;
            }
            else
            {
                $error = mysqli_error($conn);
            }
        }
    }
}

$centers_query = "SELECT id, name
                  FROM centers
                  ORDER BY name ASC";

$centers_result = mysqli_query($conn, $centers_query);

if (!$centers_result)
{
    die(mysqli_error($conn));
}

$title = $editing ? "تعديل بيانات الجهاز" : "إضافة جهاز جديد";

?>

<?php include "includes/head.php"; ?>

<body>

<div class="container-fluid">

<div class="row">

<?php include("includes/sidebar.php"); ?>

<main class="col-md-9 col-lg-10 p-0">

<?php include("includes/topbar.php"); ?>

<div class="p-4">

<div class="mb-3">

<a href="admin-machines.php" class="text-decoration-none">

← العودة للقائمة

</a>

</div>

<?php if (!empty($error)): ?>

<div class="alert alert-danger">

<?= htmlspecialchars($error); ?>

</div>

<?php endif; ?>

<div class="card p-4">

<h2 class="h5 mb-4">

<?= $editing ? "تعديل بيانات الجهاز" : "إضافة جهاز جديد"; ?>

</h2>

<form action="" method="post">

<div class="row g-3">

<div class="col-md-6">

<label class="form-label">

رقم الجهاز

</label>

<input
type="text"
name="machine_number"
class="form-control"
value="<?= htmlspecialchars($machine['machine_number'] ?? ''); ?>"
required>

</div>

<div class="col-md-6">

<label class="form-label">

المركز

</label>

<select
name="center_id"
class="form-select">

<option value="">



</option>

<?php while ($center = mysqli_fetch_assoc($centers_result)): ?>

<option
value="<?= $center['id']; ?>"
<?= isset($machine['center_id']) && $machine['center_id'] == $center['id'] ? "selected" : ""; ?>>

<?= htmlspecialchars($center['name']); ?>

</option>

<?php endwhile; ?>

</select>

</div>

<div class="col-md-6">

<label class="form-label">

الحالة

</label>

<select
name="status"
class="form-select"
required>

<option
value="available"
<?= ($machine['status'] ?? 'available') == "available" ? "selected" : ""; ?>>

متاح

</option>

<option
value="busy"
<?= ($machine['status'] ?? '') == "busy" ? "selected" : ""; ?>>

مشغول

</option>

<option
value="maintenance"
<?= ($machine['status'] ?? '') == "maintenance" ? "selected" : ""; ?>>

صيانة

</option>

</select>

</div>

<div class="col-md-6">

<label class="form-label">

آخر صيانة

</label>

<input
type="date"
name="last_maintenance"
class="form-control"
value="<?= htmlspecialchars($machine['last_maintenance'] ?? ''); ?>">

</div>

</div>

<div class="mt-4 d-flex gap-2">

<button
type="submit"
name="submit"
class="btn btn-primary">

<?= $editing ? "تحديث" : "حفظ"; ?>

</button>

<a
href="admin-machines.php"
class="btn btn-outline-secondary">

إلغاء

</a>

</div>

</form>

</div>

</div>

</main>

</div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>