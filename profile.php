<?php
require_once __DIR__ . '/inc/user.php';
$pageTitle = 'Profil';
include __DIR__ . '/inc/layoutApp.php';

$query = $db->prepare('SELECT name, surname, email FROM user WHERE user_id = :user_id LIMIT 1;');
$query->execute([':user_id' => $_SESSION['user_id']]);
$user = $query->fetch(PDO::FETCH_ASSOC);

if (!empty($_POST)) {
    $name = trim($_POST['name']);
    $surname = trim($_POST['surname']);
    $email = trim($_POST['email']);
    $errors = [];

    $name = trim(@$_POST['name']);
    if (empty($name)) {
        $errors['name'] = 'Musíte zadat své jméno';
    }

    if (strlen($_POST['name']) > 30) {
        $errors['name'] = 'Jméno musí mít maximálně 30 znaků.';
    }

    $surname = trim(@$_POST['surname']);
    if (empty($surname)) {
        $errors['surname'] = 'Musíte zadat své příjmení';
    }

    if (strlen($_POST['name']) > 50) {
        $errors['surname'] = 'Příjmení musí mít maximálně 50 znaků.';
    }

    $email = trim(@$_POST['email']);
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = 'Email není platný.';
    } else {
        $emailQuery = $db->prepare('SELECT * FROM user WHERE email = :email LIMIT 1;');
        $emailQuery->execute([
            ':email' => $email
        ]);
        $existingUser = $emailQuery->fetch(PDO::FETCH_ASSOC);

        if ($existingUser && $existingUser['user_id'] != $_SESSION['user_id']) {
            $errors['email'] = 'Tato emailová adresa je již zaregistrována jiným uživatelem.';
        }
    }

    if (empty($errors)) {
        $updateQuery = $db->prepare('UPDATE user SET name = :name, surname = :surname, email = :email WHERE user_id = :user_id;');
        $updateQuery->execute([
            ':name' => $name,
            ':surname' => $surname,
            ':email' => $email,
            ':user_id' => $_SESSION['user_id']
        ]);
        echo '<div class="alert alert-success">Informace byly úspěšně aktualizovány.</div>';
    }
}
?>

<div class="container mt-4">
    <?php
    if (!empty($errors)) {
        foreach ($errors as $error) {
            echo '<div class="alert alert-danger w-100">' . htmlspecialchars($error) . '</div>';
        }
    }
    ?>
    <form method="post" class="w-50 mb-5">
        <div class="mb-3">
            <label for="name" class="form-label">Jméno</label>
            <input type="text" name="name" id="name" class="form-control"
                value="<?= htmlspecialchars($user['name'] ?? '') ?>" required>
        </div>
        <div class="mb-3">
            <label for="surname" class="form-label">Příjmení</label>
            <input type="text" name="surname" id="surname" class="form-control"
                value="<?= htmlspecialchars($user['surname'] ?? '') ?>" required>
        </div>
        <div class="mb-3">
            <label for="email" class="form-label">E-mail</label>
            <input type="email" name="email" id="email" class="form-control"
                value="<?= htmlspecialchars($user['email'] ?? '') ?>" required>
        </div>
        <button type="submit" class="btn btn-primary">Uložit změny</button>
    </form>
    <a href="changePassword.php" class="btn btn-primary">Změnit heslo</a>
</div>


<?php
include 'inc/footer.php';
?>