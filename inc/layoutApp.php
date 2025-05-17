<?php
include 'head.php';
echo '<body style= "height: 100vh; background-color: #f8f9fa;">';
include 'sidebar.php';
?>
<main class="container" style="margin-left: 15rem;">
    <h1 class="py-4 px-2"><?php echo (!empty($pageTitle) ? $pageTitle : '') ?></h1>