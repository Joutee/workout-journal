<?php
require_once __DIR__ . '/user.php';


?>
<nav class="d-flex flex-column bg-dark-custom vh-100 p-3 shadow"
    style="width: 15vw; position:fixed; left:0; top:0; min-height:100vh;">
    <a href="index.php" class="mb-4 h4 text-decoration-none text-warning fw-bold text-center">🏋️‍♂️ Deník</a>
    <a href="index.php" class="nav-link py-2">Přehled</a>
    <a href="workouts.php" class="nav-link py-2">Tréninky</a>
    <a href="exercises.php" class="nav-link py-2">Moje cviky</a>
    <a href="profile.php" class="nav-link py-2">Profil</a>

    <?php if ($_SESSION['admin'] == true): /*     JAK TOTO UDELAT WTF OOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOO */ ?>
        <a href="editMuscleGroup.php" class="nav-link py-2">Svalové skupiny</a>
    <?php endif; ?>

    <div class="mt-auto">
        <?php if (!empty($_SESSION['user_full_name'])): ?>
            <div class="mb-3 text-light small text-center" style="font-size:1.1em;">
                <?= htmlspecialchars(implode(' ', $_SESSION['user_full_name'])) ?>
            </div>
        <?php endif; ?>
        <a href="signout.php" class="nav-link text-danger text-center fw-bold">Odhlásit</a>
    </div>
</nav>