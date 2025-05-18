<?php
require_once __DIR__ . '/inc/user.php';
$pageTitle = 'Moje cviky';
include __DIR__ . '/inc/layoutApp.php';
?>

<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <a href="newExercise.php" class="btn btn-success">Přidat nový cvik</a>
    </div>

    <?php
    $mgQuery = $db->prepare('SELECT muscle_group_id, name FROM muscle_group ORDER BY name');
    $mgQuery->execute();
    $muscleGroupsAll = $mgQuery->fetchAll(PDO::FETCH_ASSOC);

    $selectedMg = isset($_GET['muscle_group']) && is_array($_GET['muscle_group']) ? array_map('intval', $_GET['muscle_group']) : [];
    ?>

    <form method="get" class="mb-4">
        <fieldset class="border rounded p-3 w-100">
            <legend class="fw-bold">Filtrovat podle svalové skupiny:</legend>
            <div class="row">
                <?php foreach ($muscleGroupsAll as $mg): ?>
                    <div class="col-12 col-sm-6 col-md-4 col-lg-3 mb-2">
                        <label class="form-check-label w-100">
                            <input type="checkbox" class="form-check-input me-1" name="muscle_group[]"
                                value="<?= $mg['muscle_group_id'] ?>" <?= in_array($mg['muscle_group_id'], $selectedMg) ? 'checked' : '' ?>>
                            <?= htmlspecialchars($mg['name']) ?>
                        </label>
                    </div>
                <?php endforeach; ?>
            </div>
            <div class="mt-3">
                <button type="submit" class="btn btn-primary btn-sm">Filtrovat</button>
                <a href="exercises.php" class="btn btn-secondary btn-sm ms-2">Zrušit filtr</a>
            </div>
        </fieldset>
    </form>

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

    <div class="row g-3">
        <?php foreach ($exercises as $exercise): ?>
            <div class="col-md-6 col-lg-4">
                <a href="editExercise.php?id=<?= urlencode($exercise['exercise_id']) ?>"
                    class="text-decoration-none text-reset">
                    <div class="card shadow-sm h-100 bg-dark-custom">
                        <div class="card-body">
                            <h5 class="card-title text-primary-custom"><?= htmlspecialchars($exercise['name']) ?></h5>
                            <p class="card-text"><?= htmlspecialchars($exercise['description']) ?></p>
                            <?php if (!empty($exercise['muscle_groups'])): ?>
                                <p class="mb-0">
                                    <small class="text-muted">Svalové skupiny:
                                        <?= htmlspecialchars(implode(', ', $exercise['muscle_groups'])) ?></small>
                                </p>
                            <?php endif; ?>
                        </div>
                    </div>
                </a>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<?php include 'inc/footer.php'; ?>