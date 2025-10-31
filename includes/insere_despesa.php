<?php
session_name("covre_ti");
session_start();

include("../../SCA/includes/conect_sqlserver.php");

$logado = $_SESSION["usuario_logado"];

$valor				= $_POST["valor"];
$tipo_acidente_id	= $_POST["tipo_acidente_id"];
$tipo_despesa_id	= $_POST["tipo_despesa_id"];


if ($logado != '')
{

	if ($valor == 'S')
	{

		$query = "insert into acidente_tipo_despesas (tipo_acidente_id, tipo_despesa_id) 
				  values ($tipo_acidente_id, $tipo_despesa_id)";
		//print $query;
		odbc_exec($conSQL, $query) or die ('erro1 ao inserir');


		switch ($tipo_acidente_id) {
			case 1:
				$condicao = "and ra.acidente_transito = 's'";
				break;
			case 2:
				$condicao = "and ra.acidente_trabalho = 's'";
				break;
			case 3:
				$condicao = "and ra.avaria_carga = 's'";
				break;
			case 4:
				$condicao = "and ra.atraso_entrega = 's'";
				break;

		}
	
		/*	
		//verifica se houve novo tipo de acidente (se houve, o sistema irá inserir automaticamente os tipos de despesas nas areas
		$query = "select distinct tipo_despesa_acidente.id tipo_despesa_id, DATEADD(dd, prazo, getdate()) prazo, ra.acidente_id
				from tipo_despesa_acidente with (nolock)
				join acidente_tipo_despesas atd with (nolock) on
					atd.tipo_despesa_id = tipo_despesa_acidente.id
					and atd.tipo_acidente_id in ($tipo_acidente_id)
					and atd.tipo_despesa_id not in (select tipo_despesa_id
													 from lancamento_despesas_acidente lda with (nolock)
													 join registro_acidente ra with (nolock) on
														ra.acidente_id = lda.ACIDENTE_ID
													 where ra.status_id in ('p','r'))
				join despesa_area_acidente daa on
					daa.area_id = tipo_despesa_acidente.area_responsavel_id
				join registro_acidente ra with (nolock) on
					ra.acidente_id = daa.acidente_id
					and ra.status_id in ('p','r')
					$condicao
				where tipo_despesa_acidente.status_id = 'a'";
		//print $query;
		$result = odbc_exec($conSQL, $query);  	
	
		while(odbc_fetch_row($result))
		{
			$tipo_despesa_id 	= odbc_result($result,1);
			$prazo				= odbc_result($result,2);
			$acidente_id		= odbc_result($result,3);			
	
			//inserindo na table lancamento_despesas_acidente
			$query = "insert into lancamento_despesas_acidente (acidente_id, tipo_despesa_id, status_despesa_id, prazo, data_inclusao, 					  status_id) values ($acidente_id, $tipo_despesa_id, 2, '$prazo', getdate(), 'a')";
			//print $query;
			odbc_exec($conSQL, $query) or die ('erro2 ao inserir');	
	
		}
		*/		
		
	}
	else
	{

		$query = "delete
				  from acidente_tipo_despesas 
				  where tipo_acidente_id = $tipo_acidente_id
				  and tipo_despesa_id = $tipo_despesa_id";
		//print $query;
		odbc_exec($conSQL, $query) or die ('erro1 ao excluir');
	}
}
else
	print "Sessão Expirada";


?>
