<?php

session_start();

require_once("auth.php");
requireLogin();
requireRole("doctor");

require_once("config/database.php");

$title = "لوحة تحكم الطبيب";

$user_id = (int)$_SESSION['user_id'];

$query = "SELECT *
          FROM doctors
          WHERE user_id='$user_id'
          LIMIT 1";

$result = mysqli_query($conn, $query);

if (!$result)
{
    die(mysqli_error($conn));
}

$doctor = mysqli_fetch_assoc($result);

if (!$doctor)
{
    die("Doctor not found.");
}

$doctor_id = (int)$doctor['id'];

$query = "SELECT DISTINCT patients.*
          FROM patients
          INNER JOIN appointments
          ON patients.id = appointments.patient_id
          WHERE appointments.doctor_id='$doctor_id'
          AND patients.status='active'
          ORDER BY patients.full_name ASC";

$patients = mysqli_query($conn, $query);

if (!$patients)
{
    die(mysqli_error($conn));
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

                <div class="alert alert-info">

                    مرحباً

                    <strong>
                        <?= htmlspecialchars($doctor['full_name']); ?>
                    </strong>

                    ، هذه لوحة تحكم الطبيب.

                </div>

                <a
                    href="doctor-appointments.php"
                    class="btn btn-primary mb-3">

                    مواعيدي

                </a>

                <div class="card p-3">

                    <h2 class="h6 mb-3">
                        مرضاي الحاليون
                    </h2>

                    <div class="table-responsive">

                        <table class="table table-hover align-middle mb-0">

                            <thead>

                                <tr>

                                    <th>الاسم</th>
                                    <th>الهاتف</th>
                                    <th>فصيلة الدم</th>
                                    <th>الحالة</th>
                                    <th>الإجراء</th>

                                </tr>

                            </thead>

                            <tbody>

                                <?php if (mysqli_num_rows($patients) > 0): ?>

                                    <?php while ($row = mysqli_fetch_assoc($patients)): ?>

                                        <tr>

                                            <td>
                                                <?= htmlspecialchars($row['full_name']); ?>
                                            </td>

                                            <td>
                                                <?= htmlspecialchars($row['phone'] ?? '—'); ?>
                                            </td>

                                            <td>
                                                <?= htmlspecialchars($row['blood_type'] ?? '—'); ?>
                                            </td>

                                            <td>

                                                <?php if ($row['status'] == "active"): ?>

                                                    <span class="badge text-bg-success">
                                                        نشط
                                                    </span>

                                                <?php else: ?>

                                                    <span class="badge text-bg-secondary">
                                                        غير نشط
                                                    </span>

                                                <?php endif; ?>

                                            </td>

                                            <td>

                                                <a
                                                    href="patient-details.php?id=<?= (int)$row['id']; ?>"
                                                    class="btn btn-primary btn-sm">

                                                    عرض البيانات

                                                </a>

                                            </td>

                                        </tr>

                                    <?php endwhile; ?>

                                <?php else: ?>

                                    <tr>

                                        <td
                                            colspan="5"
                                            class="text-center text-muted py-4">

                                            لا يوجد مرضى مرتبطون بمواعيدك حالياً.

                                        </td>

                                    </tr>

                                <?php endif; ?>

                            </tbody>

                        </table>

                    </div>

                </div>

            </div>

        </main>

    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>