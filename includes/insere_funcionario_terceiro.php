<?php
session_name("covre_ti");
session_start();

include("../../SCA/includes/conect_sqlserver.php");

$logado = $_SESSION["usuario_logado"];

$codpessoa		= $_POST["codpessoa"];
$tipo_envolvido	= $_POST["tipo_envolvido"];
$func_ferido	= $_POST["func_ferido"];
$acidente_id	= $_POST["acidente_id"];
$tipo_ferimento	= $_POST["tipo"];
$hospital		= $_POST["hospital"];
$cpf			= $_POST["cpf"];


if ($acidente_id != '')
{

	//verifica se a pessoa já está inserida no acidente
	$query = "select codpessoa
			  from feridos_acidente_terceiro		
			  where codpessoa = $codpessoa
			  and status_id = 'a'
			  and acidente_id = $acidente_id";
	//print $query;
	$result = odbc_exec($conSQL, $query) ;
	$pessoa_inserida = odbc_result($result, 1);

	if ($pessoa_inserida == '')
	{
	
		$query = "insert into feridos_acidente_terceiro (codpessoa, acidente_id, tipo_envolvido_id, func_ferido, tipo_ferimento, 
				  hospital, status_id) values ($codpessoa, $acidente_id, $tipo_envolvido, '$func_ferido', 
				  case when '$tipo_ferimento' = '' then null else '$tipo_ferimento' end, 
				  case when '$hospital' = '' then null else '$hospital' end, 'a')";
		//print $query;
		odbc_exec($conSQL, $query) or die ('erro1 ao inserir');
		
		print "ok";
	}
	else
		print "Funcionário Já inserido";	
		

}




?>
