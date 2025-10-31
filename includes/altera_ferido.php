<?php
session_name("covre_ti");
session_start();

include("../../SCA/includes/conect_sqlserver.php");

$logado = $_SESSION["usuario_logado"];

if($logado != "")
{
	$valor 	 		= $_POST["valor"];
	$id		 		= $_POST["id"];
	$numero			= $_POST["numero"];
	$acidente_id	= $_POST["acidente_id"];

	//selecionando os dados atuais
	$query = "select case when func_ferido = 's' then 'Sim' else 'N&atilde;o' end, tipo_ferimento, hospital, nome
				from feridos_acidente with (nolock)
				join CARGOSOL..PESSOA with (nolock) on
					pessoa.PESSOA_ID = feridos_acidente.pessoa_id
				where id = $id";
	//print $query;
	$result = odbc_exec($conSQL, $query) ;
	$func_ferido_atual 		= odbc_result($result, 1);	
	$tipo_ferimento_atual	= odbc_result($result, 2);		
	$hospital_atual 		= odbc_result($result, 3);	
	$nome			 		= odbc_result($result, 4);	
	
	$valor_antigo = $nome.' Ferido: '.$func_ferido_atual.', Tipo Ferimento: '.$tipo_ferimento_atual.', Hospital: '.$hospital_atual;

	if($numero == 1)
	{
		$query = "update feridos_acidente
				  set func_ferido = '$valor'
				  where id = $id";
		odbc_exec($conSQL, $query) or die("Erro");	

		
		if ($valor == 's')
			$valor = 'Sim';
		else
			$valor = 'N&atilde;o';
		
		$valor_antigo = $nome.' - Ferido: '.$func_ferido_atual;
		$valor_novo = $nome.' - Ferido: '.$valor;		
	
		if ($valor_antigo != $valor_novo)
		{		
			//alteracao campo tipo registro
			$query = "insert into log_alteracao_acidente (acidente_id, data_alteracao, usuario_id, valor_antigo, valor_novo, campo, tela_alteracao)
					  values ($acidente_id, getdate(), (select id from usuario where usuario = '$logado'), '$valor_antigo','$valor_novo', 
					  'Funcion&aacute;rio Envolvido',2)";
			odbc_exec($conSQL, $query) or die(odbc_errormsg($conSQL)."<br>Erro1 ao inserir na tabela log de alteração<br>");			
		}
	}	
	else	
		if($numero == 2)
		{
			$query = "update feridos_acidente
					  set tipo_ferimento = case when '$valor' = '' then null else '$valor' end
					  where id = $id";
			odbc_exec($conSQL, $query) or die("Erro");	

			$valor_antigo = $nome.' - Tipo Ferimento: '.$tipo_ferimento_atual;
			$valor_novo = $nome.' - Tipo Ferimento: '.$valor;			
			
			if ($valor_antigo != $valor_novo)
			{
				//alteracao campo tipo registro
				$query = "insert into log_alteracao_acidente (acidente_id, data_alteracao, usuario_id, valor_antigo, valor_novo, campo, tela_alteracao)
						  values ($acidente_id, getdate(), (select id from usuario where usuario = '$logado'), '$valor_antigo','$valor_novo', 
						  'Funcion&aacute;rio Envolvido',2)";
				odbc_exec($conSQL, $query) or die(odbc_errormsg($conSQL)."<br>Erro1 ao inserir na tabela log de alteração<br>");			
			}
		}
		else
			if($numero == 3)
			{
			
				$query = "update feridos_acidente
						  set hospital = case when '$valor' = '' then null else '$valor' end
						  where id = $id";
				odbc_exec($conSQL, $query) or die("Erro");	

				$valor_antigo = $nome.' - Hospital: '.$hospital_atual;
				$valor_novo = $nome.' - Hospital: '.$valor;

				if ($valor_antigo != $valor_novo)
				{
					//alteracao campo tipo registro
					$query = "insert into log_alteracao_acidente (acidente_id, data_alteracao, usuario_id, valor_antigo, valor_novo, campo, tela_alteracao)
							  values ($acidente_id, getdate(), (select id from usuario where usuario = '$logado'), '$valor_antigo','$valor_novo', 
							  'Funcion&aacute;rio Envolvido',2)";
					odbc_exec($conSQL, $query) or die(odbc_errormsg($conSQL)."<br>Erro1 ao inserir na tabela log de alteração<br>");					
				}
			}
		
		
}
?>

