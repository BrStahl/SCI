<?php
session_name("covre_ti");
session_start();

include("../../SCA/includes/conect_sqlserver.php");

$logado    = $_SESSION["usuario_logado"];

$acidente_id = $_POST["acidente_id"];

if ($logado != '')
{

	$query = "update analise_ppae
			  set status_id = 'p', data_concl_analise = null, user_concl_analise = null
			  Where acidente_id = '$acidente_id'";
	$result = odbc_exec($conSQL, $query) ;
	
	//verifica se o registro foi inativado
	$query = "select status_id
			  from analise_ppae
			  Where acidente_id = '$acidente_id'";
	$result = odbc_exec($conSQL, $query) ;
	$status_id = odbc_result($result,1);

	
	if ($status_id == 'p')
	{
		//retorna o status do acidente para aguardando analise ppae
		$query = "update registro_acidente
				  set status_id = 'r'
				  Where acidente_id = $acidente_id";
		odbc_exec($conSQL, $query);


		//insere no log de alteracao
		$query = "insert into log_alteracao_acidente (acidente_id, data_alteracao, usuario_id, valor_antigo, valor_novo, campo, tela_alteracao)
				  values ($acidente_id, getdate(), (select id from usuario where usuario = '$logado'), 'An&aacute;lise Conclu&iacute;da', 
				  'An&aacute;lise Reaberta', 'Status da An&aacute;lise', 2)";
		//print $query;
		odbc_exec($conSQL, $query);
	}
	
	print $status_id;

}



?>

