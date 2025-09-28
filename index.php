<?php
// index.php - login / cadastro
session_start();
if (isset($_SESSION['user_id'])) {
    header('Location: produtos.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>ALe Skincare - Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
  </head>
  <body>
    <div class="page-bg">
      <div class="login-center">
        <div class="card p-4 shadow-sm" style="width:360px;">
          <div class="text-center mb-3">
            <img src="assets/ALe-logo.png" alt="ALe" style="width:80px;">
          </div>

          <div class="d-flex mb-3">
            <button id="tabLogin" class="btn flex-fill active-tab">Entrar</button>
            <button id="tabRegister" class="btn flex-fill">Cadastrar</button>
          </div>

          <!-- Login -->
          <form id="formLogin" method="post" action="php/autenticacao.php">
            <h4 class="text-center text-pink"><b>Bem-vindo de volta!</b></h4>
            <p class="text-center small text-muted">Entre com suas credenciais</p>
            <div class="mb-2">
              <label class="form-label">E-mail</label>
              <input class="form-control" type="email" name="email" required>
            </div>
            <div class="mb-3">
              <label class="form-label">Senha</label>
              <input class="form-control" type="password" name="password" required>
            </div>
            <button class="btn btn-pink w-100" type="submit">Entrar</button>
          </form>

          <!-- Register -->
          <form id="formRegister" method="post" action="php/cadastro.php" style="display:none;">
            <h4 class="text-center text-pink">Criar conta</h4>
            <p class="text-center small text-muted">Preencha os dados abaixo</p>
            <div class="mb-2">
              <label class="form-label">Nome completo</label>
              <input class="form-control" type="text" name="nome" required>
            </div>
            <div class="mb-2">
              <label class="form-label">E-mail</label>
              <input class="form-control" type="email" name="email" required>
            </div>
            <div class="mb-2">
              <label class="form-label">Senha</label>
              <input class="form-control" type="password" name="password" required>
            </div>
            <div class="mb-3">
              <label class="form-label">Confirmar senha</label>
              <input class="form-control" type="password" name="password_confirm" required>
            </div>
            <button class="btn btn-pink w-100" type="submit">Cadastrar</button>
          </form>
        </div>
      </div>
    </div>

    <script>
      const tabLogin = document.getElementById('tabLogin');
      const tabRegister = document.getElementById('tabRegister');
      const formLogin = document.getElementById('formLogin');
      const formRegister = document.getElementById('formRegister');

      tabLogin.addEventListener('click', () => {
        tabLogin.classList.add('active-tab'); tabRegister.classList.remove('active-tab');
        formLogin.style.display = ''; formRegister.style.display = 'none';
      });
      tabRegister.addEventListener('click', () => {
        tabRegister.classList.add('active-tab'); tabLogin.classList.remove('active-tab');
        formRegister.style.display = ''; formLogin.style.display = 'none';
      });
    </script>
  </body>
</html>
