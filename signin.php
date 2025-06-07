<?php
require_once __DIR__ . '/inc/anonymUser.php';


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
        $_SESSION['admin'] = $user['admin']; // přidáno pro admin kontrolu
        header('Location: index.php');
        exit;
    } else {
        $errors[] = 'Neplatné přihlašovací údaje.';
    }
}
$pageTitle = 'Přihlášení';
include './inc/layoutAuth.php';

?>
<form method="post" class="w-25 card d-flex flex-column align-items-center">
    <h1 class=""><?php echo (!empty($pageTitle) ? $pageTitle : '') ?></h1>
    <?php
    if (!empty($errors)) {
        foreach ($errors as $error) {
            echo '<div class="alert alert-danger w-100">' . htmlspecialchars($error) . '</div>';
        }
    }
    ?>
    <div class="form-group w-100">
        <label for="email">Email</label>
        <input type="email" class="form-control" id="email" name="email" required
            value="<?php echo htmlspecialchars(@$_POST['email'] ?? ''); ?>">
    </div>
    <div class="form-group w-100">
        <label for="password">Heslo</label>
        <input type="password" class="form-control" id="password" name="password" required>
    </div>
    <button type="submit" class="btn btn-primary w-100 mb-2">Přihlásit se</button>
    <?php
    #region Facebook login
    
    $appId = '737208485413000';
    $redirectUri = urlencode('https://eso.vse.cz/~kovp07/semestralka/fbCallback.php');
    $state = bin2hex(random_bytes(8)); // pro CSRF ochranu, ulož do $_SESSION['fb_state']
    $_SESSION['fb_state'] = $state;

    $fbLoginUrl = "https://www.facebook.com/v19.0/dialog/oauth?client_id=$appId&redirect_uri=$redirectUri&state=$state&scope=email";
    echo '<a href="' . $fbLoginUrl . '" class="btn btn-primary w-100 mb-4">Přihlásit se přes Facebook</a>';


    #endregion Facebook login
    ?>
    <a href="./signup.php">Vytvořit účet!</a>

</form>

<?php
include 'inc/footer.php';
?>