<?php
session_name("covre_ti");
session_start();

include("../../SCA/includes/conect_sqlserver.php");

$logado = $_SESSION["usuario_logado"];

$centro_custo_id	= $_POST["cc"];
//$porcentagem		= $_POST["porcentagem"];
$acidente_id		= $_POST["acidente_id"];


if ($logado != '')
{
	$query = "select id
				from usuario with (nolock)
				where usuario = '$logado'";
	//print $query;
	$result = odbc_exec($conSQL, $query) ;
	$usuario_id = odbc_result($result, 1);	
	
	//seleciona a quantidade de registros para definir porcentagem
	$query = "select COUNT(*)
			  from despesa_cc_acidente dcca with (nolock)
			  where acidente_id = $acidente_id
			  and status_id = 'a'";
	//print $query;
	$result = odbc_exec($conSQL, $query) ;
	$total_registro = odbc_result($result, 1);		

	if ($total_registro > 0)
	{
		$query = "insert into despesa_cc_acidente (acidente_id, centro_custo_id, porcentagem, data_gravacao, usuario_id, status_id) 
				  values ($acidente_id, $centro_custo_id, 0, getdate(), $usuario_id, 'a')";
		//print $query;
		odbc_exec($conSQL, $query) or die ('erro1 ao inserir');
		
		$cada_porcento = 100 / ($total_registro + 1);

		/*
		$query = "update despesa_cc_acidente 
				  set porcentagem = '$cada_porcento'
				  where acidente_id = $acidente_id";
		//print $query;
		odbc_exec($conSQL, $query) or die ('erro ao atualizar');		
		*/
	}
	else
	{
		$query = "insert into despesa_cc_acidente (acidente_id, centro_custo_id, porcentagem, data_gravacao, usuario_id, status_id) 
				  values ($acidente_id, $centro_custo_id, 100, getdate(), $usuario_id, 'a')";
		//print $query;
		odbc_exec($conSQL, $query) or die ('erro22 ao inserir');
	}

	
}


?>
