<?php
require_once __DIR__ . '/inc/user.php';
require_once __DIR__ . '/inc/admin.php';
$pageTitle = 'Úprava Svalových skupin';
include __DIR__ . '/inc/layoutApp.php';


$query = $db->query('SELECT muscle_group_id, name FROM muscle_group ORDER BY name');
$query->execute();
$muscleGroups = $query->fetchAll(PDO::FETCH_ASSOC);

$errors = [];

if (!empty($_POST['name'])) {
    $name = trim($_POST['name']);

    if (!empty($name)) {
        $query = $db->prepare('INSERT INTO muscle_group (name) VALUES (:name)');
        $query->execute([
            ':name' => $name
        ]);
        header('Location: editMuscleGroup.php');
        exit;
    } else {
        $error = 'Název svalové skupiny nesmí být prázdný.';
    }
}




if (!empty($errors)) {
    foreach ($errors as $error) {
        echo '<p style="color:red;">' . htmlspecialchars($error) . '</p>';
    }
}
?>

<div class="">
    <form method="post" class="mb-4">
        <div class="form-group">
            <label for="name">Název nové svalové skupiny</label>
            <input type="text" class="form-control w-25" id="name" name="name" required>
        </div>
        <button type="submit" class="btn btn-primary">Přidat</button>
    </form>
    <hr class="mb-4 divider">
    <h2 class="">Seznam svalových skupin</h2>
    <ul class="list-group">
        <?php foreach ($muscleGroups as $muscles): ?>
            <li class="list-group-item d-flex justify-content-between align-items-center">
                <?= htmlspecialchars($muscles['name']) ?>
                <form method="post" action="deleteMuscleGroup.php" style="display:inline;">
                    <input type="hidden" name="id" value="<?= (int) $muscles['muscle_group_id'] ?>">
                    <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Opravdu smazat?');"
                        title="Smazat">
                        &times;
                    </button>
                </form>
            </li>
        <?php endforeach; ?>
    </ul>