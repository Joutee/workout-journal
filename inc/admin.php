<?php
//require_once __DIR__ . '/user.php';


function isUserAdmin(PDO $db, $userId) {
    $query = $db->prepare('SELECT * FROM user WHERE user_id=:user_id AND admin = 1  LIMIT 1;');
    $query->execute([
        ':user_id' => $_SESSION['user_id']
    ]);
    return (bool)$query->fetch(PDO::FETCH_ASSOC);
}
