<?php
require_once __DIR__.'/inc/anonymUser.php';

$errors = [];

if (!empty($_POST)) {

    $name = trim(@$_POST['name']);
    if (empty($name)) {
        $errors['name'] = 'Musíte zadat své jméno';
    }

    $surname = trim(@$_POST['surname']);
    if (empty($surname)) {
        $errors['surname'] = 'Musíte zadat své příjmení';
    }

    $email = trim(@$_POST['email']);
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = 'Email není platný.';
    } else {
        $emailQuery = $db->prepare('SELECT * FROM user WHERE email=:email LIMIT 1;');
        $emailQuery->execute([
            ':email' => $email
        ]);
        if ($emailQuery->rowCount() > 0) {
            $errors['email'] = 'Tato emailová adresa je již zaregistrována.';
        }
    }

    if (empty($_POST['password']) || (strlen($_POST['password']) < 8)) {
        $errors[] = 'Heslo musí být dlouhé aslespoň 8 znaků.';
    } elseif ($_POST['password'] !== $_POST['password2']) {
        $errors[] = 'Hesla se neshodují.';
    }

    if (empty($errors)) {
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

        $query = $db->prepare('INSERT INTO user (name, surname, email, password) VALUES (:name, :surname, :email, :password);');
        $query->execute([
            ':name' => $name,
            ':surname' => $surname,
            ':email' => $email,
            ':password' => $password
        ]);

        //uživatele rovnou přihlásíme
        $_SESSION['user_id'] = $db->lastInsertId();
        $_SESSION['user_full_name'] = [$name, $surname];

        //přesměrování na homepage
        header('Location: index.php');
        exit();
    }
}

#endregion zpracování formuláře




$pageTitle = 'Registrace';
include 'inc/layoutAuth.php';
?>
<?php
if (!empty($errors)) {
    foreach ($errors as $error) {
        echo '<p style="color:red;">' . htmlspecialchars($error) . '</p>';
    }
}
?>
<form method="post">
    <div class="form-group">
        <label for="name">Jméno</label>
        <input type="text" class="form-control" id="name" name="name" required
            value="<?php echo htmlspecialchars(@$_POST['name'] ?? ''); ?>">
    </div>
    <div class="form-group">
        <label for="surname">Příjmení</label>
        <input type="text" class="form-control" id="surname" name="surname" required
            value="<?php echo htmlspecialchars(@$_POST['surname'] ?? ''); ?>">
    </div>
    <div class="form-group">
        <label for="username">Email</label>
        <input type="email" class="form-control" id="email" name="email" required
            value="<?php echo htmlspecialchars(@$_POST['email'] ?? ''); ?>">
    </div>
    <div class="form-group">
        <label for="password">Heslo</label>
        <input type="password" class="form-control" id="password" name="password" required value="">
    </div>
    <div class="form-group">
        <label for="password2">Heslo znovu</label>
        <input type="password" class="form-control" id="password2" name="password2" required value="">
    </div>
    <button type="submit" class="btn btn-primary">Registrovat se</button>
    <a href="./signin.php">Přihlásit se</a>
    </body>

    <?php
    include 'inc/footer.php';
    ?>