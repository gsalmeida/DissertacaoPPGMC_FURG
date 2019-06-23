<?php 
if(isset($_GET["action"])) {
    $isAvancar = false;
    if(isset($_GET["action"])) if($_GET["action"] == "avancar") $isAvancar = true;
    
    session_start();
    if (isset($_SESSION['ultimaAtividade']) && (time() - $_SESSION['ultimaAtividade'] > 600)) { // segundos
        session_unset();
        session_destroy();
        echo "<script>location.href='index.php';</script>";
        die();
    }
    $_SESSION['ultimaAtividade'] = time();
    
    $idDocumentoAnterior = $_SESSION["idDocumentoSession"];
    
    include_once "./db/Conexao.class.php";
    $conexao = new Conexao();
    
    
    $stringPolaridade = NULL;
    if(!empty($_POST["radioPositive"])) $stringPolaridade = "positiveOpinion = 1 ";
    else if(!empty($_POST["radioNeutral"])) $stringPolaridade = "neutralOpinion = 1 ";
    else if(!empty($_POST["radioNegative"])) $stringPolaridade = "negativeOpinion = 1 ";
    else $stringPolaridade = "positiveOpinion = NULL, neutralOpinion = NULL, negativeOpinion = NULL ";
    
    if($isAvancar) {
        $arraySelecionados = array();
        if( (isset($_POST["campo-select"])) && (!empty($_POST["campo-select"])) ) {
//         if( ( (isset($_POST["campo-select"])) && (!empty($_POST["campo-select"])) ) &&
//             ( ( (isset($_POST["radioNegative"])) && (!empty($_POST["radioNegative"])) ) ||
//                 ( (isset($_POST["radioNeutral"])) && (!empty($_POST["radioNeutral"])) ) ||
//                 ( (isset($_POST["radioPositive"])) && (!empty($_POST["radioPositive"])) ) ) ) {
                                
            foreach($_POST["campo-select"] as $i => $opcao) $arraySelecionados[$i] = $opcao;
    	    $stringCampos = "";
    	    $stringValues = "";
    	    $stringUpdate = "";
    	    for($a = 0; $a < count($arraySelecionados); $a++) {
    	        $stringCampos .= (($a+1) == count($arraySelecionados)) ? $arraySelecionados[$a] : $arraySelecionados[$a].", ";
    	        $stringValues .= (($a+1) == count($arraySelecionados)) ? "1" : "1, ";
    	        $stringUpdate .= (($a+1) == count($arraySelecionados)) ? $arraySelecionados[$a]." = 1" : $arraySelecionados[$a]." = 1, ";
    	    }
    		$querySQLInsertUpdate= "SELECT DocumentClassification.id 
    									FROM DocumentClassification 
    									WHERE DocumentClassification.idDocumento = ".$idDocumentoAnterior." AND
    							  			  DocumentClassification.idUserClassificationWebSystem = ".$_SESSION['ID_USER'];
    		$sqlInsertUpdate = mysqli_query($conexao->getConexao(), $querySQLInsertUpdate);
    		$isClassificado = mysqli_num_rows($sqlInsertUpdate);
    		if($isClassificado) {
    			$queryUpdate = "UPDATE DocumentClassification
    								SET alimentacao = NULL, cultura = NULL, economia = NULL, educacao = NULL, empreendedorismo = NULL, energia = NULL, 
                                        esporte = NULL, governanca = NULL, meioAmbiente = NULL, mobilidade = NULL, politica = NULL, saude = NULL, seguranca = NULL, 
                                        servico = NULL, tecnologiaInovacao = NULL, trabalho = NULL, urbanismo = NULL, 
                                        negativeOpinion = NULL, neutralOpinion = NULL, positiveOpinion = NULL 
    									WHERE DocumentClassification.idDocumento = ".$idDocumentoAnterior. " AND  
    									  	  DocumentClassification.idUserClassificationWebSystem = ".$_SESSION['ID_USER'];
    			mysqli_query($conexao->getConexao(), $queryUpdate);
    			
    			$queryUpdate = "UPDATE DocumentClassification 
    								SET ".$stringUpdate.",".$stringPolaridade."  
                                    WHERE DocumentClassification.idDocumento = ".$idDocumentoAnterior. " AND 
    									  DocumentClassification.idUserClassificationWebSystem = ".$_SESSION['ID_USER'];
    			mysqli_query($conexao->getConexao(), $queryUpdate);
    		} 
    	} else {
    		$querySQLInsertUpdate = "SELECT DocumentClassification.id 
    									FROM DocumentClassification 
    									WHERE DocumentClassification.idDocumento = ".$idDocumentoAnterior." AND 
    							  			  DocumentClassification.idUserClassificationWebSystem = ".$_SESSION['ID_USER'];
    		$sqlInsertUpdate = mysqli_query($conexao->getConexao(), $querySQLInsertUpdate);
    		$isClassificado = mysqli_num_rows($sqlInsertUpdate);
    		if($isClassificado) {
    		    $queryUpdate = "UPDATE DocumentClassification 
    								SET alimentacao = NULL, cultura = NULL, economia = NULL, educacao = NULL, empreendedorismo = NULL, energia = NULL,  
                                        esporte = NULL, governanca = NULL, meioAmbiente = NULL, mobilidade = NULL, politica = NULL, saude = NULL, seguranca = NULL, 
                                        servico = NULL, tecnologiaInovacao = NULL, trabalho = NULL, urbanismo = NULL, ".$stringPolaridade."  
    									WHERE DocumentClassification.idDocumento = ".$idDocumentoAnterior. " AND
    									  	  DocumentClassification.idUserClassificationWebSystem = ".$_SESSION['ID_USER'];
    			mysqli_query($conexao->getConexao(), $queryUpdate);
    		} 
    	}
    }
    
     echo "<script>
                   $('#ListaPresenteSelect').prop('selectedIndex', -1);
                   document.getElementById('botao-enviar-proximo').disabled = false;
           </script>";
     echo "<script>
    			         document.getElementById('radioNegative').checked = false;
				         document.getElementById('radioNeutral').checked = false;
				         document.getElementById('radioPositive').checked = false;
		  </script>";
}

?>

