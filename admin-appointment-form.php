<?php

session_start();

require_once("auth.php");
requireLogin();
requireRole("admin");

require_once("config/database.php");

$error = "";

$editing = false;
$appointment_id = "";

$patient_id = "";
$doctor_id = "";
$center_id = "";
$appointment_date = "";
$appointment_time = "";
$notes = "";
$status = "pending";

if (isset($_GET['id']) && is_numeric($_GET['id']))
{
    $appointment_id = (int)$_GET['id'];

    $query = "SELECT *
              FROM appointments
              WHERE id='$appointment_id'";

    $result = mysqli_query($conn, $query);

    if (!$result)
    {
        die(mysqli_error($conn));
    }

    if (mysqli_num_rows($result) == 1)
    {
        $appointment = mysqli_fetch_assoc($result);

        $editing = true;

        $patient_id = $appointment['patient_id'];
        $doctor_id = $appointment['doctor_id'];
        $center_id = $appointment['center_id'];
        $appointment_date = $appointment['appointment_date'];
        $appointment_time = $appointment['appointment_time'];
        $notes = $appointment['notes'];
        $status = $appointment['status'];
    }
    else
    {
        header("Location: admin-appointments.php");
        exit;
    }
}

if (isset($_POST['submit']))
{
    $patient_id = (int)($_POST['patient_id'] ?? 0);
    $doctor_id = (int)($_POST['doctor_id'] ?? 0);
    $center_id = (int)($_POST['center_id'] ?? 0);

    $appointment_date = mysqli_real_escape_string(
        $conn,
        $_POST['appointment_date'] ?? ""
    );

    $appointment_time = mysqli_real_escape_string(
        $conn,
        $_POST['appointment_time'] ?? ""
    );

    $notes = mysqli_real_escape_string(
        $conn,
        $_POST['notes'] ?? ""
    );

    $status = mysqli_real_escape_string(
        $conn,
        $_POST['status'] ?? "pending"
    );

    $allowed_statuses = [
        "pending",
        "approved",
        "completed",
        "cancelled"
    ];

    if (isset($_POST['appointment_id']) && !empty($_POST['appointment_id']))
    {
        $appointment_id = (int)$_POST['appointment_id'];
        $editing = true;
    }

    if (
        $patient_id <= 0 ||
        $doctor_id <= 0 ||
        $center_id <= 0 ||
        empty($appointment_date) ||
        empty($appointment_time)
    )
    {
        $error = "من فضلك املئي جميع الحقول المطلوبة.";
    }
    elseif (!in_array($status, $allowed_statuses))
    {
        $error = "حالة الموعد غير صحيحة.";
    }
    else
    {
        $check_patient = mysqli_query(
            $conn,
            "SELECT id FROM patients WHERE id='$patient_id'"
        );

        $check_doctor = mysqli_query(
            $conn,
            "SELECT id FROM doctors WHERE id='$doctor_id'"
        );

        $check_center = mysqli_query(
            $conn,
            "SELECT id FROM centers WHERE id='$center_id'"
        );

        if (
            mysqli_num_rows($check_patient) == 0 ||
            mysqli_num_rows($check_doctor) == 0 ||
            mysqli_num_rows($check_center) == 0
        )
        {
            $error = "المريض أو الطبيب أو المركز غير موجود.";
        }
        else
        {
            $check_query = "SELECT id
                            FROM appointments
                            WHERE doctor_id='$doctor_id'
                            AND appointment_date='$appointment_date'
                            AND appointment_time='$appointment_time'
                            AND status IN ('pending','approved')";

            if ($editing)
            {
                $check_query .= " AND id != '$appointment_id'";
            }

            $check_result = mysqli_query($conn, $check_query);

            if (mysqli_num_rows($check_result) > 0)
            {
                $error = "هذا الطبيب لديه موعد آخر في نفس التاريخ والوقت.";
            }
            else
            {
                if ($editing)
                {
                    $query = "UPDATE appointments SET
                              patient_id='$patient_id',
                              doctor_id='$doctor_id',
                              center_id='$center_id',
                              appointment_date='$appointment_date',
                              appointment_time='$appointment_time',
                              notes='$notes',
                              status='$status'
                              WHERE id='$appointment_id'";

                    if (mysqli_query($conn, $query))
                    {
                        header("Location: admin-appointments.php");
                        exit;
                    }
                    else
                    {
                        $error = mysqli_error($conn);
                    }
                }
                else
                {
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
                        '$status'
                    )";

                    if (mysqli_query($conn, $query))
                    {
                        header("Location: admin-appointments.php");
                        exit;
                    }
                    else
                    {
                        $error = mysqli_error($conn);
                    }
                }
            }
        }
    }
}

