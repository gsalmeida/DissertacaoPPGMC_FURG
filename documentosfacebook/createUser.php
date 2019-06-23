<!DOCTYPE html>
<html lang="pt-br">
<head>
<title>Sistema para classificação de documentos do Facebook</title>
<meta charset="utf-8">
<link href="./bootstrapcss/login.css" rel="stylesheet">
</head>
<body>
	<div class="div-pai-formulario">
		<form action="#" method="post">
			<div class="container">
				<label><b>Username</b></label> <br />
				<input id="username-id-create-user" type="text" placeholder="Enter Username" name="login" required>
				<br/> 
				<label><b>Password</b></label> <br/>
				<input id="password-id-create-user" type="password" placeholder="Enter Password" name="senha" required>
				 <br/>
				<button type="submit">Create</button>
			</div>
			
    		<div class="container-login-existente">
    			<span id="login-existente"></span>
    		</div>
    		
		</form>
	</div>
	<script type="text/javascript" src="./bootstrapjs/jquery.min.js"></script>
	<script type="text/javascript" src="./js/login.js"></script>
</body>
</html>

<?php 
    include_once "./db/Conexao.class.php";
    $conexao = new Conexao();
    if( (isset($_POST["login"])) && (isset($_POST["senha"])) ) {
        $login = $_POST["login"];
        $senha = $_POST["senha"];
        
        $sqlVerificarUsuario = "SELECT id FROM UserClassificationWebSystem WHERE login = '".$login."'";
        $queryVerificarUsuario = mysqli_query($conexao->getConexao(), $sqlVerificarUsuario);
        $qtde = mysqli_num_rows($queryVerificarUsuario);
        if($qtde) { 
        	echo "<script>document.getElementById('login-existente').innerHTML = 'Já existe um usuário cadastrado com este login.';</script>";
        	die();
        }
    	$sqlUser = "INSERT INTO UserClassificationWebSystem (login, senha) VALUES('".$login."', '".md5($senha)."')";
		$query = mysqli_query($conexao->getConexao(), $sqlUser);
		echo "<script>alert('Usuário criado com suesso');</script>";        
		echo "<script>location.href='index.php';</script>";    
    }
    
?>
