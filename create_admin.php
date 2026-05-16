<?php
$pdo = new PDO('mysql:host=localhost;dbname=sympet;charset=utf8mb4', 'root', '');
$pdo->exec("INSERT INTO user (email, roles, password, is_enabled) VALUES ('superadmin@sympet.com', '[\"ROLE_ADMIN\"]', '\$2y\$13\$oxTsxUVfzwuLF48Z1Xxl0eydHO6X0Kdtm6qMv9k3BQ1XXnyHjoMFW', 1) ON DUPLICATE KEY UPDATE password=VALUES(password)");
$pdo->exec("UPDATE user SET password = '\$2y\$13\$oxTsxUVfzwuLF48Z1Xxl0eydHO6X0Kdtm6qMv9k3BQ1XXnyHjoMFW' WHERE email = 'admin@gmail.com'");
echo "Admin ready\n";
