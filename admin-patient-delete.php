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

if (!isset($_GET['id']) || empty($_GET['id']))
{
    header("Location: admin-patients.php");
    exit;
}

$patient_id = mysqli_real_escape_string($conn, $_GET['id']);

$query = "SELECT user_id
          FROM patients
          WHERE id='$patient_id'";

$result = mysqli_query($conn, $query);

if (!$result)
{
    die("خطأ في البحث عن المريض: " . mysqli_error($conn));
}

if (mysqli_num_rows($result) != 1)
{
    header("Location: admin-patients.php");
    exit;
}

$row = mysqli_fetch_assoc($result);

$user_id = $row['user_id'];

mysqli_begin_transaction($conn);

$query = "DELETE FROM appointments
          WHERE patient_id='$patient_id'";

if (!mysqli_query($conn, $query))
{
    mysqli_rollback($conn);
    die("خطأ في حذف مواعيد المريض: " . mysqli_error($conn));
}

$query = "DELETE FROM activity_logs
          WHERE user_id='$user_id'";

if (!mysqli_query($conn, $query))
{
    mysqli_rollback($conn);
    die("خطأ في حذف سجل النشاط: " . mysqli_error($conn));
}

$query = "DELETE FROM patients
          WHERE id='$patient_id'";

if (!mysqli_query($conn, $query))
{
    mysqli_rollback($conn);
    die("خطأ في حذف المريض: " . mysqli_error($conn));
}

$query = "DELETE FROM users
          WHERE id='$user_id'";

if (!mysqli_query($conn, $query))
{
    mysqli_rollback($conn);
    die("خطأ في حذف حساب المستخدم: " . mysqli_error($conn));
}

mysqli_commit($conn);

header("Location: admin-patients.php");
exit;

?>