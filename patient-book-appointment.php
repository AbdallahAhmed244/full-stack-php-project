<?php

session_start();

require_once("auth.php");
requireLogin();
requireRole("patient");

require_once("config/database.php");

if ($_SESSION['role'] != "patient")
{
    header("Location: login.php");
    exit;
}

$title = "حجز موعد";

$error = "";

$user_id = $_SESSION['user_id'];

$query = "SELECT *
FROM patients
WHERE user_id='$user_id'";

$result = mysqli_query($conn, $query);

$patient = mysqli_fetch_assoc($result);

if (!$patient)
{
    die("Patient not found.");
}

$query = "SELECT *
FROM centers
ORDER BY name ASC";

$centers = mysqli_query($conn, $query);

$query = "SELECT *
FROM doctors
WHERE status='active'
ORDER BY full_name ASC";

$doctors = mysqli_query($conn, $query);

if (isset($_POST['submit']))
{
    $center_id = mysqli_real_escape_string($conn, $_POST['center_id']);
    $doctor_id = mysqli_real_escape_string($conn, $_POST['doctor_id']);
    $appointment_date = mysqli_real_escape_string($conn, $_POST['appointment_date']);
    $appointment_time = mysqli_real_escape_string($conn, $_POST['appointment_time']);
    $notes = mysqli_real_escape_string($conn, $_POST['notes']);

    if (
        !empty($center_id) &&
        !empty($doctor_id) &&
        !empty($appointment_date) &&
        !empty($appointment_time)
    )
    {
        $query = "SELECT id
        FROM appointments
        WHERE doctor_id='$doctor_id'
        AND appointment_date='$appointment_date'
        AND appointment_time='$appointment_time'
        AND status != 'cancelled'";

        $check = mysqli_query($conn, $query);

        if (mysqli_num_rows($check) > 0)
        {
            $error = "هذا الطبيب لديه موعد في نفس التاريخ والوقت.";
        }
        else
        {
            $patient_id = $patient['id'];

            $query = "INSERT INTO appointments
            (
                patient_id,
                doctor_id,
                center_id,
                appointment_date,
                appointment_time,
                notes,
                status
            )

            VALUES

            (
                '$patient_id',
                '$doctor_id',
                '$center_id',
                '$appointment_date',
                '$appointment_time',
                '$notes',
                'pending'
            )";

            if (mysqli_query($conn, $query))
            {
                $action = "Book Appointment";
                $details = "Patient booked a new appointment";

                $log_query = "INSERT INTO activity_logs
                (
                    user_id,
                    action,
                    details
                )

                VALUES

                (
                    '$user_id',
                    '$action',
                    '$details'
                )";

                mysqli_query($conn, $log_query);

                header("Location: patient-dashboard.php");
                exit;
            }
            else
            {
                $error = mysqli_error($conn);
            }
        }
    }
    else
    {
        $error = "من فضلك املئي جميع الحقول المطلوبة.";
    }
}

?>

<?php include("includes/head.php"); ?>

<body>

<div class="container-fluid">

<div class="row">

<?php include("includes/sidebar.php"); ?>

<main class="col-md-9 col-lg-10 p-0">

<?php include("includes/topbar.php"); ?>

<div class="p-4">

<div class="row justify-content-center">

<div class="col-lg-7">

<?php

if (!empty($error))
{
    echo '<div class="alert alert-danger">';
    echo htmlspecialchars($error);
    echo '</div>';
}

?>

<div class="card p-4">

<h2 class="h5 mb-3">
حجز جلسة غسيل كلوي جديدة
</h2>

<form action="" method="post">

<div class="row g-3">

<div class="col-md-6">

<label class="form-label">
المركز
</label>

<select
name="center_id"
class="form-select"
required>

<option value="">
اختر مركز
</option>

<?php

while ($center = mysqli_fetch_assoc($centers))
{

?>

<option
value="<?= $center['id']; ?>"
<?= isset($_POST['center_id']) && $_POST['center_id'] == $center['id'] ? 'selected' : ''; ?>>

<?= htmlspecialchars($center['name']); ?>

</option>

<?php

}

?>

</select>

</div>

<div class="col-md-6">

<label class="form-label">
الطبيب
</label>

<select
name="doctor_id"
class="form-select"
required>

<option value="">
اختر طبيب
</option>

<?php

while ($doctor = mysqli_fetch_assoc($doctors))
{

?>

<option
value="<?= $doctor['id']; ?>"
<?= isset($_POST['doctor_id']) && $_POST['doctor_id'] == $doctor['id'] ? 'selected' : ''; ?>>

<?= htmlspecialchars($doctor['full_name']); ?>

</option>

<?php

}

?>

</select>

</div>

<div class="col-md-6">

<label class="form-label">
التاريخ
</label>

<input
type="date"
name="appointment_date"
class="form-control"
min="<?= date('Y-m-d'); ?>"
value="<?= htmlspecialchars($_POST['appointment_date'] ?? ''); ?>"
required>

</div>

<div class="col-md-6">

<label class="form-label">
الوقت
</label>

<input
type="time"
name="appointment_time"
class="form-control"
value="<?= htmlspecialchars($_POST['appointment_time'] ?? ''); ?>"
required>

</div>

<div class="col-12">

<label class="form-label">
ملاحظات
</label>

<textarea
name="notes"
class="form-control"
rows="3"><?= htmlspecialchars($_POST['notes'] ?? ''); ?></textarea>

</div>

</div>

<div class="mt-4 d-flex gap-2">

<button
type="submit"
name="submit"
class="btn btn-primary">

تأكيد الحجز

</button>

<a
href="patient-dashboard.php"
class="btn btn-outline-secondary">

إلغاء

</a>

</div>

</form>

</div>

</div>

</div>

</div>

</main>

</div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>