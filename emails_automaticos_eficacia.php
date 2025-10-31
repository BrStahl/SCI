<?php
session_name("covre_ti");
session_start();

exit();

require("../SCA/includes/page_func.php");
include("../SCA/includes/conect_sqlserver.php");
require("../SCA/includes/phpmailer/class.phpmailer.php");

$localItem = "../registro_acidentes/emails_automaticos.php";
$logado    = $_SESSION["usuario_logado"];
//$acesso	   = valida_acesso($conSQL, $localItem, $logado);
$acesso = "permitido";

if ($acesso <> "permitido") {
	grava_acesso($conSQL, $localItem, $logado, 2, $vObservacao);

	print "
        <script language = 'JavaScript'>
           alert('Acesso negado para esta p�gina');
           window.location='centro.php';
		</script>
    ";
} //elseif
else {
	grava_acesso($conSQL, $localItem, $logado, 1, $vObservacao);




	//EMAILS PARA QUALIDADE COM DATA DE PREVISAO DE ANALISE A VENCER EM 10 DIAS (QSMA - SANTOS)
	$query = "select distinct registro_acidente.acidente_id, 'STS'
			  from registro_acidente with (nolock)
			  join feridos_acidente feridos with (nolock) on
				feridos.acidente_id = registro_acidente.acidente_id
				and feridos.status_id = 'a'
			  JOIN CARGOSOL..COLABORADOR CO WITH (NOLOCK) ON
				CO.COLABORADOR_ID = feridos.PESSOA_ID
				/*AND CO.PONTO_OPERACAO_ID IN ($pontos_op_qsma_santos)*/
			  left join CARGOSOL..PESSOA with (nolock) on
				((pessoa.nome_fantasia = registro_acidente.cliente_veiculo collate SQL_Latin1_General_CP1_CI_AS) OR
				 (pessoa.Pessoa_Id = co.Pessoa_Id))									
			  left join corpore..PPESSOA with (nolock) on
				PPESSOA.CPF collate SQL_Latin1_General_CP1_CI_AS = PESSOA.pf_cpf
			  LEFT JOIN CORPORE..PFUNC WITH (NOLOCK) ON
				PFUNC.CODPESSOA = PPESSOA.CODIGO
				AND PFUNC.CODCOLIGADA = 1
				AND PFUNC.CODSITUACAO <> 'D'
				AND PFUNC.CODTIPO <> 'A'
			  LEFT JOIN CORPORE..PSECAO WITH (NOLOCK) ON
				PSECAO.CODIGO = PFUNC.CODSECAO
				AND PSECAO.CODCOLIGADA = 1
			  LEFT JOIN CORPORE..PFUNCAO WITH (NOLOCK) ON
				PFUNCAO.CODIGO = PFUNC.CODFUNCAO
				AND PFUNCAO.CODCOLIGADA = 1
			  where 1 = 1	
			  and data_previsao_analise BETWEEN CONVERT(VARCHAR(10), GetDate(), 120) AND CONVERT(VARCHAR(10), GetDate()+10, 120)
			  and registro_acidente.status_id not in ('i','f')
			  and plano_eficaz is null
			  AND (((registro_acidente.classificacao is not null) and (registro_acidente.classificacao <> 'o')))
			  AND CASE WHEN CO.Tab_Tipo_Colaborador_Id = 3
					THEN CASE WHEN CO.TAB_TIPO_VINCULO_ID = 1
								THEN CASE WHEN (SELECT (select top 1 filial_id
														 from motorista_filial
														 where motorista_pessoa_id = dados1.pessoa_id
														 and mes_comp = month(dados1.data)
														 and ano_comp = YEAR(dados1.data)) filial
														 from (select max(data) data, pessoa_id
																from (select motorista_pessoa_id pessoa_id, 
																		filial_id,
																		cast(ano_comp as varchar)+'-'+right(replicate('0',2) + convert(VARCHAR,mes_comp),2)+'-01' data
																		from motorista_filial
																		where 1 = 1
																		and motorista_pessoa_id = PESSOA.PESSOA_ID
																	)dados
																group by dados.pessoa_id
															)dados1) IN (15,81,85,89)
												THEN 'STS'
												ELSE 'COVRE'
									  END
								ELSE CASE WHEN CO.PONTO_OPERACAO_ID IN (15,81,85,89)
											THEN 'STS'
											ELSE 'COVRE'
									 END
						  END
					ELSE CASE WHEN PFUNC.CODFILIAL IN (3,4,5,10,12)
								THEN 'STS'
								ELSE 'COVRE'
						 END
			  END = 'STS'
			  
			  
			  UNION
			  
			  select distinct registro_acidente.acidente_id, 'STS'
			  from registro_acidente with (nolock)
			  left join CARGOSOL..PESSOA with (nolock) on
				pessoa.Pf_Cpf = registro_acidente.cpf_responsavel collate SQL_Latin1_General_CP1_CI_AS									
			  JOIN CARGOSOL..COLABORADOR CO WITH (NOLOCK) ON
				CO.COLABORADOR_ID = PESSOA.PESSOA_ID
			  left join corpore..PPESSOA with (nolock) on
				PPESSOA.CPF collate SQL_Latin1_General_CP1_CI_AS = PESSOA.pf_cpf
			  LEFT JOIN CORPORE..PFUNC WITH (NOLOCK) ON
				PFUNC.CODPESSOA = PPESSOA.CODIGO
				AND PFUNC.CODCOLIGADA = 1
				AND PFUNC.CODSITUACAO <> 'D'
				AND PFUNC.CODTIPO <> 'A'
			  LEFT JOIN CORPORE..PSECAO WITH (NOLOCK) ON
				PSECAO.CODIGO = PFUNC.CODSECAO
				AND PSECAO.CODCOLIGADA = 1
			  LEFT JOIN CORPORE..PFUNCAO WITH (NOLOCK) ON
				PFUNCAO.CODIGO = PFUNC.CODFUNCAO
				AND PFUNCAO.CODCOLIGADA = 1
			  where 1 = 1	
			  and data_previsao_analise BETWEEN CONVERT(VARCHAR(10), GetDate(), 120) AND CONVERT(VARCHAR(10), GetDate()+10, 120)
			  and registro_acidente.status_id not in ('i','f')
			  and plano_eficaz is null
			  AND (((registro_acidente.classificacao is not null) and (registro_acidente.classificacao <> 'o')))
			  AND CASE WHEN CO.Tab_Tipo_Colaborador_Id = 3
					THEN CASE WHEN CO.TAB_TIPO_VINCULO_ID = 1
								THEN CASE WHEN (SELECT (select top 1 filial_id
														 from motorista_filial
														 where motorista_pessoa_id = dados1.pessoa_id
														 and mes_comp = month(dados1.data)
														 and ano_comp = YEAR(dados1.data)) filial
														 from (select max(data) data, pessoa_id
																from (select motorista_pessoa_id pessoa_id, 
																		filial_id,
																		cast(ano_comp as varchar)+'-'+right(replicate('0',2) + convert(VARCHAR,mes_comp),2)+'-01' data
																		from motorista_filial
																		where 1 = 1
																		and motorista_pessoa_id = PESSOA.PESSOA_ID
																	)dados
																group by dados.pessoa_id
															)dados1) IN (15,81,85,89)
												THEN 'STS'
												ELSE 'COVRE'
									  END
								ELSE CASE WHEN CO.PONTO_OPERACAO_ID IN (15,81,85,89)
											THEN 'STS'
											ELSE 'COVRE'
									 END
						  END
					ELSE CASE WHEN PFUNC.CODFILIAL IN (3,4,5,10,12)
								THEN 'STS'
								ELSE 'COVRE'
						 END
			  END = 'STS'";
	//print $query;
	$result = odbc_exec($conSQL, $query);
	$acidentes_qsma_santos = 0;

	while (odbc_fetch_row($result)) {

		$acidente_id	= odbc_result($result, 1);

		$acidentes_qsma_santos .= "," . $acidente_id;


		//enviando email para a qualidade - Santos
		$query1 = "select nome, email
					from permissoes_acidente pa with (nolock)
					join usuario with (nolock) on
						usuario.id = pa.usuario_id
						and usuario.status = 'a'
					where area_qualidade_sts = 'S'
					and usuario.email not in (select email
											  from log_email_acidente with (nolock)
											  where acidente_id = $acidente_id
											  and email = usuario.email
											  and motivo = 'analise_efic_vencer')";
		//print $query1;					
		$result1 = odbc_exec($conSQL, $query1);

		while (odbc_fetch_array($result1)) {
			$nome_destino = odbc_result($result1, 1);
			$email_destino = odbc_result($result1, 2);


			$enviou = enviar_email(
				"helpdesk@covre.com.br",
				"SCI - Registro de Ocorr�ncias",
				"$email_destino",
				"Ocorr�ncia: $acidente_id - An�lise de Efic�cia com prazo � vencer",
				"Email autom&aacute;tico
			<br><br><br>Prezado(a).
			<br><br><br>O prazo para AN�LISE DE EFIC�CIA do plano de a��o da ocorr�ncia n� $acidente_id ir� vencer, favor acessar o registro e atualizar as informa��es.
			<br><br><br><b>SCI - Registro de Ocorr&ecirc;ncias</b>"
			);

			if ($enviou == 1) {
				$query2 = "insert into log_email_acidente (acidente_id, data_envio, motivo, email) 
							values ($acidente_id, getdate(), 'analise_efic_vencer','$email_destino')";
				//print $query2;
				odbc_exec($conSQL, $query2);
			}
		}
	} //fim while



	//EMAILS PARA QUALIDADE COM DATA DE PREVISAO DE ANALISE A VENCER EM 10 DIAS (QSMA)
	$query = "SELECT acidente_id
			  FROM registro_acidente ra with (nolock)
			  WHERE 1 = 1
			  and data_previsao_analise BETWEEN CONVERT(VARCHAR(10), GetDate(), 120) AND CONVERT(VARCHAR(10), GetDate()+10, 120)
			  and status_id not in ('i','f')
			  and plano_eficaz is null
			  and ra.acidente_id not in ($acidentes_qsma_santos)";
	//print $query;
	$result = odbc_exec($conSQL, $query);
	$contador = 1;

	while (odbc_fetch_row($result)) {

		$acidente_id	= odbc_result($result, 1);


		//enviando email para a qualidade (exceto Santos)
		$query1 = "select nome, email
					from permissoes_acidente pa with (nolock)
					join usuario with (nolock) on
						usuario.id = pa.usuario_id
						and usuario.status = 'a'
					where area_qualidade = 'S'
					and usuario.email not in (select email
											  from log_email_acidente with (nolock)
											  where acidente_id = $acidente_id
											  and email = usuario.email
											  and motivo = 'analise_efic_vencer')";
		//print $query1;					
		$result1 = odbc_exec($conSQL, $query1);

		while (odbc_fetch_array($result1)) {
			$nome_destino = odbc_result($result1, 1);
			$email_destino = odbc_result($result1, 2);


			$nome_destino = odbc_result($result1, 1);
			$email_destino = odbc_result($result1, 2);


			$enviou = enviar_email(
				"helpdesk@covre.com.br",
				"SCI - Registro de Ocorr�ncias",
				"$email_destino",
				"Ocorr�ncia: $acidente_id - An�lise de Efic�cia com prazo � vencer",
				"Email autom&aacute;tico
			<br><br><br>Prezado(a).
			<br><br><br>O prazo para AN�LISE DE EFIC�CIA do plano de a��o da ocorr�ncia n� $acidente_id ir� vencer, favor acessar o registro e atualizar as informa��es.
			<br><br><br><b>SCI - Registro de Ocorr&ecirc;ncias</b>"
			);

			if ($enviou == 1) {
				$query2 = "insert into log_email_acidente (acidente_id, data_envio, motivo, email) 
							values ($acidente_id, getdate(), 'analise_efic_vencer','$email_destino')";
				//print $query2;
				odbc_exec($conSQL, $query2);
			}
		}

		$contador++;
	} //fim while





	//EMAILS PARA QUALIDADE COM DATA DE PREVISAO DE ANALISE COM PRAZO VENCIDO (QSMA - SANTOS)
	$query = "select distinct registro_acidente.acidente_id, 'STS'
			  from registro_acidente with (nolock)
			  join feridos_acidente feridos with (nolock) on
				feridos.acidente_id = registro_acidente.acidente_id
				and feridos.status_id = 'a'
			  JOIN CARGOSOL..COLABORADOR CO WITH (NOLOCK) ON
				CO.COLABORADOR_ID = feridos.PESSOA_ID
				/*AND CO.PONTO_OPERACAO_ID IN ($pontos_op_qsma_santos)*/
			  left join CARGOSOL..PESSOA with (nolock) on
				((pessoa.nome_fantasia = registro_acidente.cliente_veiculo collate SQL_Latin1_General_CP1_CI_AS) OR
				 (pessoa.Pessoa_Id = co.Pessoa_Id))									
			  left join corpore..PPESSOA with (nolock) on
				PPESSOA.CPF collate SQL_Latin1_General_CP1_CI_AS = PESSOA.pf_cpf
			  LEFT JOIN CORPORE..PFUNC WITH (NOLOCK) ON
				PFUNC.CODPESSOA = PPESSOA.CODIGO
				AND PFUNC.CODCOLIGADA = 1
				AND PFUNC.CODSITUACAO <> 'D'
				AND PFUNC.CODTIPO <> 'A'
			  LEFT JOIN CORPORE..PSECAO WITH (NOLOCK) ON
				PSECAO.CODIGO = PFUNC.CODSECAO
				AND PSECAO.CODCOLIGADA = 1
			  LEFT JOIN CORPORE..PFUNCAO WITH (NOLOCK) ON
				PFUNCAO.CODIGO = PFUNC.CODFUNCAO
				AND PFUNCAO.CODCOLIGADA = 1
			  where 1 = 1	
			  and data_previsao_analise < CONVERT(VARCHAR(10), GetDate(), 120)
			  and registro_acidente.status_id not in ('i','f')
			  and registro_acidente.plano_eficaz is null
			  AND (((registro_acidente.classificacao is not null) and (registro_acidente.classificacao <> 'o')))
			  AND CASE WHEN CO.Tab_Tipo_Colaborador_Id = 3
					THEN CASE WHEN CO.TAB_TIPO_VINCULO_ID = 1
								THEN CASE WHEN (SELECT (select top 1 filial_id
														 from motorista_filial
														 where motorista_pessoa_id = dados1.pessoa_id
														 and mes_comp = month(dados1.data)
														 and ano_comp = YEAR(dados1.data)) filial
														from (select max(data) data, pessoa_id
																from (select motorista_pessoa_id pessoa_id, 
																		filial_id,
																		cast(ano_comp as varchar)+'-'+right(replicate('0',2) + convert(VARCHAR,mes_comp),2)+'-01' data
																		from motorista_filial
																		where 1 = 1
																		and motorista_pessoa_id = PESSOA.PESSOA_ID
																	)dados
																group by dados.pessoa_id
															)dados1) IN (15,81,85,89)
												THEN 'STS'
												ELSE 'COVRE'
									  END
								ELSE CASE WHEN CO.PONTO_OPERACAO_ID IN (15,81,85,89)
											THEN 'STS'
											ELSE 'COVRE'
									 END
						  END
					ELSE CASE WHEN PFUNC.CODFILIAL IN (3,4,5,10,12)
								THEN 'STS'
								ELSE 'COVRE'
						 END
			  END = 'STS'
			  
			  
			  UNION
			  
			  select distinct registro_acidente.acidente_id, 'STS'
			  from registro_acidente with (nolock)
			  left join CARGOSOL..PESSOA with (nolock) on
				pessoa.Pf_Cpf = registro_acidente.cpf_responsavel collate SQL_Latin1_General_CP1_CI_AS									
			  JOIN CARGOSOL..COLABORADOR CO WITH (NOLOCK) ON
				CO.COLABORADOR_ID = PESSOA.PESSOA_ID
			  left join corpore..PPESSOA with (nolock) on
				PPESSOA.CPF collate SQL_Latin1_General_CP1_CI_AS = PESSOA.pf_cpf
			  LEFT JOIN CORPORE..PFUNC WITH (NOLOCK) ON
				PFUNC.CODPESSOA = PPESSOA.CODIGO
				AND PFUNC.CODCOLIGADA = 1
				AND PFUNC.CODSITUACAO <> 'D'
				AND PFUNC.CODTIPO <> 'A'
			  LEFT JOIN CORPORE..PSECAO WITH (NOLOCK) ON
				PSECAO.CODIGO = PFUNC.CODSECAO
				AND PSECAO.CODCOLIGADA = 1
			  LEFT JOIN CORPORE..PFUNCAO WITH (NOLOCK) ON
				PFUNCAO.CODIGO = PFUNC.CODFUNCAO
				AND PFUNCAO.CODCOLIGADA = 1
			  where 1 = 1	
			  and data_previsao_analise < CONVERT(VARCHAR(10), GetDate(), 120)
			  and registro_acidente.status_id not in ('i','f')
			  and registro_acidente.plano_eficaz is null
			  AND (((registro_acidente.classificacao is not null) and (registro_acidente.classificacao <> 'o')))
			  AND CASE WHEN CO.Tab_Tipo_Colaborador_Id = 3
					THEN CASE WHEN CO.TAB_TIPO_VINCULO_ID = 1
								THEN CASE WHEN (SELECT (select top 1 filial_id
														 from motorista_filial
														 where motorista_pessoa_id = dados1.pessoa_id
														 and mes_comp = month(dados1.data)
														 and ano_comp = YEAR(dados1.data)) filial
														 from (select max(data) data, pessoa_id
																from (select motorista_pessoa_id pessoa_id, 
																		filial_id,
																		cast(ano_comp as varchar)+'-'+right(replicate('0',2) + convert(VARCHAR,mes_comp),2)+'-01' data
																		from motorista_filial
																		where 1 = 1
																		and motorista_pessoa_id = PESSOA.PESSOA_ID
																	)dados
																group by dados.pessoa_id
															)dados1) IN (15,81,85,89)
												THEN 'STS'
												ELSE 'COVRE'
									  END
								ELSE CASE WHEN CO.PONTO_OPERACAO_ID IN (15,81,85,89)
											THEN 'STS'
											ELSE 'COVRE'
									 END
						  END
					ELSE CASE WHEN PFUNC.CODFILIAL IN (3,4,5,10,12)
								THEN 'STS'
								ELSE 'COVRE'
						 END
			  END = 'STS'";
	print "<br>" . $query;
	$result = odbc_exec($conSQL, $query);
	$acidentes_qsma_santos = 0;

	while (odbc_fetch_row($result)) {

		$acidente_id	= odbc_result($result, 1);

		$acidentes_qsma_santos .= "," . $acidente_id;


		//enviando email para a qualidade - Santos
		$query1 = "select nome, email
					from permissoes_acidente pa with (nolock)
					join usuario with (nolock) on
						usuario.id = pa.usuario_id
						and usuario.status = 'a'
					where area_qualidade_sts = 'S'";
		print "<br>" . $query1;
		$result1 = odbc_exec($conSQL, $query1);

		while (odbc_fetch_array($result1)) {
			$nome_destino = odbc_result($result1, 1);
			$email_destino = odbc_result($result1, 2);


			$enviou = enviar_email(
				"helpdesk@covre.com.br",
				"SCI - Registro de Ocorr�ncias",
				"$email_destino",
				"Ocorr�ncia: $acidente_id - An�lise de Efic�cia com prazo VENCIDO",
				"Email autom&aacute;tico
			<br><br><br>Prezado(a).
			<br><br><br>O prazo para AN�LISE DE EFIC�CIA do plano de a��o da ocorr�ncia n� $acidente_id venceu, favor acessar o registro e atualizar as informa��es.
			<br><br><br><b>SCI - Registro de Ocorr&ecirc;ncias</b>"
			);

			if ($enviou == 1) {
				$query2 = "insert into log_email_acidente (acidente_id, data_envio, motivo, email) 
							values ($acidente_id, getdate(), 'analise_efic_vencido','$email_destino')";
				//print $query2;
				odbc_exec($conSQL, $query2);
			}
		}
	} //fim while



	//EMAILS PARA QUALIDADE COM DATA DE PREVISAO DE ANALISE COM PRAZO VENCIDO (QSMA)
	$query = "SELECT acidente_id
			  FROM registro_acidente ra with (nolock)
			  WHERE 1 = 1
			  and data_previsao_analise < CONVERT(VARCHAR(10), GetDate(), 120)
			  and status_id not in ('i','f')
			  and plano_eficaz is null
			  and ra.acidente_id not in ($acidentes_qsma_santos)";
	//print $query;
	$result = odbc_exec($conSQL, $query);
	$contador = 1;

	while (odbc_fetch_row($result)) {

		$acidente_id	= odbc_result($result, 1);


		//enviando email para a qualidade (Exceto Santos)
		$query1 = "select nome, email
					from permissoes_acidente pa with (nolock)
					join usuario with (nolock) on
						usuario.id = pa.usuario_id
						and usuario.status = 'a'
					where area_qualidade = 'S'";
		//print $query1;					
		$result1 = odbc_exec($conSQL, $query1);

		while (odbc_fetch_array($result1)) {
			$nome_destino = odbc_result($result1, 1);
			$email_destino = odbc_result($result1, 2);


			$enviou = enviar_email(
				"helpdesk@covre.com.br",
				"SCI - Registro de Ocorr�ncias",
				"$email_destino",
				"Ocorr�ncia: $acidente_id - An�lise de Efic�cia com prazo VENCIDO",
				"Email autom&aacute;tico
			<br><br><br>Prezado(a).
			<br><br><br>O prazo para AN�LISE DE EFIC�CIA do plano de a��o da ocorr�ncia n� $acidente_id venceu, favor acessar o registro e atualizar as informa��es.
			<br><br><br><b>SCI - Registro de Ocorr&ecirc;ncias</b>"
			);

			if ($enviou == 1) {
				$query2 = "insert into log_email_acidente (acidente_id, data_envio, motivo, email) 
							values ($acidente_id, getdate(), 'analise_efic_vencido','$email_destino')";
				//print $query2;
				odbc_exec($conSQL, $query2);
			}
		}

		$contador++;
	} //fim while





} //else
