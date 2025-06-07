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
    $query = $db->prepare('DELETE FROM muscle_group WHERE muscle_group_id = :muscle_group_id');
    $query->execute([
        ':muscle_group_id' => $_REQUEST['id']
    ]);
    //header('Location: editMuscleGroup.php');

    echo '<div class="alert alert-success">Svalovou skupinu se podařilo úspěšně smazat.</div>';
} catch (PDOException $e) {
    if ($e->getCode() == 23000) {
        echo '<div class="alert alert-danger">Tuto svalovou skupinu nelze smazat, protože je přiřazena k nějakému cviku.</div>';
    } else {
        echo '<div class="alert alert-danger">' . $e->getMessage() . '</div>';
    }
}

exit;
