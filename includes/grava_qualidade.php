<?php
session_name("covre_ti");
session_start();

include("../../SCA/includes/conect_sqlserver.php");

$logado    = $_SESSION["usuario_logado"];

$acidente_id = $_POST["acidente_id"];

if ($logado != '')
{


	$query = "select dcca.id, CODREDUZIDO, NOME collate sql_latin1_general_cp1251_ci_as, porcentagem
				from despesa_cc_acidente dcca with (nolock)
				join CORPORE..GCCUSTO with (nolock) on
					GCCUSTO.CODREDUZIDO = dcca.centro_custo_id collate SQL_Latin1_General_CP1_CI_AI
				where acidente_id = $acidente_id
				and dcca.status_id = 'a'";
	//print $query;
	$result = odbc_exec($conSQL, $query);    
	
	$conta_porcento = 0;
	  
	while(odbc_fetch_row($result))
	{
		   $cc_id_p		 		= odbc_result($result,1);
		   $cod_reduzido_p		= odbc_result($result,2);
		   $centro_custo_p		= odbc_result($result,3);
		   $porcentagem_p		= odbc_result($result,4);
	
		   $porcentagem			= $_POST["porcentagem$cc_id_p"];
	
		   $conta_porcento = $conta_porcento + $porcentagem;
	
	}

	if ($conta_porcento == 100)
	{
		$query = "select dcca.id, CODREDUZIDO, NOME collate sql_latin1_general_cp1251_ci_as, porcentagem
					from despesa_cc_acidente dcca with (nolock)
					join CORPORE..GCCUSTO with (nolock) on
						GCCUSTO.CODREDUZIDO = dcca.centro_custo_id collate SQL_Latin1_General_CP1_CI_AI
					where acidente_id = $acidente_id
					and dcca.status_id = 'a'";
		//print $query;
		$result = odbc_exec($conSQL, $query);    
		  
		while(odbc_fetch_row($result))
		{
			   $cc_id_p		 		= odbc_result($result,1);
			   $cod_reduzido_p		= odbc_result($result,2);
			   $centro_custo_p		= odbc_result($result,3);
			   $porcentagem_p		= odbc_result($result,4);
		
			   $porcentagem			= $_POST["porcentagem$cc_id_p"];
		
		   		//atualiza os centros de custos
				$query = "update despesa_cc_acidente 
						  set porcentagem = '$porcentagem'
						  where id = $cc_id_p";
				//print $query;
				odbc_exec($conSQL, $query) or die ('erro1 ao atualizar os centros de custos');		
				
				if ($porcentagem_p != $porcentagem)
				{
					//alteracao campo porcentagem
					$query1 = "insert into log_alteracao_acidente (acidente_id, data_alteracao, usuario_id, valor_antigo, valor_novo, campo, tela_alteracao)
							  values ($acidente_id, getdate(), (select id from usuario where usuario = '$logado'), '$porcentagem_p'+' %', '$porcentagem'+' %', 
							  '% do Centro de Custo ($centro_custo_p)', '3')";
					//print $query1;
					odbc_exec($conSQL, $query1) or die(odbc_errormsg($conSQL)."<br>Erro1 ao inserir na tabela log de alteração<br>");				
				}					  
		}	   

			//insere na log alteracao caso o registro tenha sido excluido
			$query = "insert into log_alteracao_acidente (acidente_id, data_alteracao, usuario_id, valor_antigo, valor_novo, campo, tela_alteracao)
						select $acidente_id, getdate(), (select id from usuario where usuario = '$logado'), 'Centro de Custo: '+NOME, 'Exclu&iacute;do', 
						'Rateio Despesas', 3
						from despesa_cc_acidente dcca with (nolock)
						join CORPORE..GCCUSTO with (nolock) on
							GCCUSTO.CODREDUZIDO = dcca.centro_custo_id collate SQL_Latin1_General_CP1_CI_AI
						where acidente_id = $acidente_id
						and status_id = 'p'";
			//print $query;
			odbc_exec($conSQL, $query) or die ('erro2 ao atualizar os centros de custos');	   


			//atualiza os centros de custos pendentes
			$query = "update despesa_cc_acidente 
					  set status_id = 'i'
					  where acidente_id = $acidente_id
					  and status_id = 'p'";
			//print $query;
			odbc_exec($conSQL, $query) or die ('erro2 ao atualizar os centros de custos');	   

	   		print "ok";
	}
	else
		if ($cc_id_p != '')
		{
			print "erro";	
			
			//atualiza os centros de custos pendentes
			$query = "update despesa_cc_acidente 
					  set status_id = 'a'
					  where acidente_id = $acidente_id
					  and status_id = 'p'";
			//print $query;
			odbc_exec($conSQL, $query) or die ('erro3 ao atualizar os centros de custos');			
			
		}
	


}



?>

