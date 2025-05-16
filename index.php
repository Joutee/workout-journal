<?php
require_once 'inc/db.php';


include 'inc/header.php';

$query = $db->prepare('SELECT
                           *
                           FROM workout;');
$query->execute();

if (empty($_SESSION['user_id'])) {
    header('Location: signin.php');
    exit;
}
$workouts = $query->fetchAll(PDO::FETCH_ASSOC);
if (!empty($workouts)) {
    foreach ($workouts as $workout) {
        echo '<div><h2>' . htmlspecialchars($workout['name']) . '</h2>';
        echo '<p>' . htmlspecialchars($workout['date']) . '</p>';
        echo '<p>' . htmlspecialchars($workout['note']) . '</p></div>';

    }
}





include 'inc/footer.php';