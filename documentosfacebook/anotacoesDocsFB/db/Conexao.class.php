<?php

  class Conexao {

	private $usuario = "root";

	private $senha = "MySQLrootadmin123";
// 	private $senha = "root";

	private $sid = "localhost";

	private $banco = "ppgmcfull";

	private $conexao = "";  	

	public function __construct() {
  		$this->conectar();
 	}

	function conectar() {
	    $this->conexao = mysqli_connect($this->sid, $this->usuario, $this->senha); 
	    mysqli_set_charset($this->conexao, 'utf8');
  		if (!$this->conexao) die("Problema na Conexão com o Banco de Dados");
  		elseif (!mysqli_select_db($this->conexao, $this->banco)) die("Problema na Conexão com o Banco de Dados");
//   		else echo "Conectou";
	}

	function getConexao() { 
		return $this->conexao; 
	}

	function desconectar() {
		return mysqli_close($this->conexao);
	}

}

  ?>

