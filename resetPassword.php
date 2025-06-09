<?php
require_once __DIR__ . '/inc/anonymUser.php';

$errors = [];

if (!empty($_GET['token'])) {
    $token = $_GET['token'];

    // Ověření tokenu
    $query = $db->prepare('SELECT * FROM user WHERE reset_token = :token AND reset_expiry > NOW() LIMIT 1;');
    $query->execute([':token' => $token]);
    $user = $query->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        $errors['token'] = 'Neplatný nebo expirovaný token.';
    }

    if (!empty($_POST['password']) && !empty($_POST['password2']) && empty($errors)) {
        $password = trim($_POST['password']);
        $passwordConfirm = trim($_POST['password2']);

        if (strlen($_POST['password']) < 8) {
            $errors['password'] = 'Heslo musí mít alespoň 8 znaků.';
        }
        if (!preg_match('/[A-Z]/', $_POST['password'])) {
            $errors['password'] = 'Heslo musí obsahovat alespoň jedno velké písmeno.';
        }
        if (!preg_match('/[0-9]/', $_POST['password'])) {
            $errors['password'] = 'Heslo musí obsahovat alespoň jedno číslo.';
        }
        if (!preg_match('/[\W_]/', $_POST['password'])) {
            $errors['password'] = 'Heslo musí obsahovat alespoň jeden speciální znak.';
        }
        if ($_POST['password'] !== $_POST['password2']) {
            $errors['password'] = 'Hesla se neshodují.';
        }

        if (empty($errors)) {
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            $updateQuery = $db->prepare('UPDATE user SET password = :password, reset_token = NULL, reset_expiry = NULL WHERE user_id = :user_id;');
            $updateQuery->execute([
                ':password' => $hashedPassword,
                ':user_id' => $user['user_id']
            ]);
            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['user_full_name'] = [$name, $surname];

            header('Location: index.php');
            exit();
        }
    }
} else {
    $errors['token'] = 'Token nebyl poskytnut.';
}

$pageTitle = 'Reset hesla';
include './inc/layoutAuth.php';
?>

<div class="card container w-25 login-card">
    <form method="post">
        <h2 class="mb-4 text-center ">Reset hesla</h2>


        <?php if (empty($errors['token'])): ?>
            <?php
            if (!empty($errors)) {
                foreach ($errors as $error) {
                    echo '<div class="alert alert-danger w-100">' . htmlspecialchars($error) . '</div>';
                }
            }
            ?>

            <div class="mb-3">
                <label for="password" class="form-label">Nové heslo</label>
                <input type="password" name="password" id="password" class="form-control" required>
            </div>

            <div class="mb-3">
                <label for="password2" class="form-label">Potvrzení hesla</label>
                <input type="password" name="password2" id="password2" class="form-control" required>
            </div>

            <button type="submit" class="btn btn-primary w-100">Resetovat heslo</button>
        <?php endif; ?>

        <a href="signin.php" class="d-block text-center mt-3">Zpět na přihlášení</a>
    </form>
</div>