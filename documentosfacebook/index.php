<?php
    session_start();
    if( (!isset($_SESSION["LOGIN"])) or (isset($_POST["botao-logout"])) ) {
        $_SESSION['ID_USER'] = "";
        $_SESSION['LOGIN'] = "";
        $_SESSION['NOME'] = "";
        $_SESSION['EMAIL'] = "";
        $_SESSION['TABLE_DOCUMENT'] = "";
        unset($_SESSION['ID_USER']);
        unset($_SESSION['LOGIN']);
        unset($_SESSION['NOME']);
        unset($_SESSION['EMAIL']);
        unset($_SESSION['TABLE_DOCUMENT']);
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
<title>Sistema para classificação de documentos do Facebook</title>
<meta charset="utf-8">
<link href="./bootstrapcss/login.css" rel="stylesheet">
</head>
<body>
	<div class="div-pai-formulario">
		<form action="index.php" method="post">
			<div class="container">
				<label><b>Username</b></label> <br />
				<input id="username-id" type="text" placeholder="Enter Username" name="login" required>
				<br/> 
				<label><b>Password</b></label> <br/>
				<input id="password-id" type="password" placeholder="Enter Password" name="senha" required>
				 <br/>
				<button type="submit">Login</button>
			</div>
			
    		<div class="container-login-incorreto">
    			<span id="login-incorreto"></span>
    		</div>
    		
		</form>
	</div>
	<script type="text/javascript" src="./bootstrapjs/jquery.min.js"></script>
	<script type="text/javascript" src="./js/login.js"></script>
</body>
</html>

<?php 
    } else echo "<script>location.href='principal.php';</script>";
    
    include_once "./db/Conexao.class.php";
    $conexao = new Conexao();
    if( (isset($_POST["login"])) && (isset($_POST["senha"])) ) {
        $login = $_POST["login"];
        $senha = $_POST["senha"];
        $sqlUser = "SELECT * FROM UserClassificationWebSystem WHERE login = '".mysqli_real_escape_string($conexao->getConexao(), $login)."' AND senha = MD5('".mysqli_real_escape_string($conexao->getConexao(), $senha)."')";
        $query = mysqli_query($conexao->getConexao(), $sqlUser);
        $count = mysqli_num_rows($query);
        if($count == 1) {
            $result = mysqli_fetch_array($query);
            $_SESSION['ID_USER'] = $result['id'];
            $_SESSION['LOGIN'] = $result['login'];
            $_SESSION['NOME'] = $result['nome'];
            $_SESSION['EMAIL'] = $result['email'];
            $_SESSION['ultimaAtividade'] = time();
            $_SESSION["offsetQueryMySQLPrimeiroNaoClassificado"] = -1;
            $_SESSION['TABLE_DOCUMENT'] = 1;
            echo "<script>location.href='principal.php';</script>";
        } else {
            unset($_SESSION['ID_USER']);
            unset($_SESSION['LOGIN']);
            unset($_SESSION['NOME']);
            unset($_SESSION['EMAIL']);
            unset($_SESSION["offsetQueryMySQLPrimeiroNaoClassificado"]);
            unset($_SESSION['TABLE_DOCUMENT']);
            echo "<script>document.getElementById('login-incorreto').innerHTML = 'Login e/ou senha incorreto(s).';</script>";
            die();
        }
    }
    
?>
