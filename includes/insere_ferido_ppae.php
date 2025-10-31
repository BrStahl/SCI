<?php
session_name("covre_ti");
session_start();

include("../../SCA/includes/conect_sqlserver.php");

$logado = $_SESSION["usuario_logado"];

$nome			= $_POST["nome"];
$telefone		= $_POST["telefone"];
$acidente_id	= $_POST["acidente_id"];
$tipo_ferimento	= $_POST["tipo"];


	$query = "insert into feridos_acidente_ppae (acidente_id, nome, telefone, tipo_ferimento, status_id) 
			  values ($acidente_id, '$nome', '$telefone', '$tipo_ferimento', 'a')";
	print $query;
	odbc_exec($conSQL, $query) or die ('erro1 ao inserir');
	


?>
