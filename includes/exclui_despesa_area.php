<?php
session_name("covre_ti");
session_start();

include("../../SCA/includes/conect_sqlserver.php");

$logado = $_SESSION["usuario_logado"];

$id				= $_POST["id"];
$acidente_id	= $_POST["acidente_id"];
$area_id		= $_POST["area_id"];

if ($logado != '')
{

	//verifica se a area ja tem valor de despesa lancada
	$query = "select top 1 lancamento_alterado
				from lancamento_despesas_acidente lda with (nolock)
				join tipo_despesa_acidente tda with (nolock) on
					tda.id = lda.tipo_despesa_id
				where lda.acidente_id = $acidente_id
				and lda.status_id = 'a'
				and lda.AREA_ID = $area_id
				order by lancamento_alterado desc";
	//print $query;
	$result = odbc_exec($conSQL, $query) or die ('erro ao selecionar');
	$lancamento_alterado = odbc_result($result, 1);

	if ($lancamento_alterado == 's')
		print "bloqueado";
	else
	{

		//deleta os lancamentos sem valores
		$query = "update lancamento_despesas_acidente
					set status_id = 'i'
					from lancamento_despesas_acidente lda with (nolock)
					join tipo_despesa_acidente tda with (nolock) on
						tda.id = lda.tipo_despesa_id
					where lda.acidente_id = $acidente_id
					and lda.AREA_ID = $area_id";
		//print $query;
		odbc_exec($conSQL, $query) or die ('erro ao deletar os lancamentos');


		//inativa o registro
		$query = "update despesa_area_acidente
				  set status_id = 'i'
				  where id = $id";
		//print $query;
		odbc_exec($conSQL, $query) or die ('erro1 ao inativar');
		
		//seleciona o nome da área
		$query = "select '&Aacute;rea: '+area 
				  From despesa_area_acidente daa with (nolock)
				  join area_responsavel_acidente ara with (nolock) on
					  ara.id = daa.area_id
				  where daa.id = $id";
		//print $query;
		$result = odbc_exec($conSQL, $query) or die ('erro ao selecionar');
		$area = odbc_result($result, 1);
	
	
		//alteracao campo tipo registro
		$query = "insert into log_alteracao_acidente (acidente_id, data_alteracao, usuario_id, valor_antigo, valor_novo, campo, tela_alteracao)
				 values ($acidente_id, getdate(), (select id from usuario where usuario = '$logado'), '$area','Exclu&iacute;do', 
				 'Respons&aacute;vel Despesa', 3)";
		//print $query;
		odbc_exec($conSQL, $query) or die(odbc_errormsg($conSQL)."<br>Erro1 ao inserir na tabela log de alteração<br>");
		
		print "ok";
	}
}


?>
