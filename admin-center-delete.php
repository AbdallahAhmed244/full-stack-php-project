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
    header("Location: admin-centers.php");
    exit;
}

$center_id = mysqli_real_escape_string($conn, $_GET['id']);

$query = "SELECT id
          FROM doctors
          WHERE center_id='$center_id'
          LIMIT 1";

$result = mysqli_query($conn, $query);

if (!$result)
{
    die(mysqli_error($conn));
}

if (mysqli_num_rows($result) > 0)
{
    header("Location: admin-centers.php");
    exit;
}

$query = "SELECT id
          FROM appointments
          WHERE center_id='$center_id'
          LIMIT 1";

$result = mysqli_query($conn, $query);

if (!$result)
{
    die(mysqli_error($conn));
}

if (mysqli_num_rows($result) > 0)
{
    header("Location: admin-centers.php");
    exit;
}

$query = "DELETE FROM centers
          WHERE id='$center_id'";

if (mysqli_query($conn, $query))
{
    header("Location: admin-centers.php");
    exit;
}
else
{
    die(mysqli_error($conn));
}

?>