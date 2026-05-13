<?php
// Script pour insérer l'utilisateur skander@gmail.com
require_once __DIR__ . '/vendor/autoload.php';

use Symfony\Component\Dotenv\Dotenv;

$dotenv = new Dotenv();
$dotenv->loadEnv(__DIR__ . '/.env');

$dbUrl = $_ENV['DATABASE_URL'];
$parsed = parse_url($dbUrl);
$dbName = ltrim(strtok($parsed['path'], '?'), '/');

$pdo = new PDO(
    sprintf('mysql:host=%s;port=%s;dbname=%s;charset=utf8mb4',
        $parsed['host'],
        $parsed['port'] ?? 3306,
        $dbName
    ),
    $parsed['user'],
    $parsed['pass'] ?? ''
);

// Vérifier si l'utilisateur existe déjà
$stmt = $pdo->prepare('SELECT id FROM `user` WHERE email = ?');
$stmt->execute(['skander@gmail.com']);

if ($stmt->fetch()) {
    echo "L'utilisateur skander@gmail.com existe déjà.\n";
    exit;
}

// Hash du mot de passe skander123
$hash = password_hash('skander123', PASSWORD_BCRYPT, ['cost' => 13]);

$stmt = $pdo->prepare('INSERT INTO `user` (email, roles, password, nom, prenom, is_enabled) VALUES (?, ?, ?, ?, ?, ?)');
$stmt->execute([
    'skander@gmail.com',
    '["ROLE_USER"]',
    $hash,
    'Skander',
    'Skander',
    1
]);

echo "Utilisateur créé avec succès !\n";
echo "Email    : skander@gmail.com\n";
echo "Mot de passe : skander123\n";
echo "Rôle     : ROLE_USER\n";
