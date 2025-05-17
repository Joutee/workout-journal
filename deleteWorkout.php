<?php
require_once 'inc/user.php';

if (empty($_REQUEST['id'])) {
    header('Location: index.php');
    exit;
}

$query = $db->prepare('DELETE FROM workout WHERE workout_id=:workout_id;');
$result = $query->execute([
    ':workout_id' => $_REQUEST['id']
]);
if (!$result) {
    echo '<div class="alert alert-danger">Trénink se nepodařilo smazat.</div>';
} else {
    echo '<div class="alert alert-danger">Trénink úspěšně smazán.</div>';
}
exit;
