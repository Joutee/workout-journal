<?php
require_once __DIR__ . '/user.php';
require_once __DIR__ . '/admin.php';


?>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
```>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
<nav class="d-flex flex-column bg-dark-custom vh-100 p-3 shadow"
    style="width: 250px; position:fixed; left:0; top:0; min-height:100vh;">
    <a href="index.php" class="mb-4 h4 text-decoration-none text-warning fw-bold text-center d-flex flex-column">
        <img src="assets/logo.svg" alt="Logo" class="mb-2" style="height:2em; vertical-align:middle;">
        <span>Fit deník</span>
    </a>
    <a href="index.php" class="nav-link py-2 d-flex justify-content-between align-items-center">
        Přehled
        <i class="bi bi-house-door"></i>
    </a>
    <a href="workouts.php" class="nav-link py-2 d-flex justify-content-between align-items-center">
        Tréninky
        <i class="bi bi-calendar-check"></i>
    </a>
    <a href="exercises.php" class="nav-link py-2 d-flex justify-content-between align-items-center">
        Moje cviky
        <i class="bi bi-lightning-charge"></i>
    </a>

    <?php if (isUserAdmin($db, $_SESSION['user_id'])): ?>
        <a href="editMuscleGroup.php" class="nav-link py-2 d-flex justify-content-between align-items-center">
            Svalové skupiny
            <i class="bi bi-diagram-3"></i>
        </a>
        <a href="editUsers.php" class="nav-link py-2 d-flex justify-content-between align-items-center">
            Upravit uživatele
            <i class="bi bi-people"></i>

        </a>
    <?php endif; ?>
    <a href="profile.php" class="nav-link py-2 d-flex justify-content-between align-items-center">
        Profil
        <i class="bi bi-person"></i>
    </a>

    <div class="mt-auto">
        <?php if (!empty($_SESSION['user_full_name'])): ?>
            <div class="mb-3 text-light small text-center display-1">
                <?= htmlspecialchars(implode(' ', $_SESSION['user_full_name'])) ?>
            </div>
        <?php endif; ?>
        <hr class="divider">
        <a href="signout.php"
            class="nav-link text-danger text-center fw-bold d-flex justify-content-between align-items-center">
            Odhlásit
            <i class="bi bi-box-arrow-right"></i>
        </a>
    </div>
</nav>