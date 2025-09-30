<?php
session_start();
$pdo = require __DIR__ . '/conexao.php';

$name = trim($_POST['nome'] ?? '');
$email = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';
$password_confirm = $_POST['password_confirm'] ?? '';

// campos não preenchidos
if (!$name || !$email || !$password) {
    header('Location: ../index.php?erro=preencha');
    exit;
}

// as senhas não conferem
if ($password !== $password_confirm) {
    header('Location: ../index.php?erro=confirm');
    exit;
}

// Verifica se já existe
$stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
$stmt->execute([$email]);

// email já cadastrado
if ($stmt->fetch()) {
    header('Location: ../index.php?erro=emailExists');
    exit;
}

$salt = bin2hex(random_bytes(16));
$hash = hash('sha256', $salt . $password);
$store = $salt . ':' . $hash;

$stm = $pdo->prepare("INSERT INTO users (name, email, password_hash) VALUES (?, ?, ?)");
$ok = $stm->execute([$name, $email, $store]);

if ($ok) {
    $userId = $pdo->lastInsertId();
    $_SESSION['user_id'] = $userId;
    $_SESSION['user_name'] = $name;
    header('Location: ../produtos.php');
    exit;
} else {
    die('Erro ao cadastrar usuário.');
}
