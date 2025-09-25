<!DOCTYPE html>
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>ALe Skincare</title>
</head>
<body>
	<div>
		<img src="../assets/ALe-logo.png">
		<!-- adicionar botões entrar e cadastrar -->

		<h1>Bem-vindo de volta!</h1>
		<h3>Entre com suas credenciais</h3>

		<form method="post" action="../php/autentication.php" id="formlogin" name="formlogin" >
			<label>E-mail</label>
			<input type="email" name="email" id="email"/><br/>
			<label>Senha</label>
			<input type="password" name="password" id="password"/><br/>
			<input type="submit" value="Entrar"/>
		</form>
	</div>

</body>
</html>