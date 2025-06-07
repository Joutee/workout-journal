<?php
require_once __DIR__ . '/inc/user.php';
$pageTitle = 'Tréninky';
include __DIR__ . '/inc/layoutApp.php';

$exQuery = $db->prepare('SELECT exercise_id, name FROM exercise WHERE user_id = ? ORDER BY name');
$exQuery->execute([$_SESSION['user_id']]);
$allExercises = $exQuery->fetchAll(PDO::FETCH_ASSOC);

$selectedEx = isset($_GET['exercise']) && is_array($_GET['exercise']) ? array_map('intval', $_GET['exercise']) : [];

?>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">


<a href="newWorkout.php" class="btn btn-primary"> <i class="bi bi-plus me-1"></i> Přidat</a>
<hr class="divider">
<div class="mb-1 d-flex justify-content-between align-items-center flex-wrap gap-2">
    <form method="get" class="d-flex  flex-wrap flex-column">
        <div class="d-flex flex-wrap gap-2">
            <?php foreach ($allExercises as $ex): ?>
                <div class="col-auto ml-1">
                    <label class="form-check-label" style="min-width: 160px;">
                        <input class="form-check-input me-1" type="checkbox" name="exercise[]"
                            value="<?= $ex['exercise_id'] ?>" <?= in_array($ex['exercise_id'], $selectedEx) ? 'checked' : '' ?>>
                        <?= htmlspecialchars($ex['name']) ?>
                    </label>
                </div>
            <?php endforeach; ?>
        </div>
        <div class="mt-2">
            <button type="submit" class="btn btn-primary btn-sm mr-2">Filtrovat</button>
            <a href="workouts.php" class="btn btn-secondary btn-sm ms-2">Zrušit filtr</a>
        </div>
    </form>

</div>

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
?>


<div class=" py-4">
    <?php foreach ($workouts as $workout): ?>
        <div
            class="card bg-dark-custom text-light shadow h-100 mb-4  d-flex flex-column flex-md-row justify-content-between align-items-start">


            <div class="flex-grow-1">
                <div class="d-flex flex-row justify-content-between align-items-start">
                    <h3 class="card-title text-warning mb-0"><?= htmlspecialchars($workout['name']) ?>
                    </h3>
                    <div class="d-flex flex-row align-items-start ms-3">
                        <a href="editWorkout.php?id=<?= urlencode($workout['workout_id']) ?>" title="Upravit"
                            style="color:inherit; font-size:1.4rem; display:inline-block; margin-right: 0.7rem;">
                            <i class="bi bi-pencil" style="font-size:1.2rem"></i>
                        </a>
                        <a href=" duplicateWorkout.php?id=<?= urlencode($workout['workout_id']) ?>" title="Duplikovat"
                            style="color:inherit; font-size:1.4rem; display:inline-block;">
                            <i class="bi bi-files" style="font-size:1.2rem"></i>
                        </a>
                    </div>
                </div>
                <div class=" mb-2 text-secondary small"><?= htmlspecialchars($workout['date']) ?>
                </div>
                <div class="mb-2"><?= nl2br(htmlspecialchars($workout['note'])) ?></div>
                <hr class="divider">
                <?php if (!empty($workout['sets'])): ?>
                    <ul class="mb-2 list-unstyled">
                        <li class="row mb-1 fw-bold text-warning">
                            <div class="col-5 font-weight-bold">Cvik</div>
                            <div class="col-2 font-weight-bold">Opakování</div>
                            <div class="col-2 font-weight-bold">Váha</div>
                        </li>
                        <?php foreach ($workout['sets'] as $set): ?>
                            <li class="row mb-1">
                                <div class="col-5"><?= htmlspecialchars($set['exercise_name']) ?></div>
                                <div class="col-2"><?= htmlspecialchars($set['repetitions']) ?></div>
                                <div class="col-2"><?= htmlspecialchars($set['weight']) ?> kg</div>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
            </div>

        </div>
    <?php endforeach; ?>
</div>

<?php
include 'inc/footer.php';
?>