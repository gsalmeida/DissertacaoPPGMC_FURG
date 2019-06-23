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
    	
    	function retornarSQL($campo) {
    	    $stringSql = "SELECT COUNT(*) AS ".$campo." FROM (
                        SELECT DocumentClassification.idDocumento, COUNT(*) as qtde,
                        (SELECT COUNT(*) FROM DocumentClassification AS DCAux WHERE DCAux.idDocumento = DocumentClassification.idDocumento AND NOT ISNULL(DCAux.alimentacao) ) AS alimentacao,
    	    
                        (SELECT COUNT(*) FROM DocumentClassification AS DCAux WHERE DCAux.idDocumento = DocumentClassification.idDocumento AND NOT ISNULL(DCAux.cultura) ) AS cultura,
    	    
                        (SELECT COUNT(*) FROM DocumentClassification AS DCAux WHERE DCAux.idDocumento = DocumentClassification.idDocumento AND NOT ISNULL(DCAux.economia) ) AS economia,
    	    
                        (SELECT COUNT(*) FROM DocumentClassification AS DCAux WHERE DCAux.idDocumento = DocumentClassification.idDocumento AND NOT ISNULL(DCAux.educacao) ) AS educacao,
    	    
                        (SELECT COUNT(*) FROM DocumentClassification AS DCAux WHERE DCAux.idDocumento = DocumentClassification.idDocumento AND NOT ISNULL(DCAux.empreendedorismo) ) AS empreendedorismo,
    	    
                        (SELECT COUNT(*) FROM DocumentClassification AS DCAux WHERE DCAux.idDocumento = DocumentClassification.idDocumento AND NOT ISNULL(DCAux.energia) ) AS energia,
    	    
                        (SELECT COUNT(*) FROM DocumentClassification AS DCAux WHERE DCAux.idDocumento = DocumentClassification.idDocumento AND NOT ISNULL(DCAux.esporte) ) AS esporte,
    	    
                        (SELECT COUNT(*) FROM DocumentClassification AS DCAux WHERE DCAux.idDocumento = DocumentClassification.idDocumento AND NOT ISNULL(DCAux.governanca) ) AS governanca,
    	    
                        (SELECT COUNT(*) FROM DocumentClassification AS DCAux WHERE DCAux.idDocumento = DocumentClassification.idDocumento AND NOT ISNULL(DCAux.meioAmbiente) ) AS meioAmbiente,
    	    
                        (SELECT COUNT(*) FROM DocumentClassification AS DCAux WHERE DCAux.idDocumento = DocumentClassification.idDocumento AND NOT ISNULL(DCAux.mobilidade) ) AS mobilidade,
    	    
                        (SELECT COUNT(*) FROM DocumentClassification AS DCAux WHERE DCAux.idDocumento = DocumentClassification.idDocumento AND NOT ISNULL(DCAux.politica) ) AS politica,
    	    
                        (SELECT COUNT(*) FROM DocumentClassification AS DCAux WHERE DCAux.idDocumento = DocumentClassification.idDocumento AND NOT ISNULL(DCAux.saude) ) AS saude,
    	    
                        (SELECT COUNT(*) FROM DocumentClassification AS DCAux WHERE DCAux.idDocumento = DocumentClassification.idDocumento AND NOT ISNULL(DCAux.seguranca) ) AS seguranca,
    	    
                        (SELECT COUNT(*) FROM DocumentClassification AS DCAux WHERE DCAux.idDocumento = DocumentClassification.idDocumento AND NOT ISNULL(DCAux.servico) ) AS servico,
    	    
                        (SELECT COUNT(*) FROM DocumentClassification AS DCAux WHERE DCAux.idDocumento = DocumentClassification.idDocumento AND NOT ISNULL(DCAux.tecnologiaInovacao) ) AS tecnologiaInovacao,
    	    
                        (SELECT COUNT(*) FROM DocumentClassification AS DCAux WHERE DCAux.idDocumento = DocumentClassification.idDocumento AND NOT ISNULL(DCAux.trabalho) ) AS trabalho,
    	    
                        (SELECT COUNT(*) FROM DocumentClassification AS DCAux WHERE DCAux.idDocumento = DocumentClassification.idDocumento AND NOT ISNULL(DCAux.urbanismo) ) AS urbanismo
    	    
                        FROM DocumentClassification
                        GROUP BY DocumentClassification.idDocumento ";
	$stringSql .= "HAVING (qtde >= 5 AND 
		        		(
		                		(1 ";
	
                			$stringSql .= ($campo != 'alimentacao') ? " AND (".$campo." > alimentacao) " : "";
			                $stringSql .= ($campo != 'cultura') ? " AND (".$campo." > cultura) " : ""; 
			                $stringSql .= ($campo != 'economia') ? " AND (".$campo." > economia)  " : "";
			                $stringSql .= ($campo != 'educacao') ? " AND (".$campo." > educacao)  " : "";
			                $stringSql .= ($campo != 'empreendedorismo') ? " AND (".$campo." > empreendedorismo) " : "";
			                $stringSql .= ($campo != 'energia') ? " AND (".$campo." > energia) " : "";
			                $stringSql .= ($campo != 'esporte') ? " AND (".$campo." > esporte) " : "";
			                $stringSql .= ($campo != 'governanca') ? " AND (".$campo." > governanca) " : "";
			                $stringSql .= ($campo != 'meioAmbiente') ? " AND (".$campo." > meioAmbiente) " : "";
			                $stringSql .= ($campo != 'mobilidade') ? " AND (".$campo." > mobilidade) " : "";
			                $stringSql .= ($campo != 'politica') ? " AND (".$campo." > politica) " : "";
			                $stringSql .= ($campo != 'saude') ? " AND (".$campo." > saude) " : "";
			                $stringSql .= ($campo != 'seguranca') ? " AND (".$campo." > seguranca) " : "";
			                $stringSql .= ($campo != 'servico') ? " AND (".$campo." > servico) " : "";
			                $stringSql .= ($campo != 'tecnologiaInovacao') ? " AND (".$campo." > tecnologiaInovacao) " : "";
			                $stringSql .= ($campo != 'trabalho') ? " AND (".$campo." > trabalho) " : "";
			                $stringSql .= ($campo != 'urbanismo') ? " AND (".$campo." > urbanismo) " : "";
		              $stringSql .=  	")
		                	OR ".$campo." >= 3
		        		)
				)
				OR 
				(qtde = 4 AND ".$campo." >= 3) 
				OR 
				(qtde = 3 AND ".$campo." >= 3)
                        ORDER BY qtde DESC
			) AS tab".strtoupper($campo).";";
		
		echo "<pre>";
		print_r($stringSql);
		echo "</pre>";
		
	    return $stringSql;
    	}
    	
    	$eixos = array("alimentacao", "cultura", "economia", "educacao", "empreendedorismo", "energia", "esporte", "governanca", 
    	               "meioAmbiente", "mobilidade", "politica", "saude", "seguranca", "servico", "tecnologiaInovacao", "trabalho", "urbanismo");
    	
    	echo "<div style='text-align: center;'>";
    	foreach ($eixos as $i=> $eixo) {
	    if($i == 0) echo "<hr/>";
    	    $stringSql = retornarSQL($eixo);
    	    $query = mysqli_query($conexao->getConexao(), $stringSql);
    	    for ($a = 0; $dados = mysqli_fetch_assoc($query); $a++) echo strtoupper($eixo).": ".$dados[$eixo]."<BR/><BR/><hr/>";
    	}
    	echo "</div>";
    	
	?>
</body>
</html>
