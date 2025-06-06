<?php
require_once __DIR__ . '/inc/user.php';

$pageTitle = 'Přehled';
include __DIR__ . '/inc/layoutApp.php';


#region Total weight
$query = $db->prepare('
    SELECT SUM(es.weight * es.repetitions) AS total_weight
    FROM exercise_set es
    JOIN workout w ON es.workout_id = w.workout_id
    WHERE w.user_id = :user_id
');
$query->execute([':user_id' => $_SESSION['user_id']]);
$totalWeight = $query->fetchColumn();
#endregion Total weight

#region Last 3 workouts
$query = $db->prepare('SELECT * FROM workout WHERE user_id = :user_id ORDER BY date DESC LIMIT 3;');
$query->execute([':user_id' => $_SESSION['user_id']]);
$workouts = $query->fetchAll(PDO::FETCH_ASSOC);
#endregion Last 3 workouts

#region Muscle groups percentages pie chart
$query = $db->prepare('
    SELECT mg.name, COUNT(*) as count
    FROM exercise_muscle_group emg
    JOIN muscle_group mg ON emg.muscle_group_id = mg.muscle_group_id
    JOIN exercise e ON emg.exercise_id = e.exercise_id
    JOIN exercise_set es ON es.exercise_id= e.exercise_id
    JOIN workout w ON es.workout_id=w.workout_id
    WHERE e.user_id = :user_id
    AND w.date >= DATE_SUB(NOW(), INTERVAL 1 MONTH)
    GROUP BY mg.muscle_group_id
');
$query->execute([':user_id' => $_SESSION['user_id']]);
$muscleGroups = $query->fetchAll(PDO::FETCH_ASSOC);
$total = 0;
foreach ($muscleGroups as $mg) {
    $total += $mg['count'];
}
$labels = [];
$data = [];
foreach ($muscleGroups as $mg) {
    $labels[] = $mg['name'];
    $data[] = $total > 0 ? round($mg['count'] / $total * 100, 2) : 0;
}
#endregion Muscle groups percentages pie chart

#region Weight/weightless trainings ratio pie chart
$query = $db->prepare('
    SELECT 
        SUM(CASE WHEN ABS(es.weight) < 0.0001 THEN 1 ELSE 0 END) AS zero_weight,
        SUM(CASE WHEN ABS(es.weight) >= 0.0001 THEN 1 ELSE 0 END) AS nonzero_weight
    FROM exercise_set es
    JOIN workout w ON es.workout_id = w.workout_id
    WHERE w.user_id = :user_id
');
$query->execute([':user_id' => $_SESSION['user_id']]);
$weightStats = $query->fetch(PDO::FETCH_ASSOC);
$weightLabels = ['Bez zátěže', 'Se zátěží'];
$weightTotal = $weightStats['zero_weight'] + $weightStats['nonzero_weight'];
$weightData = [
    $weightTotal > 0 ? round($weightStats['zero_weight'] / $weightTotal * 100, 2) : 0,
    $weightTotal > 0 ? round($weightStats['nonzero_weight'] / $weightTotal * 100, 2) : 0
];
#endregion Weight/weightless trainings ratio pie chart

#Max weight per exercise bar chart
$query = $db->prepare('
    SELECT e.name, MAX(es.weight) AS max_weight
    FROM exercise_set es
    JOIN exercise e ON es.exercise_id = e.exercise_id
    JOIN workout w ON es.workout_id = w.workout_id
    WHERE w.user_id = :user_id
    GROUP BY es.exercise_id
');
$query->execute([':user_id' => $_SESSION['user_id']]);
$maxWeights = $query->fetchAll(PDO::FETCH_ASSOC);
usort($maxWeights, function ($a, $b) {
    return $b['max_weight'] <=> $a['max_weight'];
});
$maxLabels = [];
$maxData = [];
foreach ($maxWeights as $row) {
    $maxLabels[] = $row['name'];
    $maxData[] = (float) $row['max_weight'];
}
#endregion Max weight per exercise bar chart

#region Last set stats table
$query = $db->prepare('
    SELECT e.name, es.repetitions, es.weight, w.date
    FROM exercise_set es
    JOIN exercise e ON es.exercise_id = e.exercise_id
    JOIN workout w ON es.workout_id = w.workout_id
    WHERE w.user_id = :user_id
      AND w.date = (
          SELECT MAX(w2.date)
          FROM workout w2
          JOIN exercise_set es2 ON w2.workout_id = es2.workout_id
          WHERE w2.user_id = :user_id AND es2.exercise_id = es.exercise_id
      )
    GROUP BY es.exercise_id
    ORDER BY e.name
    LIMIT 10
');
$query->execute([':user_id' => $_SESSION['user_id']]);
$lastSets = $query->fetchAll(PDO::FETCH_ASSOC);
#endregion Last set table

#region Each workout total weight line chart
$query = $db->prepare('
    SELECT w.date, SUM(es.weight * es.repetitions) AS total_weight
    FROM workout w
    LEFT JOIN exercise_set es ON w.workout_id = es.workout_id
    WHERE w.user_id = :user_id
    GROUP BY w.workout_id
    ORDER BY w.date
');
$query->execute([':user_id' => $_SESSION['user_id']]);
$workoutWeights = $query->fetchAll(PDO::FETCH_ASSOC);
$dates = [];
$weights = [];
foreach ($workoutWeights as $row) {
    $dates[] = $row['date'];
    $weights[] = (float) ($row['total_weight'] ?? 0);
}
#endregion Each workout total weight line chart
?>

<div class="container-fluid py-4">
    <div class="row g-4">
        <div class="col-lg-4 col-md-6 col-12">
            <div class="card text-center mb-4 shadow">
                <h2 class="mb-3">Celková zvednutá váha</h2>
                <div class="display-4 fw-bold ">
                    <?php echo number_format($totalWeight ?? 0, 1, ',', ' '); ?> kg
                </div>
            </div>
            <div class="card mb-4 shadow">
                <h3 class="mb-3">Poslední zaznamenaná série pro každý cvik</h3>
                <div class="table-responsive">
                    <table class="table table-dark table-striped table-bordered align-middle mb-0">
                        <thead>
                            <tr>
                                <th>Cvik</th>
                                <th>Opakování</th>
                                <th>Váha (kg)</th>
                                <th>Datum</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($lastSets as $set): ?>
                                <tr>
                                    <td><?= htmlspecialchars($set['name']) ?></td>
                                    <td><?= htmlspecialchars($set['repetitions']) ?></td>
                                    <td><?= htmlspecialchars($set['weight']) ?></td>
                                    <td><?= htmlspecialchars($set['date']) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
        <div class="col-lg-4 col-md-6 col-12">
            <div class="card mb-4 shadow">
                <h3 class="mb-3">Vývoj celkové váhy podle tréninku</h3>
                <div style="height: 300px;">
                    <canvas class="chart" id="workoutWeightChart"></canvas>
                </div>
            </div>
            <div class="card mb-4 shadow">
                <h3 class="mb-3">Maximální váha v sérii podle cviku</h3>
                <div style="height: 300px;">
                    <canvas class="chart" id="maxWeightChart"></canvas>
                </div>
            </div>
        </div>
        <div class="col-lg-4 col-12">

            <div class="card mb-4 shadow">
                <h3 class="mb-3">Rozložení svalových skupin (%)</h3>
                <div class="mx-auto" style="max-width: 350px;">
                    <canvas class="chart" id="muscleChart" style="width:100%;height:100%;"></canvas>
                </div>
            </div>
            <div class="card mb-4 shadow">
                <h3 class="mb-3">Poměr sérií bez zátěže / se zátěží</h3>
                <div class="mx-auto" style="max-width: 350px;">
                    <canvas class="chart" id="weightChart"></canvas>
                </div>
            </div>



        </div>
    </div>

    <!-- Poslední tréninky -->
    <div class="card mt-4 shadow">
        <h3 class="mb-3">Poslední tréninky</h3>
        <?php if (!empty($workouts)): ?>
            <div class="row g-3">
                <?php foreach ($workouts as $workout): ?>
                    <div class="col-md-4 col-12">
                        <a href="editWorkout.php?id=<?= urlencode($workout['workout_id']) ?>"
                            class="text-decoration-none text-reset">
                            <div class="card bg-dark-custom h-100 p-3">
                                <h4 class="text-warning"><?= htmlspecialchars($workout['name']) ?></h4>
                                <div class="small text-secondary"><?= htmlspecialchars($workout['date']) ?></div>
                                <div><?= htmlspecialchars($workout['note']) ?></div>
                            </div>
                        </a>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <p class="text-center text-muted">Žádné tréninky zatím nejsou zaznamenány.</p>
        <?php endif; ?>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const ctx = document.getElementById('muscleChart').getContext('2d');
    new Chart(ctx, {
        type: 'pie',
        data: {
            labels: <?php echo json_encode($labels); ?>,
            datasets: [{
                label: 'Podíl svalové skupiny (%)',
                data: <?php echo json_encode($data); ?>,
                backgroundColor: [
                    'rgba(255, 99, 132, 0.6)',
                    'rgba(54, 162, 235, 0.6)',
                    'rgba(255, 206, 86, 0.6)',
                    'rgba(75, 192, 192, 0.6)',
                    'rgba(153, 102, 255, 0.6)',
                    'rgba(255, 159, 64, 0.6)',
                    'rgba(100, 200, 100, 0.6)'
                ]
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: { labels: { color: '#f1f1f1' } },
                tooltip: {
                    callbacks: {
                        label: function (context) {
                            return context.label + ': ' + context.parsed + '%';
                        }
                    }
                }
            }
        }
    });

    const ctx2 = document.getElementById('weightChart').getContext('2d');
    new Chart(ctx2, {
        type: 'pie',
        data: {
            labels: <?php echo json_encode($weightLabels); ?>,
            datasets: [{
                label: 'Podíl sérií (%)',
                data: <?php echo json_encode($weightData); ?>,
                backgroundColor: [
                    'rgba(255, 205, 86, 0.7)',
                    'rgba(54, 162, 235, 0.7)'
                ]
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: { labels: { color: '#f1f1f1' } },
                tooltip: {
                    callbacks: {
                        label: function (context) {
                            return context.label + ': ' + context.parsed + ' %';
                        }
                    }
                }
            }
        }
    });

    const ctx3 = document.getElementById('maxWeightChart').getContext('2d');
    new Chart(ctx3, {
        type: 'bar',
        data: {
            labels: <?php echo json_encode($maxLabels); ?>,
            datasets: [{
                label: 'Maximální váha (kg)',
                data: <?php echo json_encode($maxData); ?>,
                backgroundColor: 'rgba(54, 162, 235, 0.7)'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    title: { display: true, text: 'kg', color: '#f1f1f1' },
                    ticks: { color: '#f1f1f1' }
                },
                x: {
                    ticks: { color: '#f1f1f1' }
                }
            },
            plugins: {
                legend: { labels: { color: '#f1f1f1' } }
            }
        }
    });

    const ctx4 = document.getElementById('workoutWeightChart').getContext('2d');
    new Chart(ctx4, {
        type: 'line',
        data: {
            labels: <?php echo json_encode($dates); ?>,
            datasets: [{
                label: 'Celková váha (kg)',
                data: <?php echo json_encode($weights); ?>,
                fill: false,
                borderColor: 'rgba(255, 99, 132, 0.9)',
                backgroundColor: 'rgba(255, 99, 132, 0.5)',
                tension: 0.2,
                pointRadius: 4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    title: { display: true, text: 'kg', color: '#f1f1f1' },
                    ticks: { color: '#f1f1f1' }
                },
                x: {
                    title: { display: true, text: 'Datum', color: '#f1f1f1' },
                    ticks: { color: '#f1f1f1' }
                }
            },
            plugins: {
                legend: { labels: { color: '#f1f1f1' } }
            }
        }
    });
</script>

<?php include 'inc/footer.php'; ?>