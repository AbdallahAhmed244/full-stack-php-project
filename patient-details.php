<?php

session_start();

require_once("auth.php");
requireLogin();
requireRole("doctor");

require_once("config/database.php");

$doctor_user_id = $_SESSION['user_id'];

$query = "SELECT id
FROM doctors
WHERE user_id='$doctor_user_id'
LIMIT 1";

$result = mysqli_query($conn, $query);

$doctor = mysqli_fetch_assoc($result);

if (!$doctor)
{
    die("Doctor not found.");
}

if (!isset($_GET['id']) || !is_numeric($_GET['id']))
{
    header("Location: doctor-dashboard.php");
    exit;
}

$patient_id = mysqli_real_escape_string($conn, $_GET['id']);

$doctor_id = $doctor['id'];

$query = "SELECT patients.*
FROM patients

INNER JOIN appointments
ON patients.id = appointments.patient_id

WHERE patients.id='$patient_id'
AND appointments.doctor_id='$doctor_id'

LIMIT 1";

$result = mysqli_query($conn, $query);

if (!$result)
{
    die(mysqli_error($conn));
}

$patient = mysqli_fetch_assoc($result);

if (!$patient)
{
    header("Location: doctor-dashboard.php");
    exit;
}

$title = "بيانات المريض";

?>

<?php include "includes/head.php"; ?>

<body>

<div class="container-fluid">

    <div class="row">

        <?php include("includes/sidebar.php"); ?>

        <main class="col-md-9 col-lg-10 p-0">

            <?php include("includes/topbar.php"); ?>

            <div class="p-4">

                <div class="card p-4">

                    <h2 class="h5 mb-4">

                        بيانات المريض

                    </h2>

                    <div class="row">

                        <div class="col-md-6 mb-3">

                            <strong>

                                الاسم:

                            </strong>

                            <?= htmlspecialchars($patient['full_name'] ?? '—'); ?>

                        </div>

                        <div class="col-md-6 mb-3">

                            <strong>

                                الهاتف:

                            </strong>

                            <?= htmlspecialchars($patient['phone'] ?? '—'); ?>

                        </div>

                        <div class="col-md-6 mb-3">

                            <strong>

                                فصيلة الدم:

                            </strong>

                            <?= htmlspecialchars($patient['blood_type'] ?? '—'); ?>

                        </div>

                        <div class="col-md-6 mb-3">

                            <strong>

                                النوع:

                            </strong>

                            <?= htmlspecialchars($patient['gender'] ?? '—'); ?>

                        </div>

                        <div class="col-md-6 mb-3">

                            <strong>

                                تاريخ الميلاد:

                            </strong>

                            <?= htmlspecialchars($patient['birth_date'] ?? '—'); ?>

                        </div>

                        <div class="col-md-6 mb-3">

                            <strong>

                                العنوان:

                            </strong>

                            <?= htmlspecialchars($patient['address'] ?? '—'); ?>

                        </div>

                        <div class="col-md-6 mb-3">

                            <strong>

                                الحالة:

                            </strong>

                            <?php

                            if ($patient['status'] == "active")
                            {
                                echo '<span class="badge text-bg-success">نشط</span>';
                            }
                            else
                            {
                                echo '<span class="badge text-bg-secondary">غير نشط</span>';
                            }

                            ?>

                        </div>

                    </div>

                    <div class="mt-3">

                        <a
                            href="doctor-dashboard.php"
                            class="btn btn-secondary">

                            رجوع

                        </a>

                    </div>

                </div>

            </div>

        </main>

    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>