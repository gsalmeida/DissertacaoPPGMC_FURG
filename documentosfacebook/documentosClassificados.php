<?php 

session_start();
// session_regenerate_id();

if(!isset($_SESSION["LOGIN"])) echo "<script>alert('É necessário estar logado no sistema para ter acesso a rotina.'); location.href='index.php';</script>";
else if(isset($_SESSION["LOGIN"])) {
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
<title>Sistema para classificação de documentos do Facebook</title>
<meta charset="utf-8">

<!-- Isso é necessário para funcionar a versão mobile -->
<meta name="viewport"
	content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">

<!-- CSS -->
<link rel="stylesheet" type="text/css" href="./bootstrapcss/bootstrap.min.css">
<link rel="stylesheet" type="text/css" href="./bootstrapcss/locastyle.css">
<link rel="stylesheet" type="text/css" href="./bootstrapcss/logout.css">
<!--
<link rel="stylesheet" type="text/css" href="//netdna.bootstrapcdn.com/bootstrap/3.0.3/css/bootstrap.min.css">
<link rel="stylesheet" type="text/css" href="//assets.locaweb.com.br/locastyle/2.0.6/stylesheets/locastyle.css">
-->


</head>
<body>
	<!-- Header principal -->
	<header class="header" role="banner">
		<div>
			<form action="index.php" method="post">
				<button type="submit" name="botao-logout" id="botao-sair">Sair</button>
			</form>
		</div>
		<!--
		<div>
			<p id="msg-bem-vindo">Bem vindo <?php echo $_SESSION['NOME']; ?></p>
		</div>
		-->
		<div class="container">
			<span class="control-menu visible-xs ico-menu-2">
			</span>
			<h1 class="project-name">
				<a href="principal.php">Classificação de documentos do Facebook</a>
			</h1>
		</div>
	</header>

	<!-- Menu -->
	<!--
	<div class="nav-content">
		<div class="footer-menu">
			<h2 ></h2>
			<ul class="no-liststyle">
				<li><a><span></span></a></li>
			</ul>
			
		</div>
	</div>
	-->
	<div class="nav-content">
		<menu class="menu">
			<ul class="container">
				<!--<li><a href="#" class="active ico-home" role="menuitem">Home</a></li>-->
				<li><a href="documentosClassificados.php" role="menuitem">classificados</a>
				<li><a href="principal.php" role="menuitem">Não classificados</a>
				<!--
				<li><a href="#" role="menuitem">Documentos</a>
					<ul>
						<li><a href="#">Não classificados</a></li>
						<li><a href="documentosClassificados.php">Classificados</a></li>
					</ul>
				</li>
				-->
			</ul>
		</menu>
	</div>
	
	<!-- Aqui começa a parte de conteúdo dividido por colunas -->
	<main class="main">
	<div class="container">
		<div class="row">
			<div class="col-md-12 content" role="main">
    			<br/><br/>
    			<div class="recebeDadosAjax">
    			<?php
			    
			    if (isset($_SESSION['ultimaAtividade']) && (time() - $_SESSION['ultimaAtividade'] > 600)) { // segundos
    			    session_unset();
    			    session_destroy();
    			    echo "<script>location.href='index.php';</script>";
    			}
    			
    			include_once "./db/Conexao.class.php";
    			$conexao = new Conexao();
    			
    			$fromWhereSQL = " FROM Document
                                    INNER JOIN DocumentClassification ON DocumentClassification.idDocumento = Document.id
					    			WHERE (NOT ISNULL(Document.message) OR NOT ISNULL(Document.description)) AND
					    			       Document.ehRioGrande = 1 AND 
                                        Document.distinctConsidered = 1 AND Document.inconsiderateByLength = 0 AND
                                        Document.id IN (SELECT DocumentClassification.idDocumento FROM DocumentClassification
                                                            WHERE DocumentClassification.idUserClassificationWebSystem = ".$_SESSION['ID_USER'].") AND
                                        DocumentClassification.idUserClassificationWebSystem = ".$_SESSION['ID_USER'];
    			
    			
    			
    			$orderBy = " ORDER BY DocumentClassification.id ASC";

    			$stringSQLQtdeRegistros = "SELECT COUNT(Document.id) AS quantidadeTotalDeRegistros ".$fromWhereSQL." ".$orderBy;
    			
    			$quantidadeTotalDeRegistros = 0;
    			$sqlQtdeRegistros = mysqli_query($conexao->getConexao(), $stringSQLQtdeRegistros) or die("Erro em consulta SQL (1)");
    			while ($dados = mysqli_fetch_assoc($sqlQtdeRegistros)) $quantidadeTotalDeRegistros = $dados["quantidadeTotalDeRegistros"];
    			$_SESSION["quantidadeTotalDeRegistros"] = $quantidadeTotalDeRegistros;
    			
                $_SESSION["offsetQueryMySQL"] = 0;
                
// 				PODE TER SIDO LIDO E CLASSIFICADO EM NENHUM EIXO.
                $stringSQLDadosLidosEOuClassificados = "SELECT DocumentClassification.idDocumento 
                                                            FROM DocumentClassification 
                                                        WHERE DocumentClassification.idUserClassificationWebSystem = ".$_SESSION['ID_USER'];
                $sqlDadosLidosEOuClassificados = mysqli_query($conexao->getConexao(), $stringSQLDadosLidosEOuClassificados) or die("Erro em consulta SQL (2)");
                $arrayDadosLidosEOuClassificados = array();
                for ($a = 0; $dados = mysqli_fetch_assoc($sqlDadosLidosEOuClassificados); $a++) $arrayDadosLidosEOuClassificados[$a] = $dados["idDocumento"];
                $offsetUltimoClassificado = (!count($arrayDadosLidosEOuClassificados)) ? 0 : (count($arrayDadosLidosEOuClassificados) - 1);
                $_SESSION["offsetQueryMySQL"] = $offsetUltimoClassificado;
                
                $stringSQL = "SELECT Document.id, Document.name, Document.description, Document.message, 
                                    SUBSTR(Document.createdTime, 1, 10) AS createdTime, 
                                    DocumentClassification.negativeOpinion, DocumentClassification.neutralOpinion, 
                                    DocumentClassification.positiveOpinion ".$fromWhereSQL." ".$orderBy. " LIMIT 1 OFFSET ".$_SESSION["offsetQueryMySQL"]; 
                
                $sql = mysqli_query($conexao->getConexao(), $stringSQL) or die("Erro 1");
                $idDocumento = 0;
                $temRegistro = mysqli_num_rows($sql);
                $polaridade = 5;
                if($temRegistro) {
                    while ($dados = mysqli_fetch_assoc($sql)) {
                        $idDocumento = $dados["id"];
                        echo "<b>ID:</b>".$dados['id']."<br/><br/>";
                        if(!empty($dados['createdTime'])) {
                            $data = explode("-", $dados['createdTime']);
                            echo "<b>DATA:</b>".$data[2]."/".$data[1]."/".$data[0]."<br/><br/>";
                        } else echo "<b>DATA:</b><br/><br/>";
                        echo "<b>NAME:</b><br/>".str_replace("\n", "<br/>", $dados['name']);
                        echo "<br/><br/>";
                        echo "<b>DESCRIPTION:</b><br/>".str_replace("\n", "<br/>", $dados['description']);
                        echo "<br/><br/>";
                        echo "<b>MESSAGE:</b><br/>".str_replace("\n", "<br/>", $dados['message']);
                        if(!empty($dados["negativeOpinion"])) $polaridade = -1;
                        else if(!empty($dados["neutralOpinion"])) $polaridade = 0;
                        else if(!empty($dados["positiveOpinion"])) $polaridade = 1;
                    }
                    $_SESSION["idDocumentoSession"] = $idDocumento;
                }
                
                ?>

    			</div>
    			<br/>
    			<hr/>
    			<form role="form" id="formulario" method="post" action="">
    				<fieldset>
    					<!--
    					<select id="ListaPresenteSelect" name="campo-select" class="form-control" size="11">
    					-->
    					<select id="ListaPresenteSelect" name="campo-select[]" multiple="multiple" class="form-control" size="17">
    						<option id="alimentacaoOption" value="alimentacao">Alimentação</option>
    						<option id="culturaOption" value="cultura">Cultura</option>
                        	<option id="economiaOption" value="economia">Economia</option>
                        	<option id="educacaoOption" value="educacao">Educação</option>
                        	<option id="empreendedorismoOption" value="empreendedorismo">Empreendedorismo</option>
                        	<option id="energiaOption" value="energia">Energia</option>
                        	<option id="esporteOption" value="esporte">Esporte</option>
                        	<option id="governancaOption" value="governanca">Governança (Administração pública)</option>
                        	<option id="meioAmbienteOption" value="meioAmbiente">Meio Ambiente</option>
                        	<option id="mobilidadeOption" value="mobilidade">Mobilidade</option>
                        	<option id="politicaOption" value="politica">Política</option>
                        	<option id="saudeOption" value="saude">Saúde</option>
                        	<option id="segurancaOption" value="seguranca">Segurança</option>
                        	<option id="servicoOption" value="servico">Serviço</option>
                        	<option id="tecnologiaInovacaoOption" value="tecnologiaInovacao">Tecnologia e Inovação</option>
                        	<option id="trabalhoOption" value="trabalho">Trabalho</option>
                        	<option id="urbanismoOption" value="urbanismo">Urbanismo</option>
                        </select>
                        <div class="box-actions" id="box-radio-polaridade">
                        	De acordo com a sua opinião, como você define este texto em relação ao conteúdo da publicação? O texto expressa um sentimento: 
                        	<div class="radio">
                            	<label><input <?php echo ($polaridade == -1) ? 'checked="checked"' : ""; ?> type="radio" name="radioNegative" id="radioNegative">Negativo</label>
                            </div>
                            <div class="radio">
                            	<label><input <?php echo ($polaridade == 0) ? 'checked="checked"' : ""; ?> type="radio" name="radioNeutral" id="radioNeutral">Neutro</label>
                            </div>
                            <div class="radio">
                            	<label><input <?php echo ($polaridade == 1) ? 'checked="checked"' : ""; ?> type="radio" name="radioPositive" id="radioPositive">Positivo</label>
                            </div> 
                        </div>
                        <div class="box-actions">
            				<button type="submit" class="btn btn-default" style="float: right;" id="botao-enviar-proximo" 
            				<?php 
            				if(($quantidadeTotalDeRegistros == 0) || (!$temRegistro)) echo "disabled=\"disabled\"";
                            ?>
            				>Enviar / Próximo
            				</button>
            			</div>
    				</fieldset>
    			</form>
			</div>
		</div>
	</div>
	</main>

	<!-- Footer -->
	<footer class="footer">
		<div class="footer-menu">
			<nav class="container">
				<h2 ></h2>
				<ul class="no-liststyle">
					<li><a><span></span></a></li>
					<li><a><span></span></a></li>
					<li><a><span></span></a></li>
					<li><a><span></span></a></li>
				</ul>
			</nav>
		</div>
		
		<div class="container footer-info">
			<p class="copy-right">
			<a href="http://www.furg.br" target="_blank"><img src="./imagens/logoFURG.png" width="64px" height="64px"/></a>
			<br/>
			Universidade Federal do Rio Grande - FURG 
			<br/>
			Programa de Pós-Graduação em Modelagem Computacional
			</p>
			
		</div>
	</footer>

	<!-- Scripts - Atente-se na ordem das chamadas -->
	
	<script type="text/javascript" src="./bootstrapjs/jquery.min.js"></script>
	<script type="text/javascript" src="./bootstrapjs/locastyle.js"></script>
	<script type="text/javascript" src="./bootstrapjs/bootstrap.min.js"></script>
	<script type="text/javascript" src="./js/defaultClassificados.js"></script>
	<script type="text/javascript" src="./js/radioBoxFormulario.js"></script>
	
	<!--
	<script type="text/javascript" src="//code.jquery.com/jquery-2.0.3.min.js"></script>
	<script type="text/javascript" src="//assets.locaweb.com.br/locastyle/2.0.6/javascripts/locastyle.js"></script>
	<script type="text/javascript" src="//netdna.bootstrapcdn.com/bootstrap/3.0.3/js/bootstrap.min.js"></script>
	-->
	<?php 
		
		$stringJqueryOptionsSelect = "";
		if($idDocumento) {
			$sqlConsultaClassificacao = "SELECT DocumentClassification.id,
		                                        DocumentClassification.alimentacao,
		                                        DocumentClassification.cultura,
		                                        DocumentClassification.economia,
		                                        DocumentClassification.educacao,
		                                        DocumentClassification.empreendedorismo,
											    DocumentClassification.energia,
		                                        DocumentClassification.esporte,
		                                        DocumentClassification.governanca,
		                                        DocumentClassification.meioAmbiente,
		                                        DocumentClassification.mobilidade,
                                                DocumentClassification.politica,
											    DocumentClassification.saude,
		                                        DocumentClassification.seguranca,
		                                        DocumentClassification.servico,
		                                        DocumentClassification.tecnologiaInovacao,
		                                        DocumentClassification.trabalho,
		                                        DocumentClassification.urbanismo
										FROM DocumentClassification
										WHERE DocumentClassification.idDocumento = ".$idDocumento." AND
								  			  DocumentClassification.idUserClassificationWebSystem = ".$_SESSION['ID_USER'];
			$sqlQueryConsultaClassificacao = mysqli_query($conexao->getConexao(), $sqlConsultaClassificacao);
			$temEixoSelecionado = mysqli_num_rows($sqlQueryConsultaClassificacao);
			if($temEixoSelecionado) {
				while ($dados = mysqli_fetch_assoc($sqlQueryConsultaClassificacao)) {
					if($dados["alimentacao"]) $stringJqueryOptionsSelect .= "jQuery('#alimentacaoOption').attr('selected', true);";
					if($dados["cultura"]) $stringJqueryOptionsSelect .= "jQuery('#culturaOption').attr('selected', true);";
					if($dados["economia"]) $stringJqueryOptionsSelect .= "jQuery('#economiaOption').attr('selected', true);";
					if($dados["educacao"]) $stringJqueryOptionsSelect .= "jQuery('#educacaoOption').attr('selected', true);";
					if($dados["empreendedorismo"]) $stringJqueryOptionsSelect .= "jQuery('#empreendedorismoOption').attr('selected', true);";
					if($dados["energia"]) $stringJqueryOptionsSelect .= "jQuery('#energiaOption').attr('selected', true);";
					if($dados["esporte"]) $stringJqueryOptionsSelect .= "jQuery('#esporteOption').attr('selected', true);";
					if($dados["governanca"]) $stringJqueryOptionsSelect .= "jQuery('#governancaOption').attr('selected', true);";
					if($dados["meioAmbiente"]) $stringJqueryOptionsSelect .= "jQuery('#meioAmbienteOption').attr('selected', true);";
					if($dados["politica"]) $stringJqueryOptionsSelect .= "jQuery('#politicaOption').attr('selected', true);";
					if($dados["mobilidade"]) $stringJqueryOptionsSelect .= "jQuery('#mobilidadeOption').attr('selected', true);";
					if($dados["saude"]) $stringJqueryOptionsSelect .= "jQuery('#saudeOption').attr('selected', true);";
					if($dados["seguranca"]) $stringJqueryOptionsSelect .= "jQuery('#segurancaOption').attr('selected', true);";
					if($dados["servico"]) $stringJqueryOptionsSelect .= "jQuery('#servicoOption').attr('selected', true);";
					if($dados["tecnologiaInovacao"]) $stringJqueryOptionsSelect .= "jQuery('#tecnologiaInovacaoOption').attr('selected', true);";
					if($dados["trabalho"]) $stringJqueryOptionsSelect .= "jQuery('#trabalhoOption').attr('selected', true);";
					if($dados["urbanismo"]) $stringJqueryOptionsSelect .= "jQuery('#urbanismoOption').attr('selected', true);";
				}
			}
		}
	
	echo "<script>
    		$(document).ready(function() {
    			".$stringJqueryOptionsSelect."
    		});
    	 </script>";
	
	?>
</body>
</html>
<?php 
}
?>
