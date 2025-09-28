<?php
// php/conexao.php

$host = 'localhost';
$db   = 'aleskincare';
$user = 'root';
$pass = '';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;charset=$charset";

$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION, // erros em forma de exceção
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,       // resultados como array associativo
    PDO::ATTR_EMULATE_PREPARES   => false,                  // usar prepares nativos do MySQL
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);

    // Seleciona o banco (alguns ambientes exigem)
    $pdo->exec("CREATE DATABASE IF NOT EXISTS `$db` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    $pdo->exec("USE `$db`");

    return $pdo;
} catch (PDOException $e) {
    die('Erro de conexão: ' . $e->getMessage());
}
