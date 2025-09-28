<?php
// produtos.php
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
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Produtos - Ale Skincare</title>
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
    <div class="d-flex justify-content-between align-items-center mb-3">
      <ul class="nav nav-pills">
        <li class="nav-item"><a class="nav-link active" href="produtos.php">Produtos</a></li>
        <li class="nav-item"><a class="nav-link" href="fornecedores.php">Fornecedores</a></li>
        <li class="nav-item"><a class="nav-link" href="carrinho.php">Carrinho</a></li>
      </ul>
      <div>
        <button id="btnNewProduct" class="btn btn-outline-pink">+ Novo Produto</button>
        <button id="btnAddSelected" class="btn btn-pink" disabled>Adicionar selecionados</button>
      </div>
    </div>

    <!-- Formulário de novo produto (escondido inicialmente) -->
    <div id="productForm" class="card mb-4 p-3" style="display:none;">
      <h5>Cadastrar Novo Produto</h5>
      <form id="frmNewProduct">
        <div class="row">
          <div class="col-md-8 mb-2">
            <input class="form-control" name="name" placeholder="Nome do Produto" required>
          </div>
          <div class="col-md-4 mb-2">
            <input class="form-control" name="price" placeholder="Preço (R$)" required pattern="^[0-9]+(\.[0-9]{2})?$" >
          </div>
          <div class="col-md-6 mb-2">
            <select class="form-control" name="supplier_id" id="selectSupplier">
              <option value="">Selecione um fornecedor</option>
            </select>
          </div>
          <div class="col-md-6 mb-2 d-flex align-items-center">
            <div class="form-check ms-2">
              <input class="form-check-input" type="checkbox" name="in_stock" id="in_stock" checked>
              <label class="form-check-label">Produto em estoque</label>
            </div>
          </div>
          <div class="col-12 mb-2">
            <textarea class="form-control" name="description" placeholder="Descrição"></textarea>
          </div>
        </div>
        <div class="mt-2">
          <button class="btn btn-pink" type="submit">Cadastrar Produto</button>
          <button id="btnCancelProduct" type="button" class="btn btn-outline-secondary">Cancelar</button>
        </div>
      </form>
    </div>

    <!-- Filtros (simples) -->
    <div class="mb-3">
      <input id="searchBox" class="form-control mb-2" placeholder="Buscar produtos...">
    </div>

    <!-- Grid de produtos -->
    <div id="productsGrid" class="row g-3">
      <!-- cards serão inseridos aqui via JS -->
    </div>
  </div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="assets/js/main.js"></script>
</body>
</html>
