<?php
session_name("covre_ti");
session_start();

include("../../SCA/includes/conect_sqlserver.php");

$logado    = $_SESSION["usuario_logado"];

$acidente_id = $_POST["acidente_id"];

if ($logado != '')
{
	//seleciona o usuario
	$query = "select id
			  from usuario
			  Where usuario = '$logado'";
	$result = odbc_exec($conSQL, $query) ;
	$usuario_id = odbc_result($result,1);


	$query = "update registro_acidente
			  set status_id = 'f', data_conclusao = getdate(), user_conclusao = $usuario_id
			  Where acidente_id = $acidente_id";
	odbc_exec($conSQL, $query);
	
	//verifica se o registro foi inativado
	$query = "select status_id
			  from registro_acidente
			  Where acidente_id = $acidente_id";
	$result = odbc_exec($conSQL, $query) ;
	$status_id = odbc_result($result,1);

	if ($status_id == 'f')
	{
		//insere no log de alteracao
		$query = "insert into log_alteracao_acidente (acidente_id, data_alteracao, usuario_id, valor_antigo, valor_novo, campo, tela_alteracao)
				  values ($acidente_id, getdate(), $usuario_id, 'Acidente em Aberto', 'Acidente Conclu&iacute;do', 'Status do Acidente', 3)";
		//print $query;
		odbc_exec($conSQL, $query);
	}
	
	print $status_id;

}



?>

