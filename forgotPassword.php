<?php
require_once __DIR__ . '/inc/anonymUser.php';

$errors = [];
$success = '';

if (!empty($_POST['email'])) {
    $email = trim($_POST['email']);

    if (empty($email)) {
        $errors['email'] = 'E-mail je povinný.';
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = 'Neplatný formát e-mailu.';
    }

    if (empty($errors)) {
        $query = $db->prepare('SELECT * FROM user WHERE email=:email LIMIT 1;');
        $query->execute([':email' => $email]);
        $user = $query->fetch(PDO::FETCH_ASSOC);

        if ($user) {
            $resetToken = bin2hex(random_bytes(16));
            $expiryTime = date('Y-m-d H:i:s', strtotime('+1 hour'));

            $updateQuery = $db->prepare('UPDATE user SET reset_token=:reset_token, reset_expiry=:reset_expiry WHERE user_id=:user_id;');
            $updateQuery->execute([
                ':reset_token' => $resetToken,
                ':reset_expiry' => $expiryTime,
                ':user_id' => $user['user_id']
            ]);

            $resetLink = 'https://eso.vse.cz/~kovp07/semestralka/resetPassword.php?token=' . urlencode($resetToken);

            $headers = "MIME-Version: 1.0\r\n";
            $headers .= "Content-Type: text/plain; charset=UTF-8\r\n";
            $headers .= "From: noreply@fit-journal.cz\r\n";
            mail($email, 'Obnoveni hesla', 'Kliknete na tento odkaz pro obnoveni hesla: ' . $resetLink, $headers);
            $success = 'Odkaz pro obnovení hesla byl odeslán na váš e-mail.';
        }
    }
}
$pageTitle = 'Přihlášení';
include './inc/layoutAuth.php';
?>



<div class="card container w-25 login-card">
    <form method="post" class="">
        <h2 class="mb-4 text-center">Zapomenuté heslo</h2>
        <?php
        if (!empty($errors)) {
            foreach ($errors as $error) {
                echo '<div class="alert alert-danger w-100">' . htmlspecialchars($error) . '</div>';
            }
        }
        ?>

        <?php if ($success): ?>
            <div class="alert alert-success w-100"><?= htmlspecialchars($success) ?></div>
        <?php endif; ?>


        <div class="mb-3">
            <label for="email" class="form-label">E-mail</label>
            <input type="email" name="email" id="email" class="form-control" required>
        </div>

        <button type="submit" class="btn btn-primary w-100">Odeslat odkaz</button>

        <a href="signin.php" class="d-block text-center mt-3">Zpět na přihlášení</a>
    </form>
</div>