<?php
require_once 'inc/user.php';
$pageTitle = 'Tréninky';
include 'inc/layoutApp.php';

echo '<a href="newWorkout.php" class="btn btn-success mb-3">Přidat nový trénink</a>';

$query = $db->prepare('SELECT * FROM workout ORDER BY date DESC;');
$query->execute();
$workouts = $query->fetchAll(PDO::FETCH_ASSOC);

if (!empty($workouts)) {
    foreach ($workouts as $workout) {
        echo '<a href="workout_detail.php?id=' . urlencode($workout['workout_id']) . '" style="text-decoration:none; color:inherit;">';
        echo '<div><h2>' . htmlspecialchars($workout['name']) . '</h2>';
        echo '<p>' . htmlspecialchars($workout['date']) . '</p>';
        echo '<p>' . htmlspecialchars($workout['note']) . '</p></div>';
        echo '</a>';
    }
}

include 'inc/footer.php';