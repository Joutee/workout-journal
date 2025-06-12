<?php
require_once __DIR__ . '/inc/anonymUser.php';
require_once 'vendor/autoload.php';

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

// Google OAuth konfigurace
$client = new Google_Client();
$client->setClientId('272107211808-87pu5hhcaah06otrmqv7upi8e2nqv839.apps.googleusercontent.com');
$client->setClientSecret('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');
$client->setRedirectUri('https://eso.vse.cz/~kovp07/semestralka/googleCallback.php');
$client->addScope('email');
$client->addScope('profile');

// Přidání state parametru pro bezpečnost
$state = bin2hex(random_bytes(16));
$_SESSION['google_state'] = $state;
$client->setState($state);

// Vygeneruj URL pro přihlášení
$loginUrl = $client->createAuthUrl();

?>
<form method="post" class="w-25 card d-flex flex-column align-items-center login-card">
    <h2 class=""><?php echo (!empty($pageTitle) ? $pageTitle : '') ?></h2>
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

    <a href="<?= htmlspecialchars($loginUrl) ?>" class="btn btn-danger w-100 mb-2">Přihlásit se přes Google</a>
    <a href="./signup.php">Vytvořit účet!</a>
    <a href="./forgotPassword.php">Zapomenuté heslo</a>

</form>

<?php
include 'inc/footer.php';
?>