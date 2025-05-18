<?php
include __DIR__ . '/head.php';
echo '<body style= "height: 100vh; background-color: #f8f9fa;">';
include __DIR__ . '/sidebar.php';
?>
<main class="d-flex justify-content-center align-items-start" style="min-height: 100vh; margin-left: 0;">
    <div class="w-100" style="max-width: 900px;">
        <h1 class="py-4 px-2"><?php echo (!empty($pageTitle) ? $pageTitle : '') ?></h1>