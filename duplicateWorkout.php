<?php
require_once __DIR__.'/inc/user.php';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Location: workouts.php');
    exit;
}

$workout_id = $_GET['id'];

$query = $db->prepare('SELECT * FROM workout WHERE workout_id = :workout_id AND user_id = :user_id');
$query->execute([
    ':workout_id' => $workout_id,
    ':user_id' => $_SESSION['user_id']
]);
$original = $query->fetch(PDO::FETCH_ASSOC);

if (!$original) {
    header('Location: workouts.php');
    exit;
}

$newName = $original['name'] . ' - kopie';
$newDate = date('Y-m-d H:i:s');
$newNote = $original['note'] ?? '';

$insert = $db->prepare('INSERT INTO workout (user_id, name, date, note) VALUES (:user_id, :name, :date, :note)');
$insert->execute([
    ':user_id' => $_SESSION['user_id'],
    ':name' => $newName,
    ':date' => $newDate,
    ':note' => $newNote
]);
$newWorkoutId = $db->lastInsertId();

$sets = $db->prepare('SELECT * FROM exercise_set WHERE workout_id = :workout_id');
$sets->execute([
    ':workout_id' => $workout_id
]);
while ($set = $sets->fetch(PDO::FETCH_ASSOC)) {
    $insertSet = $db->prepare('INSERT INTO exercise_set (workout_id, exercise_id, repetitions, weight) VALUES (:workout_id, :exercise_id, :repetitions, :weight)');
    $insertSet->execute([
        ':workout_id' => $newWorkoutId,
        ':exercise_id' => $set['exercise_id'],
        ':repetitions' => $set['repetitions'],
        ':weight' => $set['weight']
    ]);
}

// Přesměruj na editaci nového workoutu
header('Location: workouts.php');
exit;