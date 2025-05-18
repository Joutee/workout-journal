<?php
require_once 'inc/user.php';
$pageTitle = 'Tréninky';
include 'inc/layoutApp.php';

echo '<a href="newWorkout.php" class="btn btn-success mb-3">Přidat nový trénink</a>';

$exQuery = $db->prepare('SELECT exercise_id, name FROM exercise WHERE user_id = ? ORDER BY name');
$exQuery->execute([$_SESSION['user_id']]);
$allExercises = $exQuery->fetchAll(PDO::FETCH_ASSOC);

$selectedEx = isset($_GET['exercise']) && is_array($_GET['exercise']) ? array_map('intval', $_GET['exercise']) : [];

?>
<form method="get" style="margin-bottom:20px;">
    <fieldset style="border:1px solid #eee; border-radius:8px; padding:16px; display:inline-block;">
        <legend style="font-weight:bold;">Filtrovat podle cviku:</legend>
        <div style="display:flex; flex-wrap:wrap; gap:16px; align-items:center;">
            <?php foreach ($allExercises as $ex): ?>
                <label style="margin-right:12px;">
                    <input type="checkbox" name="exercise[]" value="<?= $ex['exercise_id'] ?>"
                        <?= in_array($ex['exercise_id'], $selectedEx) ? 'checked' : '' ?>>
                    <?= htmlspecialchars($ex['name']) ?>
                </label>
            <?php endforeach; ?>
        </div>
        <div style="margin-top:12px;">
            <button type="submit" class="btn btn-primary btn-sm">Filtrovat</button>
            <a href="workouts.php" class="btn btn-secondary btn-sm" style="margin-left:8px;">Zrušit filtr</a>
        </div>
    </fieldset>
</form>
<?php

$sql = '
    SELECT w.*, es.exercise_set_id, es.repetitions, es.weight, e.name AS exercise_name, e.exercise_id
    FROM workout w
    LEFT JOIN exercise_set es ON w.workout_id = es.workout_id
    LEFT JOIN exercise e ON es.exercise_id = e.exercise_id
    WHERE w.user_id = ?
';
$params = [$_SESSION['user_id']];

if (!empty($selectedEx)) {
    $placeholders = implode(',', array_fill(0, count($selectedEx), '?'));
    $sql .= " AND w.workout_id IN (
        SELECT es2.workout_id
        FROM exercise_set es2
        WHERE es2.exercise_id IN ($placeholders)
    )";
    $params = array_merge($params, $selectedEx);
}

$sql .= ' ORDER BY w.date DESC, w.workout_id, es.exercise_set_id';

$query = $db->prepare($sql);
$query->execute($params);
$rows = $query->fetchAll(PDO::FETCH_ASSOC);

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