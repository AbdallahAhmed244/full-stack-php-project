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

$error = "";
$editing = false;
$center_id = "";
$center = null;

$title = "إضافة مركز جديد";

if (isset($_GET['id']) && !empty($_GET['id']))
{
    $center_id = mysqli_real_escape_string($conn, $_GET['id']);

    $query = "SELECT *
              FROM centers
              WHERE id='$center_id'";

    $result = mysqli_query($conn, $query);

    if (!$result)
    {
        die(mysqli_error($conn));
    }

    if (mysqli_num_rows($result) == 1)
    {
        $center = mysqli_fetch_assoc($result);
        $editing = true;
        $title = "تعديل بيانات المركز";
    }
    else
    {
        header("Location: admin-centers.php");
        exit;
    }
}

if (isset($_POST['submit']))
{
    $name = mysqli_real_escape_string($conn, trim($_POST['name'] ?? ""));
    $address = mysqli_real_escape_string($conn, trim($_POST['address'] ?? ""));
    $phone = mysqli_real_escape_string($conn, trim($_POST['phone'] ?? ""));

    if (empty($name))
    {
        $error = "من فضلك أدخلي اسم المركز.";
    }
    else
    {
        if ($editing)
        {
            $query = "UPDATE centers
                      SET name='$name',
                          address='$address',
                          phone='$phone'
                      WHERE id='$center_id'";

            if (mysqli_query($conn, $query))
            {
                header("Location: admin-centers.php");
                exit;
            }
            else
            {
                $error = mysqli_error($conn);
            }
        }
        else
        {
            $query = "INSERT INTO centers
                      (name, address, phone)
                      VALUES
                      ('$name', '$address', '$phone')";

            if (mysqli_query($conn, $query))
            {
                header("Location: admin-centers.php");
                exit;
            }
            else
            {
                $error = mysqli_error($conn);
            }
        }
    }
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

<div class="mb-3">

<a href="admin-centers.php" class="text-decoration-none">

← العودة للقائمة

</a>

</div>

<?php

if (!empty($error))
{
    echo '<div class="alert alert-danger">';
    echo htmlspecialchars($error);
    echo '</div>';
}

?>

<div class="card p-4">

<h2 class="h5 mb-4">

<?= $editing ? "تعديل بيانات المركز" : "إضافة مركز جديد"; ?>

</h2>

<form action="" method="post">

<div class="row g-3">

<div class="col-md-6">

<label class="form-label">
اسم المركز
</label>

<input
type="text"
name="name"
class="form-control"
value="<?= htmlspecialchars($center['name'] ?? ($_POST['name'] ?? '')); ?>"
required>

</div>

<div class="col-md-6">

<label class="form-label">
الهاتف
</label>

<input
type="text"
name="phone"
class="form-control"
value="<?= htmlspecialchars($center['phone'] ?? ($_POST['phone'] ?? '')); ?>">

</div>

<div class="col-12">

<label class="form-label">
العنوان
</label>

<textarea
name="address"
class="form-control"
rows="3"><?= htmlspecialchars($center['address'] ?? ($_POST['address'] ?? '')); ?></textarea>

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
href="admin-centers.php"
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