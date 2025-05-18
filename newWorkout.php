<?php
require_once 'inc/user.php';
$pageTitle = 'Nový trénink';


include 'inc/layoutApp.php';

$exercises = [];
$query = $db->prepare('SELECT exercise_id, name FROM exercise WHERE user_id=:user_id or user_id = 0 ORDER BY name;');
$query->execute([
    ':user_id' => $_SESSION['user_id']
]);
while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
    $exercises[] = $row;
}

$days = [
    1 => 'Pondělní',
    2 => 'Úterní',
    3 => 'Středeční',
    4 => 'Čtvrteční',
    5 => 'Páteční',
    6 => 'Sobotní',
    7 => 'Nedělní'
];

#region new workout
$errors = [];
if (!empty($_POST)) {
    $name = trim($_POST['name']);
    $date = $_POST['date'];
    $time = $_POST['time'];
    $note = trim($_POST['note']);
    $exerciseSets = $_POST['exercise_sets'] ?? [];
    if (empty($name)) {
        $errors['name'] = 'Název tréninku je povinný.';
    } elseif (strlen($name) > 100) {
        $errors['name'] = 'Název tréninku musí být kratší než 100 znaků.';
    } elseif (empty($date)) {
        $errors['date'] = 'Datum je povinné.';
    } elseif (empty($time)) {
        $errors['time'] = 'Čas je povinný.';
    } elseif (empty($exerciseSets)) {
        $errors['exercise_sets'] = 'Musíte přidat alespoň jednu cvičební sérii.';
    }

    $datetime = $date . ' ' . $time . ':00';

    if (empty($errors)) {
        $query = $db->prepare('INSERT INTO workout (user_id, name, date, note) VALUES (:user_id, :name, :date, :note)');
        $result = $query->execute([
            ':user_id' => $_SESSION['user_id'],
            ':name' => $name,
            ':date' => $datetime,
            ':note' => $note
        ]);
        if ($result) {
            $workout_id = $db->lastInsertId();
            foreach ($exerciseSets as $exerciseSet) {
                if (isset($exerciseSet['exercise_id']) && isset($exerciseSet['repetitions']) && isset($exerciseSet['weight'])) {
                    $query = $db->prepare('INSERT INTO exercise_set (workout_id, exercise_id, repetitions, weight) VALUES (:workout_id, :exercise_id, :repetitions, :weight)');
                    $query->execute([
                        ':workout_id' => $workout_id,
                        ':exercise_id' => $exerciseSet['exercise_id'],
                        ':repetitions' => $exerciseSet['repetitions'],
                        ':weight' => $exerciseSet['weight']
                    ]);
                } else {
                    $errors['exercise_sets'] = 'Všechny cvičební série musí mít vyplněné všechny údaje.';
                }
            }
            header('Location: workouts.php');
            exit;
        } else {
            $errors[] = 'Chyba při vkládání tréninku.';
        }
    }
}


#endregion new workout
if (!empty($errors)) {
    foreach ($errors as $error) {
        echo '<p style="color:red;">' . htmlspecialchars($error) . '</p>';
    }
}
?>

<form method="post" id="workoutForm">
    <label for="name">Název tréninku</label><br />
    <input type="text" name="name" id="name"
        value="<?php echo htmlspecialchars($_POST['name'] ?? $days[date('N')] . ' trénink'); ?>" required><br /><br />

    <label for="date">Datum</label><br />
    <input type="date" name="date" id="date" value="<?php echo htmlspecialchars($_POST['date'] ?? date('Y-m-d')); ?>"
        required><br /><br />

    <label for="time">Čas</label><br />
    <input type="time" name="time" id="time" value="<?php echo htmlspecialchars($_POST['time'] ?? date('H:i')); ?>"
        required><br /><br />

    <label for="note">Poznámka</label><br />
    <textarea name="note" id="note"><?php echo htmlspecialchars($_POST['note'] ?? ''); ?></textarea><br /><br />

    <h4>Cvičební série</h4>
    <div id="exerciseSets">
    </div>
    <button type="button" onclick="addExerciseSet()">Přidat sérii</button>
    <br /><br />
    <input type="submit" value="Přidat"><a href="index.php">Zrušit</a>

</form>
<script>
    const exercises = <?php echo json_encode(
        array_map(function ($e) {
                return ['value' => $e['exercise_id'], 'label' => $e['name']];
            }, $exercises)
    ); ?>;

    function createExerciseSet(index) {
        const div = document.createElement('div');
        div.className = 'exercise-set border rounded p-2 mb-2';
        div.innerHTML = `
    <label>Cvik:
      <select name="exercise_sets[${index}][exercise_id]" required>
        ${exercises.map(e => `<option value="${e.value}">${e.label}</option>`).join('')}
      </select>
    </label>
    <label>Počet opakování:
      <input type="number" name="exercise_sets[${index}][repetitions]" min="1" required>
    </label>
    <label>Váha (kg):
      <input type="number" name="exercise_sets[${index}][weight]" min="0" step="0.1" required>
    </label>
    <button type="button" onclick="removeExerciseSet(this)">Odebrat</button>
  `;
        return div;
    }

    function addExerciseSet() {
        const container = document.getElementById('exerciseSets');
        const index = container.children.length;
        container.appendChild(createExerciseSet(index));

    }

    function removeExerciseSet(btn) {
        btn.parentElement.remove();
    }
</script>

<?php
include 'inc/footer.php';
?>