<?php
require_once __DIR__ . '/inc/db.php';
require_once __DIR__ . '/inc/user.php';


$defaultExercises = [
    ['name' => 'Dřep', 'description' => 'Základní cvik na stehna', 'muscle_group_ids' => [14, 15]],
    ['name' => 'Bench press', 'description' => 'Základní cvik na prsa', 'muscle_group_ids' => [1, 6]],
    ['name' => 'Mrtvý tah', 'description' => 'Základní cvik na záda', 'muscle_group_ids' => [2, 14]],
    ['name' => 'Bicepsový zdvih', 'description' => 'Cvik na biceps', 'muscle_group_ids' => [4]],
    ['name' => 'Tricepsový tlak', 'description' => 'Cvik na triceps', 'muscle_group_ids' => [5]],
    ['name' => 'Tlaky na ramena', 'description' => 'Cvik na ramena', 'muscle_group_ids' => [6]],
    ['name' => 'Výpony lýtek', 'description' => 'Cvik na lýtka', 'muscle_group_ids' => [15]],
    ['name' => 'Předloktí zdvih', 'description' => 'Cvik na předloktí', 'muscle_group_ids' => [16]],
    ['name' => 'Krčení ramen', 'description' => 'Cvik na krk', 'muscle_group_ids' => [17]],
    ['name' => 'Zkracovačky', 'description' => 'Cvik na břicho', 'muscle_group_ids' => [18]],
];

foreach ($defaultExercises as $exercise) {
    $query = $db->prepare('INSERT INTO exercise (user_id, name, description) VALUES (:user_id, :name, :description)');
    $query->execute([
        ':user_id' => $_SESSION['user_id'],
        ':name' => $exercise['name'],
        ':description' => $exercise['description']
    ]);

    $exercise_id = $db->lastInsertId();

    // Insert all muscle groups for this exercise
    $query2 = $db->prepare('INSERT INTO exercise_muscle_group (exercise_id, muscle_group_id) VALUES (:exercise_id, :muscle_group_id)');
    foreach ($exercise['muscle_group_ids'] as $mgid) {
        $query2->execute([
            ':exercise_id' => $exercise_id,
            ':muscle_group_id' => $mgid
        ]);
    }
}

