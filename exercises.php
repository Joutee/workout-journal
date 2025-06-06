<?php
require_once __DIR__ . '/inc/user.php';
$pageTitle = 'Moje cviky';
include __DIR__ . '/inc/layoutApp.php';
?>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">


<?php
$mgQuery = $db->prepare('SELECT muscle_group_id, name FROM muscle_group ORDER BY name');
$mgQuery->execute();
$muscleGroupsAll = $mgQuery->fetchAll(PDO::FETCH_ASSOC);

$selectedMg = isset($_GET['muscle_group']) && is_array($_GET['muscle_group']) ? array_map('intval', $_GET['muscle_group']) : [];
?>

<a href="newExercise.php" class="btn btn-primary mt-4
">
    <i class="bi bi-plus me-1"></i> Přidat
</a>

<hr class="divider">
<div class="mb-3 d-flex justify-content-between align-items-center flex-wrap">
    <form method="get" class="d-flex  flex-wrap flex-column">
        <div class="d-flex flex-wrap">
            <?php foreach ($muscleGroupsAll as $mg): ?>
                <div class="col-auto ml-1">
                    <label class="form-check-label" style="min-width: 100px;">
                        <input type="checkbox" class="form-check-input me-1" name="muscle_group[]"
                            value="<?= $mg['muscle_group_id'] ?>" <?= in_array($mg['muscle_group_id'], $selectedMg) ? 'checked' : '' ?>>
                        <?= htmlspecialchars($mg['name']) ?>
                    </label>
                </div>
            <?php endforeach; ?>
        </div>
        <div class="mt-2">
            <button type="submit" class="btn btn-primary btn-sm mr-2">Filtrovat</button>
            <a href="exercises.php" class="btn btn-secondary btn-sm ms-2">Zrušit filtr</a>
        </div>
    </form>
</div>

<?php
$sql = '
        SELECT e.*, mg.name AS muscle_group_name, mg.muscle_group_id
        FROM exercise e
        LEFT JOIN exercise_muscle_group emg ON e.exercise_id = emg.exercise_id
        LEFT JOIN muscle_group mg ON emg.muscle_group_id = mg.muscle_group_id
        WHERE e.user_id = ?
    ';
$params = [$_SESSION['user_id']];

if (!empty($selectedMg)) {
    $placeholders = implode(',', array_fill(0, count($selectedMg), '?'));
    $sql .= " AND e.exercise_id IN (
            SELECT emg2.exercise_id
            FROM exercise_muscle_group emg2
            WHERE emg2.muscle_group_id IN ($placeholders)
        )";
    $params = array_merge($params, $selectedMg);
}

$sql .= ' ORDER BY e.name DESC, mg.name';

$query = $db->prepare($sql);
$query->execute($params);
$rows = $query->fetchAll(PDO::FETCH_ASSOC);

$exercises = [];
if (empty($rows)) {
    echo '<div class="alert alert-info">Žádné cviky nenalezeny.</div>';
} else {
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
}
?>

<div class="mt-1">
    <div class="d-flex flex-wrap">
        <?php foreach ($exercises as $exercise): ?>
            <div class="card bg-dark-custom text-light shadow mb-4 mr-4 d-flex flex-column flex-md-row align-items-md-center"
                style="flex: 1 1 320px; min-width: 280px; max-width: 350px;">
                <div class="flex-grow-1">
                    <div class="d-flex flex-row justify-content-between align-items-start">
                        <h3 class="card-title text-warning mb-2"><?= htmlspecialchars($exercise['name']) ?></h3>
                        <div class="mt-3 mt-md-0 ms-md-4 d-flex flex-column justify-content-center">
                            <a href="editExercise.php?id=<?= urlencode($exercise['exercise_id']) ?>" title="Upravit"
                                style="color:inherit; font-size:1.4rem; display:inline-block;">
                                <i class="bi bi-pencil"></i>
                            </a>
                        </div>
                    </div>
                    <p class="mb-2"><?= htmlspecialchars($exercise['description']) ?></p>
                    <?php if (!empty($exercise['muscle_groups'])): ?>
                        <p class="mb-2">
                            <small class="text-secondary">Svalové skupiny:
                                <?= htmlspecialchars(implode(', ', $exercise['muscle_groups'])) ?>
                            </small>
                        </p>
                    <?php endif; ?>
                </div>


            </div>
        <?php endforeach; ?>
    </div>
</div>


<?php include 'inc/footer.php'; ?>