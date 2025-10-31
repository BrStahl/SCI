<?php
session_name("covre_ti");
session_start();

include("../../SCA/includes/conect_sqlserver.php");

$logado = $_SESSION["usuario_logado"];


$ponto_operacao_id	= $_POST["ponto_operacao_id"];


if ($logado != '')
{

		//inativa o registro
		$query = "delete
				  from po_permissao_qsma_santos
				  where ponto_operacao_id = $ponto_operacao_id";
		//print $query;
		odbc_exec($conSQL, $query) or die ('erro1 ao excluir');	

	
}


?>
