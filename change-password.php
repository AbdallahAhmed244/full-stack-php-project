<?php

session_start();

require_once("auth.php");
requireLogin();

require_once("config/database.php");


$user_id = $_SESSION['user_id'];

$message = "";
$message_type = "";

if ($_SERVER['REQUEST_METHOD'] == "POST") {

    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    if (empty($current_password) || empty($new_password) || empty($confirm_password)) {

        $message = "من فضلك املئي جميع البيانات.";
        $message_type = "danger";

    } elseif ($new_password !== $confirm_password) {

        $message = "كلمة المرور الجديدة وتأكيدها غير متطابقين.";
        $message_type = "danger";

    } elseif (strlen($new_password) < 6) {

        $message = "كلمة المرور الجديدة يجب أن تكون 6 أحرف على الأقل.";
        $message_type = "danger";

    } else {

        $query = "SELECT password
                  FROM users
                  WHERE id = '$user_id'
                  LIMIT 1";

        $result = mysqli_query($conn, $query);

        $user = mysqli_fetch_assoc($result);

        if (!$user) {

            $message = "حدث خطأ أثناء تحميل بيانات المستخدم.";
            $message_type = "danger";

        } elseif (!password_verify($current_password, $user['password'])) {

            $message = "كلمة المرور الحالية غير صحيحة.";
            $message_type = "danger";

        } else {

            $new_password_hash = password_hash($new_password, PASSWORD_DEFAULT);

            $update = "UPDATE users
                       SET password = '$new_password_hash'
                       WHERE id = '$user_id'";

            if (mysqli_query($conn, $update)) {

                $message = "تم تغيير كلمة المرور بنجاح.";
                $message_type = "success";

            } else {

                $message = "حدث خطأ أثناء تغيير كلمة المرور.";
                $message_type = "danger";

            }

        }

    }

}

$title = "تغيير كلمة المرور";

?>

<?php include("includes/head.php"); ?>

<body>

<div class="container-fluid">

    <div class="row">

        <?php include("includes/sidebar.php"); ?>

        <main class="col-md-9 col-lg-10 p-0">

            <?php include("includes/topbar.php"); ?>

            <div class="p-4">

                <div class="card p-4">

                    <h2 class="h5 mb-4">
                        تغيير كلمة المرور
                    </h2>

                    <?php if (!empty($message)): ?>

                        <div class="alert alert-<?= $message_type; ?>">
                            <?= htmlspecialchars($message); ?>
                        </div>

                    <?php endif; ?>

                    <form method="post">

                        <div class="mb-3">

                            <label class="form-label">
                                كلمة المرور الحالية
                            </label>

                            <input
                                type="password"
                                name="current_password"
                                class="form-control"
                                required>

                        </div>

                        <div class="mb-3">

                            <label class="form-label">
                                كلمة المرور الجديدة
                            </label>

                            <input
                                type="password"
                                name="new_password"
                                class="form-control"
                                required>

                        </div>

                        <div class="mb-3">

                            <label class="form-label">
                                تأكيد كلمة المرور الجديدة
                            </label>

                            <input
                                type="password"
                                name="confirm_password"
                                class="form-control"
                                required>

                        </div>

                        <div class="mt-4">

                            <button
                                type="submit"
                                class="btn btn-primary">

                                تغيير كلمة المرور

                            </button>

                            <a
                                href="profile.php"
                                class="btn btn-secondary">

                                رجوع

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
