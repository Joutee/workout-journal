<?php
require_once __DIR__ . '/inc/user.php';
$pageTitle = 'Nový trénink';


include __DIR__ . '/inc/layoutApp.php';

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

    $today = date('Y-m-d');
    $now = date('Y-m-d H:i');
    $inputDateTime = $date . ' ' . $time;


    if (!empty($name)) {
        if (strlen($name) > 100) {
            $errors['name'] = 'Název tréninku musí být kratší než 100 znaků.';
        }
    } else {
        $errors['name'] = 'Název tréninku je povinný.';
    }
    if (!empty($date)) {
        if ($date > $today) {
            $errors['date'] = 'Datum nesmí být v budoucnosti.';
        }
    } else {
        $errors['date'] = 'Datum je povinné.';
    }
    if (!empty($time)) {
        if ($date === $today && $inputDateTime > $now) {
            $errors['time'] = 'Čas nesmí být v minulosti.';
        }
    } else {
        $errors['time'] = 'Čas je povinný.';
    }
    if (empty($exerciseSets)) {
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

<form method="post" style="max-width: 700px;">
    <div class="mb-3">
        <label for="name" class="form-label">Název tréninku</label>
        <input type="text" name="name" id="name" class="form-control"
            value="<?php echo htmlspecialchars($_POST['name'] ?? $days[date('N')] . ' trénink'); ?>" required>
    </div>

    <div class="mb-3">
        <label for="date" class="form-label">Datum</label>
        <input type="date" name="date" id="date" class="form-control"
            value="<?php echo htmlspecialchars($_POST['date'] ?? date('Y-m-d')); ?>" max="<?php echo date('Y-m-d'); ?>"
            required>
    </div>

    <div class="mb-3">
        <label for="time" class="form-label">Čas</label>
        <input type="time" name="time" id="time" class="form-control"
            value="<?php echo htmlspecialchars($_POST['time'] ?? date('H:i')); ?>" required>
    </div>

    <div class="mb-3">
        <label for="note" class="form-label">Poznámka</label>
        <textarea name="note" id="note"
            class="form-control"><?php echo htmlspecialchars($_POST['note'] ?? ''); ?></textarea>
    </div>

    <h4 class="mt-4">Cvičební série</h4>
    <div id="exerciseSets"></div>
    <button type="button" onclick="addExerciseSet()" class="btn btn-outline-primary btn-sm mt-2 mb-3">
        <i class="bi bi-plus"></i> Přidat sérii
    </button>

    <div class="d-flex mt-4">
        <input type="submit" value="Přidat" class="btn btn-primary mr-2">
        <a href="workouts.php" class="btn btn-secondary">Zrušit</a>
    </div>
</form>
<script>
    const exercises = <?php echo json_encode(
        array_map(function ($e) {
                return ['value' => $e['exercise_id'], 'label' => $e['name']];
            }, $exercises)
    ); ?>;

    function createExerciseSet(index) {
        const div = document.createElement('div');
        div.className = 'exercise-set card d-flex flex-row justify-content-between mb-2';
        div.innerHTML = `
    <div class="d-flex flex-row">
        <div class="mr-1 d-flex flex-column">
    <label>Cvik:</label>
      <select name="exercise_sets[${index}][exercise_id]" required>
        ${exercises.map(e => `<option value="${e.value}">${e.label}</option>`).join('')}
      </select>
    </div>
    <div class="mr-1 d-flex flex-column">
    <label>Počet opakování:</label>
      <input type="number" class="w-100" name="exercise_sets[${index}][repetitions]" min="1" required>
    </div>
    <div class="mr-1 d-flex flex-column">
    <label>Váha (kg):</label>
      <input type="number" name="exercise_sets[${index}][weight]" min="0" step="0.1" required>
    </div>
    </div>
    <div class="d-flex flex-column justify-content-end">
    <button type="button" onclick="removeExerciseSet(this)" class="btn btn-danger btn-sm h-50">&times;</button>
    </div>
  `;
        return div;
    }

    function addExerciseSet() {
        const container = document.getElementById('exerciseSets');
        const index = container.children.length;
        container.appendChild(createExerciseSet(index));

    }

    function removeExerciseSet(btn) {
        btn.closest('.exercise-set').remove();
    }
</script>

<?php
include 'inc/footer.php';
?>