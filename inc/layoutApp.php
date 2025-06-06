<?php
include __DIR__ . '/head.php';
echo '<body class="d-flex h-100">';
include __DIR__ . '/sidebar.php';
?>

<main class="flex-grow-1" style="margin-left: 250px; padding: 3.5em;">

    <h1 class=""><?php echo (!empty($pageTitle) ? $pageTitle : '') ?></h1>