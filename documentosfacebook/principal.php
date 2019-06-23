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
			<p id="msg-bem-vindo">Bem vindo <?php // echo $_SESSION['NOME']; ?></p>
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
			<div class="col-md-12 content" role="main" id="topo">
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
    			
    			$_SESSION["offsetQueryMySQLPrincipal"] = 0;
    			$_SESSION['TABLE_DOCUMENT'] = 1;
    			$stringSQLPrincipal = "SELECT Document.id, Document.name, Document.description, Document.message, SUBSTR(Document.createdTime, 1, 10) AS createdTime  
		    						    FROM Document1 AS Document
										LEFT JOIN DocumentClassification ON DocumentClassification.idDocumento = Document.id
										WHERE (NOT ISNULL(Document.message) OR NOT ISNULL(Document.description)) AND
												Document.ehRioGrande = 1 AND 
											    Document.id NOT IN (
											                         SELECT DocumentClassification.idDocumento
											                         FROM DocumentClassification
											                         GROUP BY DocumentClassification.idDocumento
											                         HAVING COUNT(DocumentClassification.idDocumento) >= 5
											        			   ) AND
										        Document.id NOT IN      (
										                                  SELECT DocumentClassification.idDocumento
										                                  FROM DocumentClassification
										                                  WHERE
																		  ISNULL(DocumentClassification.alimentacao) AND
																		  ISNULL(DocumentClassification.cultura) AND
																		  ISNULL(DocumentClassification.economia) AND
										                                  ISNULL(DocumentClassification.educacao) AND
										                                  ISNULL(DocumentClassification.empreendedorismo) AND
										                                  ISNULL(DocumentClassification.energia) AND
																		  ISNULL(DocumentClassification.esporte) AND
										                                  ISNULL(DocumentClassification.governanca) AND
										                                  ISNULL(DocumentClassification.meioAmbiente) AND
										                                  ISNULL(DocumentClassification.mobilidade) AND
                                                                          ISNULL(DocumentClassification.politica) AND  
										                                  ISNULL(DocumentClassification.saude) AND
										                                  ISNULL(DocumentClassification.seguranca) AND
																		  ISNULL(DocumentClassification.servico) AND
										                                  ISNULL(DocumentClassification.tecnologiaInovacao) AND
																		  ISNULL(DocumentClassification.trabalho) AND
										                                  ISNULL(DocumentClassification.urbanismo)
										            					) AND
												Document.id NOT IN     (
																	 	 SELECT DocumentClassification.idDocumento
																		 FROM DocumentClassification
																		 WHERE DocumentClassification.idUserClassificationWebSystem = ".$_SESSION['ID_USER']."
																	   )
						            	GROUP BY Document.id, Document.name, Document.description, Document.message
										ORDER BY
											COUNT(Document.id) DESC,
											Document.id IN (
															SELECT DocumentClassification.idDocumento
							                                	FROM DocumentClassification
							                                		WHERE (
																			NOT ISNULL(DocumentClassification.alimentacao) OR
																			NOT ISNULL(DocumentClassification.cultura) OR
							                                                NOT ISNULL(DocumentClassification.economia) OR
							                                                NOT ISNULL(DocumentClassification.educacao) OR
							                                                NOT ISNULL(DocumentClassification.empreendedorismo) OR
							                                                NOT ISNULL(DocumentClassification.energia) OR
																		    NOT ISNULL(DocumentClassification.esporte) OR
							                                                NOT ISNULL(DocumentClassification.governanca) OR
							                                                NOT ISNULL(DocumentClassification.meioAmbiente) OR
							                                                NOT ISNULL(DocumentClassification.mobilidade) OR
							                                                NOT ISNULL(DocumentClassification.politica) OR
							                                                NOT ISNULL(DocumentClassification.saude) OR
							                                                NOT ISNULL(DocumentClassification.seguranca) OR
																			NOT ISNULL(DocumentClassification.servico) OR
							                                                NOT ISNULL(DocumentClassification.tecnologiaInovacao) OR
																			NOT ISNULL(DocumentClassification.trabalho) OR
							                                                NOT ISNULL(DocumentClassification.urbanismo)
							                                              )
															GROUP BY DocumentClassification.id
							                                ORDER BY COUNT(DocumentClassification.idDocumento)
							                               ) DESC,
											RAND()
									    LIMIT 1 OFFSET ".$_SESSION["offsetQueryMySQLPrincipal"];
    			
                
    			$sql = mysqli_query($conexao->getConexao(), $stringSQLPrincipal) or die("Erro 1");
                $idDocumento;
                $temRegistro = mysqli_num_rows($sql);
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
                    }
                    $_SESSION["idDocumentoSessionPrincipal"] = $idDocumento;
                } else {
                	$nroDocumento = 2;
                	do {
                		$stringSQLPrincipal = "SELECT Document.id, Document.name, Document.description, Document.message, SUBSTR(Document.createdTime, 1, 10) AS createdTime  
				    						    FROM Document".$nroDocumento." AS Document
												LEFT JOIN DocumentClassification ON DocumentClassification.idDocumento = Document.id
												WHERE (NOT ISNULL(Document.message) OR NOT ISNULL(Document.description)) AND
														Document.ehRioGrande = 1 AND
													    Document.id NOT IN (
													                         SELECT DocumentClassification.idDocumento
													                         FROM DocumentClassification
													                         GROUP BY DocumentClassification.idDocumento
													                         HAVING COUNT(DocumentClassification.idDocumento) >= 5
													        			   ) AND
												        Document.id NOT IN      (
												                                  SELECT DocumentClassification.idDocumento
												                                  FROM DocumentClassification
												                                  WHERE
																				  ISNULL(DocumentClassification.alimentacao) AND
																				  ISNULL(DocumentClassification.cultura) AND
																				  ISNULL(DocumentClassification.economia) AND
												                                  ISNULL(DocumentClassification.educacao) AND
												                                  ISNULL(DocumentClassification.empreendedorismo) AND
												                                  ISNULL(DocumentClassification.energia) AND
																				  ISNULL(DocumentClassification.esporte) AND
												                                  ISNULL(DocumentClassification.governanca) AND
												                                  ISNULL(DocumentClassification.meioAmbiente) AND
												                                  ISNULL(DocumentClassification.mobilidade) AND
		                                                                          ISNULL(DocumentClassification.politica) AND
												                                  ISNULL(DocumentClassification.saude) AND
												                                  ISNULL(DocumentClassification.seguranca) AND
																				  ISNULL(DocumentClassification.servico) AND
												                                  ISNULL(DocumentClassification.tecnologiaInovacao) AND
																				  ISNULL(DocumentClassification.trabalho) AND
												                                  ISNULL(DocumentClassification.urbanismo)
												            					) AND
														Document.id NOT IN     (
																			 	 SELECT DocumentClassification.idDocumento
																				 FROM DocumentClassification
																				 WHERE DocumentClassification.idUserClassificationWebSystem = ".$_SESSION['ID_USER']."
																			   )
								            	GROUP BY Document.id, Document.name, Document.description, Document.message
												ORDER BY
													COUNT(Document.id) DESC,
													Document.id IN (
																	SELECT DocumentClassification.idDocumento
									                                	FROM DocumentClassification
									                                		WHERE (
																					NOT ISNULL(DocumentClassification.alimentacao) OR
																					NOT ISNULL(DocumentClassification.cultura) OR
									                                                NOT ISNULL(DocumentClassification.economia) OR
									                                                NOT ISNULL(DocumentClassification.educacao) OR
									                                                NOT ISNULL(DocumentClassification.empreendedorismo) OR
									                                                NOT ISNULL(DocumentClassification.energia) OR
																				    NOT ISNULL(DocumentClassification.esporte) OR
									                                                NOT ISNULL(DocumentClassification.governanca) OR
									                                                NOT ISNULL(DocumentClassification.meioAmbiente) OR
									                                                NOT ISNULL(DocumentClassification.mobilidade) OR
									                                                NOT ISNULL(DocumentClassification.politica) OR
									                                                NOT ISNULL(DocumentClassification.saude) OR
									                                                NOT ISNULL(DocumentClassification.seguranca) OR
																					NOT ISNULL(DocumentClassification.servico) OR
									                                                NOT ISNULL(DocumentClassification.tecnologiaInovacao) OR
																					NOT ISNULL(DocumentClassification.trabalho) OR
									                                                NOT ISNULL(DocumentClassification.urbanismo)
									                                              )
																	GROUP BY DocumentClassification.id
									                                ORDER BY COUNT(DocumentClassification.idDocumento)
									                               ) DESC,
													RAND()
											    LIMIT 1 OFFSET ".$_SESSION["offsetQueryMySQLPrincipal"];
                		$sql = mysqli_query($conexao->getConexao(), $stringSQLPrincipal) or die("Erro 1");
                		$idDocumento;
                		$temRegistro = mysqli_num_rows($sql);
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
                			}
                			$_SESSION["idDocumentoSessionPrincipal"] = $idDocumento;
                			$_SESSION['TABLE_DOCUMENT'] = $nroDocumento;
                		} else $nroDocumento++;
                	} while((!$temRegistro) && ($nroDocumento <= 44));
                	
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
    						<option value="alimentacao">Alimentação</option>
    						<option value="cultura">Cultura</option>
                        	<option value="economia">Economia</option>
                        	<option value="educacao">Educação</option>
                        	<option value="empreendedorismo">Empreendedorismo</option>
                        	<option value="energia">Energia</option>
                        	<option value="esporte">Esporte</option>
                        	<option value="governanca">Governança (Administração pública)</option>
                        	<option value="meioAmbiente">Meio Ambiente</option>
                        	<option value="mobilidade">Mobilidade</option>
                        	<option value="politica">Política</option>
                        	<option value="saude">Saúde</option>
                        	<option value="seguranca">Segurança</option>
                        	<option value="servico">Serviço</option>
                        	<option value="tecnologiaInovacao">Tecnologia e Inovação</option>
                        	<option value="trabalho">Trabalho</option>
                        	<option value="urbanismo">Urbanismo</option>
                        </select>
                        <div class="box-actions" id="box-radio-polaridade">
                        	De acordo com a sua opinião, como você define este texto em relação ao conteúdo da publicação? O texto expressa um sentimento:
                        	<div class="radio">
                            	<label><input type="radio" name="radioNegative" id="radioNegative">Negativo</label>
                            </div>
                            <div class="radio">
                            	<label><input type="radio" name="radioNeutral" id="radioNeutral">Neutro</label>
                            </div>
                            <div class="radio">
                            	<label><input type="radio" name="radioPositive" id="radioPositive">Positivo</label>
                            </div> 
                        </div>
                        <div class="box-actions">
            				<button type="submit" class="btn btn-default" style="float: right;" id="botao-enviar-proximo-principal" 
            				<?php 
            				if(!$temRegistro) echo "disabled=\"disabled\"";
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
	<script type="text/javascript" src="./js/defaultPrincipal.js"></script>
	<script type="text/javascript" src="./js/radioBoxFormulario.js"></script>
	
	
	<!--
	<script type="text/javascript" src="//code.jquery.com/jquery-2.0.3.min.js"></script>
	<script type="text/javascript" src="//assets.locaweb.com.br/locastyle/2.0.6/javascripts/locastyle.js"></script>
	<script type="text/javascript" src="//netdna.bootstrapcdn.com/bootstrap/3.0.3/js/bootstrap.min.js"></script>
	-->
	
</body>
</html>
<?php
}
?>
