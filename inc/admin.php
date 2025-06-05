<?php
//require_once __DIR__ . '/user.php';



if (!empty($_SESSION['user_id'])) {
    $query = $db->prepare('SELECT * FROM user WHERE user_id=:user_id AND admin = 1  LIMIT 1;');
    $query->execute([
        ':user_id' => $_SESSION['user_id']
    ]);
    $user = $query->fetch(PDO::FETCH_ASSOC);


    if (!$user) {
        header('Location: index.php');
        exit;
    }
} else {
    header('Location: index.php');
    exit;
}