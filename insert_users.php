<?php
$pdo = new PDO('mysql:host=localhost;dbname=sympet;charset=utf8mb4', 'root', '');
$sql = file_get_contents('insert_users.sql');
$pdo->exec($sql);
echo "Users imported successfully\n";
