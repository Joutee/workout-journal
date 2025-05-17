<?php
//$db = new PDO('mysql:host=127.0.0.1;dbname=kovp07;charset=utf8', 'kovp07', 'eCiopaitoo9wua9noo');
$db = new PDO('mysql:host=127.0.0.1;dbname=kovp07;charset=utf8', 'root', '');

$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
?>