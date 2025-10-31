<?php
session_name("covre_ti");
session_start();

include("../../SCA/includes/conect_sqlserver.php");

$logado    = $_SESSION["usuario_logado"];

$acidente_id = $_POST["acidente_id"];

if ($logado != '')
{

	$query = "update registro_acidente
			  set status_id = 'p', data_conclusao = null, user_conclusao = null
			  Where acidente_id = '$acidente_id'";
	$result = odbc_exec($conSQL, $query) ;
	
	//verifica se o registro foi reativado
	$query = "select status_id
			  from registro_acidente
			  Where acidente_id = '$acidente_id'";
	$result = odbc_exec($conSQL, $query) ;
	$status_id = odbc_result($result,1);

	
	if ($status_id == 'p')
	{
		//insere no log de alteracao
		$query = "insert into log_alteracao_acidente (acidente_id, data_alteracao, usuario_id, valor_antigo, valor_novo, campo, tela_alteracao)
				  values ($acidente_id, getdate(), (select id from usuario where usuario = '$logado'), 'Acidente Conclu&iacute;do', 
				  'Acidente Reaberto', 'Status do Acidente', 3)";
		//print $query;
		odbc_exec($conSQL, $query);
	}
	
	print $status_id;

}



?>

