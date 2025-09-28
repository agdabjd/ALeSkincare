<?php
// php/autenticacao.php
session_start();
$pdo = require __DIR__ . '/conexao.php';

$email = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';

if (!$email || !$password) {
    die('Preencha e-mail e senha.');
}

$stmt = $pdo->prepare("SELECT id, name, password_hash FROM users WHERE email = ?");
$stmt->execute([$email]);
$user = $stmt->fetch();

if (!$user) {
    die('Usuário não encontrado.');
}

// Espera o formato salt:hash
if (!strpos($user['password_hash'], ':')) {
    die('Formato de hash de senha inválido no registro do usuário.');
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
