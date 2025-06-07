<?php
require_once __DIR__.'/inc/user.php';
$pageTitle = 'Nový cvik';
include __DIR__.'/inc/layoutApp.php';

#region muscle_group query
$muscleGroups = [];
$query = $db->query('SELECT muscle_group_id, name FROM muscle_group ORDER BY name');
while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
    $muscleGroups[] = $row;
}
#endregion muscle_group query

$errors = [];
$selectedMuscles = [];

if (!empty($_POST)) {
    $name = trim($_POST['name']);
    $description = trim($_POST['description']);
    $muscle_group_ids = $_POST['muscle_group_ids'] ?? [];
    $selectedMuscles = $muscle_group_ids;

    #region form validation
    if (strlen($description) > 300) {
        $errors['description'] = 'Popis cviku musí být kratší než 300 znaků.';
    }
    if (empty($name)) {
        $errors['name'] = 'Název cviku je povinný.';
    } else {
        if (strlen($name) > 50) {
            $errors['name'] = 'Název cviku musí být kratší něž 50 znaků.';
        }
    }
    if (empty($muscle_group_ids)) {
        $errors['muscle_group_ids'] = 'Vyberte alespoň jednu svalovou skupinu.';
    }
    #endregion form validation

    #region database insertion
    if (empty($errors)) {
        // exercise insertion
        $query = $db->prepare('INSERT INTO exercise (user_id, name, description) VALUES (:user_id, :name, :description)');
        $result = $query->execute([
                ':user_id' => $_SESSION['user_id'],
                ':name' => $name,
                ':description' => $description
              ]);
        if ($result) {
            $exercise_id = $db->lastInsertId();
            // muscle_group insertion        
            $query = $db->prepare('INSERT INTO exercise_muscle_group (exercise_id, muscle_group_id) VALUES (:exercise_id, :muscle_group_id)');
            foreach ($muscle_group_ids as $muscle_group_id) {
                $query->execute([
                    ':exercise_id' => $exercise_id,
                    ':muscle_group_id' => $muscle_group_id
                ]);
            }
            echo '<div class="alert alert-success">Cvik byl úspěšně přidán.</div>';
            header('Location: exercises.php');
            exit;
        } else {
            $errors[] = 'Došlo k chybě při vkládání cviku.';
        }
    }
    #endregion database insertion
}
if (!empty($errors)) {
    foreach ($errors as $error) {
        echo '<div class="alert alert-danger w-50">' . htmlspecialchars($error) . '</div>';
    }
}
?>
<div class="card">
<form method="post" action="">
    <div class="form-group">
        <label for="name">Název cviku</label>
        <input type="text" class="form-control w-50" id="name" name="name" required
            value="<?php echo htmlspecialchars(@$_POST['name'] ?? ''); ?>">
    </div>
    <div class="form-group">
        <label for="description">Popis (volitelné)</label>
        <textarea class="form-control w-50" id="description"
            name="description"><?php echo htmlspecialchars(@$_POST['description'] ?? ''); ?></textarea>
    </div>
    <div class="form-group">
        <label>Svalové skupiny</label><br>
        <?php foreach ($muscleGroups as $group): ?>
            <div class="form-check form-check-inline">
                <input
                    class="form-check-input"
                    type="checkbox"
                    id="mg_<?php echo $group['muscle_group_id']; ?>"
                    name="muscle_group_ids[]"
                    value="<?php echo $group['muscle_group_id']; ?>"
                    <?php if (!empty($selectedMuscles) && in_array($group['muscle_group_id'], $selectedMuscles)) echo 'checked'; ?>
                >
                <label class="form-check-label" for="mg_<?php echo $group['muscle_group_id']; ?>">
                    <?php echo htmlspecialchars($group['name']); ?>
                </label>
            </div>
        <?php endforeach; ?>
    </div>
    <button type="submit" class="btn btn-primary mr-2">Přidat</button>
    <a href="exercises.php" class="btn btn-secondary">Zrušit</a>
</form>
</div>

<?php
include 'inc/footer.php';
?>