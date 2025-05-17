<?php
require_once 'inc/user.php';
$pageTitle = 'Moje cviky';
include 'inc/layoutApp.php';

echo '<a href="newExercise.php" class="btn btn-success mb-3">Přidat nový cvik</a>';


$query = $db->prepare('
    SELECT e.*, mg.name AS muscle_group_name
    FROM exercise e
    LEFT JOIN exercise_muscle_group emg ON e.exercise_id = emg.exercise_id
    LEFT JOIN muscle_group mg ON emg.muscle_group_id = mg.muscle_group_id
    WHERE e.user_id = :user_id
    ORDER BY e.name DESC, mg.name
');
$query->execute([
    ':user_id' => $_SESSION['user_id']
]);
$rows = $query->fetchAll(PDO::FETCH_ASSOC);

$exercises = [];
foreach ($rows as $row) {
    $id = $row['exercise_id'];
    if (!isset($exercises[$id])) {
        $exercises[$id] = [
            'name' => $row['name'],
            'description' => $row['description'],
            'muscle_groups' => [],
            'exercise_id' => $id
        ];
    }
    if ($row['muscle_group_name']) {
        $exercises[$id]['muscle_groups'][] = $row['muscle_group_name'];
    }
}


foreach ($exercises as $exercise) {
    echo '<a href="exercise_detail.php?id=' . urlencode($exercise['exercise_id']) . '" style="text-decoration:none; color:inherit;">';
    echo '<div><b>' . htmlspecialchars($exercise['name']) . '</b>';
    echo '<p>' . htmlspecialchars($exercise['description']) . '</p>';
    if (!empty($exercise['muscle_groups'])) {
        echo '<p>' . htmlspecialchars(implode(' ', $exercise['muscle_groups'])) . '</p>';
    }
    echo '</div>';
    echo '</a>';
}

include 'inc/footer.php';
?>