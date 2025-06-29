<?php
require_once __DIR__ . '/inc/user.php';
$pageTitle = 'Změna hesla';
include __DIR__ . '/inc/layoutApp.php';

$errors = [];

if (!empty($_POST)) {
    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];
    $new_password_confirm = $_POST['new_password_confirm'];

    if (empty($current_password) || empty($new_password) || empty($new_password_confirm)) {
        $errors['password'] = 'Všechna pole jsou povinná.';
    }
    if ($current_password == $new_password) {
        $errors['password'] = 'Nové heslo nesmí být stejné jako současné heslo.';
    }
    if (strlen($new_password) < 8) {
        $errors['password'] = 'Nové heslo musí mít alespoň 8 znaků.';
    }
    if (!preg_match('/[A-Z]/', $new_password)) {
        $errors['password'] = 'Nové heslo musí obsahovat alespoň jedno velké písmeno.';
    }
    if (!preg_match('/[0-9]/', $new_password)) {
        $errors['password'] = 'Nové heslo musí obsahovat alespoň jedno číslo.';
    }
    if (!preg_match('/[\W_]/', $new_password)) {
        $errors['password'] = 'Nové heslo musí obsahovat alespoň jeden speciální znak.';
    }
    if ($new_password !== $new_password_confirm) {
        $errors['password'] = 'Nová hesla se neshodují.';
    }
    if (empty($errors)) {
        $query = $db->prepare('SELECT * FROM user WHERE user_id=:user_id;');
        $query->execute([':user_id' => $_SESSION['user_id']]);
        $user = $query->fetch(PDO::FETCH_ASSOC);

        if (password_verify($current_password, $user['password'])) {
            $hashed_new_password = password_hash($new_password, PASSWORD_DEFAULT);
            $update_query = $db->prepare('UPDATE user SET password=:password WHERE user_id=:user_id;');
            $update_query->execute([
                ':password' => $hashed_new_password,
                ':user_id' => $_SESSION['user_id']
            ]);
            echo '<div class="alert alert-success">Heslo bylo úspěšně změněno.</div>';
        } else {
            echo '<div class="alert alert-danger">Současné heslo je nesprávné.</div>';
        }
    }
    if (!empty($errors)) {
        foreach ($errors as $error) {
            echo '<div class="alert alert-danger">' . htmlspecialchars($error) . '</div>';
        }
    }
}
?>

<form method="post">
    <div class="form-group">
        <label for="current_password">Současné heslo</label>
        <input type="password" class="form-control w-25" id="current_password" name="current_password" required>
    </div>
    <div class="form-group">
        <label for="new_password">Nové heslo</label>
        <input type="password" class="form-control w-25" id="new_password" name="new_password" required>
    </div>
    <div class="form-group">
        <label for="new_password_confirm">Potvrzení nového hesla</label>
        <input type="password" class="form-control w-25" id="new_password_confirm" name="new_password_confirm" required>
    </div>
    <button type="submit" class="btn btn-primary mr-2">Změnit heslo</button>
    <a href="profile.php" class="btn btn-secondary">Zrušit</a>
</form>

<?php


include 'inc/footer.php';
?>