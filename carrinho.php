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
    <link rel="stylesheet" href="css/style.css">

    <style>
      .custom-nav {
        background-color: #fff;
        border-radius: 1rem;
        box-shadow: 10px 10px;
      }

      .custom-nav .nav-link {
        border-radius: 0.8rem;
        color: #d63384; /* rosa */
        font-weight: 500;
        padding: 0.8rem 1.2rem;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.2s ease-in-out;
      }

      .custom-nav .nav-link.active {
        background-color: #fc3099; /* rosa forte */
        color: #fff;
      }

      .custom-nav .nav-link:hover:not(.active) {
        background-color: #f8d7e6;
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
      <div class="d-flex justify-content-center">
        <ul class="nav nav-pills custom-nav shadow-sm nav-fill w-100">
          <li class="nav-item">
            <a class="nav-link" href="produtos.php">
              <img src="assets/package-pink.png">
              <b>Produtos</b>
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="fornecedores.php">
              <img src="assets/supplier-pink.png">
              <b>Fornecedores</b>
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link active" href="carrinho.php">
              <img src="assets/cart-white.png">
              <b>Carrinho</b>
            </a>
          </li>
        </ul>
      </div>
    </div>

    <div class="container mt-4">
      <div class="d-flex justify-content-between align-items-center mb-3">

        <!-- Título com ícone e badge -->
        <div class="d-flex align-items-center">
          <h3 class="mb-0 fw-bold text-pink d-flex align-items-center" style="color: #d60464;">
            <img src="assets/cart-title.png">Carrinho de Compras
          </h3>
        </div>
      </div>

      <div class="row mt-3">
        <div id="cartItems" class="col-12"></div>
        <div id="cartSummary" class="col-12 col-md-4"></div>
      </div>

    </div>

    <script src="js/main.js"></script>
  </body>
</html>
