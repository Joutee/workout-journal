<?php
require_once __DIR__ . '/inc/user.php';
include __DIR__ . '/inc/layoutApp.php';

if (empty($_GET['id'])) {
    header('Location: index.php');
    exit;
}


$query = $db->prepare('SELECT workout_id FROM workout WHERE workout_id = :workout_id AND user_id = :user_id LIMIT 1;');
$query->execute([
    ':workout_id' => $_POST['id'],
    ':user_id' => $_SESSION['user_id']
]);
$workout = $query->fetch(PDO::FETCH_ASSOC);

if (!$workout) {
    echo '<div class="alert alert-danger">Nemáte oprávnění smazat tento trénink.</div>';
    exit;
}


$query = $db->prepare('DELETE FROM workout WHERE workout_id=:workout_id;');
$result = $query->execute([
    ':workout_id' => $_REQUEST['id']
]);
if (!$result) {
    echo '<div class="alert alert-danger">Trénink se nepodařilo smazat.</div>';
} else {
    echo '<div class="alert alert-success">Trénink úspěšně smazán.</div>';
}
