<?php
session_name("covre_ti");
session_start();

include("../../SCA/includes/conect_sqlserver.php");

$logado = $_SESSION["usuario_logado"];

$id				= $_POST["id"];
$acidente_id	= $_POST["acidente_id"];


if ($logado != '')
{
/*
	//verifica se existe apenas um registro
	$query = "select COUNT(*)
				from despesa_cc_acidente
				where acidente_id = $acidente_id
				and status_id = 'a'";
	//print $query;
	$result = odbc_exec($conSQL, $query) or die ('erro1 ao selecionar o contador');	
	$total_cc = odbc_result($result, 1);
	
	if ($total_cc == 1)
	{
*/
		//inativa o registro
		$query = "update despesa_cc_acidente
				  set status_id = 'i'
				  where id = $id";
		//print $query;
		odbc_exec($conSQL, $query) or die ('erro1 ao inativar');	
		
		//insere na log alteracao caso o registro tenha sido excluido
		$query = "insert into log_alteracao_acidente (acidente_id, data_alteracao, usuario_id, valor_antigo, valor_novo, campo, tela_alteracao)
					select $acidente_id, getdate(), (select id from usuario where usuario = '$logado'), 'Centro de Custo: '+NOME, 'Exclu&iacute;do', 
					'Rateio Despesas', 3
					from despesa_cc_acidente dcca with (nolock)
					join CORPORE..GCCUSTO with (nolock) on
						GCCUSTO.CODREDUZIDO = dcca.centro_custo_id collate SQL_Latin1_General_CP1_CI_AI
					where dcca.id = $id";
		//print $query;
		odbc_exec($conSQL, $query) or die ('erro2 ao atualizar os centros de custos');			
/*
	}
	else
	{
		//inativa o registro
		$query = "update despesa_cc_acidente
				  set status_id = 'p'
				  where id = $id";
		//print $query;
		odbc_exec($conSQL, $query) or die ('erro2 ao inativar');		
	}
*/	
	
}


?>
