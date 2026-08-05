<?php

session_start();

require_once("auth.php");
requireLogin();
requireRole("admin");

require_once("config/database.php");

$title = "إدارة المرضى";

$error = "";

$is_edit = false;
$patient_id = "";
$user_id = "";

$full_name = "";
$email = "";
$phone = "";
$password = "";
$blood_type = "";
$gender = "";
$birth_date = "";
$address = "";
$status = "active";

if (isset($_GET['id'])) {

    $patient_id = $_GET['id'];
    $is_edit = true;

    $query = "SELECT patients.*, users.email
              FROM patients
              INNER JOIN users ON patients.user_id = users.id
              WHERE patients.id = '$patient_id'";

    $result = mysqli_query($conn, $query);

    if (!$result) {
        die(mysqli_error($conn));
    }

    if (mysqli_num_rows($result) == 1) {

        $row = mysqli_fetch_assoc($result);

        $user_id = $row['user_id'];
        $full_name = $row['full_name'];
        $email = $row['email'];
        $phone = $row['phone'];
        $blood_type = $row['blood_type'];
        $gender = $row['gender'];
        $birth_date = $row['birth_date'];
        $address = $row['address'];
        $status = $row['status'];

    } else {

        header("Location: admin-patients.php");
        exit;

    }
}

