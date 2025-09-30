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

    <style>
      .custom-nav {
        background-color: #fff;
        border-radius: 1rem;
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
      }

      .custom-nav .nav-link {
        border-radius: 0.8rem;
        color: #d63384;
        font-weight: 500;
        padding: 0.8rem 1.2rem;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.2s ease-in-out;
      }

      .custom-nav .nav-link.active {
        background-color: #fc3099;
        color: #fff;
      }

      .custom-nav .nav-link:hover:not(.active) {
        background-color: #f8d7e6;
      }

      /* Seção título + botão */
      .page-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin: 2rem 0 1rem;
      }

      .page-header h3 {
        color: #d63384;
        font-weight: bold;
        display: flex;
        align-items: center;
      }

      .page-header h3 img {
        margin-right: 10px;
      }

      /* Card da lista */
      .card-custom {
        border-radius: 1rem;
        overflow: hidden;
      }


    </style>
  </head>
  <body>
    <nav class="navbar navbar-light bg-white shadow-sm">
      <div class="container d-flex justify-content-between align-items-center">
        
        <!-- Agrupando logo + nome -->
        <a class="navbar-brand d-flex align-items-center" href="#" style="color: #d60464; font-weight: bold;">
          <img src="assets/ALe-logo.png" alt="ALe" style="width:50px;" class="me-2">
          Ale Skincare
        </a>
        
        <!-- Usuário -->
        <div style="color: #ff2e8d;">
          Olá, <?=htmlspecialchars($userName)?> &nbsp; 
          <a href="php/logout.php" style="color: #ff2e8d;">Sair</a>
        </div>
      </div>
    </nav>

    <div class="container mt-4">
      <div class="container mt-4">
        <div class="d-flex justify-content-center">
          <ul class="nav nav-pills custom-nav shadow nav-fill w-100">
            <li class="nav-item">
              <a class="nav-link" href="produtos.php">
                <img src="assets/package-pink.png">
                <b>Produtos</b>
              </a>
            </li>
            <li class="nav-item">
              <a class="nav-link active" href="fornecedores.php">
                <img src="assets/supplier-white.png">
                <b>Fornecedores</b>
              </a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="carrinho.php">
                <img src="assets/cart-pink.png">
                <b>Carrinho</b>
              </a>
            </li>
          </ul>
        </div>
      </div>

      <div class="page-header">
        <h3 style="color: #d60464;">
          <img src="assets/supplier-title.png"> Fornecedores
        </h3>
        <button id="btnNewSupplier" class="btn btn-pink">
          <img src="assets/add-white.png">
          Novo Fornecedor
        </button>
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

      <!-- Lista de fornecedores -->
      <div id="suppliersList" class="card card-custom p-3">
        <p style="color: #d60464;">Lista de Fornecedores</p>
        <div id="suppliersList" class="card p-3">
          <div id="suppliersTable">
            <!-- tabela via JS -->
          </div>
        </div>
      </div>
    </div>

    <script src="js/main.js"></script>
  </body>
</html>