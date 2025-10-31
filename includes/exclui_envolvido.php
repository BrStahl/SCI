<?php
session_name("covre_ti");
session_start();

include("../../SCA/includes/conect_sqlserver.php");

$logado = $_SESSION["usuario_logado"];

$id				= $_POST["id"];
$acidente_id	= $_POST["acidente_id"];
$tela			= $_POST["tela"];
$tipo_func_env	= $_POST["tipo_func_env"];


if ($logado != '')
{
	if ($tipo_func_env == 1)
	{
		//inativa o registro
		$query = "update feridos_acidente
				  set status_id = 'i'
				  where id = $id";
		//print $query;
		odbc_exec($conSQL, $query) or die ('erro1 ao inativar');
	}
	else
	{
		//inativa o registro
		$query = "update feridos_acidente_terceiro
				  set status_id = 'i'
				  where id = $id";
		//print $query;
		odbc_exec($conSQL, $query) or die ('erro2 ao inativar');
	}	
	
	//alteracao campo tipo registro
	$query = "insert into log_alteracao_acidente (acidente_id, data_alteracao, usuario_id, valor_antigo, valor_novo, campo, tela_alteracao)
			  values ($acidente_id, getdate(), (select id from usuario where usuario = '$logado'), '$id','Exclu&iacute;do', 'Funcion&aacute;rio Envolvido', 
			  $tela)";
	odbc_exec($conSQL, $query) or die(odbc_errormsg($conSQL)."<br>Erro1 ao inserir na tabela log de alteração<br>");	
	
}

?>
