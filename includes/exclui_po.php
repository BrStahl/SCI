<?php
session_name("covre_ti");
session_start();

include("../../SCA/includes/conect_sqlserver.php");

$logado = $_SESSION["usuario_logado"];


$id		= $_POST["id"];



if ($logado != '')
{
	$query = "delete
			  from po_tipo_acidente 
			  where id = $id";
	//print $query;
	odbc_exec($conSQL, $query) or die ('erro1 ao deletar');
}
else
	print "Sessao Expirada";


?>
