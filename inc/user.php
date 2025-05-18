<?php
require_once __DIR__.'/db.php';
session_start();

function logout_and_redirect()
{
    session_unset();
    session_destroy();
    header('Location: signin.php');
    exit;
}

if (!empty($_SESSION['user_id'])) {
    $query = $db->prepare('SELECT * FROM user WHERE user_id=:user_id LIMIT 1;');
    $query->execute([
        ':user_id' => $_SESSION['user_id']
    ]);
    $user = $query->fetch(PDO::FETCH_ASSOC);
    if ($user) {
        $_SESSION['user_full_name'] = [$user['name'], $user['surname']];
    } else {
        logout_and_redirect();
    }
} else {
    logout_and_redirect();
}