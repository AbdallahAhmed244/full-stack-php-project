<?php

session_start();

require_once("auth.php");
requireLogin();
requireRole("admin");

require_once("config/database.php");

$title = "الأدوار";

$query = "SELECT role, COUNT(*) AS users_count
          FROM users
          GROUP BY role";

$result = mysqli_query($conn, $query);

if (!$result)
{
    die(mysqli_error($conn));
}

$role_counts = [
    "admin" => 0,
    "doctor" => 0,
    "patient" => 0
];

while ($row = mysqli_fetch_assoc($result))
{
    $role = strtolower(trim($row['role']));

    if (isset($role_counts[$role]))
    {
        $role_counts[$role] = (int)$row['users_count'];
    }
}

$roles = [
    [
        "slug" => "admin",
        "name" => "مدير",
        "description" => "إدارة النظام بالكامل",
        "count" => $role_counts["admin"]
    ],
    [
        "slug" => "doctor",
        "name" => "طبيب",
        "description" => "إدارة المواعيد ومتابعة المرضى",
        "count" => $role_counts["doctor"]
    ],
    [
        "slug" => "patient",
        "name" => "مريض",
        "description" => "حجز ومتابعة المواعيد",
        "count" => $role_counts["patient"]
    ]
];

?>

<?php include("includes/head.php"); ?>

<body>

<div class="container-fluid">

    <div class="row">

        <?php include("includes/sidebar.php"); ?>

        <main class="col-md-9 col-lg-10 p-0">

            <?php include("includes/topbar.php"); ?>

            <div class="p-4">

                <div class="card">

                    <div class="table-responsive">

                        <table class="table align-middle mb-0">

                            <thead>

                                <tr>

                                    <th>#</th>
                                    <th>المعرّف</th>
                                    <th>الاسم بالعربية</th>
                                    <th>الوصف</th>
                                    <th>عدد المستخدمين</th>

                                </tr>

                            </thead>

                            <tbody>

                                <?php foreach ($roles as $index => $role): ?>

                                    <tr>

                                        <td>
                                            <?= $index + 1; ?>
                                        </td>

                                        <td>
                                            <code>
                                                <?= htmlspecialchars($role['slug']); ?>
                                            </code>
                                        </td>

                                        <td>
                                            <?= htmlspecialchars($role['name']); ?>
                                        </td>

                                        <td class="small text-muted">
                                            <?= htmlspecialchars($role['description']); ?>
                                        </td>

                                        <td>
                                            <span class="badge text-bg-primary">
                                                <?= (int)$role['count']; ?>
                                            </span>
                                        </td>

                                    </tr>

                                <?php endforeach; ?>

                            </tbody>

                        </table>

                    </div>

                </div>

                <p class="small text-muted mt-3 mb-0">
                    الأدوار الحالية في النظام هي: مدير، طبيب، مريض.
                </p>

            </div>

        </main>

    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>
