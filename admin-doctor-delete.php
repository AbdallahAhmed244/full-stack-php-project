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

if (!isset($_GET['id']))
{
    header("Location: admin-doctors.php");
    exit;
}

$doctor_id = $_GET['id'];

$query = "UPDATE doctors
SET status='inactive'
WHERE id='$doctor_id'";

if (mysqli_query($conn, $query))
{
    header("Location: admin-doctors.php");
    exit;
}
else
{
    die(mysqli_error($conn));
}

?>