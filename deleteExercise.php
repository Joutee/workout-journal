<?php
require_once 'inc/user.php';

if (empty($_REQUEST['id'])) {
    header('Location: index.php');
    exit;
}

$query = $db->prepare('DELETE FROM exercise WHERE exercise_id=:exercise_id;');
$result = $query->execute([
    ':exercise_id' => $_REQUEST['id']
]);
if (!$result) {
    echo '<div class="alert alert-danger">Cvik se nepodařilo smazat.</div>';
} else {
    echo '<div class="alert alert-danger">Cvik úspěšně smazán.</div>';
}
exit;