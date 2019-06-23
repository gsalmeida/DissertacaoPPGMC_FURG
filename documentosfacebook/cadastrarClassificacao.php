<?php 

    session_start();
    if (isset($_SESSION['ultimaAtividade']) && (time() - $_SESSION['ultimaAtividade'] > 600)) { // segundos
        session_unset();
        session_destroy();
        echo "<script>location.href='index.php';</script>";
        die();
    }
    $_SESSION['ultimaAtividade'] = time();
    
//     $_SESSION["offsetQueryMySQLPrincipal"]++;
    $idDocumentoAnterior = $_SESSION["idDocumentoSessionPrincipal"];
    
    
    include_once "./db/Conexao.class.php";
    $conexao = new Conexao();
    

    $tableDocument = $_SESSION['TABLE_DOCUMENT'];
    
    $temRegistro = false;
    while((!$temRegistro) && ($tableDocument <= 44)) {
        $stringSQLPrincipal = "SELECT Document.id, Document.name, Document.description, Document.message, SUBSTR(Document.createdTime, 1, 10) AS createdTime
    				    						    FROM Document".$tableDocument." AS Document
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
            $tableDocument++;
            $_SESSION['TABLE_DOCUMENT'] = $tableDocument;
        }
    }
    
    if(!empty($idDocumento)) $_SESSION["idDocumentoSessionPrincipal"] = $idDocumento;
    if(!$idDocumento) echo "<script>document.getElementById('botao-enviar-proximo-principal').disabled = false;</script>";
    
    $stringCamposPolaridade = NULL;
    $valuesCamposPolaridade = 1;
    if(!empty($_POST["radioPositive"])) $stringCamposPolaridade = "positiveOpinion";
    else if(!empty($_POST["radioNeutral"])) $stringCamposPolaridade = "neutralOpinion";
    else if(!empty($_POST["radioNegative"])) $stringCamposPolaridade = "negativeOpinion";
    else {
        $stringCamposPolaridade = "positiveOpinion, neutralOpinion, negativeOpinion";
        $valuesCamposPolaridade = "NULL, NULL, NULL";
    }
    
    $arraySelecionados = array();
    if( (isset($_POST["campo-select"])) && (!empty($_POST["campo-select"])) ) {
        echo "<script>$('#ListaPresenteSelect').prop('selectedIndex', -1);</script>";
        foreach($_POST["campo-select"] as $i => $opcao) $arraySelecionados[$i] = $opcao;
        $stringCampos = "";
        $stringValues = "";
        for($a = 0; $a < count($arraySelecionados); $a++) {
            $stringCampos .= (($a+1) == count($arraySelecionados)) ? $arraySelecionados[$a] : $arraySelecionados[$a].", ";
            $stringValues .= (($a+1) == count($arraySelecionados)) ? "1" : "1, ";
        }
        $queryInsert = "INSERT INTO DocumentClassification (idDocumento, idUserClassificationWebSystem, ".$stringCampos.", ".$stringCamposPolaridade.")
    								VALUES (".$idDocumentoAnterior.", ".$_SESSION['ID_USER'].", ".$stringValues.", ".$valuesCamposPolaridade.")";
        mysqli_query($conexao->getConexao(), $queryInsert);
    } else {
		echo "<script>$('#ListaPresenteSelect').prop('selectedIndex', -1);</script>";
		$queryInsert = "INSERT INTO DocumentClassification (idDocumento, idUserClassificationWebSystem, ".$stringCamposPolaridade.") 
                                                    VALUES (".$idDocumentoAnterior.", ".$_SESSION['ID_USER'].", ".$valuesCamposPolaridade.")";
		mysqli_query($conexao->getConexao(), $queryInsert);
	}
    echo "<script>document.getElementById('botao-enviar-proximo-principal').disabled = false;</script>";
    
    echo "<script>
    			         document.getElementById('radioNegative').checked = false;
				         document.getElementById('radioNeutral').checked = false;
				         document.getElementById('radioPositive').checked = false;
                         location.href='#topo';
		  </script>";
?>
