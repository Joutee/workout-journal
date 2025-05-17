<?php
require_once 'inc/user.php';
$pageTitle = 'Tréninky';
include 'inc/layoutApp.php';

echo '<a href="newWorkout.php" class="btn btn-success mb-3">Přidat nový trénink</a>';

// Jeden dotaz s JOINy na všechny workouty a jejich série
$query = $db->prepare('
    SELECT w.*, es.exercise_set_id, es.repetitions, es.weight, e.name AS exercise_name
    FROM workout w
    LEFT JOIN exercise_set es ON w.workout_id = es.workout_id
    LEFT JOIN exercise e ON es.exercise_id = e.exercise_id
    WHERE w.user_id = :user_id
    ORDER BY w.date DESC, w.workout_id, es.exercise_set_id
');
$query->execute([
    ':user_id' => $_SESSION['user_id']
]);
$rows = $query->fetchAll(PDO::FETCH_ASSOC);

// Seskupení dat podle workoutu
$workouts = [];
foreach ($rows as $row) {
    $workout_id = $row['workout_id'];
    if (!isset($workouts[$workout_id])) {
        $workouts[$workout_id] = [
            'name' => $row['name'],
            'date' => $row['date'],
            'note' => $row['note'],
            'workout_id' => $workout_id,
            'sets' => []
        ];
    }
    if ($row['exercise_set_id']) {
        $workouts[$workout_id]['sets'][] = [
            'exercise_name' => $row['exercise_name'],
            'repetitions' => $row['repetitions'],
            'weight' => $row['weight']
        ];
    }
}

// Výpis
foreach ($workouts as $workout) {
    echo '<a href="editWorkout.php?id=' . urlencode($workout['workout_id']) . '" style="text-decoration:none; color:inherit;">';
    echo '<div><h2>' . htmlspecialchars($workout['name']) . '</h2>';
    echo '<p>' . htmlspecialchars($workout['date']) . '</p>';
    echo '<p>' . htmlspecialchars($workout['note']) . '</p>';
    if (!empty($workout['sets'])) {
        echo '<ul>';
        foreach ($workout['sets'] as $set) {
            echo '<li>' . htmlspecialchars($set['exercise_name']) . ': ';
            echo htmlspecialchars($set['repetitions']) . 'x, ';
            echo htmlspecialchars($set['weight']) . ' kg</li>';
        }
        echo '</ul>';
    }
    echo '</div>';
    echo '</a>';
}

include 'inc/footer.php';