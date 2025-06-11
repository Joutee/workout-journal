<?php
require_once __DIR__ . '/inc/user.php';
include __DIR__ . '/inc/layoutApp.php';

if (empty($_GET['id'])) {
    header('Location: index.php');
    exit;
}

$query = $db->prepare('SELECT exercise_id FROM exercise WHERE exercise_id = :exercise_id AND user_id = :user_id LIMIT 1;');
$query->execute([
    ':exercise_id' => $_REQUEST['id'],
    ':user_id' => $_SESSION['user_id']
]);
$exercise = $query->fetch(PDO::FETCH_ASSOC);

if (!$exercise) {
    echo '<div class="alert alert-danger">Nemáte oprávnění smazat tento cvik.</div>';
    exit;
}


try {
    $query = $db->prepare('DELETE FROM exercise WHERE exercise_id=:exercise_id;');
    $result = $query->execute([
        ':exercise_id' => $_REQUEST['id']
    ]);
    echo '<div class="alert alert-success">Cvik se podařilo úspěšně smazat.</div>';

} catch (PDOException $e) {
    if ($e->getCode() == 23000) {
        echo '<div class="alert alert-danger">Cvik nelze smazat, protože je přiřazen k nějakému tréninku.</div>';
        exit;
    } else {
        echo '<div class="alert alert-danger">' . $e->getMessage() . '</div>';
        exit;
    }
}


exit; //testik