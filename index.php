<?php
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
    <style type="text/css">
      .form-control { background-color: #f2f2f2; }  

      .active-tab { background-color: #FFFFFF; color: black;}

      .inactive { background-color: #dedede; color: black;}

      .border { border-radius: 30px }

      .text-small { font-size: small; }

      .text-small-bold { font-size: small; font-weight:bold; }
    </style>
  </head>
  <body>

  <?php

    // erro: falta de email ou senha
    if (isset($_GET['erro']) && $_GET['erro'] == 'email') {
      echo '<script>
        alert("Preencha e-mail e senha.");
      </script>';
    }

    // erro: usuário não encontrado
    if (isset($_GET['erro']) && $_GET['erro'] == 'user') {
      echo '<script>
        alert("Usuário não encontrado.");
      </script>';
    }

    // Espera o formato salt:hash
    if (isset($_GET['erro']) && $_GET['erro'] == 'hash') {
      echo '<script>
        alert("Formato de hash de senha inválido no registro do usuário.");
      </script>';
    }

    // campos não preenchidos
    if (isset($_GET['erro']) && $_GET['erro'] == 'preencha') {
      echo '<script>
        alert("Preencha todos os campos.");
      </script>';
    }

    // as senhas não conferem
    if (isset($_GET['erro']) && $_GET['erro'] == 'confirm') {
      echo '<script>
        alert("As senhas não conferem.");
      </script>';
    }

    // email já cadastrado
    if (isset($_GET['erro']) && $_GET['erro'] == 'emailExists') {
      echo '<script>
        alert("E-mail já cadastrado.");
      </script>';
    }

  ?>

    <div class="page-bg">
      <div class="login-center">
        <div class="card p-4 shadow" style="width:420px;">
          <div class="text-center mb-3">
            <img src="assets/ALe-logo.png" alt="ALe" style="width:80px;">
          </div>

          <div class="d-flex mb-3 border" style="background-color: #dedede; border: 3px solid #f2f2f2;">
            <button id="tabLogin" class="btn flex-fill active-tab border text-small-bold">Entrar</button>
            <button id="tabRegister" class="btn flex-fill inactive border text-small-bold">Cadastrar</button>
          </div>

          <!-- Login -->
          <form id="formLogin" method="post" action="php/autenticacao.php">
            <h4 class="text-center" style="color: #d60464;"><b>Bem-vindo de volta!</b></h4>
            <p class="text-center small text-pink">Entre com suas credenciais</p>
            <div class="mb-2">
              <label class="form-label text-small-bold">E-mail</label>
              <input class="form-control text-small" type="email" name="email" placeholder="seu@email.com" required>
            </div>
            <div class="mb-3">
              <label class="form-label text-small-bold">Senha</label>
              <input class="form-control text-small" type="password" name="password" placeholder="••••••••" required>
            </div>
            <button class="btn btn-pink w-100 text-small-bold" type="submit">Entrar</button>
          </form>

          <!-- Register -->
          <form id="formRegister" method="post" action="php/cadastro.php" style="display:none;">
            <h4 class="text-center" style="color: #d60464;"><b>Criar conta</b></h4>
            <p class="text-center small text-pink">Preencha os dados abaixo</p>
            <div class="mb-2">
              <label class="form-label text-small-bold">Nome completo</label>
              <input class="form-control text-small" type="text" name="nome" placeholder="Seu nome" required>
            </div>
            <div class="mb-2">
              <label class="form-label text-small-bold">E-mail</label>
              <input class="form-control text-small" type="email" name="email" placeholder="seu@email.com" required>
            </div>
            <div class="mb-2">
              <label class="form-label text-small-bold">Senha</label>
              <input class="form-control text-small" type="password" name="password" placeholder="••••••••" required>
            </div>
            <div class="mb-3">
              <label class="form-label text-small-bold">Confirmar senha</label>
              <input class="form-control text-small" type="password" name="password_confirm" placeholder="••••••••" required>
            </div>
            <button class="btn btn-pink w-100 text-small-bold" type="submit">Cadastrar</button>
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
        tabLogin.classList.add('active-tab'); tabRegister.classList.remove('active-tab')
        tabLogin.classList.remove('inactive'); tabRegister.classList.add('inactive');
        formLogin.style.display = ''; formRegister.style.display = 'none';
      });
      tabRegister.addEventListener('click', () => {
        tabRegister.classList.add('active-tab'); tabLogin.classList.remove('active-tab');
        tabRegister.classList.remove('inactive'); tabLogin.classList.add('inactive');
        formRegister.style.display = ''; formLogin.style.display = 'none';
      });
    </script>
  </body>
</html>
