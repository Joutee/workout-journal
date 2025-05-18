<?php
require_once __DIR__ . '/db.php';
session_start();

if (!empty($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}
?>