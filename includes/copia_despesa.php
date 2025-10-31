<?php
session_name("covre_ti");
session_start();

include("../../SCA/includes/conect_sqlserver.php");

$logado = $_SESSION["usuario_logado"];

$id			= $_POST["id"];
//$acidente_id	= $_POST["acidente_id"];


if ($logado != '')
{

	//inserindo na table lancamento_despesas_acidente
	$query = "insert into lancamento_despesas_acidente (acidente_id, tipo_despesa_id, status_despesa_id, prazo, data_inclusao, area_id, status_id) 
			  select acidente_id, tipo_despesa_id, '2', prazo, GETDATE(), area_id, 'a'
			  from lancamento_despesas_acidente
			  where lancamento_id = $id";
	//print $query;
	odbc_exec($conSQL, $query) or die ('erro ao inserir');	
	
}


?>
