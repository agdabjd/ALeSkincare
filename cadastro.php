<?php
// php/cadastro.php
session_start();
$pdo = require __DIR__ . '/conexao.php';

// Recebe dados do formulário (index.php)
$name = trim($_POST['nome'] ?? '');
$email = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';
$password_confirm = $_POST['password_confirm'] ?? '';

// Validações simples
if (!$name || !$email || !$password) {
    die('Preencha todos os campos.');
}
if ($password !== $password_confirm) {
    die('As senhas não conferem.');
}

// Verifica se já existe
$stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
$stmt->execute([$email]);
if ($stmt->fetch()) {
    die('E-mail já cadastrado.');
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
