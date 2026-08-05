<?php

session_start();

require_once("auth.php");
requireLogin();
requireRole("admin");

require_once("config/database.php");

if (!isset($_GET['id']) || !is_numeric($_GET['id']))
{
    header("Location: admin-machines.php");
    exit;
}

$id = (int) $_GET['id'];

$query = "DELETE FROM machines
          WHERE id='$id'";

if (mysqli_query($conn, $query))
{
    header("Location: admin-machines.php");
    exit;
}

die(mysqli_error($conn));

?>