if (isset($_POST['submit'])) {

    $full_name = mysqli_real_escape_string($conn, $_POST['full_name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $phone = mysqli_real_escape_string($conn, $_POST['phone']);
    $password = $_POST['password'];
    $blood_type = mysqli_real_escape_string($conn, $_POST['blood_type']);
    $gender = mysqli_real_escape_string($conn, $_POST['gender']);
    $birth_date = mysqli_real_escape_string($conn, $_POST['birth_date']);
    $address = mysqli_real_escape_string($conn, $_POST['address']);
    $status = mysqli_real_escape_string($conn, $_POST['status']);

    if (empty($full_name) || empty($email)) {

        $error = "Please fill all required fields.";

    } else {

        if (isset($_POST['patient_id']) && !empty($_POST['patient_id'])) {

            $patient_id = $_POST['patient_id'];
            $is_edit = true;

            $query = "SELECT user_id
                      FROM patients
                      WHERE id='$patient_id'";

            $result = mysqli_query($conn, $query);

            if (!$result) {
                $error = mysqli_error($conn);
            } elseif (mysqli_num_rows($result) == 1) {

                $row = mysqli_fetch_assoc($result);
                $user_id = $row['user_id'];

                $query = "SELECT id
                          FROM users
                          WHERE email='$email'
                          AND id != '$user_id'";

                $result = mysqli_query($conn, $query);

                if (!$result) {

                    $error = mysqli_error($conn);

                } elseif (mysqli_num_rows($result) > 0) {

                    $error = "Email already exists.";

                } else {

                    if (!empty($password)) {

                        $password_hash = password_hash($password, PASSWORD_DEFAULT);

                        $query = "UPDATE users
                                  SET email='$email',
                                      password='$password_hash'
                                  WHERE id='$user_id'";

                    } else {

                        $query = "UPDATE users
                                  SET email='$email'
                                  WHERE id='$user_id'";

                    }

                    if (mysqli_query($conn, $query)) {

                        $query = "UPDATE patients
                                  SET full_name='$full_name',
                                      phone='$phone',
                                      blood_type='$blood_type',
                                      gender='$gender',
                                      birth_date='$birth_date',
                                      address='$address',
                                      status='$status'
                                  WHERE id='$patient_id'";

                        if (mysqli_query($conn, $query)) {

                            header("Location: admin-patients.php");
                            exit;

                        } else {

                            $error = mysqli_error($conn);

                        }

                    } else {

                        $error = mysqli_error($conn);

                    }
                }

            } else {

                $error = "Patient not found.";

            }

        } else {

            if (empty($password)) {

                $error = "Please enter a password.";

            } else {

                $query = "SELECT id
                          FROM users
                          WHERE email='$email'";

                $result = mysqli_query($conn, $query);

                if (!$result) {

                    $error = mysqli_error($conn);

                } elseif (mysqli_num_rows($result) > 0) {

                    $error = "Email already exists.";

                } else {

                    $username = "patient" . time();
                    $password_hash = password_hash($password, PASSWORD_DEFAULT);

                    $query = "INSERT INTO users
                              (username, email, password, role, image)

                              VALUES

                              ('$username',
                              '$email',
                              '$password_hash',
                              'patient',
                              'default.png')";

                    if (mysqli_query($conn, $query)) {

                        $user_id = mysqli_insert_id($conn);

                        $query = "INSERT INTO patients
                                  (user_id, full_name, phone, blood_type, gender, birth_date, address, status)

                                  VALUES

                                  ('$user_id',
                                  '$full_name',
                                  '$phone',
                                  '$blood_type',
                                  '$gender',
                                  '$birth_date',
                                  '$address',
                                  '$status')";

                        if (mysqli_query($conn, $query)) {

                            header("Location: admin-patients.php");
                            exit;

                        } else {

                            $error = mysqli_error($conn);

                        }

                    } else {

                        $error = mysqli_error($conn);

                    }
                }
            }
        }
    }
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

                <div class="mb-3">

                    <a
                        href="admin-patients.php"
                        class="text-decoration-none">

                        ← العودة للقائمة

                    </a>

                </div>

                <?php if (!empty($error)): ?>

                    <div class="alert alert-danger">

                        <?= htmlspecialchars($error); ?>

                    </div>

                <?php endif; ?>

                <div class="card p-4">

                    <h2 class="h5 mb-4">

                        <?= $is_edit ? "تعديل بيانات المريض" : "إضافة مريض جديد"; ?>

                    </h2>

                    <form action="" method="post">

                        <?php if ($is_edit): ?>

                            <input
                                type="hidden"
                                name="patient_id"
                                value="<?= $patient_id; ?>">

                        <?php endif; ?>

                        <div class="row g-3">

                            <div class="col-md-6">

                                <label class="form-label">
                                    الاسم الكامل
                                </label>

                                <input
                                    type="text"
                                    name="full_name"
                                    class="form-control"
                                    value="<?= htmlspecialchars($full_name); ?>"
                                    required>

                            </div>

                            <div class="col-md-6">

                                <label class="form-label">
                                    البريد الإلكتروني
                                </label>

                                <input
                                    type="email"
                                    name="email"
                                    class="form-control"
                                    value="<?= htmlspecialchars($email); ?>"
                                    required>

                            </div>

                            <div class="col-md-6">

                                <label class="form-label">
                                    الهاتف
                                </label>

                                <input
                                    type="text"
                                    name="phone"
                                    class="form-control"
                                    value="<?= htmlspecialchars($phone); ?>">

                            </div>

                            <div class="col-md-6">

                                <label class="form-label">
                                    كلمة المرور
                                </label>

                                <input
                                    type="password"
                                    name="password"
                                    class="form-control"
                                    <?= !$is_edit ? "required" : ""; ?>>

                                <?php if ($is_edit): ?>

                                    <small class="text-muted">
                                        اتركيها فارغة إذا كنتِ لا تريدين تغيير كلمة المرور.
                                    </small>

                                <?php endif; ?>

                            </div>

                            <div class="col-md-4">

                                <label class="form-label">
                                    تاريخ الميلاد
                                </label>

                                <input
                                    type="date"
                                    name="birth_date"
                                    class="form-control"
                                    value="<?= htmlspecialchars($birth_date); ?>">

                            </div>

                            <div class="col-md-4">

                                <label class="form-label">
                                    الجنس
                                </label>

                                <select
                                    name="gender"
                                    class="form-select">

                                    <option value="">
                                        اختر
                                    </option>

                                    <option
                                        value="male"
                                        <?= $gender == "male" ? "selected" : ""; ?>>

                                        ذكر

                                    </option>

                                    <option
                                        value="female"
                                        <?= $gender == "female" ? "selected" : ""; ?>>

                                        أنثى

                                    </option>

                                </select>

                            </div>

                            <div class="col-md-4">

                                <label class="form-label">
                                    فصيلة الدم
                                </label>

                                <select
                                    name="blood_type"
                                    class="form-select">

                                    <option value="">
                                        اختر
                                    </option>

                                    <option value="A+" <?= $blood_type == "A+" ? "selected" : ""; ?>>
                                        A+
                                    </option>

                                    <option value="A-" <?= $blood_type == "A-" ? "selected" : ""; ?>>
                                        A-
                                    </option>

                                    <option value="B+" <?= $blood_type == "B+" ? "selected" : ""; ?>>
                                        B+
                                    </option>

                                    <option value="B-" <?= $blood_type == "B-" ? "selected" : ""; ?>>
                                        B-
                                    </option>

                                    <option value="AB+" <?= $blood_type == "AB+" ? "selected" : ""; ?>>
                                        AB+
                                    </option>

                                    <option value="AB-" <?= $blood_type == "AB-" ? "selected" : ""; ?>>
                                        AB-
                                    </option>

                                    <option value="O+" <?= $blood_type == "O+" ? "selected" : ""; ?>>
                                        O+
                                    </option>

                                    <option value="O-" <?= $blood_type == "O-" ? "selected" : ""; ?>>
                                        O-
                                    </option>

                                </select>

                            </div>

                            <div class="col-md-6">

                                <label class="form-label">
                                    الحالة
                                </label>

                                <select
                                    name="status"
                                    class="form-select">

                                    <option
                                        value="active"
                                        <?= $status == "active" ? "selected" : ""; ?>>

                                        نشط

                                    </option>

                                    <option
                                        value="inactive"
                                        <?= $status == "inactive" ? "selected" : ""; ?>>

                                        غير نشط

                                    </option>

                                </select>

                            </div>

                            <div class="col-12">

                                <label class="form-label">
                                    العنوان
                                </label>

                                <input
                                    type="text"
                                    name="address"
                                    class="form-control"
                                    value="<?= htmlspecialchars($address); ?>">

                            </div>

                        </div>

                        <div class="mt-4 d-flex gap-2">

                            <button
                                type="submit"
                                name="submit"
                                class="btn btn-primary">

                                <?= $is_edit ? "تحديث" : "حفظ"; ?>

                            </button>

                            <a
                                href="admin-patients.php"
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