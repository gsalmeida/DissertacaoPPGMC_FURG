<!DOCTYPE html>
<html lang="pt-br">
<head>
<title>Sistema para classificação de documentos do Facebook</title>
<meta charset="utf-8">
<link href="./bootstrapcss/login.css" rel="stylesheet">
</head>
<body>
	<?php 
    	include_once "./db/Conexao.class.php";
    	$conexao = new Conexao();
    	
  	$stringSql = "SELECT UserClassificationWebSystem.login, DocumentClassification.idUserClassificationWebSystem, COUNT(*) AS qtde 
		      FROM DocumentClassification 
		      LEFT JOIN UserClassificationWebSystem ON UserClassificationWebSystem.id = DocumentClassification.idUserClassificationWebSystem 
		      GROUP BY idUserClassificationWebSystem 
		      ORDER BY qtde DESC;";
    	
	echo "<div style='text-align: center;'>";

    	$query = mysqli_query($conexao->getConexao(), $stringSql);
	echo "LOGIN\t\t|QTDE<br/><hr/>";
    	for ($a = 0; $dados = mysqli_fetch_assoc($query); $a++) echo $dados["login"]."\t\t|".$dados["qtde"]."<br/><hr/>";

    	echo "</div>";
	?>
</body>
</html>
