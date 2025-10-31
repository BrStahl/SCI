<?php
session_name("covre_ti");
session_start();

include("../../SCA/includes/conect_sqlserver.php");

$logado = $_SESSION["usuario_logado"];

$id				= $_POST["id"];
$acidente_id	= $_POST["acidente_id"];

if ($logado != '')
{
	//inativa o registro
	$query = "update veiculos_acidente
			  set status_id = 'i'
			  where id = $id";
	//print $query;
	odbc_exec($conSQL, $query) or die ('erro1 ao inativar');
	
	//inativa o registro
	$query = "select PLACA
				from veiculos_acidente VA WITH (NOLOCK)
				JOIN CARGOSOL..VEICULO_FORNECEDOR VF WITH (NOLOCK) ON
					VF.VEICULO_FORNECEDOR_ID = VA.veiculo_fornecedor_id
				WHERE VA.id = $id";
	//print $query;
	$result = odbc_exec($conSQL, $query) or die ('erro ao SELECIONAR A PLACA');
	$placa = odbc_result($result, 1);	
	
	//alteracao campo tipo registro
	$query = "insert into log_alteracao_acidente (acidente_id, data_alteracao, usuario_id, valor_antigo, valor_novo, campo, tela_alteracao)
			  values ($acidente_id, getdate(), (select id from usuario where usuario = '$logado'), '$placa','Exclu&iacute;do', 'Placa do Ve&iacute;culo',1)";
	odbc_exec($conSQL, $query) or die(odbc_errormsg($conSQL)."<br>Erro1 ao inserir na tabela log de alteração<br>");	
	
}	

?>