$title = $editing ? "تعديل الموعد" : "إضافة موعد";

$patients_query = "SELECT id, full_name
                   FROM patients
                   WHERE status='active'
                   ORDER BY full_name ASC";

$patients_result = mysqli_query($conn, $patients_query);

$doctors_query = "SELECT id, full_name
                  FROM doctors
                  WHERE status='active'
                  ORDER BY full_name ASC";

$doctors_result = mysqli_query($conn, $doctors_query);

$centers_query = "SELECT id, name
                  FROM centers
                  ORDER BY name ASC";

$centers_result = mysqli_query($conn, $centers_query);

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

<a
href="admin-appointments.php"
class="text-decoration-none">

← العودة للمواعيد

</a>

</div>

<?php if (!empty($error)): ?>

<div class="alert alert-danger">

<?= htmlspecialchars($error); ?>

</div>

<?php endif; ?>

<div class="card p-4">

<h2 class="h5 mb-4">

<?= $editing ? "تعديل بيانات الموعد" : "إضافة موعد جديد"; ?>

</h2>

<form action="" method="post">

<?php if ($editing): ?>

<input
type="hidden"
name="appointment_id"
value="<?= $appointment_id; ?>">

<?php endif; ?>

<div class="row g-3">

<div class="col-md-6">

<label class="form-label">
المريض
</label>

<select
name="patient_id"
class="form-select"
required>

<option value="">
اختر المريض
</option>

<?php while ($patient = mysqli_fetch_assoc($patients_result)): ?>

<option
value="<?= $patient['id']; ?>"
<?= $patient_id == $patient['id'] ? "selected" : ""; ?>>

<?= htmlspecialchars($patient['full_name']); ?>

</option>

<?php endwhile; ?>

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
اختر الطبيب
</option>

<?php while ($doctor = mysqli_fetch_assoc($doctors_result)): ?>

<option
value="<?= $doctor['id']; ?>"
<?= $doctor_id == $doctor['id'] ? "selected" : ""; ?>>

<?= htmlspecialchars($doctor['full_name']); ?>

</option>

<?php endwhile; ?>

</select>

</div>

<div class="col-md-6">

<label class="form-label">
المركز
</label>

<select
name="center_id"
class="form-select"
required>

<option value="">
اختر المركز
</option>

<?php while ($center = mysqli_fetch_assoc($centers_result)): ?>

<option
value="<?= $center['id']; ?>"
<?= $center_id == $center['id'] ? "selected" : ""; ?>>

<?= htmlspecialchars($center['name']); ?>

</option>

<?php endwhile; ?>

</select>

</div>

<div class="col-md-3">

<label class="form-label">
التاريخ
</label>

<input
type="date"
name="appointment_date"
class="form-control"
value="<?= htmlspecialchars($appointment_date); ?>"
min="<?= date('Y-m-d'); ?>"
required>

</div>

<div class="col-md-3">

<label class="form-label">
الوقت
</label>

<input
type="time"
name="appointment_time"
class="form-control"
value="<?= htmlspecialchars($appointment_time); ?>"
required>

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
value="pending"
<?= $status == "pending" ? "selected" : ""; ?>>

بانتظار التأكيد

</option>

<option
value="approved"
<?= $status == "approved" ? "selected" : ""; ?>>

مؤكد

</option>

<option
value="completed"
<?= $status == "completed" ? "selected" : ""; ?>>

تمت

</option>

<option
value="cancelled"
<?= $status == "cancelled" ? "selected" : ""; ?>>

ملغي

</option>

</select>

</div>

<div class="col-12">

<label class="form-label">
ملاحظات
</label>

<textarea
name="notes"
class="form-control"
rows="4"
placeholder="اكتبي أي ملاحظات خاصة بالموعد"><?= htmlspecialchars($notes); ?></textarea>

</div>

</div>

<div class="mt-4 d-flex gap-2">

<button
type="submit"
name="submit"
class="btn btn-primary">

<?= $editing ? "تحديث الموعد" : "حفظ الموعد"; ?>

</button>

<a
href="admin-appointments.php"
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