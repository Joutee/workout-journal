<?php
require_once 'inc/user.php';
$pageTitle = 'Nový trénink';
include 'inc/layoutApp.php';

$exercises = [];
$query = $db->prepare('SELECT exercise_id, name FROM exercise WHERE user_id=:user_id ORDER BY name;');
$query->execute([
    ':user_id' => $_SESSION['user_id']
]);
while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
    $exercises[] = $row;
}
?>

<form method="post" id="workoutForm">
    <label for="name">Název tréninku</label><br />
    <input type="text" name="name" id="name" value="<?php echo htmlspecialchars(@$_POST['name']); ?>"
        required><br /><br />

    <label for="date">Datum</label><br />
    <input type="date" name="date" id="date" value="<?php echo htmlspecialchars(@$_POST['date']); ?>"
        required><br /><br />

    <label for="note">Poznámka</label><br />
    <textarea name="note" id="note"><?php echo htmlspecialchars(@$_POST['note']); ?></textarea><br /><br />

    <h4>Cvičební série</h4>
    <div id="exerciseSets">
    </div>
    <button type="button" onclick="addExerciseSet()">Přidat sérii</button>
    <br /><br />
    <input type="submit" value="Uložit"><a href="index.php">Zrušit</a>
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
      <select name="exercise_sets[${index}][type]" required>
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