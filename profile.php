<?php

session_start();

require_once("auth.php");
requireLogin();

require_once("config/database.php");

$user_id = (int)$_SESSION['user_id'];

$query = "SELECT id, username, email, role
          FROM users
          WHERE id='$user_id'
          LIMIT 1";

$result = mysqli_query($conn, $query);

if (!$result)
{
    die(mysqli_error($conn));
}

$user = mysqli_fetch_assoc($result);

if (!$user)
{
    session_destroy();
    header("Location: login.php");
    exit;
}

$title = "الملف الشخصي";

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
                        الملف الشخصي
                    </h2>

                    <div class="row">

                        <div class="col-md-6 mb-3">

                            <label class="form-label">
                                اسم المستخدم
                            </label>

                            <input
                                type="text"
                                class="form-control"
                                value="<?= htmlspecialchars($user['username'] ?? '—'); ?>"
                                readonly>

                        </div>

                        <div class="col-md-6 mb-3">

                            <label class="form-label">
                                البريد الإلكتروني
                            </label>

                            <input
                                type="email"
                                class="form-control"
                                value="<?= htmlspecialchars($user['email'] ?? '—'); ?>"
                                readonly>

                        </div>

                        <div class="col-md-6 mb-3">

                            <label class="form-label">
                                نوع الحساب
                            </label>

                            <input
                                type="text"
                                class="form-control"
                                value="<?= htmlspecialchars($user['role'] ?? '—'); ?>"
                                readonly>

                        </div>

                    </div>

                    <div class="mt-3">

                        <a
                            href="change-password.php"
                            class="btn btn-primary">

                            تغيير كلمة المرور

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