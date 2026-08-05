<header id="topbar" class="kc-topbar px-4 py-3 d-flex justify-content-between align-items-center">

    <h1 class="h5 mb-0">
        <?= $title ?>
    </h1>

    <div class="text-muted small">
        مرحباً،

        <?= $_SESSION['user_name'] ?? "Admin"; ?>

        <span class="badge text-bg-light border kc-badge-role">
            Admin
        </span>
    </div>

</header>