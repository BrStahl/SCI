<?php
session_name("covre_ti");
session_start();

require("../SCA/includes/page_func.php");
include("../SCA/includes/conect_sqlserver.php");
require("../SCA/includes/phpmailer/class.phpmailer.php");
require_once "class/EnvolvidoSts.php";



$envolvidos_sts = new EnvolvidoSts;

$ip = $_SERVER['HTTP_HOST'];

$localItem = "../registro_acidentes/emails_automaticos.php";
$logado    = $_SESSION["usuario_logado"];
//$acesso	   = valida_acesso($conSQL, $localItem, $logado);
$acesso = "permitido";

if ($acesso <> "permitido") {
	grava_acesso($conSQL, $localItem, $logado, 2, $vObservacao);

	print "
        <script language = 'JavaScript'>
           alert('Acesso negado para esta página');
           window.location='centro.php';
		</script>
    ";
} //elseif
else {
	grava_acesso($conSQL, $localItem, $logado, 1, $vObservacao);

	//comentario para teste 


	//EMAILS PARA RESPONSAVEL DA AREA COM LANCAMENTOS DE DESPESAS PENDENTES (toda segunda-feira)
	$query = "select distinct usuario.email, lda.ACIDENTE_ID
	from LANCAMENTO_DESPESAS_ACIDENTE lda with (nolock)
	join tipo_despesa_acidente tda with (nolock) on
		tda.id = lda.TIPO_DESPESA_ID
	join area_responsavel_acidente ara with (nolock) on
		ara.id = lda.AREA_ID
	join usuario with (nolock) on
		(usuario.id = ara.responsavel_id) or (usuario.id = ara.sub_1_id) or (usuario.id = ara.sub_2_id)
		and usuario.status = 'a'
	left join veiculos_acidente VA with (nolock) on
		VA.acidente_id = lda.ACIDENTE_ID
		and VA.status_id = 'a'
	join registro_acidente ra with (nolock) on
		ra.acidente_id = lda.ACIDENTE_ID
		and ra.status_id NOT IN ('i','f')								
	where STATUS_DESPESA_ID = 2
	and (DATEPART(DW,getdate())) = 2
	and lda.STATUS_ID = 'a'
	
	UNION
	
	select distinct usuario.email, lda.ACIDENTE_ID
	from LANCAMENTO_DESPESAS_ACIDENTE lda with (nolock)
	join tipo_despesa_acidente tda with (nolock) on
		tda.id = lda.TIPO_DESPESA_ID
	join area_responsavel_acidente ara with (nolock) on
		ara.id = lda.AREA_ID
	join permissoes_acidente pa with (nolock) on
		pa.area_id_leitura = ara.id
	join usuario with (nolock) on
		usuario.id = pa.usuario_id
		and usuario.status = 'a'
	left join veiculos_acidente VA with (nolock) on
		VA.acidente_id = lda.ACIDENTE_ID
		and VA.status_id = 'a'
	join registro_acidente ra with (nolock) on
		ra.acidente_id = lda.ACIDENTE_ID
		and ra.status_id NOT IN ('i','f')								
	where STATUS_DESPESA_ID = 2
	and (DATEPART(DW,getdate())) = 2
	and lda.STATUS_ID = 'a'";
	//print $query;
	$result = odbc_exec($conSQL, $query);

	$emails = array();
	while (odbc_fetch_row($result)) {

		$email	= odbc_result($result, 1);
		$id = odbc_result($result, 2);

		if (!isset($emails[$email])) {
			$emails[$email] = array();
		}

		//BUSCA AS PLACAS DOS REGISTROS
		$query1 = "select vf.placa
					from veiculos_acidente va with (nolock)
					join CARGOSOL..VEICULO_FORNECEDOR VF with (nolock) on
						VF.VEICULO_FORNECEDOR_ID = VA.veiculo_fornecedor_id		
					where acidente_id = $id
					and va.status_id = 'a'";
		//print $query1;
		$result1 = odbc_exec($conSQL, $query1);

		$placas = '';
		$conta_placa = 1;
		while (odbc_fetch_row($result1)) {
			$placa			= odbc_result($result1, 1);

			if ($conta_placa == 1)
				$placas = $placa;
			else
				$placas .= ', ' . $placa;

			$conta_placa++;
		}


		$emails[$email][$id] = $placas;
	} //fim while

	foreach ($emails as $email => $ids) {

		$html = '';
		//print_r($ids);
		foreach ($ids as $id => $placas) {
			$html .= $id . ' - Placas: ' . $placas . '</br>';
		}
		$enviou = enviar_email(
			"helpdesk@covre.com.br",
			"SCI - Registro de Ocorrências",
			$email,
			"Acidente: $acidente_id - Despesas Pendentes",
			"Email automático
		<br><br>O(s) acidente(s) abaixo, porruem despesas pendentes sobre sua responsabilidade:
		<br><br>$html
		<br><br>Favor verificar.
		<br><br><b>SCI - Registro de Ocorrências</b>",
			null,
			null,
			true
		);

		if ($enviou == 1) {

			foreach ($ids as $id => $placas) {
				$query2 = "insert into log_email_acidente (acidente_id, data_envio, motivo, email) 
						values ($id, getdate(), 'despesas pendentes','$email')";
				//print $query2;
				odbc_exec($conSQL, $query2);
			}
		}
	}

	//exit();


	//EMAILS PARA RESPONSAVEL DE ATIVIDADE DO PLANO DE ACAO COM PRAZO A VENCER EM 10 DIAS
	$query = "SELECT distinct usuario.email, num_ocorrencia
				from itens_rms_processos with (nolock)
				join rms_processos with (nolock) on
					rms_processos.rms_processos_id = itens_rms_processos.rms_processos_id
					and rms_processos.status = 'a'
				join registro_acidente ra with (nolock) on
					ra.acidente_id = rms_processos.num_ocorrencia
					--and ra.analise_rms is not null
				join responsavel_atividade responsavel with (nolock) on
					responsavel.item_id = itens_rms_processos.item_id
					and responsavel.status_id = 'a'
				join usuario with (nolock) on
					usuario.pessoa_id = responsavel.pessoa_id
					and usuario.status = 'a'
				WHERE rms_processos.num_ocorrencia is not null
				and itens_rms_processos.status = 'a'
				and rms_processos.status = 'a'
				and itens_rms_processos.status_id <> '3'
				and ISNULL(itens_rms_processos.PRAZO_NOVO,itens_rms_processos.PRAZO) BETWEEN CONVERT(VARCHAR(10), GetDate(), 120) AND CONVERT(VARCHAR(10), GetDate()+10, 120)
				and usuario.email not in (select email
										  from log_email_acidente with (nolock)
										  where acidente_id = num_ocorrencia
										  and email = usuario.email
										  and motivo = 'plano_acao_vencer')		

				UNION

				SELECT distinct usuario.email, num_ocorrencia
				from itens_rms_processos with (nolock)
				join rms_processos with (nolock) on
					rms_processos.rms_processos_id = itens_rms_processos.rms_processos_id
				join registro_acidente ra with (nolock) on
					ra.acidente_id = rms_processos.num_ocorrencia
					--and ra.analise_rms is not null
				join responsavel_atividade responsavel with (nolock) on
					responsavel.item_id = itens_rms_processos.item_id
					and responsavel.status_id = 'a'
				join usuario with (nolock) on
					usuario.id = responsavel.user_gravacao
				WHERE rms_processos.num_ocorrencia is not null
				and responsavel.pessoa_id not in (select pessoa_id	
												  from usuario with (nolock)
												  where status = 'a')
				and itens_rms_processos.status = 'a'
				and rms_processos.status = 'a'
				and itens_rms_processos.status_id <> '3'
				and ISNULL(itens_rms_processos.PRAZO_NOVO,itens_rms_processos.PRAZO) BETWEEN CONVERT(VARCHAR(10), GetDate(), 120) AND CONVERT(VARCHAR(10), GetDate()+10, 120)
				and usuario.email not in (select email
											from log_email_plano_acao with (nolock)
											where plano_id = rms_processos.rms_processos_id
											and email = usuario.email
											and motivo = 'plano_acao_vencer')	
				order by email										  		
				";
	//print $query;
	$result = odbc_exec($conSQL, $query);
	$emails = array();
	while (odbc_fetch_row($result)) {

		$email	= odbc_result($result, 1);
		$id = odbc_result($result, 2);

		if (!isset($emails[$email])) {
			$emails[$email] = array();
		}

		$emails[$email][] = $id;
	} //fim while


	foreach ($emails as $email => $ids) {


		$enviou = enviar_email(
			"helpdesk@covre.com.br",
			"SCI - Registro de Ocorrências",
			$email,
			"Ocorrências com plano de Ação com prazo à vencer",
			"Email automático
		<br><br><br>Prezado(a).
		<br><br><br>Na(s) ocorrência(s) " . implode(", ", $ids) . " constam planos de ação de sua responsabilidade com prazo(s) à vencer, favor acessar o(s) registro(s) e atualizar as informações.
		<br><br><br><b>SCI - Registro de Ocorrências</b>",
			null,
			null,
			true
		);

		if ($enviou == 1) {

			foreach ($ids as $id) {
				$query2 = "insert into log_email_acidente (acidente_id, data_envio, motivo, email) 
						values ($id, getdate(), 'plano_acao_vencer','$email')";
				//print $query2;
				odbc_exec($conSQL, $query2);
			}
		}
	}



	//EMAILS PARA RESPONSAVEL DE ATIVIDADE DO PLANO DE ACAO COM PRAZO VENCIDO
	$query = "SELECT distinct usuario.email, num_ocorrencia, usuario.nome
				from itens_rms_processos with (nolock)
				join rms_processos with (nolock) on
					rms_processos.rms_processos_id = itens_rms_processos.rms_processos_id
					and rms_processos.status = 'a'
				join registro_acidente ra with (nolock) on
					ra.acidente_id = rms_processos.num_ocorrencia
					--and ra.analise_rms is not null
				join responsavel_atividade responsavel with (nolock) on
					responsavel.item_id = itens_rms_processos.item_id
					and responsavel.status_id = 'a'
				join usuario with (nolock) on
					usuario.pessoa_id = responsavel.pessoa_id
					and usuario.status = 'a'
				WHERE rms_processos.num_ocorrencia is not null
				and itens_rms_processos.status = 'a'
				and rms_processos.status = 'a'
				and itens_rms_processos.status_id <> '3'
				and ISNULL(itens_rms_processos.PRAZO_NOVO,itens_rms_processos.PRAZO) < CONVERT(VARCHAR(10), GetDate(), 120)
				
				UNION
				
				SELECT distinct usuario.email, num_ocorrencia, usuario.nome
				from itens_rms_processos with (nolock)
				join rms_processos with (nolock) on
					rms_processos.rms_processos_id = itens_rms_processos.rms_processos_id
				join registro_acidente ra with (nolock) on
					ra.acidente_id = rms_processos.num_ocorrencia
					--and ra.analise_rms is not null
				join responsavel_atividade responsavel with (nolock) on
					responsavel.item_id = itens_rms_processos.item_id
					and responsavel.status_id = 'a'
				join usuario with (nolock) on
					usuario.pessoa_id = responsavel.pessoa_id
					and usuario.status = 'a'
				join funcionario_equipe_monitor fem with (nolock) on
					fem.chapa = usuario.registro
					and fem.status_id = 'a'
				join gestor_monitor gm with (nolock) on
					gm.id = fem.gestor_id
				join usuario gestor with (nolock) on
					gestor.pessoa_id = gm.pessoa_id
					and gm.status_id = 'a'
				WHERE rms_processos.num_ocorrencia is not null
				and itens_rms_processos.status = 'a'
				and rms_processos.status = 'a'
				and itens_rms_processos.status_id <> '3'
				and ISNULL(itens_rms_processos.PRAZO_NOVO,itens_rms_processos.PRAZO) < CONVERT(VARCHAR(10), GetDate(), 120)				
				
				UNION

				SELECT usuario.email, num_ocorrencia, usuario.nome
				from itens_rms_processos with (nolock)
				join rms_processos with (nolock) on
					rms_processos.rms_processos_id = itens_rms_processos.rms_processos_id
				join registro_acidente ra with (nolock) on
					ra.acidente_id = rms_processos.num_ocorrencia
					--and ra.analise_rms is not null
				join responsavel_atividade responsavel with (nolock) on
					responsavel.item_id = itens_rms_processos.item_id
					and responsavel.status_id = 'a'
				join usuario with (nolock) on
					usuario.id = responsavel.user_gravacao
				WHERE rms_processos.num_ocorrencia is not null
				and responsavel.pessoa_id not in (select pessoa_id	
												  from usuario with (nolock)
												  where status = 'a')
				and itens_rms_processos.status = 'a'
				and rms_processos.status = 'a'
				and itens_rms_processos.status_id <> '3'
				and ISNULL(itens_rms_processos.PRAZO_NOVO,itens_rms_processos.PRAZO) < CONVERT(VARCHAR(10), GetDate(), 120)	
				order by email";
	//print $query;
	$result = odbc_exec($conSQL, $query);

	$emails = array();
	while (odbc_fetch_row($result)) {

		$email	= odbc_result($result, 1);
		$id = odbc_result($result, 2);

		if (!isset($emails[$email])) {
			$emails[$email] = array();
		}

		$emails[$email][] = $id;
	} //fim while

	foreach ($emails as $email => $ids) {


		$enviou = enviar_email(
			"helpdesk@covre.com.br",
			"SCI - Registro de Ocorrências",
			$email,
			"Ocorrências com Plano de Ação com prazo VENCIDO",
			"Email automático
		<br><br><br>Prezado(a).
		<br><br><br>Na(s) ocorrência(s) " . implode(", ", $ids) . " constam planos de ação de sua responsabilidade com prazo(s) vencido(s), favor acessar o(s) registro(s) e atualizar as informações.
		<br><br><br><b>SCI - Registro de Ocorrências</b>",
			null,
			null,
			true
		);

		if ($enviou == 1) {

			foreach ($ids as $id) {
				$query2 = "insert into log_email_acidente (acidente_id, data_envio, motivo, email) 
						values ($id, getdate(), 'plano_acao_vencido','$email')";
				//print $query2;
				odbc_exec($conSQL, $query2);
			}
		}
	}



	//EMAILS PARA RESPONSAVEL DE ATIVIDADE DO PLANO DE ACAO SEM PRAZO (TODA SEGUNDA-FEIRA)
	$query = "SELECT distinct usuario.email, num_ocorrencia, usuario.nome
				from itens_rms_processos with (nolock)
				join rms_processos with (nolock) on
					rms_processos.rms_processos_id = itens_rms_processos.rms_processos_id
				join registro_acidente ra with (nolock) on
					ra.acidente_id = rms_processos.num_ocorrencia
					--and ra.analise_rms is not null
				join responsavel_atividade responsavel with (nolock) on
					responsavel.item_id = itens_rms_processos.item_id
					and responsavel.status_id = 'a'
				join usuario with (nolock) on
					usuario.pessoa_id = responsavel.pessoa_id
					and usuario.status = 'a'
				WHERE rms_processos.num_ocorrencia is not null
				and itens_rms_processos.status = 'a'
				and rms_processos.status = 'a'
				and (DATEPART(DW,getdate())) = 2
				and itens_rms_processos.status_id <> '3'
				and itens_rms_processos.PRAZO_NOVO is null
				and itens_rms_processos.PRAZO is null
				
				UNION
				
				SELECT distinct usuario.email, num_ocorrencia, usuario.nome
				from itens_rms_processos with (nolock)
				join rms_processos with (nolock) on
					rms_processos.rms_processos_id = itens_rms_processos.rms_processos_id
				join registro_acidente ra with (nolock) on
					ra.acidente_id = rms_processos.num_ocorrencia
					--and ra.analise_rms is not null
				join responsavel_atividade responsavel with (nolock) on
					responsavel.item_id = itens_rms_processos.item_id
					and responsavel.status_id = 'a'
				join usuario with (nolock) on
					usuario.pessoa_id = responsavel.pessoa_id
					and usuario.status = 'a'
				join funcionario_equipe_monitor fem with (nolock) on
					fem.chapa = usuario.registro
					and fem.status_id = 'a'
				join gestor_monitor gm with (nolock) on
					gm.id = fem.gestor_id
				join usuario gestor with (nolock) on
					gestor.pessoa_id = gm.pessoa_id
					and gm.status_id = 'a'
				WHERE rms_processos.num_ocorrencia is not null
				and itens_rms_processos.status = 'a'
				and rms_processos.status = 'a'
				and (DATEPART(DW,getdate())) = 2
				and itens_rms_processos.status_id <> '3'
				and itens_rms_processos.PRAZO_NOVO is null
				and itens_rms_processos.PRAZO is null				
				
				UNION

				SELECT distinct usuario.email, num_ocorrencia, usuario.nome
				from itens_rms_processos with (nolock)
				join rms_processos with (nolock) on
					rms_processos.rms_processos_id = itens_rms_processos.rms_processos_id
				join registro_acidente ra with (nolock) on
					ra.acidente_id = rms_processos.num_ocorrencia
					--and ra.analise_rms is not null
				join responsavel_atividade responsavel with (nolock) on
					responsavel.item_id = itens_rms_processos.item_id
					and responsavel.status_id = 'a'
				join usuario with (nolock) on
					usuario.id = responsavel.user_gravacao
				WHERE rms_processos.num_ocorrencia is not null
				and responsavel.pessoa_id not in (select pessoa_id	
												  from usuario with (nolock)
												  where status = 'a')
				and itens_rms_processos.status = 'a'
				and rms_processos.status = 'a'
				and (DATEPART(DW,getdate())) = 2
				and itens_rms_processos.status_id <> '3'
				and itens_rms_processos.PRAZO_NOVO is null
				and itens_rms_processos.PRAZO is null	

			order by email				
			";
	//print $query;
	$result = odbc_exec($conSQL, $query);

	$emails = array();
	while (odbc_fetch_row($result)) {

		$email	= odbc_result($result, 1);
		$id = odbc_result($result, 2);

		if (!isset($emails[$email])) {
			$emails[$email] = array();
		}

		$emails[$email][] = $id;
	} //fim while
	foreach ($emails as $email => $ids) {


		$enviou = enviar_email(
			"helpdesk@covre.com.br",
			"SCI - Registro de Ocorrências",
			$email,
			"Ocorrências com Plano de Ação com prazo VENCIDO",
			"Email autom&aacute;tico
		<br><br><br>Prezado(a).
		<br><br><br>Na(s) ocorrência(s) " . implode(", ", $ids) . " constam planos de ação de sua responsabilidade com prazo(s) vencido(s), favor acessar o(s) registro(s) e atualizar as informações.
		<br><br><br><b>SCI - Registro de Ocorrências</b>",
			null,
			null,
			true
		);

		if ($enviou == 1) {

			foreach ($ids as $id) {
				$query2 = "insert into log_email_acidente (acidente_id, data_envio, motivo, email) 
						values ($id, getdate(), 'plano_acao_vencido','$email')";
				//print $query2;
				odbc_exec($conSQL, $query2);
			}
		}
	}



	//EMAILS PARA QUALIDADE COM DATA DE PREVISAO DE ANALISE A VENCER EM 10 DIAS (QSMA - SANTOS)
	$query = "select distinct registro_acidente.acidente_id, 'STS'
			  from registro_acidente with (nolock)
			  join feridos_acidente feridos with (nolock) on
				feridos.acidente_id = registro_acidente.acidente_id
				and feridos.status_id = 'a'
			  JOIN CARGOSOL..COLABORADOR CO WITH (NOLOCK) ON
				CO.COLABORADOR_ID = feridos.PESSOA_ID
				--AND CO.PONTO_OPERACAO_ID IN ($pontos_op_qsma_santos)
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

	$emails = array();

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
			$email = odbc_result($result1, 2);

			if (!isset($emails[$email])) {
				$emails[$email] = array();
			}

			$emails[$email][] = $acidente_id;
		}
	} //fim while

	foreach ($emails as $email => $ids) {


		$enviou = enviar_email(
			"helpdesk@covre.com.br",
			"SCI - Registro de Ocorrências",
			$email,
			"Ocorrência(s) com Análise de Eficêcia com prazo à vencer",
			"Email autom&aacute;tico
			<br><br><br>Prezado(a).
			<br><br><br>O prazo para a(s) ANÁLISE(S) DE EFICÁCIA do plano de ação da(s) ocorrência(s) " . implode(", ", $ids) . " irá vencer, favor acessar o(s) registro(s) e atualizar as informações.
			<br><br><br><b>SCI - Registro de Ocorrências</b>",
			null,
			null,
			true
		);

		if ($enviou == 1) {
			foreach ($ids as $id) {
				$query2 = "insert into log_email_acidente (acidente_id, data_envio, motivo, email) 
							values ($id, getdate(), 'analise_efic_vencer','$email')";
				//print $query2;
				odbc_exec($conSQL, $query2);
			}
		}
	}


	//EMAILS PARA QUALIDADE COM DATA DE PREVISAO DE ANALISE A VENCER EM 10 DIAS (QSMA)
	$query = "SELECT acidente_id
			  FROM registro_acidente ra with (nolock)
			  WHERE 1 = 1
			  and data_previsao_analise BETWEEN CONVERT(VARCHAR(10), GetDate(), 120) AND CONVERT(VARCHAR(10), GetDate()+10, 120)
			  and status_id not in ('i','f')
			  and plano_eficaz is null
			  and ra.acidente_id not in ($acidentes_qsma_santos)
			  ";
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
			$email = odbc_result($result1, 2);

			if (!isset($emails[$email])) {
				$emails[$email] = array();
			}

			$emails[$email][] = $acidente_id;
		} //fim while

	}

	foreach ($emails as $email => $ids) {


		$enviou = enviar_email(
			"helpdesk@covre.com.br",
			"SCI - Registro de Ocorrências",
			$email,
			"Ocorrência(s) com Análise(s) de Eficácia com prazo à vencer",
			"Email automático
				<br><br><br>Prezado(a).
				<br><br><br>O prazo para ANÁLISE DE EFICÁCIA do plano de ação da(s) ocorrência(s) " . implode(", ", $ids) . " irá vencer, favor acessar o(s) registro(s) e atualizar as informações.
				<br><br><br><b>SCI - Registro de Ocorrências</b>",
			null,
			null,
			true
		);

		if ($enviou == 1) {
			foreach ($ids as $id) {
				$query2 = "insert into log_email_acidente (acidente_id, data_envio, motivo, email) 
							values ($id, getdate(), 'analise_efic_vencer','$email')";
				//print $query2;
				odbc_exec($conSQL, $query2);
			}
		}
	}



	//EMAILS PARA QUALIDADE COM DATA DE PREVISAO DE ANALISE COM PRAZO VENCIDO (QSMA - SANTOS)
	$query = "select distinct registro_acidente.acidente_id, 'STS'
			  from registro_acidente with (nolock)
			  join feridos_acidente feridos with (nolock) on
				feridos.acidente_id = registro_acidente.acidente_id
				and feridos.status_id = 'a'
			  JOIN CARGOSOL..COLABORADOR CO WITH (NOLOCK) ON
				CO.COLABORADOR_ID = feridos.PESSOA_ID
				--AND CO.PONTO_OPERACAO_ID IN ($pontos_op_qsma_santos)
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
	//print $query;
	$result = odbc_exec($conSQL, $query);
	$acidentes_qsma_santos = 0;
	$acidentes = array();
	while (odbc_fetch_row($result)) {

		$acidentes[] = odbc_result($result, 1);

		$acidentes_qsma_santos .= "," . odbc_result($result, 1);
	} //fim while


	if (!empty($acidentes)) {
		//enviando email para a qualidade - Santos
		$query1 = "select nome, email
	from permissoes_acidente pa with (nolock)
	join usuario with (nolock) on
		usuario.id = pa.usuario_id
		and usuario.status = 'a'
	where area_qualidade_sts = 'S'";
		//print $query1;					
		$result1 = odbc_exec($conSQL, $query1);
		while (odbc_fetch_array($result1)) {
			$nome_destino = odbc_result($result1, 1);
			$email = odbc_result($result1, 2);

			$enviou = enviar_email(
				"helpdesk@covre.com.br",
				"SCI - Registro de Ocorrências",
				$email,
				"Ocorrência(s) com Análise de Eficácia com prazo VENCIDO",
				"Email automático
<br><br><br>Prezado(a).
<br><br><br>O prazo para ANÁLISE DE EFICÁCIA do(s) plano(s) de ação da(s) ocorrência(s) " . implode(", ", $acidentes) . " venceu, favor acessar o(s) registro(s) e atualizar as informações.
<br><br><br><b>SCI - Registro de Ocorrências</b>",
				null,
				null,
				true
			);

			if ($enviou == 1) {
				foreach ($ids as $id) {
					$query2 = "insert into log_email_acidente (acidente_id, data_envio, motivo, email) 
			values ($id, getdate(), 'analise_efic_vencido','$email')";
					//print $query2;
					odbc_exec($conSQL, $query2);
				}
			}
		}
	}


	//EMAILS PARA QUALIDADE COM DATA DE PREVISAO DE ANALISE COM PRAZO VENCIDO (QSMA)
	$query = "SELECT acidente_id
			  FROM registro_acidente ra with (nolock)
			  WHERE 1 = 1
			  and data_previsao_analise < CONVERT(VARCHAR(10), GetDate(), 120)
			  and status_id not in ('i','f')
			  and plano_eficaz is null
			  and ra.acidente_id not in ($acidentes_qsma_santos)
			  ";
	print $query;
	$result = odbc_exec($conSQL, $query);
	$contador = 1;
	$acidentes = array();
	while (odbc_fetch_row($result)) {

		$acidentes[] = odbc_result($result, 1);
	}

	if (!empty($acidentes)) {
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
				"SCI - Registro de Ocorrências",
				$email_destino,
				"Ocorrências com Análise de Eficácia com prazo VENCIDO",
				"Email automático
			<br><br><br>Prezado(a).
			<br><br><br>O prazo para ANÁLISE DE EFICÁCIA do(s) plano(s) de ação da(s) ocorrência(s) " . implode(", ", $acidentes) . " venceu, favor acessar o(s) registro(s) e atualizar as informaçõeses.
			<br><br><br><b>SCI - Registro de Ocorrências</b>",
				null,
				null,
				true
			);

			if ($enviou == 1) {
				foreach ($acidentes as $id) {
					$query2 = "insert into log_email_acidente (acidente_id, data_envio, motivo, email) 
							values ($id, getdate(), 'analise_efic_vencido','$email_destino')";
					//print $query2;
					odbc_exec($conSQL, $query2);
				}
			}
		}
	}


	//EMAILS PARA RESPONSAVEL DE ANALISE/RMS SEM PREENCHIMENTO DA CAUSA RAIZ NA ANALISE DE CAUSA APOS 7 DIAS DO VINCULO (DIARIO)
	$query = "select usuario.email, responsavel.acidente_id
	from responsavel_analise_rms responsavel with (nolock)
	join usuario with (nolock) on
		usuario.pessoa_id = responsavel.pessoa_id
		and usuario.status = 'a'
	join registro_acidente ra with (nolock) on
		ra.acidente_id = responsavel.acidente_id
		and ra.status_id not in ('i','f')
	left join analise_causa with (nolock) on
		analise_causa.acidente_id = responsavel.acidente_id
		and analise_causa.status_id = 'a'
	where responsavel.status_id = 'a'
	and causa_raiz is null
	and CONVERT(VARCHAR(10), responsavel.data_gravacao+7, 120) < CONVERT(VARCHAR(10), GetDate(), 120)
	and responsavel.pessoa_id<>0
	order by email				
				";
	//print $query;
	$result = odbc_exec($conSQL, $query);

	$emails = array();
	while (odbc_fetch_row($result)) {

		$email	= odbc_result($result, 1);
		$id = odbc_result($result, 2);

		if (!isset($emails[$email])) {
			$emails[$email] = array();
		}

		$emails[$email][] = $id;
	} //fim while

	foreach ($emails as $email => $ids) {


		$enviou = enviar_email(
			"helpdesk@covre.com.br",
			"SCI - Registro de Ocorrências",
			$email,
			"Ocorrência(s) com Análise de Causa",
			"Email autom&aacute;tico
		<br><br><br>Prezado(a).
		<br><br><br>A(s) ocorrência(s) " . implode(", ", $ids) . " está aguardando o preenchimento da análise de causa, favor acessar o(s) registro(s) e atualizar as informações.
		<br><br><br><b>SCI - Registro de Ocorr&ecirc;ncias</b>",
			null,
			null,
			true
		);

		if ($enviou == 1) {

			foreach ($ids as $id) {
				$query2 = "insert into log_email_acidente (acidente_id, data_envio, motivo, email) 
						values ($id, getdate(), 'analise_causa_vazia','$email')";
				//print $query2;
				odbc_exec($conSQL, $query2);
			}
		}
	}

	//EMAILS PARA RESPONSAVEL DE ANALISE/RMS SEM PREENCHIMENTO DO PLANO DE ACAO APOS 7 DIAS DO VINCULO
	$query = "select usuario.email, responsavel.acidente_id
			from responsavel_analise_rms responsavel with (nolock)
			join usuario with (nolock) on
				usuario.pessoa_id = responsavel.pessoa_id
				and usuario.status = 'a'
			left join rms_processos with (nolock) on
				rms_processos.num_ocorrencia = responsavel.acidente_id
				and rms_processos.status = 'a'
			left join itens_rms_processos itens with (nolock) on
				itens.rms_processos_id = rms_processos.rms_processos_id
				and itens.status = 'a'
			where responsavel.status_id = 'a'
			and itens.item_id is null
			and responsavel.pessoa_id<>0
			and CONVERT(VARCHAR(10), responsavel.data_gravacao+7, 120) BETWEEN CONVERT(VARCHAR(10), GetDate()-7, 120) AND 
			CONVERT(VARCHAR(10), GetDate(), 120)
			and usuario.email not in (select email
									  from log_email_acidente with (nolock)
									  where acidente_id = responsavel.acidente_id
									  and email = usuario.email
									  and motivo = 'plano_acao_vazio')				
				";
	//print $query;
	$result = odbc_exec($conSQL, $query);

	$emails = array();
	while (odbc_fetch_row($result)) {

		$email	= odbc_result($result, 1);
		$id = odbc_result($result, 2);

		if (!isset($emails[$email])) {
			$emails[$email] = array();
		}

		$emails[$email][] = $id;
	} //fim while

	foreach ($emails as $email => $ids) {

		$enviou = enviar_email(
			"helpdesk@covre.com.br",
			"SCI - Registro de Ocorrências",
			$email,
			"Ocorrências aguardando preenchimento - Plano de Ação",
			"Email automático
		<br><br><br>Prezado(a).
		<br><br><br>A(s) ocorrência(s) " . implode(", ", $ids) . " está(ão) aguardando o preenchimento do plano de ação, favor acessar o(s) registro(s) e atualizar as informações.
		<br><br><br><b>SCI - Registro de Ocorr&ecirc;ncias</b>",
			null,
			null,
			true
		);

		if ($enviou == 1) {

			foreach ($ids as $id) {
				$query2 = "insert into log_email_acidente (acidente_id, data_envio, motivo, email) 
						values ($id, getdate(), 'plano_acao_vazio','$email')";
				//print $query2;
				odbc_exec($conSQL, $query2);
			}
		}
	}


	//EMAILS PARA RESPONSAVEL PAE DE ANALISE PENDENTE
	$query_ppae = "SELECT
					'97' SISTEMA_ID,
					REGISTRO_ACIDENTE.ACIDENTE_ID,
					REGISTRO_ACIDENTE.NOME_INFORMANTE,
					REGISTRO_ACIDENTE.TELEFONE_INFORMANTE,
					REGISTRO_ACIDENTE.ENDERECO_ACIDENTE
				FROM REGISTRO_ACIDENTE WITH(NOLOCK)
				
				LEFT JOIN ANALISE_PPAE WITH(NOLOCK)
					ON ANALISE_PPAE.ACIDENTE_ID = REGISTRO_ACIDENTE.ACIDENTE_ID

				WHERE PROD_QUIMICO = 'S'
					AND VAZAMENTO = 'S'		
					AND (ANALISE_PPAE.STATUS_ID IS NULL OR ANALISE_PPAE.STATUS_ID = 'P')
					AND REGISTRO_ACIDENTE.STATUS_ID <> 'I'
					AND REGISTRO_ACIDENTE.STATUS_ID <> 'F'
				
				ORDER BY REGISTRO_ACIDENTE.ACIDENTE_ID DESC";
	//print "<pre>$query_ppae</pre>";
	$result_ppae = odbc_exec($conSQL, $query_ppae);
	while (odbc_fetch_row($result_ppae)) {

		$sistema_id			  = odbc_result($result_ppae, 1);
		$acidente_id		  = odbc_result($result_ppae, 2);
		$nome_informante	  = odbc_result($result_ppae, 3);
		$telefone_informante  = odbc_result($result_ppae, 4);
		$endereco_acidente	  = odbc_result($result_ppae, 5);


		//VERIFICA SE TEM ALGUM FERIDO DE SANTOS
		$dados_envolvidos_sts = $envolvidos_sts->envolvidos($acidente_id);
		$ferido_sts = $dados_envolvidos_sts->local;

		if ($ferido_sts != '') {

			$query_ppae_sts = "SELECT email
								FROM PERMISSOES_ACIDENTE PERMISSOES WITH (NOLOCK)
								JOIN USUARIO WITH (NOLOCK) ON
									USUARIO.ID = PERMISSOES.USUARIO_ID
								WHERE COORDENADOR_PPAE_STS = 'S'";
			//print "<pre>$query_ppae_sts</pre>";
			$result_ppae_sts = odbc_exec($conSQL, $query_ppae_sts);
			while (odbc_fetch_row($result_ppae_sts)) {

				$email_destino_sts	 = odbc_result($result_ppae_sts, 1);


				$enviou_ppae_sts = enviar_email(
					"helpdesk@covre.com.br",
					"SCI - Registro de Ocorrências",
					$email_destino_sts,
					"Ocorrência: " . $acidente_id . " - Análise PAE - STS Pendente",
					"Email automático
					<br><br><br>Prezado(a).
					<br><br><br>A ocorrência <b>" . $acidente_id . "</b> esta com a Análise do coordenador PAE Nacional - STS pendente. Acesse o registro para realizar a Análise.
					<br><br><br><b>SCI - Registro de Ocorrências</b>",
					null,
					null,
					true
				);


				if ($enviou_ppae_sts == 1) {
					$query2_ppae_sts = "insert into log_email_acidente (acidente_id, data_envio, motivo, email) 
								values (" . $acidente_id . ", getdate(), 'analise_ppae_sts_pendente','$email_destino_sts')";
					//	print $query2;
					odbc_exec($conSQL, $query2_ppae_sts);
				}
			}
		} else {

			$query_ppae_mtz = "SELECT email
								FROM PERMISSOES_ACIDENTE PERMISSOES WITH (NOLOCK)
								JOIN USUARIO WITH (NOLOCK) ON
									USUARIO.ID = PERMISSOES.USUARIO_ID
								WHERE COORDENADOR_PPAE = 'S'";
			//print "<pre>$query_ppae_mtz</pre>";
			$result_ppae_mtz = odbc_exec($conSQL, $query_ppae_mtz);
			while (odbc_fetch_row($result_ppae_mtz)) {

				$email_destino_mtz		 = odbc_result($result_ppae_mtz, 1);


				$enviou_ppae_mtz = enviar_email(
					"helpdesk@covre.com.br",
					"SCI - Registro de Ocorrências",
					$email_destino_mtz,
					"Ocorrência: " . $acidente_id . " - Análise PAE Pendente",
					"Email automático
					<br><br><br>Prezado(a).
					<br><br><br>A ocorrência <b>" . $acidente_id . "</b> está com a Análise do coordenador PAE Nacional pendente. Acesse o registro para realizar a Análise.
					<br><br><br><b>SCI - Registro de Ocorrências</b>",
					null,
					null,
					true
				);


				if ($enviou_ppae_mtz == 1) {
					$query2_ppae_mtz = "insert into log_email_acidente (acidente_id, data_envio, motivo, email) 
								values (" . $acidente_id . ", getdate(), 'analise_ppae_pendente','$email_destino_mtz')";
					//	print $query2;
					odbc_exec($conSQL, $query2_ppae_mtz);
				}
			}
		}
	}
} //else
