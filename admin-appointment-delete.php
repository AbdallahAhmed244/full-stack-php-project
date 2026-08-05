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

if (!isset($_GET['id']) || !is_numeric($_GET['id']))
{
    header("Location: admin-appointments.php");
    exit;
}

$appointment_id = (int)$_GET['id'];


$query = "SELECT id, patient_id, doctor_id
          FROM appointments
          WHERE id='$appointment_id'";

$result = mysqli_query($conn, $query);

if (!$result)
{
    die(mysqli_error($conn));
}

if (mysqli_num_rows($result) == 0)
{
    header("Location: admin-appointments.php");
    exit;
}

$appointment = mysqli_fetch_assoc($result);



$query = "DELETE FROM appointments
          WHERE id='$appointment_id'";

if (mysqli_query($conn, $query))
{
   

    $user_id = (int)$_SESSION['user_id'];

    $action = mysqli_real_escape_string(
        $conn,
        "Delete Appointment"
    );

    $details = mysqli_real_escape_string(
        $conn,
        "Admin deleted appointment #" . $appointment_id
    );

    $log_query = "INSERT INTO activity_logs
                  (user_id, action, details)
                  VALUES
                  ('$user_id', '$action', '$details')";

    mysqli_query($conn, $log_query);

    header("Location: admin-appointments.php");
    exit;
}
else
{
    die("خطأ أثناء حذف الموعد: " . mysqli_error($conn));
}

?>