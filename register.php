<?php
session_start();

require_once("config/database.php");

$title = "Register";
include("includes/head.php");

if(isset($_POST['submit']))
{
    $full_name = mysqli_real_escape_string($conn,$_POST['full_name']);
    $email = mysqli_real_escape_string($conn,$_POST['email']);
    $phone = mysqli_real_escape_string($conn,$_POST['phone']);
    $birth_date = $_POST['birth_date'];
    $gender = $_POST['gender'];
    $blood_type = $_POST['blood_type'];
    $address = mysqli_real_escape_string($conn,$_POST['address']);
    $password = $_POST['password'];
$confirm_password = $_POST['confirm_password'];
$password_hash = password_hash($password, PASSWORD_DEFAULT);

if($password != $confirm_password)
{
    $error = "Password does not match.";
}
else
{
    $query = "SELECT * FROM users
    WHERE email='$email'";

    $result = mysqli_query($conn,$query);

    if(mysqli_num_rows($result) > 0)
    {
        $error = "Email already exists.";
    }
    else
    {
        $query = "INSERT INTO users
        (username,email,password,role)

        VALUES

        ('$full_name',
        '$email',
        '$password_hash',
        'patient')";

        if(mysqli_query($conn,$query))
        {
            $user_id = mysqli_insert_id($conn);

            $query = "INSERT INTO patients
            (user_id,full_name,phone,blood_type,gender,birth_date,address,status)

            VALUES

            ('$user_id',
            '$full_name',
            '$phone',
            '$blood_type',
            '$gender',
            '$birth_date',
            '$address',
            'active')";

            mysqli_query($conn,$query);

            header("Location: login.php");
            exit;
        }
        else
        {
            $error = mysqli_error($conn);
        }
    }
}
}
?>

<body class="kc-auth-body">
  <main class="container py-5">
    <div class="text-center mb-4">
      <a href="login.php" class="kc-brand-link text-decoration-none">
        <i class="bi bi-heart-pulse-fill"></i> RenalCare
      </a>
      <p class="text-muted mb-0 mt-1">نظام إدارة مراكز الغسيل الكلوي</p>
    </div>

    <div class="row justify-content-center">
      <div class="col-lg-8">
        <div id="registerAlert" class="alert alert-success d-none"></div>

        <div class="card kc-auth-card p-4">
          <h1 class="h4 mb-3 text-center">تسجيل مريض جديد</h1>
          <p class="text-muted text-center small mb-4">إنشاء حساب للمرضى فقط — حسابات الأطباء والمديرين تُنشأ بواسطة الإدارة</p>

          <?php
if(isset($error))
{
?>
<div class="alert alert-danger">
    <?= $error; ?>
</div>
<?php
}
?>

<form action="" method="post">

<div class="row g-3">

<div class="col-md-6">
<label class="form-label">الاسم الكامل</label>
<input
type="text"
name="full_name"
class="form-control"
required>
</div>

<div class="col-md-6">
<label class="form-label">البريد الإلكتروني</label>
<input
type="email"
name="email"
class="form-control"
required>
</div>

<div class="col-md-6">
<label class="form-label">رقم الهاتف</label>
<input
type="text"
name="phone"
class="form-control"
required>
</div>

<div class="col-md-6">
<label class="form-label">تاريخ الميلاد</label>
<input
type="date"
name="birth_date"
class="form-control"
required>
</div>

<div class="col-md-6">
<label class="form-label">الجنس</label>

<select
name="gender"
class="form-select"
required>

<option value="">اختر</option>
<option value="male">ذكر</option>
<option value="female">أنثى</option>

</select>

</div>

<div class="col-md-6">
<label class="form-label">فصيلة الدم</label>

<select
name="blood_type"
class="form-select">

<option value="">اختر</option>
<option>A+</option>
<option>A-</option>
<option>B+</option>
<option>B-</option>
<option>AB+</option>
<option>AB-</option>
<option>O+</option>
<option>O-</option>

</select>

</div>

<div class="col-12">
<label class="form-label">العنوان</label>

<input
type="text"
name="address"
class="form-control">

</div>

<div class="col-md-6">
<label class="form-label">كلمة المرور</label>

<input
type="password"
name="password"
class="form-control"
required>

</div>

<div class="col-md-6">
<label class="form-label">تأكيد كلمة المرور</label>

<input
type="password"
name="confirm_password"
class="form-control"
required>

</div>

<div class="col-12">

<button
type="submit"
name="submit"
class="btn btn-primary w-100">

إنشاء الحساب

</button>

</div>

</div>

</form>

          <p class="text-center mt-3 mb-0 small">
            لديك حساب؟
           <a href="login.php">تسجيل الدخول</a>
          </p>
        </div>
      </div>
    </div>
  </main>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
