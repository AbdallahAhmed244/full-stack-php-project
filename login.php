<?php

session_start();

require_once("config/database.php");

$error = "";

if (isset($_POST['submit'])) {

    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = $_POST['password'];

    if (empty($email) || empty($password)) {

        $error = "من فضلك أدخلي البريد الإلكتروني وكلمة المرور.";

    } else {

        $query = "SELECT *
                  FROM users
                  WHERE email = '$email'
                  LIMIT 1";

        $result = mysqli_query($conn, $query);

        if (!$result) {
            die(mysqli_error($conn));
        }

        if (mysqli_num_rows($result) > 0) {

            $user = mysqli_fetch_assoc($result);

            if (password_verify($password, $user['password'])) {

                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_name'] = $user['username'];
                $_SESSION['role'] = $user['role'];

                $user_id = $user['id'];
                $action = "Login";
                $details = "User logged into the system";

                $log_query = "INSERT INTO activity_logs
                              (user_id, action, details)
                              VALUES
                              ('$user_id', '$action', '$details')";

                mysqli_query($conn, $log_query);

                if ($user['role'] == "admin") {

                    header("Location: admin-dashboard.php");
                    exit;

                } elseif ($user['role'] == "doctor") {

                    header("Location: doctor-dashboard.php");
                    exit;

                } elseif ($user['role'] == "patient") {

                    header("Location: patient-dashboard.php");
                    exit;

                } else {

                    $error = "نوع الحساب غير معروف.";

                }

            } else {

                $error = "البريد الإلكتروني أو كلمة المرور غير صحيحة.";

            }

        } else {

            $error = "البريد الإلكتروني أو كلمة المرور غير صحيحة.";

        }

    }

}

$title = "تسجيل الدخول";

?>

<?php include("includes/head.php"); ?>

<body class="kc-auth-body">

<main class="container py-5">

    <div class="text-center mb-4">

        <a
            href="login.php"
            class="kc-brand-link text-decoration-none">

            <i class="bi bi-heart-pulse-fill"></i>

            RenalCare

        </a>

        <p class="text-muted mb-0 mt-1">

            نظام إدارة مراكز الغسيل الكلوي

        </p>

    </div>


    <div class="row justify-content-center">

        <div class="col-md-6 col-lg-5">


            <div class="card kc-auth-card p-4">

                <h1 class="h4 mb-3 text-center">

                    تسجيل الدخول

                </h1>


                <p class="text-muted text-center small mb-4">

                    أدخل بيانات حسابك للمتابعة

                </p>


                <?php if (!empty($error)): ?>

                    <div class="alert alert-danger">

                        <?= htmlspecialchars($error); ?>

                    </div>

                <?php endif; ?>


                <form
                    action="login.php"
                    method="post">


                    <div class="mb-3">

                        <label
                            for="email"
                            class="form-label">

                            البريد الإلكتروني

                        </label>


                        <input
                            type="email"
                            class="form-control"
                            id="email"
                            name="email"
                            required
                            autocomplete="username">

                    </div>


                    <div class="mb-3">

                        <label
                            for="password"
                            class="form-label">

                            كلمة المرور

                        </label>


                        <input
                            type="password"
                            class="form-control"
                            id="password"
                            name="password"
                            required
                            autocomplete="current-password">

                    </div>


                    <div class="form-check mb-3">

                        <input
                            class="form-check-input"
                            type="checkbox"
                            value="1"
                            id="remember"
                            name="remember">


                        <label
                            class="form-check-label"
                            for="remember">

                            تذكرني على هذا الجهاز

                        </label>

                    </div>


                    <button
                        type="submit"
                        name="submit"
                        class="btn btn-primary w-100">

                        دخول

                    </button>

                </form>


                <p class="text-center mt-3 mb-0 small">

                    مريض جديد؟

                    <a href="register.php">

                        إنشاء حساب

                    </a>

                </p>

            </div>

        </div>

    </div>

</main>


<script
    src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js">
</script>

</body>

</html>