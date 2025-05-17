<?php
require_once 'inc/db.php';
session_start();

if (!empty($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}

$errors = [];

if (!empty($_POST['password']) && !empty($_POST['email'])) {
    $email = @$_POST['email'];
    $password = @$_POST['password'];

    $query = $db->prepare('SELECT * FROM user WHERE email=:email LIMIT 1;');
    $query->execute([
        ':email' => $email
    ]);
    $user = $query->fetch(PDO::FETCH_ASSOC);
    if (!empty($user) && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['user_id'];
        $_SESSION['user_full_name'] = [$user['name'], $user['surname']];
        header('Location: index.php');
        exit;
    } else {
        $errors[] = 'Neplatné přihlašovací údaje.';
    }
}
$pageTitle = 'Přihlášení';
include './inc/layoutAuth.php';

if (!empty($errors)) {
    foreach ($errors as $error) {
        echo '<p style="color:red;">' . htmlspecialchars($error) . '</p>';
    }
}
?>
<form method="post">
    <div class="form-group">
        <label for="email">Email</label>
        <input type="email" class="form-control" id="email" name="email" required
            value="<?php echo htmlspecialchars(@$_POST['email'] ?? ''); ?>">
    </div>
    <div class="form-group">
        <label for="password">Heslo</label>
        <input type="password" class="form-control" id="password" name="password" required>
    </div>
    <button type="submit" class="btn btn-primary">Přihlásit se</button>
    <a href="./signup.php">Vytvořit účet!</a>
    </mai>
    <?php
    include 'inc/footer.php';
    ?>