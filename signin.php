<?php
require_once 'inc/db.php';




$pageTitle = 'Přihlášení';
include 'inc/header.php';
?>

<body>
    <h2 class="text-center">Přihlášení</h2>
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
</body>
<?php
include 'inc/footer.php';
?>