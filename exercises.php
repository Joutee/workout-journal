<?php
require_once 'inc/user.php';
$pageTitle = 'Moje cviky';
include 'inc/layoutApp.php';

echo '<a href="newExercise.php" class="btn btn-success mb-3">Přidat nový cvik</a>';

$mgQuery = $db->prepare('SELECT muscle_group_id, name FROM muscle_group ORDER BY name');
$mgQuery->execute();
$muscleGroupsAll = $mgQuery->fetchAll(PDO::FETCH_ASSOC);

$selectedMg = isset($_GET['muscle_group']) && is_array($_GET['muscle_group']) ? array_map('intval', $_GET['muscle_group']) : [];

?>
<form method="get" style="margin-bottom:20px;">
    <fieldset style="border:1px solid #eee; border-radius:8px; padding:16px; display:inline-block;">
        <legend style="font-weight:bold;">Filtrovat podle svalové skupiny:</legend>
        <div style="display:flex; flex-wrap:wrap; gap:16px; align-items:center;">
            <?php foreach ($muscleGroupsAll as $mg): ?>
                <label style="margin-right:12px;">
                    <input type="checkbox" name="muscle_group[]" value="<?= $mg['muscle_group_id'] ?>"
                        <?= in_array($mg['muscle_group_id'], $selectedMg) ? 'checked' : '' ?>>
                    <?= htmlspecialchars($mg['name']) ?>
                </label>
            <?php endforeach; ?>
        </div>
        <div style="margin-top:12px;">
            <button type="submit" class="btn btn-primary btn-sm">Filtrovat</button>
            <a href="exercises.php" class="btn btn-secondary btn-sm" style="margin-left:8px;">Zrušit filtr</a>
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
    echo '<p>Žádné cviky nenalezeny.</p>';
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

foreach ($exercises as $exercise) {
    echo '<a href="editExercise.php?id=' . urlencode($exercise['exercise_id']) . '" style="text-decoration:none; color:inherit;">';
    echo '<div><b>' . htmlspecialchars($exercise['name']) . '</b>';
    echo '<p>' . htmlspecialchars($exercise['description']) . '</p>';
    if (!empty($exercise['muscle_groups'])) {
        echo '<p><small>Svalové skupiny: ' . htmlspecialchars(implode(', ', $exercise['muscle_groups'])) . '</small></p>';
    }
    echo '</div>';
    echo '</a>';
}

include 'inc/footer.php';
?>