<?php

session_start();

require_once("auth.php");
requireLogin();
requireRole("admin");

require_once("config/database.php");

$title = "إدارة المراكز";

$search = "";

if (isset($_GET['search'])) {
    $search = trim($_GET['search']);
}

$search_safe = mysqli_real_escape_string($conn, $search);

$query = "SELECT *
          FROM centers
          WHERE name LIKE '%$search_safe%'
          OR address LIKE '%$search_safe%'
          OR phone LIKE '%$search_safe%'
          ORDER BY id DESC";

$result = mysqli_query($conn, $query);

if (!$result) {
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

                <div class="d-flex flex-wrap gap-2 justify-content-between align-items-center mb-3">

                    <form
                        action=""
                        method="get"
                        class="d-flex gap-2">

                        <input
                            type="search"
                            name="search"
                            value="<?= htmlspecialchars($search); ?>"
                            class="form-control"
                            placeholder="بحث بالاسم / العنوان / الهاتف">

                        <button
                            type="submit"
                            class="btn btn-outline-primary">

                            بحث

                        </button>

                    </form>

                    <a
                        href="admin-center-form.php"
                        class="btn btn-primary">

                        إضافة مركز

                    </a>

                </div>

                <div class="card">

                    <div class="table-responsive">

                        <table class="table table-hover align-middle mb-0">

                            <thead>

                                <tr>

                                    <th>#</th>
                                    <th>اسم المركز</th>
                                    <th>العنوان</th>
                                    <th>الهاتف</th>
                                    <th>الإجراءات</th>

                                </tr>

                            </thead>

                            <tbody>

                                <?php if (mysqli_num_rows($result) > 0): ?>

                                    <?php while ($row = mysqli_fetch_assoc($result)): ?>

                                        <tr>

                                            <td>
                                                <?= $row['id']; ?>
                                            </td>

                                            <td>
                                                <?= htmlspecialchars($row['name']); ?>
                                            </td>

                                            <td>
                                                <?= htmlspecialchars($row['address'] ?? '—'); ?>
                                            </td>

                                            <td>
                                                <?= htmlspecialchars($row['phone'] ?? '—'); ?>
                                            </td>

                                            <td class="text-nowrap">

                                                <a
                                                    href="admin-center-form.php?id=<?= $row['id']; ?>"
                                                    class="btn btn-sm btn-outline-primary">

                                                    تعديل

                                                </a>

                                                <a
                                                    href="admin-center-delete.php?id=<?= $row['id']; ?>"
                                                    class="btn btn-sm btn-outline-danger"
                                                    onclick="return confirm('هل أنت متأكد من حذف هذا المركز؟');">

                                                    حذف

                                                </a>

                                            </td>

                                        </tr>

                                    <?php endwhile; ?>

                                <?php else: ?>

                                    <tr>

                                        <td
                                            colspan="5"
                                            class="text-center p-4">

                                            لا توجد نتائج.

                                        </td>

                                    </tr>

                                <?php endif; ?>

                            </tbody>

                        </table>

                    </div>

                </div>

                <p class="small text-muted mt-2">

                    الإجمالي: <?= mysqli_num_rows($result); ?>

                </p>

            </div>

        </main>

    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>