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
    <title>Fornecedores - Ale Skincare</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
  </head>
  <body>
    <nav class="navbar navbar-light bg-white shadow-sm">
      <div class="container">
        <img src="assets/ALe-logo.png" alt="ALe" style="width:80px;">
        <a class="navbar-brand text-pink" href="#">Ale Skincare</a>
        <div>
          Olá, <?=htmlspecialchars($userName)?> &nbsp; <a href="php/logout.php">Sair</a>
        </div>
      </div>
    </nav>

    <div class="container mt-4">
      <div class="d-flex justify-content-between align-items-center mb-3">
        <ul class="nav nav-pills">
          <li class="nav-item"><a class="nav-link" href="produtos.php">Produtos</a></li>
          <li class="nav-item"><a class="nav-link active" href="fornecedores.php">Fornecedores</a></li>
          <li class="nav-item"><a class="nav-link" href="carrinho.php">Carrinho</a></li>
        </ul>
        <button id="btnNewSupplier" class="btn btn-pink">+ Novo Fornecedor</button>
      </div>

      <!-- Form novo fornecedor -->
      <div id="supplierForm" class="card mb-4 p-3" style="display:none;">
        <h5>Cadastrar Novo Fornecedor</h5>
        <form id="frmNewSupplier">
          <div class="row">
            <div class="col-md-6 mb-2"><input class="form-control" name="name" placeholder="Nome da Empresa" required></div>
            <div class="col-md-6 mb-2"><input class="form-control" name="contact" placeholder="E-mail / Telefone"></div>
            <div class="col-12 mb-2"><input class="form-control" name="address" placeholder="Endereço"></div>
          </div>
          <div>
            <button class="btn btn-pink">Cadastrar Fornecedor</button>
            <button id="btnCancelSupplier" class="btn btn-outline-secondary" type="button">Cancelar</button>
          </div>
        </form>
      </div>

      <div id="suppliersList" class="card p-3">
        <h5>Lista de Fornecedores</h5>
        <div id="suppliersTable">
          <!-- tabela via JS -->
        </div>
      </div>
    </div>

  <script src="js/main.js"></script>
  </body>
</html>
