<?php
require_once __DIR__ . '/inc/user.php';
require_once __DIR__ . '/inc/admin.php';
if (!isUserAdmin($db, $_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}
include __DIR__ . '/inc/layoutApp.php';

if (empty($_REQUEST['id'])) {
    header('Location: index.php');
    exit;
}


try {
    $query = $db->prepare('DELETE FROM user WHERE user_id = :user_id');
    $query->execute([
        ':user_id' => $_REQUEST['id']
    ]);
    //header('Location: editMuscleGroup.php');

    echo '<div class="alert alert-success">Uživatele skupinu se podařilo úspěšně smazat.</div>';
} catch (PDOException $e) {
    echo '<div class="alert alert-danger">' . $e->getMessage() . '</div>';
}

exit;
