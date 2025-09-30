<?php
// php/autenticacao.php
session_start();
$pdo = require __DIR__ . '/conexao.php';

$email = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';

// erro: falta de email ou senha
if (!$email || !$password) {
    header('Location: ../index.php?erro=email');
    exit;
}

$stmt = $pdo->prepare("SELECT id, name, password_hash FROM users WHERE email = ?");
$stmt->execute([$email]);
$user = $stmt->fetch();

// erro: usuário não encontrado
if (!$user) {
    header('Location: ../index.php?erro=user');
    exit;
}

// Espera o formato salt:hash
if (!strpos($user['password_hash'], ':')) {
    header('Location: ../index.php?erro=hash');
    exit;
}
list($salt, $hash) = explode(':', $user['password_hash']);
$calc = hash('sha256', $salt . $password);

if (hash_equals($hash, $calc)) {
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['user_name'] = $user['name'];
    header('Location: ../produtos.php');
    exit;
} else {
    die('Senha incorreta.');
}
