<?php

session_start();

require_once("auth.php");
requireLogin();
requireRole("doctor");

require_once("config/database.php");

$title = "مواعيدي";

$user_id = (int)$_SESSION['user_id'];

$query = "SELECT id, full_name
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

if (isset($_POST['update_status']))
{
    $appointment_id = (int)($_POST['appointment_id'] ?? 0);
    $status = $_POST['status'] ?? "";

    $allowed_statuses = [
        "approved",
        "completed",
        "cancelled"
    ];

    if ($appointment_id > 0 && in_array($status, $allowed_statuses))
    {
        $status = mysqli_real_escape_string($conn, $status);

        $query = "UPDATE appointments
                  SET status='$status'
                  WHERE id='$appointment_id'
                  AND doctor_id='$doctor_id'";

        if (!mysqli_query($conn, $query))
        {
            die(mysqli_error($conn));
        }
    }

    header("Location: doctor-appointments.php");
    exit;
}

$query = "SELECT
          appointments.id,
          appointments.appointment_date,
          appointments.appointment_time,
          appointments.notes,
          appointments.status,
          patients.full_name AS patient_name,
          centers.name AS center_name

          FROM appointments

          INNER JOIN patients
          ON appointments.patient_id = patients.id

          LEFT JOIN centers
          ON appointments.center_id = centers.id

          WHERE appointments.doctor_id='$doctor_id'

          ORDER BY
          appointments.appointment_date ASC,
          appointments.appointment_time ASC";

$appointments = mysqli_query($conn, $query);

if (!$appointments)
{
    die(mysqli_error($conn));
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

                <div class="d-flex justify-content-between align-items-center mb-3">

                    <h2 class="h5 mb-0">
                        مواعيدي
                    </h2>

                    <a
                        href="doctor-dashboard.php"
                        class="btn btn-outline-primary btn-sm">

                        لوحة التحكم

                    </a>

                </div>

                <div class="card">

                    <div class="table-responsive">

                        <table class="table table-hover align-middle mb-0">

                            <thead>

                                <tr>

                                    <th>التاريخ</th>
                                    <th>الوقت</th>
                                    <th>المريض</th>
                                    <th>المركز</th>
                                    <th>ملاحظات</th>
                                    <th>الحالة</th>
                                    <th>الإجراءات</th>

                                </tr>

                            </thead>

                            <tbody>

                                <?php if (mysqli_num_rows($appointments) > 0): ?>

                                    <?php while ($row = mysqli_fetch_assoc($appointments)): ?>

                                        <tr>

                                            <td>
                                                <?= htmlspecialchars($row['appointment_date'] ?? '—'); ?>
                                            </td>

                                            <td>
                                                <?= htmlspecialchars($row['appointment_time'] ?? '—'); ?>
                                            </td>

                                            <td>
                                                <?= htmlspecialchars($row['patient_name'] ?? '—'); ?>
                                            </td>

                                            <td>
                                                <?= htmlspecialchars($row['center_name'] ?? '—'); ?>
                                            </td>

                                            <td>
                                                <?= htmlspecialchars($row['notes'] ?? '—'); ?>
                                            </td>

                                            <td>

                                                <?php if ($row['status'] == "pending"): ?>

                                                    <span class="badge text-bg-warning">
                                                        قيد الانتظار
                                                    </span>

                                                <?php elseif ($row['status'] == "approved"): ?>

                                                    <span class="badge text-bg-primary">
                                                        مقبول
                                                    </span>

                                                <?php elseif ($row['status'] == "completed"): ?>

                                                    <span class="badge text-bg-success">
                                                        تمت
                                                    </span>

                                                <?php elseif ($row['status'] == "cancelled"): ?>

                                                    <span class="badge text-bg-danger">
                                                        ملغي
                                                    </span>

                                                <?php else: ?>

                                                    <span class="badge text-bg-secondary">
                                                        <?= htmlspecialchars($row['status']); ?>
                                                    </span>

                                                <?php endif; ?>

                                            </td>

                                            <td class="text-nowrap">

                                                <?php if ($row['status'] == "pending"): ?>

                                                    <form method="post" class="d-inline">

                                                        <input
                                                            type="hidden"
                                                            name="appointment_id"
                                                            value="<?= (int)$row['id']; ?>">

                                                        <input
                                                            type="hidden"
                                                            name="status"
                                                            value="approved">

                                                        <button
                                                            type="submit"
                                                            name="update_status"
                                                            class="btn btn-sm btn-outline-success">

                                                            قبول

                                                        </button>

                                                    </form>

                                                    <form method="post" class="d-inline">

                                                        <input
                                                            type="hidden"
                                                            name="appointment_id"
                                                            value="<?= (int)$row['id']; ?>">

                                                        <input
                                                            type="hidden"
                                                            name="status"
                                                            value="cancelled">

                                                        <button
                                                            type="submit"
                                                            name="update_status"
                                                            class="btn btn-sm btn-outline-danger">

                                                            إلغاء

                                                        </button>

                                                    </form>

                                                <?php elseif ($row['status'] == "approved"): ?>

                                                    <form method="post" class="d-inline">

                                                        <input
                                                            type="hidden"
                                                            name="appointment_id"
                                                            value="<?= (int)$row['id']; ?>">

                                                        <input
                                                            type="hidden"
                                                            name="status"
                                                            value="completed">

                                                        <button
                                                            type="submit"
                                                            name="update_status"
                                                            class="btn btn-sm btn-outline-success">

                                                            تمت الجلسة

                                                        </button>

                                                    </form>

                                                    <form method="post" class="d-inline">

                                                        <input
                                                            type="hidden"
                                                            name="appointment_id"
                                                            value="<?= (int)$row['id']; ?>">

                                                        <input
                                                            type="hidden"
                                                            name="status"
                                                            value="cancelled">

                                                        <button
                                                            type="submit"
                                                            name="update_status"
                                                            class="btn btn-sm btn-outline-danger">

                                                            إلغاء

                                                        </button>

                                                    </form>

                                                <?php elseif ($row['status'] == "cancelled"): ?>

                                                    <form method="post" class="d-inline">

                                                        <input
                                                            type="hidden"
                                                            name="appointment_id"
                                                            value="<?= (int)$row['id']; ?>">

                                                        <input
                                                            type="hidden"
                                                            name="status"
                                                            value="approved">

                                                        <button
                                                            type="submit"
                                                            name="update_status"
                                                            class="btn btn-sm btn-outline-success">

                                                            إعادة قبول

                                                        </button>

                                                    </form>

                                                <?php elseif ($row['status'] == "completed"): ?>

                                                    <span class="text-muted small">
                                                        تم إنهاء الموعد
                                                    </span>

                                                <?php endif; ?>

                                            </td>

                                        </tr>

                                    <?php endwhile; ?>

                                <?php else: ?>

                                    <tr>

                                        <td
                                            colspan="7"
                                            class="text-center text-muted p-4">

                                            لا توجد مواعيد.

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

<script src="assets/js/layout.js"></script>

</body>

</html>