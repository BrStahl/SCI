<?php
session_name("covre_ti");
session_start();

include("../../SCA/includes/conect_sqlserver.php");
require_once "../class/Dados.php";

$dados = new Dados;	

$acidente_id 	= $_POST["acidente_id"];


	
	$consulta = $dados->buscaDespesas($acidente_id);  
	
	$retorno = $consulta->retorno;
	
	print $retorno;


?>

