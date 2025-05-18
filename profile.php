<?php
require_once __DIR__.'/inc/user.php';
$pageTitle = 'Profil';
include __DIR__.'/inc/layoutApp.php';

echo '<a href="changePassword.php" class="btn btn-primary">Změnit heslo</a>';


include 'inc/footer.php';
?>