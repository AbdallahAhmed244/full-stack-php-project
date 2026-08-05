<?php

session_start();

require_once("auth.php");

requireLogin();
requireRole("admin");

require_once("config/database.php");

$error = "";
$editing = false;
$doctor_id = "";
$doctor = null;

if (isset($_GET['id']))
{
    $doctor_id = $_GET['id'];

    $query = "SELECT doctors.*, users.email, users.image, users.id AS user_id
              FROM doctors
              LEFT JOIN users ON doctors.user_id = users.id
              WHERE doctors.id='$doctor_id'";

    $result = mysqli_query($conn, $query);

    if (mysqli_num_rows($result) == 1)
    {
        $doctor = mysqli_fetch_assoc($result);
        $editing = true;
    }
    else
    {
        header("Location: admin-doctors.php");
        exit;
    }
}

$centers_query = "SELECT * FROM centers ORDER BY id DESC";
$centers_result = mysqli_query($conn, $centers_query);

if (isset($_POST['submit']))
{
    $full_name = mysqli_real_escape_string($conn, $_POST['full_name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $specialty = mysqli_real_escape_string($conn, $_POST['specialty']);
    $phone = mysqli_real_escape_string($conn, $_POST['phone']);
    $center_id = $_POST['center_id'];
    $status = $_POST['status'];
    $password = $_POST['password'];
    $password_confirmation = $_POST['password_confirmation'];

    if (empty($full_name) || empty($email) || empty($specialty) || empty($phone))
    {
        $error = "Please fill all required fields.";
    }
    elseif ($password !== $password_confirmation)
    {
        $error = "Passwords do not match.";
    }
    else
    {
        $image_name = "";

        if (isset($_FILES['image']) && $_FILES['image']['error'] != UPLOAD_ERR_NO_FILE)
        {
            if ($_FILES['image']['error'] != UPLOAD_ERR_OK)
            {
                $error = "حدث خطأ أثناء رفع الصورة.";
            }
            else
            {
                $allowed_types = [
                    "image/jpeg",
                    "image/png",
                    "image/webp"
                ];

                $file_type = $_FILES['image']['type'];
                $file_size = $_FILES['image']['size'];

                if (!in_array($file_type, $allowed_types))
                {
                    $error = "يسمح فقط بصور JPG أو PNG أو WEBP.";
                }
                elseif ($file_size > 2 * 1024 * 1024)
                {
                    $error = "حجم الصورة يجب ألا يتجاوز 2MB.";
                }
                else
                {
                    $extension = strtolower(
                        pathinfo(
                            $_FILES['image']['name'],
                            PATHINFO_EXTENSION
                        )
                    );

                    $image_name = "doctor_" . time() . "_" . rand(1000, 9999) . "." . $extension;

                    $upload_folder = "uploads/doctors/";
                    $upload_path = $upload_folder . $image_name;

                    if (!is_dir($upload_folder))
                    {
                        mkdir($upload_folder, 0777, true);
                    }

                    if (!move_uploaded_file($_FILES['image']['tmp_name'], $upload_path))
                    {
                        $error = "فشل رفع الصورة.";
                    }
                }
            }
        }

        if (empty($error))
        {
            if ($editing)
            {
                $user_id = $doctor['user_id'];

                $query = "SELECT id
                          FROM users
                          WHERE email='$email'
                          AND id!='$user_id'";

                $result = mysqli_query($conn, $query);

                if (mysqli_num_rows($result) > 0)
                {
                    $error = "Email already exists.";
                }
                else
                {
                    if (!empty($password))
                    {
                        $password_hash = password_hash(
                            $password,
                            PASSWORD_DEFAULT
                        );

                        $query = "UPDATE users
                                  SET email='$email',
                                      password='$password_hash'
                                  WHERE id='$user_id'";
                    }
                    else
                    {
                        $query = "UPDATE users
                                  SET email='$email'
                                  WHERE id='$user_id'";
                    }

                    if (mysqli_query($conn, $query))
                    {
                        if (!empty($image_name))
                        {
                            $old_image = $doctor['image'];

                            $query = "UPDATE users
                                      SET image='$image_name'
                                      WHERE id='$user_id'";

                            mysqli_query($conn, $query);

                            if (
                                !empty($old_image) &&
                                $old_image != "default.png"
                            )
                            {
                                $old_path = "uploads/doctors/" . $old_image;

                                if (file_exists($old_path))
                                {
                                    unlink($old_path);
                                }
                            }
                        }

                        $center_value = ($center_id !== "")
                            ? "'$center_id'"
                            : "NULL";

                        $query = "UPDATE doctors
                                  SET full_name='$full_name',
                                      specialty='$specialty',
                                      phone='$phone',
                                      center_id=$center_value,
                                      status='$status'
                                  WHERE id='$doctor_id'";

                        if (mysqli_query($conn, $query))
                        {
                            header("Location: admin-doctors.php");
                            exit;
                        }
                        else
                        {
                            $error = mysqli_error($conn);
                        }
                    }
                    else
                    {
                        $error = mysqli_error($conn);
                    }
                }
            }
            else
            {
                if (empty($password))
                {
                    $error = "Please enter a password.";
                }
                else
                {
                    $query = "SELECT id
                              FROM users
                              WHERE email='$email'";

                    $result = mysqli_query($conn, $query);

                    if (mysqli_num_rows($result) > 0)
                    {
                        $error = "Email already exists.";
                    }
                    else
                    {
                        $username = "doctor" . time();

                        $password_hash = password_hash(
                            $password,
                            PASSWORD_DEFAULT
                        );

                        if (empty($image_name))
                        {
                            $image_name = "default.png";
                        }

                        $query = "INSERT INTO users
                                  (username, email, password, role, image)
                                  VALUES
                                  ('$username',
                                   '$email',
                                   '$password_hash',
                                   'doctor',
                                   '$image_name')";

                        if (mysqli_query($conn, $query))
                        {
                            $user_id = mysqli_insert_id($conn);

                            $center_value = ($center_id !== "")
                                ? "'$center_id'"
                                : "NULL";

                            $query = "INSERT INTO doctors
                                      (user_id, full_name, specialty, phone, center_id, status)
                                      VALUES
                                      ('$user_id',
                                       '$full_name',
                                       '$specialty',
                                       '$phone',
                                       $center_value,
                                       '$status')";

                            if (mysqli_query($conn, $query))
                            {
                                header("Location: admin-doctors.php");
                                exit;
                            }
                            else
                            {
                                $error = mysqli_error($conn);
                            }
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

                    <a href="admin-doctors.php" class="text-decoration-none">
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
                        <?= $editing ? "تعديل بيانات الطبيب" : "إضافة طبيب جديد"; ?>
                    </h2>

                    <form action="" method="post" enctype="multipart/form-data">

                        <div class="row g-3">

                            <div class="col-md-6">

                                <label class="form-label">
                                    الاسم الكامل
                                </label>

                                <input
                                    type="text"
                                    name="full_name"
                                    class="form-control"
                                    value="<?= htmlspecialchars($doctor['full_name'] ?? ''); ?>"
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
                                    value="<?= htmlspecialchars($doctor['email'] ?? ''); ?>"
                                    required>

                            </div>

                            <div class="col-md-6">

                                <label class="form-label">
                                    التخصص
                                </label>

                                <input
                                    type="text"
                                    name="specialty"
                                    class="form-control"
                                    value="<?= htmlspecialchars($doctor['specialty'] ?? ''); ?>"
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
                                    value="<?= htmlspecialchars($doctor['phone'] ?? ''); ?>"
                                    required>

                            </div>

                            <div class="col-md-6">

                                <label class="form-label">
                                    المركز
                                </label>

                                <select name="center_id" class="form-select">

                                    <option value="">
                                        اختر المركز
                                    </option>

                                    <?php while ($center = mysqli_fetch_assoc($centers_result)): ?>

                                        <option
                                            value="<?= $center['id']; ?>"
                                            <?= isset($doctor['center_id']) && $doctor['center_id'] == $center['id'] ? 'selected' : ''; ?>>

                                            <?= htmlspecialchars($center['name']); ?>

                                        </option>

                                    <?php endwhile; ?>

                                </select>

                            </div>

                            <div class="col-md-6">

                                <label class="form-label">
                                    الحالة
                                </label>

                                <select name="status" class="form-select">

                                    <option
                                        value="active"
                                        <?= isset($doctor['status']) && $doctor['status'] == "active" ? "selected" : ""; ?>>

                                        نشط

                                    </option>

                                    <option
                                        value="inactive"
                                        <?= isset($doctor['status']) && $doctor['status'] == "inactive" ? "selected" : ""; ?>>

                                        غير نشط

                                    </option>

                                </select>

                            </div>

                            <div class="col-md-6">

                                <label class="form-label">
                                    <?= $editing ? "كلمة المرور الجديدة (اختياري)" : "كلمة المرور"; ?>
                                </label>

                                <input
                                    type="password"
                                    name="password"
                                    class="form-control"
                                    <?= !$editing ? "required" : ""; ?>>

                            </div>

                            <div class="col-md-6">

                                <label class="form-label">
                                    تأكيد كلمة المرور
                                </label>

                                <input
                                    type="password"
                                    name="password_confirmation"
                                    class="form-control"
                                    <?= !$editing ? "required" : ""; ?>>

                            </div>

                            <div class="col-md-6">

                                <label class="form-label">
                                    صورة الطبيب
                                </label>

                                <input
                                    type="file"
                                    name="image"
                                    class="form-control"
                                    accept=".jpg,.jpeg,.png,.webp">

                                <small class="text-muted">
                                    JPG / PNG / WEBP — الحد الأقصى 2MB
                                </small>

                            </div>

                            <?php if ($editing && !empty($doctor['image']) && $doctor['image'] != "default.png"): ?>

                                <div class="col-md-6">

                                    <label class="form-label d-block">
                                        الصورة الحالية
                                    </label>

                                    <img
                                        src="uploads/doctors/<?= htmlspecialchars($doctor['image']); ?>"
                                        alt="Doctor"
                                        style="width:100px;height:100px;object-fit:cover;border-radius:50%;">

                                </div>

                            <?php endif; ?>

                        </div>

                        <div class="mt-4 d-flex gap-2">

                            <button
                                type="submit"
                                name="submit"
                                class="btn btn-primary">

                                <?= $editing ? "تحديث" : "حفظ"; ?>

                            </button>

                            <a
                                href="admin-doctors.php"
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