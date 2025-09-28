<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}
$userName = $_SESSION['user_name'] ?? 'Usuário';
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Carrinho - Ale Skincare</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
<nav class="navbar navbar-light bg-white shadow-sm">
  <div class="container">
    <a class="navbar-brand text-pink" href="#">Ale Skincare</a>
    <div>
      Olá, <?=htmlspecialchars($userName)?> &nbsp; <a href="php/logout.php">Sair</a>
    </div>
  </div>
</nav>

<div class="container mt-4">
  <h3>Carrinho de Compras</h3>
  <div class="row">
    <div class="col-md-8">
      <div id="cartItems" class="card p-3">
        <!-- itens via JS -->
      </div>
    </div>
    <div class="col-md-4">
      <div id="cartSummary" class="card p-3">
        <!-- resumo via JS -->
      </div>
    </div>
  </div>
</div>

<script src="assets/js/main.js"></script>
</body>
</html>
