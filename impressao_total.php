<?php
session_name("covre_ti");
session_start();

require("../SCA/includes/page_func.php");
include("../SCA/includes/conect_sqlserver.php");
require("../SCA/includes/phpmailer/class.phpmailer.php");
require_once "class/Dados.php";
require_once "class/EnvolvidoSts.php";
require_once "class/GravaLog.php";
require_once "class/RelatoriosInv.php";
require_once "class/PlanoAcao.php";


$dados = new Dados;
$envolvidos_sts = new EnvolvidoSts;
$grava_log = new GravaLog;
$relatorios = new RelatoriosInv;	
$exibe_atividade = new PlanoAcao;	

$localItem = "../registro_acidentes/registro_acidente_parte1.php";
$logado    = $_SESSION["usuario_logado"];
$logado1   = $_SESSION['cpf'];

$ip = $_SERVER['HTTP_HOST'];

// variavel ambiente para não enviar emails em homologação
$homologacao = FALSE;
$email_homologacao = "";
if ($ip == '192.168.10.148'){
	$homologacao = TRUE;
}else{
	$homologacao = FALSE;
}



if ($logado1 != '')
	$acesso = "permitido";
else
	$acesso  = valida_acesso($conSQL, $localItem, $logado);


if($acesso <> "permitido")
{
    grava_acesso($conSQL, $localItem, $logado, 2, $vObservacao);

    print "
        <script language = 'JavaScript'>
           alert('Acesso negado para esta p�gina');
           window.location='centro.php';
		</script>
    ";
}//elseif
else{
	 grava_acesso($conSQL, $localItem, $logado, 1, $vObservacao);


	$registro_terminal		= $_GET["terminal"];

	$acidente_id_get		= $_GET["id"];
	$sistema_id				= $_GET["si"];

	$sistema_id = 97;

	$acidente_id 			= $_POST["acidente_id"];
	$tipo_registro_id 		= $_POST["tipo_registro_id"];
	$data_inclusao	 		= $_POST["data_inclusao"];
	$acidente_transito	 	= $_POST["acidente_transito"];
	$acidente_trabalho	 	= $_POST["acidente_trabalho"];
	$avaria_carga		 	= $_POST["avaria_carga"];
	$atraso_entrega		 	= $_POST["atraso_entrega"];
	$data_fato			 	= $_POST["data_1"];
	$hora_fato			 	= $_POST["hora_fato"];
	$opcao_int_ext			= $_POST["radio"];
	$nome_informante	 	= $_POST["nome_informante"];
	$telefone_informante 	= $_POST["telefone_informante"];
	$opcao_ferida			= $_POST["radio1"];
	$opcao_vaz_carr			= $_POST["radio2"];
	$cliente_veiculo		= $_POST["cliente_veiculo"];
	$endereco_acidente		= $_POST["endereco_acidente"];
	$bairro_acidente		= $_POST["bairro_acidente"];
	$municipio_acidente		= $_POST["municipio_acidente"];
	$ponto_ref_acidente		= $_POST["ponto_ref_acidente"];
	$opcao_quimico			= $_POST["radio3"];
	$opcao_vazamento		= $_POST["radio4"];
	$opcao_cond_tempo		= $_POST["radio5"];
	$opcao_gravidade		= $_POST["radio6"];
	$opcao_carregado		= $_POST["radio7"];
	$observacao				= $_POST["observacao"];
	$data_gravacao			= $_POST["data_gravacao"];
	$municipio_id			= $_POST["municipio_id"];
	$acao_imediata			= $_POST["acao_imediata"];

	$patrimonio_covre		= $_POST["patrimonio_covre"];
	$patrimonio_cliente		= $_POST["patrimonio_cliente"];
	$patrimonio_terceiro	= $_POST["patrimonio_terceiro"];
	$dano_covre_caminhao	= $_POST["dano_covre_caminhao"];

	$func_envolvido			= $_POST["func_envolvido"];
	$funcionario_id			= $_POST["funcionario_id"];
	$codpessoa				= $_POST["codpessoa"];
	$func_ferido			= $_POST["func_ferido"];
	$tipo_envolvido			= $_POST["tipo_envolvido"];
	$tipo_ferimento			= $_POST["tipo_ferimento"];
	$hospital				= $_POST["hospital"];
	$veic_envolvido			= $_POST["veic_envolvido"];
	$data_sac				= $_POST["data_10"];
	$referencia_cliente 	= $_POST["referencia_cliente"];


	$observacao 	= utf8_decode($observacao);
	$acao_imediata 	= utf8_decode($acao_imediata);

	$observacao 	= str_replace("'", "''", $observacao);
	$acao_imediata 	= str_replace("'", "''", $acao_imediata);


	if ($acidente_id_get != '')
		$acidente_id = $acidente_id_get;

	if ($data_gravacao == '')
	{
		$dt_registro = date("d/m/Y");
		$hr_registro = date("Hi");
	}
	else
	{
		$dt_registro = $data_inclusao;
		$hr_registro = substr($data_gravacao,11,2).substr($data_gravacao,14,2);
	}

	if ($logado1 != '')
	{
		$cpf = $logado1;

		$query = "select pf_cpf, pa.coordenador_ppae, pa.area_qualidade, usuario.id, pa.area_rh, pa.area_ti,
					pa.area_qualidade_sts, pa.sac, pa.somente_leitura, area_id_leitura,
					pessoa.pessoa_id, pa.coordenador_ppae_sts
					from CARGOSOL..pessoa with (nolock)
					left join usuario with (nolock) on
						usuario.pessoa_id = pessoa.PESSOA_ID
						and usuario.status = 'a'
					left join permissoes_acidente pa with (nolock) on
						pa.usuario_id = usuario.id
					where pessoa.pf_cpf = '$cpf'
					and pessoa.TAB_STATUS_ID in (1,1071)";
		//print $query;
		$result = odbc_exec($conSQL, $query) ;
		$cpf 					= odbc_result($result, 1);
		$coordenador_ppae 		= odbc_result($result, 2);
		$area_qualidade 		= odbc_result($result, 3);
		$usuario_id		 		= odbc_result($result, 4);
		$area_rh 				= odbc_result($result, 5);
		$area_ti 				= odbc_result($result, 6);
		$area_qualidade_sts 	= odbc_result($result, 7);
		$area_sac			 	= odbc_result($result, 8);
		$somente_leitura	 	= odbc_result($result, 9);
		$area_id_leitura	 	= odbc_result($result, 10);
		$pessoa_id_logado	 	= odbc_result($result, 11);
		$coordenador_ppae_sts	= odbc_result($result, 12);
	}
	else
	{
		$query = "select pf_cpf, pa.coordenador_ppae, pa.area_qualidade, usuario.id, pa.area_rh, pa.area_ti,
					pa.area_qualidade_sts, pa.sac, pa.somente_leitura, area_id_leitura,
					pessoa.pessoa_id, pa.coordenador_ppae_sts
					from usuario with (nolock)
					join CARGOSOL..PESSOA with (nolock) on
						pessoa.PESSOA_ID = usuario.pessoa_id
					left join permissoes_acidente pa with (nolock) on
						pa.usuario_id = usuario.id
					where usuario = '$logado'
					and pessoa.TAB_STATUS_ID in (1,1071)";
		//print $query;
		$result = odbc_exec($conSQL, $query) ;
		$cpf 					= odbc_result($result, 1);
		$coordenador_ppae 		= odbc_result($result, 2);
		$area_qualidade 		= odbc_result($result, 3);
		$usuario_id		 		= odbc_result($result, 4);
		$area_rh 				= odbc_result($result, 5);
		$area_ti 				= odbc_result($result, 6);
		$area_qualidade_sts 	= odbc_result($result, 7);
		$area_sac			 	= odbc_result($result, 8);
		$somente_leitura	 	= odbc_result($result, 9);
		$area_id_leitura	 	= odbc_result($result, 10);
		$pessoa_id_logado	 	= odbc_result($result, 11);
		$coordenador_ppae_sts	= odbc_result($result, 12);
	}



	if ($acidente_id == '')
	{
		//SELECIONANDO OS DADOS DO RESPONSAVEL
		$query = "select DISTINCT PPESSOA.NOME, PSECAO.DESCRICAO
					from corpore..PPESSOA with (nolock)
					JOIN CORPORE..PFUNC WITH (NOLOCK) ON
						PFUNC.CODPESSOA = PPESSOA.CODIGO
						AND PFUNC.CODCOLIGADA = 1
						AND PFUNC.CODSITUACAO <> 'D'
					JOIN CORPORE..PSECAO WITH (NOLOCK) ON
						PSECAO.CODIGO = PFUNC.CODSECAO
						AND PSECAO.CODCOLIGADA = 1
					where CPF = '$cpf'";
		//print $query;
		$result = odbc_exec($conSQL, $query) ;
		$responsavel_preenchimento 	= odbc_result($result, 1);
		$secao_responsavel		 	= odbc_result($result, 2);
	}
	else
	{
		//verifica se o logado tem permissao para cadastrar despesas
		$query = "select top 1 lancamento_id, ara.id
					from lancamento_despesas_acidente lda with (nolock)
					join tipo_despesa_acidente tda with (nolock) on
						tda.id = lda.tipo_despesa_id
					join area_responsavel_acidente ara with (nolock) on
						ara.id = lda.AREA_ID
						and ((ara.responsavel_id = $usuario_id) or (ara.sub_1_id = $usuario_id) or (ara.sub_2_id = $usuario_id))
					join despesa_area_acidente daa with (nolock) on
						daa.acidente_id = lda.acidente_id
						and daa.status_id <> 'i'
					where lda.acidente_id = $acidente_id
					and ((daa.area_id = ara.id) or ('$area_qualidade' = 'S'))
					";
		//print $query;
		$result = odbc_exec($conSQL, $query) ;
		$lancamento_id		= odbc_result($result, 1);
		$area_id		 	= odbc_result($result, 2);

		//VERIFICA SE O USUARIO � RESPONSAVEL POR ALGUMA AREA
		$query = "select top 1 id, area
					from area_responsavel_acidente area with (nolock)
					where area.status_id = 'a'
					and ((responsavel_id = $usuario_id) or (sub_1_id = $usuario_id) or (sub_2_id = $usuario_id))
					order by area";
		//print $query;
		$result = odbc_exec($conSQL, $query) ;
		$responsavel_area 	= odbc_result($result, 1);


	}

	if ($acidente_id != '')
	{
		//PERMISSAO PARA ANALISE DE ACIDENTES E ANALISE DE CAUSAS
		$dados_permissao = $dados->permissaoAnalise($acidente_id);
		$permissao_analise = $dados_permissao->responsavel_id;

		//PERMISSAO PARA PLANO DE ACAO
		$dados_permissao_plano = $dados->permissaoPlanoAcao($acidente_id);
		$permissao_plano_acao = $dados_permissao_plano->responsavel_item_id;

		/*
		//VERIFICA AS PERMISSOES QSMA - SANTOS
		if ($area_qualidade_sts == 'S')
		{
			$dados_permissao_qsma_santos = $dados->ocorrenciaQSMASantos($acidente_id);
			$ocorrencia_sts = $dados_permissao_qsma_santos->ocorrencia_sts;
		}
		*/
		$dados_permissao_qsma_santos = $dados->ocorrenciaQSMASantos($acidente_id);
		$ocorrencia_sts = $dados_permissao_qsma_santos->ocorrencia_sts;


		//SELECIONA AS PLACAS DO ACIDENTE
		$query = "SELECT DISTINCT PLACA
					FROM VEICULOS_ACIDENTE VA WITH (NOLOCK)
					JOIN CARGOSOL..VEICULO_FORNECEDOR VF WITH (NOLOCK) ON
						VF.VEICULO_FORNECEDOR_ID = VA.VEICULO_FORNECEDOR_ID
					WHERE ACIDENTE_ID = $acidente_id
					AND VA.STATUS_ID = 'A'";
		//print $query;
		$result = odbc_exec($conSQL, $query);

		$placas = '';
		$contador = 1;

		while(odbc_fetch_row($result))
		{
			   $placa 	= odbc_result($result,1);

				if ($contador == 1)
					$placas = $placa;
				else
					$placas .= ", ".$placa;

				$contador++;
		}
		
		$dados_invest_an = $dados->dadosInvAn($acidente_id);
		$sem_lesao = $dados_invest_an->sem_lesao;
		$com_lesao = $dados_invest_an->com_lesao;
		$sem_afastamento = $dados_invest_an->sem_afastamento;
		$com_afastamento = $dados_invest_an->com_afastamento;
		$tempo_prev_afastamento = $dados_invest_an->tempo_prev_afastamento;
		$observ_lesao = $dados_invest_an->observ_lesao;
		$opcao_emitido_cat = $dados_invest_an->emitido_cat;
		$numero_cat = $dados_invest_an->numero_cat;		
		$cronologia = $dados_invest_an->cronologia;
		$informacao_acao_chefia = $dados_invest_an->informacao_acao_chefia;
		$desc_jornada_trabalho = $dados_invest_an->desc_jornada_trabalho;
		$opcao_maquina_ferramental = $dados_invest_an->maquina_ferramental;
		$desc_maquina_ferramental = $dados_invest_an->desc_maquina_ferramental;
		$opcao_dificuldade_trabalho = $dados_invest_an->dificuldade_trabalho;
		$desc_dificuldade_trabalho = $dados_invest_an->desc_dificuldade_trabalho;
		$opcao_orientacao_chefia = $dados_invest_an->orientacao_chefia;
		$desc_orientacao_chefia = $dados_invest_an->desc_orientacao_chefia;
		$opcao_outras_pessoas_local = $dados_invest_an->outras_pessoas_local;
		$desc_outras_pessoas_local = $dados_invest_an->desc_outras_pessoas_local;
		$opcao_comunic_superior = $dados_invest_an->comunic_superior;
		$desc_comunic_superior = $dados_invest_an->desc_comunic_superior;
		$opcao_dia_evento = $dados_invest_an->dia_evento;
		$desc_dia_evento = $dados_invest_an->desc_dia_evento;
		$opcao_remedio_continuo = $dados_invest_an->remedio_continuo;
		$desc_remedio_continuo = $dados_invest_an->desc_remedio_continuo;																																								
		$opcao_usando_epi = $dados_invest_an->usando_epi;
		$desc_usando_epi = $dados_invest_an->desc_usando_epi;
		$opcao_conhecimento_risco = $dados_invest_an->conhecimento_risco;
		$opcao_recebeu_vale_transp = $dados_invest_an->recebeu_vale_transp;
		$opcao_apresentado_bo = $dados_invest_an->apresentado_bo;
		$desc_apresentado_bo = $dados_invest_an->desc_apresentado_bo;
		$opcao_trajeto_rota = $dados_invest_an->trajeto_rota;
		$opcao_horario_jornada = $dados_invest_an->horario_jornada;
		$desc_horario_jornada = $dados_invest_an->desc_horario_jornada;
		$opcao_acidente_trajeto = $dados_invest_an->acidente_trajeto;
		$desc_acidente_trajeto = $dados_invest_an->desc_acidente_trajeto;
		$opcao_disco_tacografo_disp = $dados_invest_an->disco_tacografo_disp;
		$veloc_evidenc_disco = $dados_invest_an->veloc_evidenc_disco;
		$comentario_disco_tacografo = $dados_invest_an->comentario_disco_tacografo;
		$opcao_tempo = $dados_invest_an->tempo;
		$opcao_luz = $dados_invest_an->luz;
		$opcao_rodovia = $dados_invest_an->rodovia;
		$opcao_transito = $dados_invest_an->transito;
		$opcao_veiculo = $dados_invest_an->veiculo;
		$opcao_carga = $dados_invest_an->carga;
		$opcao_motorista = $dados_invest_an->motorista;
		$opcao_cinto_seguranca = $dados_invest_an->cinto_seguranca;
		$observacao_investig = $dados_invest_an->observacao;
		$limite_veloc_pista = $dados_invest_an->limite_veloc_pista;
		$opcao_feito_bo = $dados_invest_an->feito_bo;
		$dados_bo = $dados_invest_an->dados_bo;
		$data_bo = $dados_invest_an->data_bo;
		$desc_vel_pontas = $dados_invest_an->desc_vel_pontas;
		$opcao_testemunha = $dados_invest_an->testemunha;
		$nome_testemunha = $dados_invest_an->nome_testemunha;
		$funcao_testemunha = $dados_invest_an->funcao_testemunha;
		$relato_testemunha = $dados_invest_an->relato_testemunha;	
		$observ_exame_per = $dados_invest_an->observ_exame_per;		
		$investigacao_analise_id = $dados_invest_an->id;	
		$acid_traj_nao_aplic = $dados_invest_an->acid_traj_nao_aplic;
		$acid_tran_nao_aplic = $dados_invest_an->acid_tran_nao_aplic;
		$acid_env_prod_nao_aplic = $dados_invest_an->acid_env_prod_nao_aplic;		
		
		
		$dados_analise_causa = $dados->dadosAnCausa($acidente_id);
		$analise_causa_id = $dados_analise_causa->id;	
		$efeito_nc = $dados_analise_causa->efeito_nc;
		$causa_raiz = $dados_analise_causa->causa_raiz;			
		
		//DADOS ANALISE PPAE
		$query = "select relato_fato, acionamento_nucleo, observacao, prod_solido, prod_liquido, prod_gasoso, tambores, bombonas, 
				  isotank, sacaria, flag_outra_embalagem, outra_embalagem, rio, mangue, lago, bueiros, flag_outros_locais, 
				  outros_locais, acionamento_cotec, tombamento, colisao, queda_carga, vazamento_transito, flag_outra_ocorrencia, 
				  outra_ocorrencia, numero_superior, numero_inferior, equip_atend_emerg, policia, concessionaria, orgao, 
				  seguradora, remetente, destinatario, imprensa, flag_outro_presente, outro_presente, analise_ppae_id, status_id
				  from analise_ppae with (nolock)
				  where acidente_id = $acidente_id";
		//print $query;
		$result = odbc_exec($conSQL, $query);
	
		$relato_fato			= odbc_result($result, 1);	
		$opcao_nucleo			= odbc_result($result, 2);	
		$observacao_ppae				= odbc_result($result, 3);	
		$prod_solido			= odbc_result($result, 4);	
		$prod_liquido			= odbc_result($result, 5);	
		$prod_gasoso			= odbc_result($result, 6);	
		$tambores				= odbc_result($result, 7);	
		$bombonas				= odbc_result($result, 8);	
		$isotank				= odbc_result($result, 9);	
		$sacaria				= odbc_result($result, 10);	
		$flag_outra_embalagem	= odbc_result($result, 11);	
		$outra_embalagem		= odbc_result($result, 12);	
		$rio					= odbc_result($result, 13);	
		$mangue					= odbc_result($result, 14);	
		$lago					= odbc_result($result, 15);	
		$bueiros				= odbc_result($result, 16);	
		$flag_outros_locais		= odbc_result($result, 17);	
		$outros_locais			= odbc_result($result, 18);	
		$opcao_cotec			= odbc_result($result, 19);	
		$tombamento				= odbc_result($result, 20);	
		$colisao				= odbc_result($result, 21);	
		$queda_carga			= odbc_result($result, 22);	
		$vazamento_transito		= odbc_result($result, 23);	
		$flag_outra_ocorrencia	= odbc_result($result, 24);	
		$outra_ocorrencia		= odbc_result($result, 25);	
		$numero_superior		= odbc_result($result, 26);	
		$numero_inferior		= odbc_result($result, 27);	
		$equip_atend_emerg		= odbc_result($result, 28);	
		$policia				= odbc_result($result, 29);	
		$concessionaria			= odbc_result($result, 30);	
		$orgao					= odbc_result($result, 31);	
		$seguradora				= odbc_result($result, 32);	
		$remetente				= odbc_result($result, 33);	
		$destinatario			= odbc_result($result, 34);	
		$imprensa				= odbc_result($result, 35);	
		$flag_outro_presente	= odbc_result($result, 36);	
		$outro_presente			= odbc_result($result, 37);	
		$analise_ppae_id		= odbc_result($result, 38);		
		$status_id				= odbc_result($result, 39);			
		
		//DADOS ANALISE QSMA
		$query = "select classificacao, gravidade, impacto_sig, tipo_ocorrencia_id, local_ocorrencia_id, conclusao, 
				  obs_impacto_sig, area_qsma_qualidade, area_qsma_seguranca, area_qsma_saude, area_qsma_meio_ambiente, 
				  plano_acao, necess_doc, necess_conv, CONVERT(varchar(10), data_plano_acao, 103), docto_necess_rev, 
				  requer_mudanca, mudanca_analise_risco, prox_audit_interna, prox_audit_externa, prox_audit_requisito, 
				  atraves_levantamento,	outras_analise_critica, desc_outras_analise_critica, evidencias, plano_eficaz, 
				  num_prox_relatorio, rms_processos_id, CONVERT(varchar(10), data_previsao_analise, 103),
				  (select top 1 'pendente'
				   from itens_rms_processos itens with (nolock)
				   where itens.rms_processos_id = rms_processos.rms_processos_id
				   and status_id <> 3
				   and status <> 'i') status_atividade_plano_acao,
				   area_responsavel_id,
				   registro_acidente.tipo_registro_id
				  from registro_acidente with (nolock)
				  left join rms_processos with (nolock) on
					rms_processos.num_ocorrencia = registro_acidente.acidente_id
					and rms_processos.status <> 'i'
				  where acidente_id = $acidente_id";
		//print $query;
		$result = odbc_exec($conSQL, $query);
	
		$opcao_classificacao 			= odbc_result($result, 1);
		$opcao_gravidade				= odbc_result($result, 2);
		$opcao_impacto	 				= odbc_result($result, 3);
		$tipo_ocorrencia_id	 			= odbc_result($result, 4);
		$local_ocorrencia_id			= odbc_result($result, 5);
		$opcao_conclusao				= odbc_result($result, 6);									
		$obs_impacto_sig				= odbc_result($result, 7);
		$area_qsma_qualidade			= odbc_result($result, 8);								
		$area_qsma_seguranca			= odbc_result($result, 9);								
		$area_qsma_saude				= odbc_result($result, 10);								
		$area_qsma_meio_ambiente		= odbc_result($result, 11);		
		$opcao_plano_acao				= odbc_result($result, 12);		
		$opcao_necess_doc				= odbc_result($result, 13);		
		$opcao_necess_conv				= odbc_result($result, 14);		
		$data_plano_acao				= odbc_result($result, 15);		
		$docto_necess_rev				= odbc_result($result, 16);	
		$opcao_requer_mudanca			= odbc_result($result, 17);	
		$mudanca_analise_risco			= odbc_result($result, 18);	
		$prox_audit_interna				= odbc_result($result, 19);	
		$prox_audit_externa				= odbc_result($result, 20);	
		$prox_audit_requisito			= odbc_result($result, 21);	
		$atraves_levantamento			= odbc_result($result, 22);	
		$outras_analise_critica			= odbc_result($result, 23);	
		$desc_outras_analise_critica	= odbc_result($result, 24);	
		$evidencias						= odbc_result($result, 25);	
		$opcao_plano_eficaz				= odbc_result($result, 26);	
		$num_prox_relatorio				= odbc_result($result, 27);	
		$rms_processos_id				= odbc_result($result, 28);	
		$data_previsao_analise			= odbc_result($result, 29);
		$status_atividade_plano_acao	= odbc_result($result, 30);	
		$area_responsavel_acidente		= odbc_result($result, 31);	
		$tipo_registro_id				= odbc_result($result, 32);				
		
	}



if($fechar != ""){
	print"
		<script language='javascript'>
			open(location, '_self').close();
		</script>
	";
}

if($novo != "")
{
		print"
		<script language='javascript'>
			window.location.href='registro_acidente_parte1.php';
		</script>
		";
}

		$query = "select CLIENTE.PESSOA_ID
					from CARGOSOL..CLIENTE WITH (NOLOCK)
					JOIN CARGOSOL..PESSOA WITH (NOLOCK) ON
						PESSOA.PESSOA_ID = CLIENTE.PESSOA_ID
						AND PESSOA.TAB_STATUS_ID = 1
					WHERE CLIENTE.TAB_STATUS_ID = 1
					AND PESSOA.NOME_FANTASIA = '$cliente_veiculo'";
		//print $query;
		$result = odbc_exec($conSQL, $query) ;
		$cliente_id = odbc_result($result, 1);


		$query = "select id
					from analise_causa with (nolock)
					where acidente_id = $acidente_id";
		//print $query;
		$result = odbc_exec($conSQL, $query) ;
		$analise_causa_id = odbc_result($result, 1);


	if ($acidente_id != '')
	{
		if($erro != 1)
		{
			$query = "select tipo_registro_id, CONVERT(varchar(10), data_inclusao, 103), acidente_transito, acidente_trabalho,
						avaria_carga, atraso_entrega,  CONVERT(varchar(10), data_fato, 103), hora_fato, acidente_int_ext,
						nome_informante, telefone_informante, pessoa_ferida, veiculo_vaz_carr, cliente_veiculo,
						endereco_acidente, bairro_acidente, municipio_acidente, ponto_referencia, prod_quimico, vazamento,
						condicao_tempo, gravidade, observacao, PPESSOA.NOME, PSECAO.DESCRICAO, registro_acidente.status_id,
						data_gravacao,
						(select top 1 MUNICIPIO_ID
						 from CARGOSOL..municipio with (nolock)
						 where municipio+'/'+uf = municipio_acidente collate SQL_Latin1_General_CP1_CI_AS
						 and tab_status_id = 1),
						 cpf_responsavel, acao_imediata, patrimonio_covre, patrimonio_cliente, patrimonio_terceiro,
						 local_carregamento,
						 CONVERT(varchar(10), data_sac, 103),
						 referencia_cliente_sac,
						 dano_covre_com_caminhao,
						 rms_processos_id,
						 registro_acidente.analise_rms,
						 classificacao
						FROM registro_acidente with (nolock)
						LEFT JOIN CORPORE..PPESSOA with (nolock) on
							PPESSOA.CPF = registro_acidente.cpf_responsavel collate SQL_Latin1_General_CP1_CI_AI
						LEFT JOIN CORPORE..PFUNC WITH (NOLOCK) ON
							PFUNC.CODPESSOA = PPESSOA.CODIGO
							AND PFUNC.CODCOLIGADA = 1
							AND PFUNC.CODSITUACAO <> 'D'
						LEFT JOIN CORPORE..PSECAO WITH (NOLOCK) ON
							PSECAO.CODIGO = PFUNC.CODSECAO
							AND PSECAO.CODCOLIGADA = 1
					    LEFT join rms_processos with (nolock) on
							rms_processos.num_ocorrencia = registro_acidente.acidente_id
							and rms_processos.status not in ('i')
						where acidente_id = $acidente_id";
			//print $query;
			$result = odbc_exec($conSQL, $query) or die ('Erro1 ao selecionar os dados do acidente');

			$tipo_registro_id 			= odbc_result($result, 1);
			$data_inclusao	 			= odbc_result($result, 2);
			$acidente_transito	 		= odbc_result($result, 3);
			$acidente_trabalho	 		= odbc_result($result, 4);
			$avaria_carga		 		= odbc_result($result, 5);
			$atraso_entrega		 		= odbc_result($result, 6);
			$data_fato			 		= odbc_result($result, 7);
			$hora_fato			 		= odbc_result($result, 8);
			$opcao_int_ext				= odbc_result($result, 9);
			$nome_informante	 		= odbc_result($result, 10);
			$telefone_informante 		= odbc_result($result, 11);
			$opcao_ferida				= odbc_result($result, 12);
			$opcao_vaz_carr				= odbc_result($result, 13);
			$cliente_veiculo			= odbc_result($result, 14);
			$endereco_acidente			= odbc_result($result, 15);
			$bairro_acidente			= odbc_result($result, 16);
			$municipio_acidente			= odbc_result($result, 17);
			$ponto_ref_acidente			= odbc_result($result, 18);
			$opcao_quimico				= odbc_result($result, 19);
			$opcao_vazamento			= odbc_result($result, 20);
			$opcao_cond_tempo			= odbc_result($result, 21);
			$opcao_gravidade			= odbc_result($result, 22);
			$observacao					= odbc_result($result, 23);
			$responsavel_preenchimento 	= odbc_result($result, 24);
			$secao_responsavel		 	= odbc_result($result, 25);
			$status_acidente		 	= odbc_result($result, 26);
			$data_gravacao			 	= odbc_result($result, 27);
			$municipio_id			 	= odbc_result($result, 28);
			$cpf_responsavel		 	= odbc_result($result, 29);
			$acao_imediata			 	= odbc_result($result, 30);
			$patrimonio_covre			= odbc_result($result, 31);
			$patrimonio_cliente			= odbc_result($result, 32);
			$patrimonio_terceiro		= odbc_result($result, 33);
			$opcao_carregado			= odbc_result($result, 34);
			$data_sac					= odbc_result($result, 35);
			$referencia_cliente			= odbc_result($result, 36);
			$dano_covre_caminhao		= odbc_result($result, 37);
			$rms_processos_id			= odbc_result($result, 38);
			$analise_rms				= odbc_result($result, 39);
			$classificacao_registro		= odbc_result($result, 40);
		}
		else
		{
			$query = "select registro_acidente.status_id, cpf_responsavel
						from registro_acidente
						where acidente_id = $acidente_id";
			//print $query;
			$result = odbc_exec($conSQL, $query) or die ('Erro2 ao selecionar os dados do acidente');

			$status_acidente		 	= odbc_result($result, 1);
			$cpf_responsavel		 	= odbc_result($result, 2);

		}

	}





?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<link href="../SCA/includes/estilo.css" rel="stylesheet" type="text/css">

<script type="text/javascript" src="../SCA/includes/js/jquery-autocomplete/lib/jquery.js"></script>
<script type="text/javascript" src="../SCA/includes/js/jquery-autocomplete/lib/jquery.bgiframe.min.js"></script>
<script type="text/javascript" src="../SCA/includes/js/jquery-autocomplete/lib/jquery.ajaxQueue.js"></script>
<script type="text/javascript" src="../SCA/includes/js/jquery-autocomplete/lib/thickbox-compressed.js"></script>
<script type="text/javascript" src="../SCA/includes/js/jquery-autocomplete/jquery.autocomplete.js"></script>

<script type="text/javascript" src="../SCA/includes/calendario/_scripts/jquery.click-calendario-1.0-min.js"></script>
<script type="text/javascript" src="../SCA/includes/calendario/_scripts/exemplo-calendario.js"></script>

<link href="../SCA/includes/calendario/_style/jquery.click-calendario-1.0.css" rel="stylesheet" type="text/css"/>

<link rel="stylesheet" type="text/css" href="../SCA/includes/js/jquery-autocomplete/jquery.autocomplete.css"/>
<link rel="stylesheet" type="text/css" href="../SCA/includes/js/jquery-autocomplete/lib/thickbox.css"/>

<style type="text/css">
fieldset { padding: 22px 12px 12px 15px; position: relative; margin: 12px 0 0px 0px; }
</style>

<style>
.break { page-break-before: always; }
</style>

<script>

function pagina(janela, largura, altura, descricao) {
	tamanho = "height=" + altura + ",width=" + largura + ",scrollbars=yes,resizable=yes";
	window.open(janela, descricao, tamanho);
}
</script>

<script type="text/javascript">
$(document).ready(function(){
	$("#func_envolvido").autocomplete("completar_funcionario.php", {
		width:600,
		selectFirst: false
	});
});
</script>

<script type="text/javascript">
$(document).ready(function(){
	$("#veic_envolvido").autocomplete("completar_veiculo.php", {
		width:600,
		selectFirst: false
	});
});
</script>

<script type="text/javascript">
$(document).ready(function(){
	$("#municipio_acidente").autocomplete("completar_municipio.php", {
		width:600,
		selectFirst: false
	});
});
</script>

<script type="text/javascript">
$(document).ready(function(){

	$("#cliente_veiculo").autocomplete("completar_cliente.php", {
		width:600,
		selectFirst: false,
	});
	$("#cliente_veiculo").bind("result", function(event, data){
		$.ajax({
			url: 'getEnderecoFromCliente.php?cliente='+data,
			method: 'GET',
			dataType: "json",
			success: function(resp_obj){
				if(resp_obj){
					$("#endereco_acidente").val(resp_obj.logradouro);
					$("#bairro_acidente").val(resp_obj.bairro);
					$("#ponto_ref_acidente").val(resp_obj.ponto_referencia);
					$("#municipio_acidente").val(resp_obj.municipio_uf);
					$("#municipio_id").val(resp_obj.municipio_id);
				}else{
					$("#endereco_acidente").val('');
					$("#bairro_acidente").val('');
					$("#ponto_ref_acidente").val('');
					$("#municipio_acidente").val('');
					$("#municipio_id").val('');
				}
				var municipio_acidente = document.getElementById("municipio_acidente");
				valida_municipio(municipio_acidente);
			}
		});
	})

});
</script>

<script type="text/javascript">
function valida_telefone(e) {
	if (( event.keyCode < 48 || event.keyCode > 57 ) && event.keyCode!= 45 && event.keyCode!= 40 && event.keyCode!= 41 && event.keyCode!= 42)
		{event.returnValue = false;}
}
</script>

<script language="javascript">
function insere_funcionario(funcionario_id, codpessoa, tipo_envolvido, func_ferido, acidente_id, tipo_ferimento, hospital, cpf)
{
	//alert(cpf);

	if (acidente_id == '')
		var temp = 's';

	if ((funcionario_id == "") && (codpessoa == ""))
		alert(unescape('Favor inserir um Funcion%E1rio/Terceiro v%E1lido'));
	else
	if ((codpessoa != "") && (acidente_id == ""))
		alert(unescape('Obrigat&oacute;rio grava&ccedil;&atilde;o do registro para inserir este tipo de colaborador'));
	else
	if (tipo_envolvido == "")
		alert(unescape('Favor informar o Tipo Envolvido'));
	else
	if (func_ferido == "")
		alert(unescape('Favor informar se o Funcion%E1rio/Terceiro ficou ferido'));
	else
	{

		if (funcionario_id != '')
			var inserir = 'insere_funcionario.php';
		else
			var inserir = 'insere_funcionario_terceiro.php';

		$.ajax({type: "POST",//define o met�do de passagem de parametros
			url: "includes/"+inserir, //chama uma pagina
			data: "pessoa_id="+funcionario_id + "&tipo_envolvido="+tipo_envolvido+ "&func_ferido="+func_ferido + "&acidente_id="+acidente_id + "&tipo="+tipo_ferimento + "&hospital="+hospital + "&cpf="+cpf + "&codpessoa="+codpessoa,
			success: function(msg){  //pega o retorno da pagina chamada
				//alert(msg);
				if (temp != 's')
				{
					if(msg.indexOf("ok") == -1)
						alert(unescape('Envolvido j%E1 inserido anteriormente'));
					else
					{
						if (acidente_id != '')
						{
							document.form1.func_envolvido.value 	= '';
							document.form1.tipo_envolvido.value 	= '';
							document.form1.func_ferido.value 		= '';
							document.form1.funcionario_id.value 	= '';
							document.form1.codpessoa.value 			= '';
						}

						document.form1.gravar.click();

					}
				}
				else
				{
					$("#temp").html(msg);
					$("#temp").show();

					document.form1.func_envolvido.value 	= '';
					document.form1.tipo_envolvido.value 	= '';
					document.form1.func_ferido.value 		= '';
					document.form1.tipo_ferimento.value 	= '';
					document.form1.hospital.value 			= '';
					document.form1.funcionario_id.value 	= '';
					document.form1.codpessoa.value 			= '';

				}
			}
		});
	 }
}
</script>

<script language="javascript">
function insere_placa(veiculo_fornecedor_id, acidente_id, cpf)
{
	//alert(veiculo_fornecedor_id);
	if (acidente_id == '')
		var temp = 's';


	//if (acidente_id == "")
		//alert(unescape('Para incluir ve%EDculos %E9 necess%E1rio a grava%E7%E3o do registro de acidente'));
	//else

	if (veiculo_fornecedor_id == "")
		alert(unescape('Favor inserir uma placa v%E1lida'));
	else
	{
		$.ajax({type: "POST",//define o met�do de passagem de parametros
			url: "includes/insere_placa.php", //chama uma pagina
			data: "veiculo_fornecedor_id="+veiculo_fornecedor_id + "&acidente_id="+acidente_id + "&cpf="+cpf,
			success: function(msg){  //pega o retorno da pagina chamada
				//alert(msg);

				if (temp != 's')
				{
					if(msg.indexOf("ok") == -1)
						alert(unescape('Veiculo j%E1 inserido anteriormente'));
					else
						document.form1.gravar.click();
				}
				else
				{
					$("#temp_veiculo").html(msg);
					$("#temp_veiculo").show();

					document.form1.veic_envolvido.value 	= '';


				}


			}
		});
	 }
}
</script>

<script language="javascript">
function insere_nota(nota_fiscal, acidente_id, cpf)
{
	//alert(veiculo_fornecedor_id);
	if (acidente_id == '')
		var temp = 's';


	if (nota_fiscal == "")
		alert(unescape('Favor inserir a nota fiscal'));
	else
	{
		$.ajax({type: "POST",//define o met�do de passagem de parametros
			url: "includes/insere_nota.php", //chama uma pagina
			data: "nota_fiscal="+nota_fiscal + "&acidente_id="+acidente_id + "&cpf="+cpf,
			success: function(msg){  //pega o retorno da pagina chamada
				//alert(msg);

				if (temp != 's')
				{
					if(msg.indexOf("ok") == -1)
						alert(unescape('Nota Fiscal j%E1 inserida anteriormente'));
					else
					{
						document.form1.nota_fiscal.value 	= '';
						document.form1.gravar.click();
					}
				}
				else
				{
					$("#temp_nota_fiscal").html(msg);
					$("#temp_nota_fiscal").show();

					document.form1.nota_fiscal.value 	= '';


				}


			}
		});
	 }
}
</script>



<script language="javascript">
function busca_pessoa_id(elmnt)
{
	//alert(elmnt.value);
	if (elmnt.value != ""){
		$.ajax({type: "POST",//define o met�do de passagem de parametros
			url: "includes/busca_pessoa_id.php", //chama uma pagina
			data: "nome="+elmnt.value, //passa os parametros, se necess�rio
			success: function(msg){  //pega o retorno da pagina chamada
				//alert(msg);
				var dados = msg.split("|");

				if(dados[1].indexOf("invalido") == -1)
				{
					if (dados[0] == 1)
						document.form1.funcionario_id.value	= dados[1];
					else
						document.form1.codpessoa.value	= dados[1];
				}
				else
				{
					document.form1.funcionario_id.value	= '';
					document.form1.codpessoa.value	= '';
				}
			}
		});
	}
}
</script>

<script language="javascript">
function busca_veiculo_id(elmnt)
{
	//alert(elmnt.value);
	if (elmnt.value != "")
	{
		if (elmnt.value.length < 7)
			document.form1.veic_envolvido.focus();
		else
		{

			$.ajax({type: "POST",//define o met�do de passagem de parametros
				url: "includes/busca_veiculo_id.php", //chama uma pagina
				data: "placa="+elmnt.value, //passa os parametros, se necess�rio
				success: function(msg){  //pega o retorno da pagina chamada

								//alert(msg);
								var dados = msg.split("|");

								if(dados[1].indexOf("invalido") == -1)
									document.form1.veiculo_fornecedor_id.value	= dados[1];
								else
								{
									alert(unescape("Placa inv%E1lida ou n%E3o cadastrada"));
									document.form1.veic_envolvido.value	= '';
									document.form1.veiculo_fornecedor_id.value	= '';
									document.form1.veic_envolvido.focus();
								}
				}
			});
		}
	}
}
</script>

<script language="javascript">
function valida_municipio(elmnt)
{
	//alert(elmnt.value);
	if (elmnt.value != ""){
		$.ajax({type: "POST",//define o met�do de passagem de parametros
			url: "includes/valida_municipio.php", //chama uma pagina
			data: "municipio="+elmnt.value, //passa os parametros, se necess�rio
			success: function(msg){  //pega o retorno da pagina chamada

							//alert(msg);
							var dados = msg.split("|");

							if(dados[1].indexOf("invalido") != -1)
							{
								//alert(unescape("Munic%EDpio inv%E1lido ou n%E3o cadastrado"));
								//document.form1.municipio_acidente.value	= '';
								document.form1.municipio_id.value	= '';
								//document.form1.municipio_acidente.focus();
							}
							else
								document.form1.municipio_id.value	= dados[1];

			}
		});
	}
}
</script>

<script language="javascript">
function exclui_envolvido(id, acidente_id, tela, tipo_func_env)
{
	if (id != "")
	{
		$.ajax({type: "POST",//define o met�do de passagem de parametros
			url: "includes/exclui_envolvido.php", //chama uma pagina
			data: "id="+id+"&acidente_id="+acidente_id+"&tela="+tela+"&tipo_func_env="+tipo_func_env,
			success: function(msg){  //pega o retorno da pagina chamada
            	//alert (msg);
				window.location.href='registro_acidente_parte1.php?id=<?php print $acidente_id ?>&si=<?php print $sistema_id ?>';

		    }
		});
	 }
}

</script>

<script language="javascript">
function exclui_placa(id, acidente_id)
{
	if (id != "")
	{
		$.ajax({type: "POST",//define o met�do de passagem de parametros
			url: "includes/exclui_placa.php", //chama uma pagina
			data: "id="+id + "&acidente_id="+acidente_id, //passa os parametros, se necess�rio
			success: function(msg){  //pega o retorno da pagina chamada
            	//alert (msg);
            	document.form1.gravar.click();

		    }
		});
	 }
}

</script>

<script language="javascript">
function exclui_nota(id, acidente_id)
{
	if (id != "")
	{
		$.ajax({type: "POST",//define o met�do de passagem de parametros
			url: "includes/exclui_nota.php", //chama uma pagina
			data: "id="+id + "&acidente_id="+acidente_id, //passa os parametros, se necess�rio
			success: function(msg){  //pega o retorno da pagina chamada
            	//alert (msg);
            	document.form1.gravar.click();

		    }
		});
	 }
}

</script>

<script language="javascript">
function habilita_cliente()
{
	//alert();
	if(document.form1.radio2[1].checked == 1)
	{
		document.form1.radio7[0].disabled = false;
		document.form1.radio7[1].disabled = false;
	}
    else
	{
  	  	document.form1.radio7[0].disabled = true;
  	  	document.form1.radio7[1].disabled = true;
	}
}
</script>


<script language="javascript">
function impressao()
{
	$("#impress").hide();
	window.print();
	$("#impress").show();
}
</script>

<!--<script language="javascript">
function desabilita_tipo_acidente(tipo_acidente)
{
	if (tipo_acidente == 1)
	{
		document.form1.acidente_transito.checked = 1;
		document.form1.acidente_transito.disabled = true;
	}
	else
		if (tipo_acidente == 2)
		{
			document.form1.acidente_trabalho.checked = 1;
			document.form1.acidente_trabalho.disabled = true;
		}
		else
			if (tipo_acidente == 3)
			{
				document.form1.avaria_carga.checked = 1;
				document.form1.avaria_carga.disabled = true;
			}
			else
				if (tipo_acidente == 4)
				{
					document.form1.atraso_entrega.checked = 1;
					document.form1.atraso_entrega.disabled = true;
				}
}
</script>
 -->
<script language="javascript">
function exclui_acidente(elmnt){
	if (confirm(unescape("Deseja realmente excluir o registro de acidente?")))
	{
			//alert("entrou na exclus�o");
			if (elmnt.value != ""){
				$.ajax({type: "POST",//define o met�do de passagem de parametros
					url: "includes/exclui_acidente.php", //chama uma pagina
					data: "acidente_id="+elmnt, //passa os parametros, se necess�rio
					success: function(msg){  //pega o retorno da pagina chamada
						alert(msg);
						window.location.href='registro_acidente_parte2.php?id=<?php print $acidente_id ?>&si=<?php print $sistema_id ?>';

					}
				});
			}

	}

}
</script>

<script language="javascript">
function verifica_data_fato (elmnt, dt_registro) {
    var data_1 = new Date(elmnt.value);
    var data_2 = new Date(dt_registro);
    if (data_1 > data_2)
	{
        alert(unescape("Data do fato n%E3o pode ser maior que data do registro"));
        return false;
		document.form1.data_fato.focus();
    }
	else
	{
        return true
		verifica_data_1();
    }
}
</script>


<script language="javascript">
function habilita_combo()
{
	if (document.form1.patrimonio_covre.checked == 1)
		document.form1.dano_covre_caminhao.disabled = false;
    else
  	  document.form1.dano_covre_caminhao.disabled = true;

}
</script>


<script language="javascript">
function alert_anexos()
{
	alert(unescape('Para incluir anexos %E9 necess%E1rio a grava%E7%E3o do registro de acidente'));
}
</script>

<script language="javascript">
function alert_anexos1()
{
	alert(unescape('Este registro n%E3o tem logs de altera%E7%E3o'));
}
</script>

</head>
<body>
<form action="" name="form1" method="post" enctype="multipart/form-data" style="width:1150px"  >
<fieldset>
  <center><b><font size="+2" color="#0000CC">Registro de Ocorr&ecirc;ncia</font></b></center>	
  <p>&nbsp;</p> 
      <!--<table width="100" height="22" border="1">
    <tr>
      <td width="240"><center>
        <a href="lancamento_acidente.php"><strong>Anexos</strong></a>
      </center></td>
     </tr>
  </table> -->
    <table width="1150">
      <tr>
        <td width="165" height="27"><p><font size="-1"><strong>Ocorrência N&uacute;mero:</strong></font></p></td>
        <td width="193" valign="middle"><font color="#0000FF"><b><?php print $acidente_id ?></b></font>
        <input align="center" name="acidente_id" type="hidden" id="acidente_id" size="06" maxlength="12" value="<?php print $acidente_id ?>" /></td>
        <td width="152"><font size="-1"><font color="red">* </font><b>Tipo de Registro:</b></font></td>
        <td width="339">
			<?php

				if (($tipo_registro_id == 3) || ($tipo_registro_id == 4))
					$condicao_tipo_registro = "where id = '$tipo_registro_id'";
				else
					$condicao_tipo_registro = "where id not in (3,4)";

					$query = "select id, descricao collate sql_latin1_general_cp1251_ci_as
							  from tipo_registro_acidente
							  $condicao_tipo_registro";
					//print $query;
					$result = odbc_exec($conSQL, $query);

					print "<select name='tipo_registro_id' id='tipo_registro_id' class='lista' ><option value=''></option>";

					while(odbc_fetch_array($result))
					{
						if (odbc_result($result, 1) == $tipo_registro_id)
							$selected = "selected='selected'";
						else
							$selected = "";

						 print "<option value='".odbc_result($result, 1)."'$selected>".odbc_result($result, 2)."</option>";
					}
					print"</select>";
			 ?>
        </td>
        <td width="153"><strong><font size="-1">Data da Inclus&atilde;o:</font></strong></td>
        <td width="120">
		<?php
	  	if ($acidente_id == '')
			print date('d/m/Y');
		else
		    print rtrim($data_inclusao);
	    ?></td>
      </tr>
      <tr>
        <td height="14">&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td><input align="center" name="data_gravacao" type="hidden" id="data_gravacao" size="17" value="<?php print $data_gravacao ?>" readonly="readonly" /></td>
      </tr>

    </table>

   <table width="1150" border="1">
          <td width="200" height="33">
        	<table width="185" height="50" border="0">
            	<tr>
                	<td width="322" height="22"><font size="-1"><font color="red"> *</font> Data/Hora Fato: </font></td>
               	</tr>
            	<tr>
            	  <td height="22"><font size="-1">
            	    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php print $data_fato ?>"
           	      <?php print $hora_fato ?>
            	  </font></td>
          	  </tr>
           	</table>
        </td>

        <td width="621">
        	<table width="776" height="50" border="0">
            	<tr>
                	<td width="491" height="22"><font size="-1"><font color="red"> *</font> Dados do Informante: </font></td>
                	<td width="275"><font size="-1"><font color="red">&nbsp;</font></font></td>
               	</tr>
            	<tr>
            	  <td height="22"><font size="-1">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Nome:
                      <?php print $nome_informante ?>
                  </font></td>
            	  <td><font size="-1"><font color="red">&nbsp;&nbsp;&nbsp;&nbsp;</font> Telefone:
                      <?php print $telefone_informante ?>
                  </font></td>
          	  </tr>

           	</table>

        </td>
      </tr>
   </table>
<p>&nbsp;</p>
    <p></p>
    <table width="1150" border="1" frame="box" rules="none">
      <tr>
        <td width="1160" height="26" colspan="6">
			<?php

                $query = "select DISTINCT PESSOA.NOME collate sql_latin1_general_cp1251_ci_as,
								CASE CO.TAB_TIPO_VINCULO_ID
									WHEN 1 THEN PFUNCAO.NOME collate sql_latin1_general_cp1251_ci_as
									WHEN 2 THEN 'MOTORISTA AGREGADO'
									WHEN 3 THEN 'MOTORISTA TERCEIRO'
								END FUNCAO,

								CASE WHEN ((CO.Tab_Tipo_Colaborador_Id = 3) AND (CO.Tab_Tipo_Vinculo_Id <> 1))
										then 'Motorista'
										else PSECAO.DESCRICAO  collate sql_latin1_general_cp1251_ci_as
								end secao,

								CASE WHEN FA.FUNC_FERIDO = 's'
                                        THEN 'Sim'
                                        ELSE 'Nao'
                                END,
								FA.ID,
								TIPO_FERIMENTO,
								HOSPITAL,
								pessoa.pessoa_id,
								tipo_envolvido.descricao tipo_envolvido,
								NULL codpessoa
                                from cargosol..PESSOA with (nolock)
								JOIN CARGOSOL..COLABORADOR CO WITH (NOLOCK) ON
									CO.COLABORADOR_ID = PESSOA.PESSOA_ID
                                LEFT JOIN CORPORE..PFUNC WITH (NOLOCK) ON
                                    PFUNC.CHAPA = CO.NUM_FUNCIONARIO_ALFA COLLATE SQL_Latin1_General_CP1_CI_AI
                                    AND PFUNC.CODCOLIGADA = 1
                                    AND PFUNC.CODSITUACAO <> 'D'
									AND PFUNC.CODTIPO <> 'A'
                                LEFT JOIN CORPORE..PSECAO WITH (NOLOCK) ON
                                    PSECAO.CODIGO = PFUNC.CODSECAO
                                    AND PSECAO.CODCOLIGADA = 1
                                JOIN FERIDOS_ACIDENTE FA WITH (NOLOCK) ON
                                    FA.PESSOA_ID = PESSOA.PESSOA_ID
                                LEFT JOIN CORPORE..PFUNCAO WITH (NOLOCK) ON
                                    PFUNCAO.CODIGO = PFUNC.CODFUNCAO
                                    AND PFUNCAO.CODCOLIGADA = 1
								LEFT JOIN tipo_envolvido_acidente tipo_envolvido with (nolock) on
									tipo_envolvido.id = FA.TIPO_ENVOLVIDO_ID
									and tipo_envolvido.status_id = 'a'
                            where FA.ACIDENTE_ID = '$acidente_id'
							and FA.status_id = 'a'

							UNION

							SELECT DISTINCT PPESSOA.NOME collate sql_latin1_general_cp1251_ci_as,
								'FUNCIONARIO TERCEIRIZADO' FUNCAO,
								NULL secao,
								CASE WHEN FAT.FUNC_FERIDO = 's'
                                        THEN 'Sim'
                                        ELSE 'Nao'
                                END,
								FAT.ID,
								TIPO_FERIMENTO,
								HOSPITAL,
								NULL,
								tipo_envolvido.descricao tipo_envolvido,
								FAT.codpessoa
                                from CORPORE..PPESSOA WITH (NOLOCK)
                                JOIN FERIDOS_ACIDENTE_TERCEIRO FAT WITH (NOLOCK) ON
                                    FAT.CODPESSOA = PPESSOA.CODIGO
								LEFT JOIN tipo_envolvido_acidente tipo_envolvido with (nolock) on
									tipo_envolvido.id = FAT.TIPO_ENVOLVIDO_ID
									and tipo_envolvido.status_id = 'a'
                            where FAT.ACIDENTE_ID = '$acidente_id'
							and FAT.status_id = 'a'

							UNION

							SELECT DISTINCT PESSOA.NOME collate sql_latin1_general_cp1251_ci_as,
								CASE CO.TAB_TIPO_VINCULO_ID
									WHEN 1 THEN PFUNCAO.NOME collate sql_latin1_general_cp1251_ci_as
									WHEN 2 THEN 'MOTORISTA AGREGADO'
									WHEN 3 THEN 'MOTORISTA TERCEIRO'
								END FUNCAO,

								CASE WHEN ((CO.Tab_Tipo_Colaborador_Id = 3) AND (CO.Tab_Tipo_Vinculo_Id <> 1))
										then 'Motorista'
										else PSECAO.DESCRICAO  collate sql_latin1_general_cp1251_ci_as
								end secao,

								CASE WHEN FAT.FUNC_FERIDO = 's'
                                        THEN 'Sim'
                                        ELSE 'Nao'
                                END,
								NULL,
								TIPO_FERIMENTO,
								HOSPITAL,
								pessoa.pessoa_id,
								tipo_envolvido.descricao tipo_envolvido,
								NULL codpessoa
							from cargosol..PESSOA with (nolock)
							JOIN CARGOSOL..COLABORADOR CO WITH (NOLOCK) ON
								CO.COLABORADOR_ID = PESSOA.PESSOA_ID
                            LEFT JOIN CORPORE..PFUNC WITH (NOLOCK) ON
                                PFUNC.CHAPA = CO.NUM_FUNCIONARIO_ALFA COLLATE SQL_Latin1_General_CP1_CI_AI
                                AND PFUNC.CODCOLIGADA = 1
                                AND PFUNC.CODSITUACAO <> 'D'
								AND PFUNC.CODTIPO <> 'A'
							LEFT JOIN CORPORE..PSECAO WITH (NOLOCK) ON
								PSECAO.CODIGO = PFUNC.CODSECAO
								AND PSECAO.CODCOLIGADA = 1
							JOIN FERIDOS_ACIDENTE_TEMP FAT WITH (NOLOCK) ON
								FAT.PESSOA_ID = PESSOA.PESSOA_ID
							LEFT JOIN CORPORE..PFUNCAO WITH (NOLOCK) ON
								PFUNCAO.CODIGO = PFUNC.CODFUNCAO
								AND PFUNCAO.CODCOLIGADA = 1
							LEFT JOIN tipo_envolvido_acidente tipo_envolvido with (nolock) on
								tipo_envolvido.id = FAT.TIPO_ENVOLVIDO_ID
								and tipo_envolvido.status_id = 'a'
						where FAT.CPF = '$cpf'";
                //print "<pre>$query</pre>";
                $result = odbc_exec($conSQL, $query);


                print "<table width='1140' border='1' >";

				if ($acidente_id != '')
				print "
					  <tr>
						<td bgcolor='#CCCCCC'><div align='center'><strong>
							<font size='-2'>FUNCION&Aacute;RIO ENVOLVIDO </font></strong></div></td>
						<td bgcolor='#CCCCCC'><div align='center'><p align='center'><strong>
							<font size='-2'>FUN&Ccedil;&Atilde;O</font></strong></p></div></td>
						<td bgcolor='#CCCCCC'><div align='center'><strong>
							<font size='-2'>SE&Ccedil;&Atilde;O</font></strong></div></td>
						<td bgcolor='#CCCCCC'><div align='center'><p align='center'><strong>
							<font size='-2'>TIPO ENVOLVIDO</font></strong></p></div></td>
						<td bgcolor='#CCCCCC'><div align='center'><p align='center'><strong>
							<font size='-2'>FERIDO</font></strong></p></div></td>
						<td bgcolor='#CCCCCC'><div align='center'><p align='center'><strong>
							<font size='-2'>TIPO FERIMENTO</font></strong></p></div></td>
						<td bgcolor='#CCCCCC'><div align='center'><p align='center'><strong>
							<font size='-2'>HOSPITAL</font></strong></p></div></td>
						<td width='60' bgcolor='#CCCCCC'><div align='center'><p align='center'>
							<font color='black' size='-2'><b>EXCLUIR</b></font></p></div></td>
					  </tr>";

                 while(odbc_fetch_row($result))
                 {
                       $nome_envolvido 		= odbc_result($result,1);
                       $funcao				= odbc_result($result,2);
                       $secao		 		= odbc_result($result,3);
                       $ferido		 		= odbc_result($result,4);
					   $ferido_acidente_id	= odbc_result($result,5);
					   $tipo_ferimento		= odbc_result($result,6);
					   $hospital			= odbc_result($result,7);
					   $pessoa_id_env		= odbc_result($result,8);
					   $desc_tipo_env		= odbc_result($result,9);
					   $codpessoa_env		= odbc_result($result,10);

					   if ($pessoa_id_env == $pessoa_id_logado)
					   		$habilita_gravar	= 'S';

					   if ($pessoa_id_env != '')
					   		$tipo_func_env = 1;
					   else
					   		$tipo_func_env = 2;


                      print "<tr>
                             <td bgcolor='#FFFFFF'><center><font size='-2'>".$nome_envolvido."</center></b></td>
                             <td bgcolor='#FFFFFF'><center><font size='-2'>".$funcao."</center></b></td>
                             <td bgcolor='#FFFFFF'><center><font size='-2'>".$secao."</center></b></td>
							 <td bgcolor='#FFFFFF'><center><font size='-2'>".$desc_tipo_env."</center></b></td>
                             <td bgcolor='#FFFFFF'><center><font size='-2'>".$ferido."</center></b></td>
                             <td bgcolor='#FFFFFF'><center><font size='-2'>".$tipo_ferimento."</center></b></td>
                             <td bgcolor='#FFFFFF'><center><font size='-2'>".$hospital."</center></b></td>";

							 if ((($status_acidente == 'p') || ($status_acidente == 'r')) && ($somente_leitura == ''))
								 print "
								 <td bgcolor='#FFFFFF'><center><font size='-2'><a href='javascript:exclui_envolvido($ferido_acidente_id, $acidente_id, 1, $tipo_func_env);'>
									<img src='../SCA/images/excluir1.jpg' width='25' heigth='25' style='border:none'></a></center></b></td>";
							 else
								 print "
								 <td bgcolor='#FFFFFF'><center><font size='-2'> - </center></b></td>";


                       print "</tr>";

                 }
                 print "</table>";

            ?>
            <div id="temp"></div>
        </td>
      </tr>
    </table>
    <p>&nbsp;</p>
    <table width="1150" border="1" frame="box" rules="none">
      <tr>
        <td width="440"><div align="left"><font size="-1"><font color="red"> *</font></font> <font size="-1">H&aacute; outras pessoas feridas?</font>
          &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
          <input type="radio" name="radio1" id="feridas_sim" value="s" <?php if ($opcao_ferida == 's'){print "checked='checked'";} ?>/>
          <font size="-2">Sim</font><font size="-1">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</font>
          <input type="radio" name="radio1" id="feridas_nao" value="n"<?php if ($opcao_ferida == 'n'){print "checked='checked'";} ?>/>
          <font size="-2">N&atilde;o</font></div></td>
        <td width="198">&nbsp;</td>
        <td width="540"><font size="-1" color="#FF0000"><font color="#FF0000">Se houver outros feridos o Coordenador PAE Nacional ser&aacute; comunicado</font></td>
      </tr>
    </table>

    <p>&nbsp;</p>
    <table width="1150"  border="1" frame="box" rules="none">
      <tr>
        <td colspan="3">
			<?php

                $query = "SELECT VF.PLACA,
							CASE WHEN VF.TAB_TIPO_PROP_VEICULO_ID = 1
									THEN 'EMPRESA DE TRANSPORTES COVRE LTDA'
									ELSE ISNULL(PROPRIETARIO.NOME, '&nbsp;')
							END,
							PONTO_OPERACAO.NOME_FANTASIA, TTV.DESC_TIPO_VEICULO, VA.ID
							FROM CARGOSOL..VEICULO_FORNECEDOR VF WITH (NOLOCK)
							LEFT JOIN CARGOSOL..PESSOA PROPRIETARIO WITH (NOLOCK) ON
								PROPRIETARIO.PESSOA_ID = VF.FORNECEDOR_ID
							LEFT JOIN CARGOSOL..PESSOA NOME_PROP WITH (NOLOCK) ON
								NOME_PROP.PESSOA_ID = PROPRIETARIO.PESSOA_ID
							JOIN CARGOSOL..PONTO_OPERACAO PO WITH (NOLOCK) ON
								PO.PONTO_OPERACAO_ID = VF.PONTO_OPERACAO_ID
							JOIN CARGOSOL..PESSOA PONTO_OPERACAO WITH (NOLOCK) ON
								PONTO_OPERACAO.PESSOA_ID = PO.PESSOA_ID
							JOIN CARGOSOL..TAB_TIPO_VEICULO TTV WITH (NOLOCK) ON
								TTV.TAB_TIPO_VEICULO_ID = VF.TAB_TIPO_VEICULO_ID
							JOIN VEICULOS_ACIDENTE VA WITH (NOLOCK) ON
								VA.VEICULO_FORNECEDOR_ID = VF.VEICULO_FORNECEDOR_ID
							WHERE VA.ACIDENTE_ID = '$acidente_id'
							and VA.status_id = 'a'

							UNION

							SELECT VF.PLACA,
							CASE WHEN VF.TAB_TIPO_PROP_VEICULO_ID = 1
									THEN 'EMPRESA DE TRANSPORTES COVRE LTDA'
									ELSE ISNULL(PROPRIETARIO.NOME, '&nbsp;')
							END,
							PONTO_OPERACAO.NOME_FANTASIA, TTV.DESC_TIPO_VEICULO, NULL
							FROM CARGOSOL..VEICULO_FORNECEDOR VF WITH (NOLOCK)
							LEFT JOIN CARGOSOL..PESSOA PROPRIETARIO WITH (NOLOCK) ON
								PROPRIETARIO.PESSOA_ID = VF.FORNECEDOR_ID
							LEFT JOIN CARGOSOL..PESSOA NOME_PROP WITH (NOLOCK) ON
								NOME_PROP.PESSOA_ID = PROPRIETARIO.PESSOA_ID
							JOIN CARGOSOL..PONTO_OPERACAO PO WITH (NOLOCK) ON
								PO.PONTO_OPERACAO_ID = VF.PONTO_OPERACAO_ID
							JOIN CARGOSOL..PESSOA PONTO_OPERACAO WITH (NOLOCK) ON
								PONTO_OPERACAO.PESSOA_ID = PO.PESSOA_ID
							JOIN CARGOSOL..TAB_TIPO_VEICULO TTV WITH (NOLOCK) ON
								TTV.TAB_TIPO_VEICULO_ID = VF.TAB_TIPO_VEICULO_ID
							JOIN VEICULOS_ACIDENTE_TEMP VAT WITH (NOLOCK) ON
								VAT.VEICULO_FORNECEDOR_ID = VF.VEICULO_FORNECEDOR_ID
							WHERE CPF = '$cpf'
							";
                //print $query;
                $result = odbc_exec($conSQL, $query);


                print "<table width='1140' border='1' >";

				if ($acidente_id != '')

				print "
                  <tr>
                    <td width='100' bgcolor='#CCCCCC'><div align='center'><strong><font size='-2'>PLACA</font></strong></div></td>
                    <td bgcolor='#CCCCCC'>
						<div align='center'><p align='center'><strong><font size='-2'>PROPRIET&Aacute;RIO</font></strong></p></div></td>
                    <td bgcolor='#CCCCCC'><div align='center'><strong><font size='-2'>PONTO OPERA&Ccedil;&Atilde;O</font></strong></div></td>
                    <td bgcolor='#CCCCCC'>
						<div align='center'><p align='center'><strong><font size='-2'>TIPO Veículo</font></strong></p></div></td>
                    <td width='60' bgcolor='#CCCCCC'><div align='center'><p align='center'><font color='black' size='-2'><b>EXCLUIR</b></font></p></div></td>
                  </tr>";


                 while(odbc_fetch_row($result))
                 {
                       $placa 				= odbc_result($result,1);
                       $proprietario		= odbc_result($result,2);
                       $ponto_operacao_veic	= odbc_result($result,3);
                       $tipo_veiculo		= odbc_result($result,4);
					   $veiculo_acidente_id	= odbc_result($result,5);

                       print "<tr>
                             <td bgcolor='#FFFFFF'><center><font size='-2'>".$placa."</center></b></td>
                             <td bgcolor='#FFFFFF'><center><font size='-2'>".$proprietario."</center></b></td>
                             <td bgcolor='#FFFFFF'><center><font size='-2'>".$ponto_operacao_veic."</center></b></td>
                             <td bgcolor='#FFFFFF'><center><font size='-2'>".$tipo_veiculo."</center></b></td>";

					   if ((($status_acidente == 'p') || ($status_acidente == 'r')) && ($somente_leitura == ''))
                       		print "<td bgcolor='#FFFFFF'><center><font size='-2'><a href='javascript:exclui_placa($veiculo_acidente_id, $acidente_id);'>
							 		<img src='../SCA/images/excluir1.jpg' width='25' heigth='25' style='border:none'></a></center></b></td>";
					   else
                       		print "<td bgcolor='#FFFFFF'><center><font size='-2'> - </center></b></td>";

                       print "</tr>";

                 }
                 print "</table>";

            ?>
            <div id="temp_veiculo"></div>
        </td>
      </tr>
      <tr>
        <td colspan="3">&nbsp;</td>
      </tr>
      <tr>
        <td width="341"><div align="left"><font size="-1"> Veículo</a>:</font>
          <input type="radio" name="radio2" id="veiculo_vazio" value="v" <?php if ($opcao_vaz_carr == 'v'){print "checked='checked'";} ?>
          onclick="javascript:habilita_cliente(this)"/>
          <font size="-1">Vazio</font>&nbsp;&nbsp;&nbsp;
          <input type="radio" name="radio2" id="veiculo_carregado" value="c" <?php if ($opcao_vaz_carr == 'c'){print "checked='checked'";} ?>
          onclick="javascript:habilita_cliente(this)"/>
        <font size="-1">Carregado</font></div></td>
        <td width="422"><font size="-1">Local Carregamento:
          <input type="radio" name="radio7" id="carregado_covre" value="v" <?php if ($opcao_carregado == 'v'){print "checked='checked'";} ?>
          onclick="javascript:habilita_cliente(this)"/>
          <font size="-1">Covre</font>&nbsp;&nbsp;&nbsp;
          <input type="radio" name="radio7" id="carregado_cliente" value="c" <?php if ($opcao_carregado == 'c'){print "checked='checked'";} ?>
          onclick="javascript:habilita_cliente(this)"/>
        <font size="-1">Cliente</font></td>
        <td width="415"><font size="-1">Cliente: <?php print $cliente_veiculo ?>
        </font></td>
      </tr>
    </table>
    <p>&nbsp;</p>
    <table width="1150" height="69" border="1" frame="box" rules="none">
      <tr>
        <td height="20" colspan="6"><font size="-1"><font color="red">*</font> Local do Fato:&nbsp;&nbsp;&nbsp;
            <input type="radio" name="radio" id="acidente_interno" value="i" <?php if ($opcao_int_ext == 'i'){print "checked='checked'";} ?> />
            <font size="-1"> Interno &nbsp;&nbsp;&nbsp;&nbsp;</font>
            <input type="radio" name="radio" id="acidente_externo" value="e" <?php if ($opcao_int_ext == 'e'){print "checked='checked'";} ?> />
            <font size="-1">Externo &nbsp;&nbsp;&nbsp;&nbsp;
            <input type="radio" name="radio" id="acidente_em_transito" value="t" <?php if ($opcao_int_ext == 't'){print "checked='checked'";} ?> />
        <font size="-1">Em tr&acirc;nsito</font></font></font></td>
      </tr>
      <tr>
        <td width="150" height="20"><div align="left"><font size="-1">&nbsp;&nbsp;<font color="red">*</font> Endere&ccedil;o:</font></div></td>
                <td width="588"><font size="-1"><?php print $endereco_acidente ?>
        <div align="left"></div></td>
          <td width="112">&nbsp;</td>
          <td width="322">&nbsp;</td>
      </tr>
      <tr>
        <td width="150" height="20"><div align="left">&nbsp;&nbsp;<font size="-1">&nbsp;&nbsp;Bairro:</font></div></td>
        <td width="588"><font size="-1"><?php print $bairro_acidente ?>
        <div align="left"></div></td>
          <td><font size="-1"><font color="red">* </font>Munic&iacute;pio/UF:</font></td>
          <td><font size="-1"><?php print $municipio_acidente ?>
          </td>
      </tr>
         <tr>
        <td>&nbsp;&nbsp;<font size="-1">&nbsp;&nbsp;Ponto Refer&ecirc;ncia:</font></td>
        <td colspan="6"><font size="-1"><?php print $ponto_ref_acidente ?></td>
      </tr>
    </table>
    <p>&nbsp;</p>
    <table width="1150" border="1" frame="box" rules="none">
    	<tr>
       	  <td width="89"><font size="-1">Danos: </font></td>
            <td width="405"><input type="checkbox" name="patrimonio_covre" id="patrimonio_covre" <?PHP if ($patrimonio_covre == 's') print 'checked'; ?> onclick="javascript:habilita_combo()"/>
            <font size="-1">a patrim&ocirc;nio Covre<font>
            <?php

					$query = "select 's', 'Envolve caminh&atilde;o'";
					$result = odbc_exec($conSQL, $query);

					print "<select name='dano_covre_caminhao' id='dano_covre_caminhao' class='lista' disabled><option value=''></option>";

					while(odbc_fetch_array($result))
					{
						if (odbc_result($result, 1) == $dano_covre_caminhao)
							$selected = "selected='selected'";
						else
							$selected = "";

						 print "<option value='".odbc_result($result, 1)."'$selected>".odbc_result($result, 2)."</option>";
					}
					print"</select>";
			  ?></td>
                      <td width="316"><input type="checkbox" name="patrimonio_cliente" id="patrimonio_cliente" <?PHP if ($patrimonio_cliente == 's') print 'checked'; ?> />
                        <font size="-1">a patrim&ocirc;nio cliente<font></td>
                      <td width="362"><input type="checkbox" name="patrimonio_terceiro" id="patrimonio_terceiro" <?PHP if ($patrimonio_terceiro == 's') print 'checked'; ?>/>
                      <font size="-1">a patrim&ocirc;nio de terceiros (exceto cliente)</td>
        </tr>
    </table>
    <p>&nbsp;</p>
    <table width="1150" border="1" frame="box" rules="none">
      <tr>
        <td width="204"><div align="left"><font size="-1"><font color="red"> * </font>Envolve carga qu&iacute;mica?</font></div></td>
        <td width="446"><input type="radio" name="radio3" id="quimico_sim" value="s" <?php if ($opcao_quimico == 's'){print "checked='checked'";} ?>/>
          <font size="-1">Sim</font><font size="-1">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</font>
          <input type="radio" name="radio3" id="quimico_nao" value="n" <?php if ($opcao_quimico == 'n'){print "checked='checked'";} ?>/>
          <font size="-1">N&atilde;o</font></td>
        <td width="515"><font size="-1">&nbsp;<font color="#FF0000">Acionar coordenador PAE Nacional s&oacute; em caso de vazamento de qu&iacute;mico</font></td>
        <td></td>
      </tr>
      <tr>
        <td colspan="3"><div align="left"><font size="-1"><font color="red">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;* </font>H&aacute; vazamento de produto qu&iacute;mico ou &oacute;leo do tanque de combustivel?</font> <strong>&nbsp;</strong><strong></strong>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
          <input type="radio" name="radio4" id="vaza_oleo_sim" value="s" <?php if ($opcao_vazamento == 's'){print "checked='checked'";} ?>/>
          <font size="-1">Sim</font><font size="-1">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</font>
          <input type="radio" name="radio4" id="vaza_oleo_nao" value="n" <?php if ($opcao_vazamento == 'n'){print "checked='checked'";} ?>/>
          <font size="-1">N&atilde;o</font></div></td>
        <td width="7"></td>
      </tr>
      <tr>
        <td height="26"><div align="left"><font size="-1"><font color="red">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; </font>Condi&ccedil;&otilde;es do tempo:</font></div></td>
        <td colspan="2"><input type="radio" name="radio5" id="tempo_sol" value="s" <?php if ($opcao_cond_tempo == 's'){print "checked='checked'";} ?>/>
          <font size="-1">Com sol</font>&nbsp;
          <input type="radio" name="radio5" id="tempo_nublado" value="n" <?php if ($opcao_cond_tempo == 'n'){print "checked='checked'";} ?>/>
          <font size="-1">Nublado</font>&nbsp;
          <input type="radio" name="radio5" id="tempo_chovendo" value="c" <?php if ($opcao_cond_tempo == 'c'){print "checked='checked'";} ?>/>
          <font size="-1">Chovendo</font></td>
        <td></td>
      </tr>
    </table>
    <p>&nbsp;</p>
    <table width="882" border="0" frame="box">
      <tr>
        <td><font  size="-1"><strong><font color="#0000FF">Informa&ccedil;&otilde;es SAC:</strong></font></td>
      </tr>
    </table>
    <table width="1150"  border="1" frame="box" rules="none">
      <tr>
        <td width="1178" colspan="3">
			<?php

                $query = "SELECT NA.NOTA_FISCAL, NA.ID
							FROM NOTAS_ACIDENTE NA WITH (NOLOCK)
							WHERE NA.ACIDENTE_ID = '$acidente_id'
							and NA.status_id = 'a'

							UNION

							SELECT NAT.NOTA_FISCAL, NULL
							FROM NOTAS_ACIDENTE_TEMP NAT WITH (NOLOCK)
							where NAT.CPF = '$cpf'
							";
                //print $query;
                $result = odbc_exec($conSQL, $query);


                print "<table width='300' border='1' >";

				if ($acidente_id != '')

				print "
                  <tr>
                    <td bgcolor='#CCCCCC'><div align='center'><strong><font size='-2'>NOTA FISCAL</font></strong></div></td>
                    <td width='60' bgcolor='#CCCCCC'><div align='center'><p align='center'><font color='black' size='-2'><b>EXCLUIR</b></font></p></div></td>
                  </tr>";


                 while(odbc_fetch_row($result))
                 {

					   $nota_fiscal_p	 = odbc_result($result,1);
					   $nota_id		 	 = odbc_result($result,2);

                       print "<tr>
                             <td bgcolor='#FFFFFF'><center><font size='-2'>".$nota_fiscal_p."</center></b></td>";

					   if ((($status_acidente == 'p') || ($status_acidente == 'r')) && ($somente_leitura == ''))
                       		print "<td bgcolor='#FFFFFF'><center><font size='-2'>
							<a href='javascript:exclui_nota($nota_id, $acidente_id);'>
							 		<img src='../SCA/images/excluir1.jpg' width='25' heigth='25' style='border:none'></a></center></b></td>";
					   else
                       		print "<td bgcolor='#FFFFFF'><center><font size='-2'> - </center></b></td>";

                       print "</tr>";

                 }
                 print "</table>";

            ?>
            <div id="temp_nota_fiscal"></div>
        </td>
      </tr>
    </table>
    <p>&nbsp;</p>

    <table width="1150" border="1" frame="box" rules="none">
      <tr>
        <td width="845" height="26"><div align="left"><font size="-1"><font color="red">*</font></font></b><font size="-1"> Respons&aacute;vel pelo preenchimento das informa&ccedil;&otilde;es:</font></div></td>
        <td width="339" height="26">&nbsp;</td>
      </tr>
      <tr>
        <td height="26">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<font size="-1">Nome: <?php print $responsavel_preenchimento ?></td>
        <td height="26"><div align="left"><font size="-1"> Se&ccedil;&atilde;o: <?php print $secao_responsavel ?>
<label for="secao_responsavel"></label>
        </div></td>
      </tr>
    </table>
    <p>&nbsp;</p>
    <table width="1150" border="1">
      <tr>
        <td width="1150" bgcolor="#CCCCCC"><div align="center"><strong><font size="-1">Descri&ccedil;&atilde;o do Fato</font></strong></div></td>
      </tr>
      <tr>
        <td><font size="-1">
          <?php print utf8_encode($observacao) ?>
        </font></td>
      </tr>
    </table>
    <p>&nbsp;</p>
    <p>&nbsp;</p>
    <table width="1150" border="1">
      <tr>
        <td width="1150" bgcolor="#CCCCCC"><div align="center"><strong><font size="-1">A&ccedil;&atilde;o Imediata</font></strong></div></td>
      </tr>
      <tr>
        <td><font size="-1">
          <?php print utf8_encode($acao_imediata) ?>
        </font></td>
      </tr>
    </table>

<p>&nbsp;</p>
</fieldset>
<?php
}
?>

<p class="break"></p>
<p>&nbsp;</p>
<fieldset>
  <center><b><font size="+2" color="#0000CC">An&aacute;lise do Coordenador do PAE Nacional</font></b></center>	
  <p>&nbsp;</p>  
      <!--<table width="100" height="22" border="1">
    <tr>
      <td width="240"><center>
        <a href="lancamento_acidente.php"><strong>Anexos</strong></a>
      </center></td>
     </tr>
  </table> -->
    <p>&nbsp;</p>
    <table width="1150" border="1">
      <tr>
        <td bgcolor="#CCCCCC"><div align="center"><font size="-1"> <strong>RELATO DO FATO</strong></font></div></td>
      </tr>
      <tr>
        <td height="22"><font size="-1"> <?php print utf8_encode($relato_fato) ?></td>
      </tr>
    </table>
    <p>&nbsp;</p>
      <input align="center" name="analise_ppae_id" type="hidden" id="analise_ppae_id" size="06" maxlength="12" value="<?php print $analise_ppae_id ?>" />
    </p>
    <table width="1150" border="1" frame="box" rules="none">
      <tr>
        <td width="444" height="26"><font color="red" size="-1">&nbsp; </font><font size="-1">Funcion&aacute;rio/Agregado/Terceiro ferido:
          
</td>
      </tr>
      <tr>
        <td height="26">
        <?php 
            
                $query = "select DISTINCT PPESSOA.NOME collate sql_latin1_general_cp1251_ci_as, PFUNCAO.NOME collate sql_latin1_general_cp1251_ci_as FUNCAO, 
								PSECAO.DESCRICAO collate sql_latin1_general_cp1251_ci_as,
                                CASE WHEN FA.FUNC_FERIDO = 's'
                                        THEN 'Sim'
                                        ELSE 'Nao'
                                END,
								FA.ID,
								TIPO_FERIMENTO,
								HOSPITAL,
								FUNC_FERIDO,
								FA.PESSOA_ID,
								NULL
                                from corpore..PPESSOA with (nolock)
                                join CARGOSOL..PESSOA with (nolock) on
                                    PESSOA.pf_cpf = PPESSOA.CPF collate SQL_Latin1_General_CP1_CI_AS
                                JOIN CORPORE..PFUNC WITH (NOLOCK) ON
                                    PFUNC.CODPESSOA = PPESSOA.CODIGO
                                    AND PFUNC.CODCOLIGADA = 1
                                    AND PFUNC.CODSITUACAO <> 'D'
                                JOIN CORPORE..PSECAO WITH (NOLOCK) ON
                                    PSECAO.CODIGO = PFUNC.CODSECAO
                                    AND PSECAO.CODCOLIGADA = 1
                                JOIN FERIDOS_ACIDENTE FA WITH (NOLOCK) ON
                                    FA.PESSOA_ID = PESSOA.PESSOA_ID
                                JOIN CORPORE..PFUNCAO WITH (NOLOCK) ON
                                    PFUNCAO.CODIGO = PFUNC.CODFUNCAO
                                    AND PFUNCAO.CODCOLIGADA = 1
                            where FA.ACIDENTE_ID = $acidente_id
							and FA.status_id = 'a'

							UNION

							select DISTINCT PPESSOA.NOME collate sql_latin1_general_cp1251_ci_as, 'FUNCIONARIO TERCEIRIZADO', 
								NULL,
                                CASE WHEN FAT.FUNC_FERIDO = 's'
                                        THEN 'Sim'
                                        ELSE 'Nao'
                                END,
								FAT.ID,
								TIPO_FERIMENTO,
								HOSPITAL,
								FUNC_FERIDO,
								NULL,
								FAT.CODPESSOA
                                from CORPORE..PPESSOA with (nolock)
                                JOIN FERIDOS_ACIDENTE_TERCEIRO FAT WITH (NOLOCK) ON
                                    FAT.CODPESSOA = PPESSOA.CODIGO
                            where FAT.ACIDENTE_ID = $acidente_id
							and FAT.status_id = 'a'						
							
							";
                //print $query;
                $result = odbc_exec($conSQL, $query);    
            
            
                print "<table width='1140' border='1' >
                  <tr>
                    <td bgcolor='#CCCCCC'><div align='center'><strong><font size='-2'>FUNCION&Aacute;RIO ENVOLVIDO </font></strong></div></td>
                    <td bgcolor='#CCCCCC'>
						<div align='center'><p align='center'><strong><font size='-2'>FUN&Ccedil;&Atilde;O</font></strong></p></div></td>
                    <td bgcolor='#CCCCCC'><div align='center'><strong><font size='-2'>SE&Ccedil;&Atilde;O</font></strong></div></td>
                    <td bgcolor='#CCCCCC'><div align='center'><p align='center'><strong><font size='-2'>FERIDO</font></strong></p></div></td>
                    <td bgcolor='#CCCCCC'><div align='center'><p align='center'><strong><font size='-2'>TIPO FERIMENTO</font></strong></p></div></td>
                    <td bgcolor='#CCCCCC'><div align='center'><p align='center'><strong><font size='-2'>HOSPITAL</font></strong></p></div></td>										
                    <td width='60' bgcolor='#CCCCCC'><div align='center'><p align='center'><font color='black' size='-2'><b>EXCLUIR</b></font></p></div></td>
                  </tr>";
                  
                  
                 while(odbc_fetch_row($result))
                 {
                       $nome_envolvido 		= odbc_result($result,1);
                       $funcao				= odbc_result($result,2);
                       $secao		 		= odbc_result($result,3);
                       $ferido		 		= odbc_result($result,4);
					   $ferido_acidente_id	= odbc_result($result,5);		   
					   $tipo_ferimento		= odbc_result($result,6);	
					   $hospital			= odbc_result($result,7);	
					   $func_ferido_sn		= odbc_result($result,8);
					   $pessoa_id_env		= odbc_result($result,9);	
					   $codpessoa_env		= odbc_result($result,10);						   						   					
					   
					   if ($pessoa_id_env != '')
					   		$tipo_func_env = 1;
					   else	
					   		$tipo_func_env = 2;					      					   
            
					   $lista_opcao_sn = str_replace("selected = 'selected'","", $lista_opcao_sn);
					   $lista_opcao_sn = str_replace("value='$func_ferido_sn'","value='$func_ferido_sn' selected = 'selected'", $lista_opcao_sn);
			
			
                    print "<tr>
                             <td bgcolor='#FFFFFF'><center><font size='-2'>".$nome_envolvido."</center></b></td>
                             <td bgcolor='#FFFFFF'><center><font size='-2'>".$funcao."</center></b></td>
                             <td bgcolor='#FFFFFF'><center><font size='-2'>".$secao."</center></b></td>";

							  
							 if (($status_id != 'f') && ($coordenador_ppae == 'S'))
							 {
								 print "
								 <td bgcolor='#FFFFFF'><center><font size='-2'>
										<select name='ferido$ferido_acidente_id' id='ferido$ferido_acidente_id' class='linha$ferido_acidente_id' 
										onchange='javascript: altera_ferido(this.value, $ferido_acidente_id, 1, $acidente_id, $tipo_func_env)'>
															$lista_opcao_sn
										</select>
								 </td>
							 	
								 <td bgcolor='#FFFFFF'><center>
							 	 <input name='tipo_ferimento$tipo_ferimento' type='text' id='tipo_ferimento$tipo_ferimento' value='$tipo_ferimento' size='20' 
						   		 onblur='javascript: altera_ferido(this.value, $ferido_acidente_id, 2, $acidente_id, $tipo_func_env)' /></td>							 

								 <td bgcolor='#FFFFFF'><center>
									<input name='hospital$hospital' type='text' id='hospital$hospital' value='$hospital' size='20' 
									onblur='javascript: altera_ferido(this.value, $ferido_acidente_id, 3, $acidente_id,$tipo_func_env)' /></td>	
	
								 <td bgcolor='#FFFFFF'><center><font size='-2'><a href='javascript:exclui_envolvido($ferido_acidente_id, $acidente_id, 2, $tipo_func_env);'>
									<img src='../SCA/images/excluir1.jpg' width='25' heigth='25' style='border:none'></a></center></b></td>";
							 }
							 else
							 {
							 	print "
								 <td bgcolor='#FFFFFF'><center><center><font size='-2'>".$ferido."</center></b></td>					
								 <td bgcolor='#FFFFFF'><center><center><font size='-2'>".$tipo_ferimento."</center></b></td>							 								 								 <td bgcolor='#FFFFFF'><center><center><font size='-2'>".$hospital."</center></b></td>							 								 								 <td bgcolor='#FFFFFF'><center><center><font size='-2'> - </center></b></td>";
							 }		
							 					 
                           print "</tr>";
            
                 }
                 print "</table>";
            
            ?></td>
      </tr>
    </table>
    <p>&nbsp;</p>
    <table width="1150" border="1" frame="box" rules="none">
      <tr>
        <td width="366" height="26"><font size="-1">Outros feridos:</font></td>
        <td width="277">&nbsp;</td>
        <td width="460" height="26">&nbsp;</td>
        <td width="69">&nbsp;</td>
      </tr>
      <tr>
        <td height="26" colspan="4">


		  <?php 
            
                $query = "select id, nome, telefone, tipo_ferimento
						  from feridos_acidente_ppae
						  where acidente_id = $acidente_id
						  and status_id = 'a'";
                //print $query;
                $result = odbc_exec($conSQL, $query);    
            
            
                print "<table width='1140' border='1' >
                  <tr>
                    <td bgcolor='#CCCCCC'><div align='center'><strong><font size='-2'>NOME</font></strong></div></td>
                    <td bgcolor='#CCCCCC'><div align='center'><p align='center'><strong><font size='-2'>TELEFONE</font></strong></p></div></td>
                    <td bgcolor='#CCCCCC'><div align='center'><p align='center'><strong><font size='-2'>TIPO FERIMENTO</font></strong></p></div></td>
                    <td width='60' bgcolor='#CCCCCC'><div align='center'><p align='center'><font color='black' size='-2'><b>EXCLUIR</b></font></p></div></td>
                  </tr>";
                  
                  
                 while(odbc_fetch_row($result))
                 {
					   $id_ferido			= odbc_result($result,1);
                       $nome		 		= odbc_result($result,2);
                       $telefone			= odbc_result($result,3);
					   $tipo_ferimento		= odbc_result($result,4);	

            
                    print "<tr>
                             <td bgcolor='#FFFFFF'><center><font size='-2'>".$nome."</center></b></td>
                             <td bgcolor='#FFFFFF'><center><font size='-2'>".$telefone."</center></b></td>
                             <td bgcolor='#FFFFFF'><center><font size='-2'>".$tipo_ferimento."</center></b></td>";
							 
							 if (($status_id != 'f') && ($coordenador_ppae == 'S'))
							 	print "<td bgcolor='#FFFFFF'><center><font size='-2'><a href='javascript:exclui_ferido_ppae($id_ferido, $acidente_id);'>
									   <img src='../SCA/images/excluir1.jpg' width='25' heigth='25' style='border:none'></a></center></b></td>";
							 else
							 	print "<td bgcolor='#FFFFFF'><center><font size='-2'> - </center></b></td>";							 
                           print "</tr>";
            
                 }
                 print "</table>";
            
            ?>
	      </td>
      </tr>
    </table>
    <p>&nbsp;</p>
    <table width="1150" border="1" frame="box" rules="none">
      <tr>
        <td width="334" height="26"><div align="left"><font color="red"> * </font><font size="-1">Houve acionamento do N&uacute;cleo de Per&iacute;cias?</font></div></td>
         <td><input type="radio" name="radio" id="acion_nucleo_sim" value="s" <?php if ($opcao_nucleo == 's'){print "checked='checked'";} ?>/>
          <font size="-1">Sim</font><font size="-1">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</font>
          <input type="radio" name="radio" id="acion_nucleo_nao" value="n" <?php if ($opcao_nucleo == 'n'){print "checked='checked'";} ?>/>
          <font size="-1">N&atilde;o</font></div></td>
      </tr>
    </table>
    <table width="1150" border="1">
      <tr>
        <td bgcolor="#CCCCCC"><div align="center"><font size="-1"><strong>OBSERVA&Ccedil;&Atilde;O</strong></font></div></td>
      </tr>
      <tr>
        <td><font size="-1">
          <?php print utf8_encode($observacao_ppae) ?>
        </font></td>
      </tr>
    </table>
    <p>&nbsp;</p>
    <p><b>Dados da Carga</b></p>
    <table width="1150" border="1" frame="box" rules="none">
      <tr>
        <td width="207" height="22"><font size="-1"> Tipo de Produto/carga:</font></td>
        <td width="143"><input type="checkbox" name="prod_solido" id="prod_solido" <?PHP if ($prod_solido == 's') print 'checked'; ?>/>
          <font size="-1">Produto S&oacute;lido</font></td>
        <td width="142"><input type="checkbox" name="prod_liquido" id="prod_liquido" <?PHP if ($prod_liquido == 's') print 'checked'; ?>/>
          <font size="-1">Produto L&iacute;quido</font></td>
        <td width="138"><input type="checkbox" name="prod_gasoso" id="prod_gasoso" <?PHP if ($prod_gasoso == 's') print 'checked'; ?>/>
          <font size="-1">Produto Gasoso</font></td>
        <td>&nbsp;</td>
        <td width="420">&nbsp;</td>
      </tr>
      <tr>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
      </tr>
      <tr>
        <td width="207" height="26"><font size="-1"> Tipo de Embalagem:</font></td>
        <td width="143"><input type="checkbox" name="tambores" id="tambores" <?PHP if ($tambores == 's') print 'checked'; ?>/>          <font size="-1">Tambores</font></td>
        <td width="142"><input type="checkbox" name="bombonas" id="bombonas" <?PHP if ($bombonas == 's') print 'checked'; ?>/>          <font size="-1">Bombonas</font></td>
        <td width="138"><input type="checkbox" name="isotank" id="isotank" <?PHP if ($isotank == 's') print 'checked'; ?>/>          <font size="-1">Isotank </font></td>
        <td width="110"><input type="checkbox" name="sacaria" id="sacaria" <?PHP if ($sacaria == 's') print 'checked'; ?>/>          <font size="-1"> Sacaria</font></td>
        <td><input type="checkbox" name="flag_outra_embalagem" id="flag_outra_embalagem" <?PHP if ($flag_outra_embalagem == 's') print 'checked'; ?>
        onclick="javascript:habilita_campo_1()"/>
        <font size="-1"> Outros: </font><font size="-1"><?php print $outra_embalagem ?></td>
      </tr>
      <tr>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
           <td>&nbsp;</td>
  
      </tr>
      <tr>
        <td width="207" height="26"><font size="-1"> H&aacute; vazamento pr&oacute;ximo a:</font></td>
        <td width="143"><input type="checkbox" name="rio" id="rio" <?PHP if ($rio == 's') print 'checked'; ?>/>          <font size="-1"> Rio</font></td>
        <td width="142"><input type="checkbox" name="mangue" id="mangue" <?PHP if ($mangue == 's') print 'checked'; ?>/>          <font size="-1"> Mangue</font></td>
        <td width="138"><input type="checkbox" name="lago" id="lago" <?PHP if ($lago == 's') print 'checked'; ?>/>          <font size="-1">Lago</font></td>
        <td width="110"><input type="checkbox" name="bueiros" id="bueiros" <?PHP if ($bueiros == 's') print 'checked'; ?>/>          <font size="-1">Bueiros</font></td>
        <td><input type="checkbox" name="flag_outros_locais" id="flag_outros_locais" <?PHP if ($flag_outros_locais == 's') print 'checked'; ?> 
        onclick="javascript:habilita_campo_2()"/>
        <font size="-1"> Outros: </font><font size="-1"><?php print $outros_locais ?></td>
     
      </tr>
      <tr>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
      </tr>
       
              <tr>
        <td colspan="7"><font size="-1"><font color="red"> * </font>Houve acionamento da Ambipar?</font>
       <input type="radio" name="radio1" id="opcao_cotec_sim" value="s" <?php if ($opcao_cotec == 's'){print "checked='checked'";} ?>/>
          <font size="-1">Sim</font><font size="-1">&nbsp;&nbsp;&nbsp;
          <input type="radio" name="radio1" id="opcao_cotec_nao" value="n" <?php if ($opcao_cotec == 'n'){print "checked='checked'";} ?> />
        </font><font size="-1">N&atilde;o<font size="-1">&nbsp;&nbsp;</font></font></font></td>
      </tr>
  
    </table>
  
    <p>&nbsp;</p>
    <table width="1150" border="1" frame="box" rules="none">
      <tr>
        <td width="156"><div align="left"><font size="-1"><a href="#" onclick="window.open('placas_veiculos.php','nome','height =400, width=700, scrollbars=yes')"> Tipo de Ocorr&ecirc;ncia:</a></font></div></td>
        <td width="151"><div align="left">
          <input type="checkbox" name="tombamento" id="tombamento" <?PHP if ($tombamento == 's') print 'checked'; ?>/>
        <font size="-1">Tombamento</font></div></td>
        <td width="209"><div align="left">
          <input type="checkbox" name="colisao" id="colisao" <?PHP if ($colisao == 's') print 'checked'; ?>/>
        <font size="-1">Colis&atilde;o</font></div></td>
        <td width="214"><div align="left">
          <input type="checkbox" name="queda_carga" id="queda_carga" <?PHP if ($queda_carga == 's') print 'checked'; ?>/>
        <font size="-1">Queda de Carga</font></div></td>
        <td width="436"><div align="left">
          <input type="checkbox" name="vazamento_transito" id="vazamento_transito" <?PHP if ($vazamento_transito == 's') print 'checked'; ?>/>
        <font size="-1">Vazamento em Tr&acirc;nsito</font></div></td>
      </tr>
        <tr>
        <td width="156"></td>
           <td colspan="2"><div align="left">
             <input type="checkbox" name="flag_outra_ocorrencia" id="flag_outra_ocorrencia" <?PHP if ($flag_outra_ocorrencia == 's') print 'checked'; ?>
             onclick="javascript:habilita_campo_3()"/>
          <font size="-1"> Outros</font>: <font size="-1"><?php print $outra_ocorrencia ?>
           </div></td>
      </tr>
    </table>
    <p>&nbsp;</p>
    <table width="1150" height="56" border="1" frame="box" rules="none">
      <tr>
        <td height="24" colspan="2"><font size="-1"> <strong>N&uacute;meros do Painel de Seguran&ccedil;a (aplic&aacute;vel se produto qu&iacute;mico)</strong></font></td>
      </tr>
      <tr>
        <td><font size="-1">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;N&uacute;mero superior (N&uacute;mero de risco com at&eacute; 4 algarismos): <font size="-1"><?php print $numero_superior ?>
        </font></td>
        <td height="24"><font size="-1">N&uacute;mero inferior (N&uacute;mero da ONU com 4 algarismos): </font><font size="-1"><?php print $numero_inferior ?></td>
      </tr>
    </table>
    <p>&nbsp;</p>
    <table width="1150" height="36" border="1" frame="box" rules="none">
      <tr>
        <td width="193"><font size="-1"> Presente no Local:</font></td>
        <td width="313" height="22"><input type="checkbox" name="equip_atend_emerg" id="equip_atend_emerg" <?PHP if ($equip_atend_emerg == 's') print 'checked'; ?>/>          <font size="-1">Equipe de Atendimento a Emerg&ecirc;ncia</font></td>
        <td width="208"><input type="checkbox" name="policia" id="policia" <?PHP if ($policia == 's') print 'checked'; ?>/>          
        <font size="-1">Pol&iacute;cia Rodovi&aacute;ria/Militar</font></td>
        <td width="187"><input type="checkbox" name="concessionaria" id="concessionaria" <?PHP if ($concessionaria == 's') print 'checked'; ?>/>          <font size="-1">Concession&aacute;ria</font></td>
        <td width="265"><input type="checkbox" name="orgao" id="orgao" <?PHP if ($orgao == 's') print 'checked'; ?>/>          <font size="-1">&Oacute;rg&atilde;o do Meio Ambiente</font></td>
      </tr>
      <tr>
        <td>&nbsp;</td>

        <td><input type="checkbox" name="seguradora" id="seguradora" <?PHP if ($seguradora == 's') print 'checked'; ?>/>          <font size="-1">Seguradora</font></td>
        <td><input type="checkbox" name="remetente" id="remetente" <?PHP if ($remetente == 's') print 'checked'; ?>/>          <font size="-1">Remetente</font></td>
        <td><input type="checkbox" name="destinatario" id="destinatario" <?PHP if ($destinatario == 's') print 'checked'; ?>/>          <font size="-1">Destinat&aacute;rio</font></td>
        <td height="22"><input type="checkbox" name="imprensa" id="imprensa" <?PHP if ($imprensa == 's') print 'checked'; ?>/>          <font size="-1">Imprensa</font></td>
      </tr>
      <tr>
        <td>&nbsp;</td>
        <td height="40"=""><input type="checkbox" name="flag_outro_presente" id="flag_outro_presente" <?PHP if ($flag_outro_presente == 's') print 'checked'; ?> onclick="javascript:habilita_campo_4()"/>
        <font size="-1"> Outros: <font size="-1"><?php print $outro_presente ?></td>
      </tr>
    </table>
    <p>&nbsp;</p>
    <table width="1150" border="1" frame="box" rules="none">
      <tr>
        <td width="845" height="26"><div align="left"><font size="-1"><font color="red">*</font></font><font size="-1"> Responsavel pelo preenchimento das informa&ccedil;&otilde;es:</font></div></td>
        <td width="339" height="26">&nbsp;</td>
      </tr>
      <tr>
        <td height="26">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<font size="-1"><?php print $responsavel_preenchimento ?></td>
        <td height="26"><div align="left"><font size="-1"><font color="red">*</font> Se&ccedil;&atilde;o:</font><font size="-1"> <?php print $secao_responsavel ?>
        </div></td>
      </tr>
    </table>
	<p>&nbsp;</p>
</fieldset>
<?php 

if (1 == 2) //nao exibir
{

?>
    
 <p class="break"></p>   
 <p>&nbsp;</p>    
<fieldset>
    <center><b><font size="+2" color="#0000CC">An&aacute;lise de QSMA</font></b></center>	
  <p>&nbsp;</p>
      <!--<table width="100" height="22" border="1">
    <tr>
      <td width="240"><center>
        <a href="lancamento_acidente.php"><strong>Anexos</strong></a>
      </center></td>
     </tr>
  </table> -->
    <table width="1177" border="1" frame="box" rules="none">
        <tr>
          <td width="116" height="26"><font size="-1"><b>Classifica&ccedil;&atilde;o: </b></font></td>
          <td colspan="2"><input type="radio" name="radio" id="incidente" value="i" <?php if ($opcao_classificacao == 'i'){print "checked='checked'";} ?> <?php print $disabled_class ?> onclick="javascript:habilita_resp_impacto()"/>
            <font size="-1">Incidente</font>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            <input type="radio" name="radio" id="acidente_trabalho" value="a" <?php if ($opcao_classificacao == 'a'){print "checked='checked'";} ?> <?php print $disabled_class ?> onclick="javascript:habilita_resp_impacto()"/>
            <font size="-1">Acidente de Trabalho</font>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            <input type="radio" name="radio" id="nao_conformidade" value="n" <?php if ($opcao_classificacao == 'n'){print "checked='checked'";} ?> <?php print $disabled_class ?> onclick="javascript:habilita_resp_impacto()"/>
            <font size="-1">N&atilde;o Conformidade</font>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            <input type="radio" name="radio" id="registro_oea" value="o" <?php if ($opcao_classificacao == 'o'){print "checked='checked'";} ?> <?php print $disabled_class ?> onclick="javascript:habilita_resp_impacto()"/>
            <font size="-1">Assuntos Estrat&eacute;gicos</font></td>            
          <td width="22"></td>
        </tr>
    </table>
    <p>&nbsp;</p>
    <table width="1178" border="1" frame="box" rules="none">
      <tr>
        <td width="170"><font size="-1">Respons&aacute;vel/Despesa:</font></td>
        <td width="261">&nbsp;</td>
        <td width="747">&nbsp;</td>
      </tr>
      <tr>
        <td colspan="3">
			<?php 
            
                $query = "select daa.id, ara.area, usuario.nome, convert(varchar(10),daa.data_gravacao, 103)+' '+convert(varchar(10),daa.data_gravacao, 108),
							ara.id
							from despesa_area_acidente daa with (nolock)
							join area_responsavel_acidente ara with (nolock) on
								ara.id = daa.area_id
							join usuario with (nolock) on
								usuario.id = ara.responsavel_id
							where daa.acidente_id = $acidente_id
							and daa.status_id = 'a'
							order by ara.area";
                //print $query;
                $result = odbc_exec($conSQL, $query);    
            
            
                print "<table width='1100' border='1' >
                  <tr>
                    <td bgcolor='#CCCCCC' width='400'><div align='center'><strong><font size='-2'>&Aacute;REA</font></strong></div></td>
                    <td bgcolor='#CCCCCC' width='500'><div align='center'><p align='center'><strong><font size='-2'>RESPONS&Aacute;VEL</font></strong></p></div></td>
                    <td bgcolor='#CCCCCC' width='292'><div align='center'><strong><font size='-2'>DATA E HORA DO LAN&Ccedil;AMENTO</font></strong></div></td>
                  </tr>";
                  
                  
                 while(odbc_fetch_row($result))
                 {
                       $despesa_area_id		= odbc_result($result,1);
                       $area		 		= odbc_result($result,2);
                       $nome_responsavel	= odbc_result($result,3);
                       $data_hora	 		= odbc_result($result,4);
                       $area_id		 		= odbc_result($result,5);					   
			
                    print "<tr>
                             <td bgcolor='#FFFFFF'><center>
							 	<a href=javascript:pagina('lancamento_despesas.php?area_id=$area_id&acidente_id=$acidente_id&voltar=0','1350','900','Logs')>
								<font size='-2'>".$area."</center></b></td>
                             <td bgcolor='#FFFFFF'><center><font size='-2'>".$nome_responsavel."</center></b></td>
                             <td bgcolor='#FFFFFF'><center><font size='-2'>".$data_hora."</center></b></td>";
                    
					print "</tr>";
            
                 }
                 print "</table>";
				 
				 
			 if (($opcao_classificacao != '') && ($tipo_registro_id == 6))
				print "&nbsp;<a href=javascript:pagina('lancamento_despesas.php?area_id=$area_id&acidente_id=$acidente_id','1150','400','Manual')><font size='-2'><u>Lan&ccedil;amento Manual</u></font></a>";
            
            ?>
        </td>
      </tr>
    </table>    
    <p>&nbsp;</p>
    <table width="1178" border="1" frame="box" rules="none">
      <tr>
        <td><font size="-1"> Despesa Financeira:</font></td>
      </tr>
      <tr>
        <td><?php 
            
				//DADOS E VALORES DO CORPORE
			
				$valor_Total_d = 0;
				$valor_Total_c = 0;				
			
                $query = "SELECT FLAN.HISTORICO collate sql_latin1_general_cp1251_ci_as, CONVERT(VARCHAR(10), FLAN.DATAVENCIMENTO, 103) DATA_VENCIMENTO, 
							CONVERT(VARCHAR(10), FLAN.DATABAIXA, 103) DATA_DATABAIXA, CAST(FLAN.VALORORIGINAL AS NUMERIC(15,2)), NUMERODOCUMENTO, 
							FLAN.IDLAN, 'D'
							from CORPORE..FLANCOMPL WITH (NOLOCK)
							JOIN CORPORE..FLAN WITH (NOLOCK) ON
								FLAN.IDLAN = FLANCOMPL.IDLAN
								AND FLAN.CODCOLIGADA = FLANCOMPL.CODCOLIGADA
								AND FLAN.IDMOV IS NULL
							WHERE REGACIDENTE = $acidente_id
							AND FLANCOMPL.CODCOLIGADA = 1
							AND FLAN.STATUSLAN <> 2
							
							UNION ALL
														
							SELECT TMOVHISTORICO.HISTORICOLONGO collate sql_latin1_general_cp1251_ci_as, CONVERT(VARCHAR(10), FLAN.DATAVENCIMENTO, 103), 
							CONVERT(VARCHAR(10), DATABAIXA, 103), FLAN.VALORORIGINAL, NUMERODOCUMENTO, FLAN.IDLAN, 'D'
							FROM CORPORE..TMOVCOMPL WITH (NOLOCK) 
							LEFT JOIN CORPORE..FLAN WITH (NOLOCK) ON
								FLAN.IDMOV = TMOVCOMPL.IDMOV
								AND FLAN.CODCOLIGADA = TMOVCOMPL.CODCOLIGADA
								AND FLAN.STATUSLAN <> 2
							LEFT JOIN CORPORE..FLANCOMPL WITH (NOLOCK) ON
								FLANCOMPL.IDLAN = FLAN.IDLAN
								AND FLANCOMPL.CODCOLIGADA = TMOVCOMPL.CODCOLIGADA
							JOIN CORPORE..TMOV WITH (NOLOCK) ON
								TMOV.IDMOV = TMOVCOMPL.IDMOV
								AND TMOV.CODCOLIGADA = TMOVCOMPL.CODCOLIGADA
							LEFT JOIN CORPORE..TMOVHISTORICO WITH (NOLOCK) ON
								TMOVHISTORICO.IDMOV = TMOVCOMPL.IDMOV
								AND TMOVHISTORICO.CODCOLIGADA = TMOVCOMPL.CODCOLIGADA
							WHERE TMOVCOMPL.REGACIDENTE = $acidente_id
							AND TMOVCOMPL.CODCOLIGADA = 1	
							AND TMOV.STATUS <> 'C'
							AND TMOV.CODTMV LIKE '1.2%'	
							
							UNION ALL
							
							SELECT FXCX.HISTORICO  collate sql_latin1_general_cp1251_ci_as, CONVERT(VARCHAR(10), FXCX.DATAVENCIMENTO, 103) DATA_VENCIMENTO, 
							CONVERT(VARCHAR(10), FLAN.DATABAIXA, 103) DATA_DATABAIXA, 
							CASE WHEN FXCX.VALOR < 0
									THEN CAST((FXCX.VALOR*(-1)) AS NUMERIC(15,2))
									ELSE CAST(FXCX.VALOR AS NUMERIC(15,2)) 
							END VALOR,
							FXCX.NUMERODOCUMENTO, FLAN.IDLAN,
							CASE WHEN FXCX.VALOR < 0
									THEN 'D'
									ELSE 'C'
							END							
							FROM CORPORE..FXCXCOMPL WITH (NOLOCK)
							JOIN CORPORE..FXCX WITH (NOLOCK) ON
								FXCX.IDXCX = FXCXCOMPL.IDXCX
								AND FXCX.CODCOLIGADA = 1
							LEFT JOIN CORPORE..FLANBAIXA WITH (NOLOCK) ON
								FLANBAIXA.IDXCX = FXCXCOMPL.IDXCX
								AND FLANBAIXA.CODCOLIGADA = 1
								AND FLANBAIXA.STATUS = 0
							LEFT JOIN CORPORE..FLAN WITH (NOLOCK) ON
								FLAN.IDXCX = FXCXCOMPL.IDXCX
								AND FLAN.CODCOLIGADA = 1
							WHERE REGACIDENTE = $acidente_id
							AND FXCX.CODCOLIGADA = 1	
							AND FXCX.COMPENSADO = 1							
							";
                //print $query;
                $result = odbc_exec($conSQL, $query);    
            
            
                print "<table width='1100' border='1' >
                  <tr>
                    <td bgcolor='#CCCCCC'><div align='center'><strong><font size='-2'>HIST&Oacute;RICO</font></strong></div></td>
                    <td bgcolor='#CCCCCC'><div align='center'><p align='center'><strong><font size='-2'>DATA VENCIMENTO</font></strong></p></div></td>
                    <td bgcolor='#CCCCCC'><div align='center'><strong><font size='-2'>DATA_BAIXA</font></strong></div></td>
                    <td bgcolor='#CCCCCC' width='50'><div align='center'><strong><font size='-2'>TIPO</font></strong></div></td>		
                    <td bgcolor='#CCCCCC'><div align='center'><strong><font size='-2'>VALOR</font></strong></div></td>					
                    <td bgcolor='#CCCCCC'><div align='center'><strong><font size='-2'>REFER&Ecirc;NCIA</font></strong></div></td>					
                    <td bgcolor='#CCCCCC'><div align='center'><strong><font size='-2'>DOCUMENTO</font></strong></div></td>					
                  </tr>";
                  
                  
                 while(odbc_fetch_row($result))
                 {
                       $historico	 		= odbc_result($result,1);
                       $data_vencto			= odbc_result($result,2);
                       $data_baixa			= odbc_result($result,3);
                       $valor				= odbc_result($result,4);
                       $numero_docto		= odbc_result($result,5);	
                       $referencia			= odbc_result($result,6);
                       $tipo_lancamento		= odbc_result($result,7);					   						   
					   				   
                       
					   if ($tipo_lancamento == 'D')
					   		$valor_Total_d	 = $valor_Total_d + $valor;
						else
							$valor_Total_c	 = $valor_Total_c + $valor;
			
                    print "<tr>
                             <td bgcolor='#FFFFFF'><center><font size='-2'>".$historico."</center></b></td>
                             <td width='150' bgcolor='#FFFFFF'><center><font size='-2'>".$data_vencto."</center></b></td>
                             <td width='150' bgcolor='#FFFFFF'><center><font size='-2'>".$data_baixa."</center></b></td>
                             <td bgcolor='#FFFFFF'><center><font size='-2'>".$tipo_lancamento."</center></b></td>							 							                              <td bgcolor='#FFFFFF'><center><font size='-2'>R$ ".number_format($valor, 2, ',', '.')."</center></b></td>
                             <td bgcolor='#FFFFFF'><center><font size='-2'>".$referencia."</center></b></td>
                             <td bgcolor='#FFFFFF'><center><font size='-2'>".$numero_docto."</center></b></td>							 
                    	   </tr>";
            
                 }
                 print "</table>";
            
			
			//VALOR TOTAL DOS LANCAMENTOS

			$query = "select SUM(valor)
						from lancamento_despesas_acidente
						where ACIDENTE_ID = $acidente_id
						and status_id = 'a'";
			//print $query;
			$result = odbc_exec($conSQL, $query); 
			$valor_total_lancamentos 	= odbc_result($result,1); 			
		
			
            ?></td>
      </tr>      
    </table>    
    <p>&nbsp;</p>
    <table width="1178" border="1" frame="box" rules="none">
      <tr>
            <td width="280" bgcolor="#FFFFFF" td><div align="left"><font size="-1">Total D&eacute;bitos Financeiro:</font></div></td>
            <td bgcolor="#FFFFFF"><div align="right"><font size="-1" color="green"><?php print "R$ ".number_format($valor_Total_d, 2, ',', '.'); ?></font></div></td>
      </tr>
      <tr>
 	        <td td bgcolor="#FFFFFF"><div align="left"><font size="-1">Total Cr&eacute;ditos Financeiro:</font></div></td>
 	        <td bgcolor="#FFFFFF"><div align="right"><font size="-1" color="green"><?php print "R$ ".number_format($valor_Total_c, 2, ',', '.'); ?></font></div></td>
      </tr>
      <tr>
 	    <td td bgcolor="#FFFFFF"><div><font size="-1">Total lan&ccedil;amento:</font></div></td>
 	    <td bgcolor="#FFFFFF"><div align="right"><font size="-1" color="green"><?php print "R$ ".number_format($valor_total_lancamentos, 2, ',', '.'); ?></font></div></td>
      </tr>
    </table>
    <p>&nbsp;</p>

	<?php

		//$valor_Total = '16600,00';    
    
    ?>


    <p>&nbsp;</p>
    <table width="924" border="1" frame="box" rules="none">
      <tr>
        <td width="163"><font size="-1">Rateio Despesas:</font></td>
        <td width="491">&nbsp;</td>
        <td width="127">&nbsp;</td>
        <td width="115">&nbsp;</td>
      </tr>
      <tr>
        <td colspan="4"><?php 
        
		//conta os registros
		$query = "select count(*)
					from despesa_cc_acidente dcca with (nolock)
					join CORPORE..GCCUSTO with (nolock) on
						GCCUSTO.CODREDUZIDO = dcca.centro_custo_id collate SQL_Latin1_General_CP1_CI_AI
					where acidente_id = $acidente_id
					and dcca.status_id = 'a'";
		//print $query;
		$result = odbc_exec($conSQL, $query);   	
		$total_registros = odbc_result($result,1);
	
		
		if (($conta_porcento == 100) || ($conta_porcento == 0) || ($conta_porcento == ''))
		{
		    
			$query = "select dcca.id, CODREDUZIDO, NOME collate sql_latin1_general_cp1251_ci_as, porcentagem
						from despesa_cc_acidente dcca with (nolock)
						join CORPORE..GCCUSTO with (nolock) on
							GCCUSTO.CODREDUZIDO = dcca.centro_custo_id collate SQL_Latin1_General_CP1_CI_AI
						where acidente_id = $acidente_id
						and dcca.status_id = 'a'";
			//print $query;
			$result = odbc_exec($conSQL, $query);    
		
		
			print "<table width='924' border='1' >
			  <tr>
				<td width='100' bgcolor='#CCCCCC'><div align='center'><strong><font size='-2'>COD. REDUZIDO</font></strong></div></td>
				<td bgcolor='#CCCCCC'><div align='center'><p align='center'><strong><font size='-2'>CENTRO DE CUSTO</font></strong></p></div></td>
				<td bgcolor='#CCCCCC'><div align='center'><strong><font size='-2'> % </font></strong></div></td>
				<td bgcolor='#CCCCCC'><div align='center'><strong><font size='-2'>VALOR RATEADO</font></strong></div></td>					
				<td width='60' bgcolor='#CCCCCC'><div align='center'><p align='center'><font color='black' size='-2'><b>EXCLUIR</b></font></p></div></td>
			  </tr>";
			  
			  
			 while(odbc_fetch_row($result))
			 {
				   $cc_id		 		= odbc_result($result,1);
				   $cod_reduzido		= odbc_result($result,2);
				   $centro_custo		= odbc_result($result,3);
				   $porcentagem			= odbc_result($result,4);
				   
				   $valor	 			= ($porcentagem * $valor_total_lancamentos) / 100;
				   
		
				print "<tr>
						 <td bgcolor='#FFFFFF'><center><font size='-2'>".$cod_reduzido."</center></b></td>
						 <td bgcolor='#FFFFFF'><center><font size='-2'>".$centro_custo."</center></b></td>";
						 
				if (($status_acidente == 'p') || ($status_acidente == 'r'))
					print "
						 <td bgcolor='#FFFFFF'><center>
							<input name='porcentagem$cc_id' type='text' id='porcentagem$cc_id' value='$porcentagem' size='2' maxlength='6' /></td>";
				else
					print "
						 <td bgcolor='#FFFFFF'><center><font size='-2'>".$porcentagem."</center></b></td>";

				print "<td bgcolor='#FFFFFF'><center><font size='-2'>R$ ".number_format($valor, 2, ',', '.')."</center></b></td>";
				
				if ((($status_acidente == 'p') || ($status_acidente == 'r')) && ($porcentagem == '0.00'))
					print "<td bgcolor='#FFFFFF'><center><font size='-2'><a href='javascript:exclui_despesa_cc($cc_id, $acidente_id);'>
							<img src='../SCA/images/excluir1.jpg' width='25' heigth='25' style='border:none'></a></center></b></td>";
				else
					if ((($status_acidente == 'p') || ($status_acidente == 'r')) && ($total_registros == 1))
						print "<td bgcolor='#FFFFFF'><center><font size='-2'><a href='javascript:exclui_despesa_cc($cc_id, $acidente_id);'>
								<img src='../SCA/images/excluir1.jpg' width='25' heigth='25' style='border:none'></a></center></b></td>";					
					else					
						print "<td bgcolor='#FFFFFF'><center><font size='-2'> - </center></b></td>";

				print "</tr>";
		
			 }
			 print "</table>";
		}
		else
		{
			$query = "select dcca.id, CODREDUZIDO, NOME collate sql_latin1_general_cp1251_ci_as, porcentagem
						from despesa_cc_acidente dcca with (nolock)
						join CORPORE..GCCUSTO with (nolock) on
							GCCUSTO.CODREDUZIDO = dcca.centro_custo_id collate SQL_Latin1_General_CP1_CI_AI
						where acidente_id = $acidente_id
						and dcca.status_id = 'a'";
			//print $query;
			$result = odbc_exec($conSQL, $query);    
			
			print "<table width='924' border='1' >
			  <tr>
				<td width='100' bgcolor='#CCCCCC'><div align='center'><strong><font size='-2'>COD. REDUZIDO</font></strong></div></td>
				<td bgcolor='#CCCCCC'><div align='center'><p align='center'><strong><font size='-2'>CENTRO DE CUSTO</font></strong></p></div></td>
				<td bgcolor='#CCCCCC'><div align='center'><strong><font size='-2'> % </font></strong></div></td>
				<td bgcolor='#CCCCCC'><div align='center'><strong><font size='-2'>VALOR RATEADO</font></strong></div></td>					
				<td width='60' bgcolor='#CCCCCC'><div align='center'><p align='center'><font color='black' size='-2'><b>EXCLUIR</b></font></p></div></td>
			  </tr>";

			  
			while(odbc_fetch_row($result))
			{
				   $cc_id		 		= odbc_result($result,1);
				   $cod_reduzido		= odbc_result($result,2);
				   $centro_custo		= odbc_result($result,3);
				   $porcentagem			= odbc_result($result,4);
			
				   $porcentagem			= $_POST["porcentagem$cc_id"];
			
					$valor	 			= ($porcentagem * $valor_Total) / 100;
					   
	
                    print "<tr>
                             <td bgcolor='#FFFFFF'><center><font size='-2'>".$cod_reduzido."</center></b></td>
                             <td bgcolor='#FFFFFF'><center><font size='-2'>".$centro_custo."</center></b></td>";
							 
					if (($status_acidente == 'p') || ($status_acidente == 'r'))
						print "
                             <td bgcolor='#FFFFFF'><center>
							 	<input name='porcentagem$cc_id' type='text' id='porcentagem$cc_id' value='$porcentagem' size='2' maxlength='6' /></td>";
					else
						print "
                             <td bgcolor='#FFFFFF'><center><font size='-2'>".$porcentagem."</center></b></td>";

					print "<td bgcolor='#FFFFFF'><center><font size='-2'>R$ ".number_format($valor, 2, ',', '.')."</center></b></td>";

					if ((($status_acidente == 'p') || ($status_acidente == 'r')) && ($porcentagem == '0.00'))
						print "<td bgcolor='#FFFFFF'><center><font size='-2'><a href='javascript:exclui_despesa_cc($cc_id, $acidente_id);'>
								<img src='../SCA/images/excluir1.jpg' width='25' heigth='25' style='border:none'></a></center></b></td>";
					else
						if ((($status_acidente == 'p') || ($status_acidente == 'r')) && ($total_registros == 1))
							print "<td bgcolor='#FFFFFF'><center><font size='-2'><a href='javascript:exclui_despesa_cc($cc_id, $acidente_id);'>
									<img src='../SCA/images/excluir1.jpg' width='25' heigth='25' style='border:none'></a></center></b></td>";					
						else					
							print "<td bgcolor='#FFFFFF'><center><font size='-2'> - </center></b></td>";

                    print "</tr>";
            
			 }
			 print "</table>";
			
			
		}
		
		
            ?>
            
            
            </td>
      </tr>
    </table>
    <p>&nbsp;</p>
    <table width="1173" border="1" frame="box" rules="none">
      <tr>
        <td width="192"><div align="left"><font size="-1"><b>Gravidade:</font></div></td>
        <td width="161"><input type="radio" name="radio1" id="gravidade_leve" value="l" <?php if ($opcao_gravidade == 'l'){print "checked='checked'";} ?>/>
          <font size="-1">Leve</font></td>
        <td width="170"><input type="radio" name="radio1" id="gravidade_moderado" value="m" <?php if ($opcao_gravidade == 'm'){print "checked='checked'";} ?>/>
          <font size="-1">Moderado</font></td>
        <td colspan="2"><input type="radio" name="radio1" id="gravidade_grave" value="g" <?php if ($opcao_gravidade == 'g'){print "checked='checked'";} ?>/>
          <font size="-1">Grave</font></td>
      </tr>
      <tr>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td colspan="2">&nbsp;</td>
      </tr>
      <tr>
        <td><div align="left"><font size="-1"><b>&Aacute;reas do QSMA:</font></div></td>
        <td><input type="checkbox" name="area_qsma_qualidade" id="area_qsma_qualidade" <?PHP if ($area_qsma_qualidade == 's') print 'checked'; ?> />
          <font size="-1">Qualidade</td>
        <td><input type="checkbox" name="area_qsma_seguranca" id="area_qsma_seguranca" <?PHP if ($area_qsma_seguranca == 's') print 'checked'; ?> /> 
          <font size="-1">Seguran&ccedil;a
</td>
        <td><input type="checkbox" name="area_qsma_saude" id="area_qsma_saude" <?PHP if ($area_qsma_saude == 's') print 'checked'; ?> />
        <font size="-1">Sa&uacute;de</td>
        <td><input type="checkbox" name="area_qsma_meio_ambiente" id="area_qsma_meio_ambiente" <?PHP if ($area_qsma_meio_ambiente == 's') print 'checked'; ?> />
        <font size="-1">Meio Ambiente</td>
      </tr>
      <tr>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td colspan="2">&nbsp;</td>
      </tr>
      <tr>
        <td><font size="-1"><b>Traz impacto para o SIG:</td>
        <td><input type="radio" name="radio2" id="impacto_sim" value="s" <?php if ($opcao_impacto == 's'){print "checked='checked'";} ?> onclick="javascript:habilita_resp_impacto()"/>
        <font size="-1">Sim</font></td>
        <td><input type="radio" name="radio2" id="impacto_nao" value="n" <?php if ($opcao_impacto == 'n'){print "checked='checked'";} ?> onclick="javascript:habilita_resp_impacto()"/>
          <font size="-1">N&atilde;o</font></td>
        <td width="185"><font size="-1">Tipo da ocorr&ecirc;ncia:        </td>
        <td width="458"><?php
					$query = "select *
							  from tipo_ocorrencia_acidente";
					$result = odbc_exec($conSQL, $query);           
			  
					print "<select name='tipo_ocorrencia_id' id='tipo_ocorrencia_id' class='lista' ><option value=''></option>";
					
					while(odbc_fetch_array($result))
					{
						if (odbc_result($result, 1) == $tipo_ocorrencia_id)
							$selected = "selected='selected'";
						else
							$selected = "";						
						
					   print "<option value='".odbc_result($result, 1)."'$selected>".utf8_encode(odbc_result($result, 2))."</option>";
					}     
					print"</select>";
			  ?></td>
      </tr>
      <tr>
        <td>&nbsp;</td>
        <td colspan="2"><input name="responsaveis_analise_rms" type="button" class="botao_site" id="responsaveis_analise_rms" value="Respons&aacute;veis An&aacute;lise/RMS" maxlength="14" onclick="javascript:pagina('responsaveis_analise_rms.php?id=<?php print $acidente_id ?>','700','500','responsaveis')"/></td>
        <td><font size="-1">Local da ocorr&ecirc;ncia: </td>
        <td><?php
					$query = "select *
							  from local_ocorrencia_acidente";
					$result = odbc_exec($conSQL, $query);           
			  
					print "<select name='local_ocorrencia_id' id='local_ocorrencia_id' class='lista'><option value=''></option>";
					
					while(odbc_fetch_array($result))
					{
						if (odbc_result($result, 1) == $local_ocorrencia_id)
							$selected = "selected='selected'";
						else
							$selected = "";						
						
					   print "<option value='".odbc_result($result, 1)."'$selected>".utf8_encode(odbc_result($result, 2))."</option>";
					}     
					print"</select>";
			  ?></td>
      </tr>
      
       <tr>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td><font size="-1">Area:
        <label for="obs_impacto_sig"></label></td>
        <td>
        	<?php
					$query = "select
									id,
									area								
								from area_responsavel_acidente with(nolock)";
								
					$result = odbc_exec($conSQL, $query);           
			  
					print "<select name='area_id' id='area_id' class='lista'><option value=''></option>";
					
					while(odbc_fetch_array($result))
					{
						if (odbc_result($result, 1) == $area_responsavel_acidente)
							$selected = "selected='selected'";
						else
							$selected = "";						
						
					   print "<option value='".odbc_result($result, 1)."'$selected>".utf8_encode(odbc_result($result, 2))."</option>";
					}     
					print"</select>";
			  ?>
        </td>
      </tr>
      
      <tr>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td><font size="-1">Observa&ccedil;&atilde;o:
        <label for="obs_impacto_sig"></label></td>
        <td><font size="-1"><?php print utf8_encode($obs_impacto_sig) ?></td>
      </tr>
      
      <tr>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td colspan="2">&nbsp;</td>
      </tr>
      <tr>
        <td colspan="5"><div align="left"><font size="-1"><b>Plano de a&ccedil;&atilde;o enviado para cliente/parte interessada:</b></font> 
          &nbsp;&nbsp;&nbsp;&nbsp;
          <input type="radio" name="radio4" id="plano_acao_sim" value="s" <?php if ($opcao_plano_acao == 's'){print "checked='checked'";} ?> onclick="javascript:habilita_data(this)"/>
        <font size="-1">Sim</font> 
        &nbsp;&nbsp;&nbsp;&nbsp;<input type="radio" name="radio4" id="plano_acao_nao" value="n" <?php if ($opcao_plano_acao == 'n'){print "checked='checked'";} ?> onclick="javascript:habilita_data(this)"/>
        <font size="-1">N&atilde;o</font>
        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<font size="-1">Data: </font><font size="-1">
        <input name="data_1" type="text" id="data_1" value="<?php print $data_plano_acao ?>" size="06" maxlength="10" align="center" onkeypress="valida_conteudo_data(this)" onblur="javascript:verifica_data_1(this)"/>
        </font></div></td>
      </tr>
      <tr>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td colspan="2">&nbsp;</td>
      </tr>
      <tr>
        <td colspan="5"><div align="left"><font size="-1"><b>H&aacute; necessidade de revis&atilde;o de procedimentos / documento / sistema de gest&atilde;o?</b> </font>
          &nbsp;&nbsp;&nbsp;&nbsp;
          <input type="radio" name="radio5" id="necess_doc_sim" value="s" <?php if ($opcao_necess_doc == 's'){print "checked='checked'";} ?> onclick="javascript:habilita_necessidade(this)"/>
          <font size="-1">Sim</font> &nbsp;&nbsp;&nbsp;&nbsp;
          <input type="radio" name="radio5" id="necess_doc_nao" value="n" <?php if ($opcao_necess_doc == 'n'){print "checked='checked'";} ?> onclick="javascript:habilita_necessidade(this)"/>
        <font size="-1">N&atilde;o</font>
        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<font size="-1">Qual: </font><font size="-1"><?php print $docto_necess_rev ?>
        </div></td>
      </tr>
      <tr>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td colspan="2">&nbsp;</td>
      </tr>
      <tr>
        <td colspan="5"><div align="left"><font size="-1"><b>Necess&aacute;ria convoca&ccedil;&atilde;o de reuni&atilde;o extraordin&aacute;ria da CIPA (situa&ccedil;&atilde;o de  risco grave, iminente, acidentes graves ou fatais):</b></font> 
          &nbsp;&nbsp;&nbsp;&nbsp;<input type="radio" name="radio6" id="necess_conv_sim" value="s" <?php if ($opcao_necess_conv == 's'){print "checked='checked'";} ?>/>
          <font size="-1">Sim</font> &nbsp;&nbsp;&nbsp;&nbsp;
          <input type="radio" name="radio6" id="necess_conv_nao" value="n" <?php if ($opcao_necess_conv == 'n'){print "checked='checked'";} ?>/>
        <font size="-1">N&atilde;o</font></div></td>
      </tr>
      <tr>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td colspan="2">&nbsp;</td>
      </tr>
      <tr>
        <td colspan="5"><font size="-1"><b>Requer mudan&ccedil;a na an&aacute;lise de Risco (SWOT / LAIA /  APR)?</b></font> &nbsp;&nbsp;&nbsp;&nbsp;
          <input type="radio" name="radio7" id="requer_mudanca_sim" value="s" <?php if ($opcao_requer_mudanca == 's'){print "checked='checked'";} ?> onclick="javascript:habilita_mudanca_analise(this)"/>
          <font size="-1">Sim</font> &nbsp;&nbsp;&nbsp;&nbsp;
          <input type="radio" name="radio7" id="requer_mudanca_nao" value="n" <?php if ($opcao_requer_mudanca == 'n'){print "checked='checked'";} ?> onclick="javascript:habilita_mudanca_analise(this)"/>
          <font size="-1">N&atilde;o</font> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<font size="-1">Qual: </font>
        <font size="-1"><?php print $mudanca_analise_risco ?></td>
      </tr>
      <tr>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td colspan="2">&nbsp;</td>
      </tr>
      <tr>
        <td><font size="-1"><b>Conclus&atilde;o:</td>
        <td><input type="radio" name="radio3" id="conclusao_ato_inseguro" value="a" <?php if ($opcao_conclusao == 'a'){print "checked='checked'";} ?> onclick="javascript:exibe_msg_avaliar('a')"/>
        <font size="-1">Ato Inseguro</font></td>
        <td><input type="radio" name="radio3" id="conclusao_condicao_insegura" value="c" <?php if ($opcao_conclusao == 'c'){print "checked='checked'";} ?> onclick="javascript:exibe_msg_avaliar('c')"/>
        <font size="-1">Condi&ccedil;&atilde;o Insegura</font></td>
        <td><input type="radio" name="radio3" id="conclusao_ato_cond_insegura" value="d" <?php if ($opcao_conclusao == 'd'){print "checked='checked'";} ?> onclick="javascript:exibe_msg_avaliar('d')"/>
        <font size="-1">Ato/Condi&ccedil;&atilde;o Insegura </font></td>
        <td><input type="radio" name="radio3" id="conclusao_na" value="n" <?php if ($opcao_conclusao == 'n'){print "checked='checked'";} ?> onclick="javascript:exibe_msg_avaliar('n')"/>
        <font size="-1">N.A. </font></td>
      </tr>
      <tr>
        <td>&nbsp;</td>
        <td colspan="4"><font size="-1" color="#FF0000"><div id="msg_avaliar"></div></font></td>
      </tr>
    </table>
    <p>&nbsp;</p>
    <p>&nbsp;</p>
    <font color="#0000FF" size="-1"><b>An&aacute;lise cr&iacute;tica da A&ccedil;&atilde;o Imediata /Plano de A&ccedil;&atilde;o</b></font>
    
    <table width="1176" border="1" frame="box" rules="none">
      <tr>
        <td width="1" height="36">&nbsp;</td>
      	<td width="491"><font size="-1"><b>Forma de An&aacute;lise:</b></td>
      	<td width="1284"><font size="-1"><font color="red">*</font> <b>Data prevista para An&aacute;lise: </b>
      	    <font size="-1"><?php print $data_previsao_analise ?>
      	</font></td>    
	  </tr>
      <tr>
        <td colspan="3">&nbsp;<input type="checkbox" name="prox_audit_interna" id="prox_audit_interna" <?PHP if ($prox_audit_interna == 's') print 'checked'; ?> />
        <font size="-1">Na pr&oacute;xima auditoria interna</font></td>
      </tr>
      <tr>
        <td colspan="3">&nbsp;<input type="checkbox" name="prox_audit_externa" id="prox_audit_externa" <?PHP if ($prox_audit_externa == 's') print 'checked'; ?> />
        <font size="-1">Na pr&oacute;xima auditoria externa</font></td>
      </tr>
      <tr>
        <td colspan="3">&nbsp;<input type="checkbox" name="prox_audit_requisito" id="prox_audit_requisito" <?PHP if ($prox_audit_requisito == 's') print 'checked'; ?> />
        <font size="-1">Na pr&oacute;xima auditoria de requisito legal</font></td>
      </tr>
      <tr>
        <td colspan="3">&nbsp;<input type="checkbox" name="atraves_levantamento" id="atraves_levantamento" <?PHP if ($atraves_levantamento == 's') print 'checked'; ?> />
        <font size="-1">Atrav&eacute;s de levantamento de amostras para an&aacute;lise de reincid&ecirc;ncia</font></td>
      </tr>
      <tr>
        <td colspan="3">&nbsp;<input type="checkbox" name="outras_analise_critica" id="outras_analise_critica" <?PHP if ($outras_analise_critica == 's') print 'checked'; ?> />
          <font size="-1">Outras: </font> 
        <font size="-1"><?php print utf8_encode($desc_outras_analise_critica); ?></td>
      </tr>

      <tr>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
      </tr>
      <tr>
        <td>&nbsp;</td>
        <td><font size="-1"><b>Evid&ecirc;ncias:</b></font></td>
        <td>&nbsp;</td>
      </tr>
      <tr>
        <td>&nbsp;</td>
        <td colspan="2"><font size="-1"><?php print $evidencias	= utf8_encode($evidencias); ?></td>
      </tr>
      <tr>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
      </tr>
      <tr>
        <td>&nbsp;</td>
        <td colspan="2"><font size="-1"><b>Plano de A&ccedil;&atilde;o Eficaz?</b>&nbsp;&nbsp;&nbsp;&nbsp;
            <input type="radio" name="radio8" id="plano_eficaz_sim" value="s" <?php if ($opcao_plano_eficaz == 's'){print "checked='checked'";} ?> onclick="javascript:habilita_mudanca_analise(this)"/>
            <font size="-1">Sim</font> - Encerrar relat&oacute;rio&nbsp;&nbsp;&nbsp;&nbsp;
            <input type="radio" name="radio8" id="plano_eficaz_nao" value="n" <?php if ($opcao_plano_eficaz == 'n'){print "checked='checked'";} ?> onclick="javascript:habilita_mudanca_analise(this)"/>
            <font size="-1">N&atilde;o</font> &nbsp;&nbsp;- N&ordm; pr&oacute;ximo relat&oacute;rio&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            <font size="-1"><?php print $num_prox_relatorio ?>
        </font></td>
      </tr>
      <tr>
        <td colspan="3">&nbsp;</td>
      </tr>
    </table>    
    <p>&nbsp;</p>
</fieldset>
<?php
}

if ($investigacao_analise_id != '')
{
?>

<p class="break"></p>
<p>&nbsp;</p>
<fieldset>
  <center><b><font size="+2" color="#0000CC">Investiga&ccedil;&atilde;o e An&aacute;lise de Acidente / Incidente / Desvios</font></b></center>	
  <p>&nbsp;</p>      

    <table width="871" border="0">
    	<tr>
        	<td><font color="#0000FF" size="-1"><b>Dados do Funcion&aacute;rio
        	  
        	</b></font></td>
        </tr>
    </table>
    <table width="871" border="0">
    	<tr>
        	<td><font color="#0000FF" size="-1"><b>
        	  <?PHP
    
				$relatorio_envolvidos = $relatorios->dadosEnvolvidos('F',$acidente_id);
				
				$rel_envolvidos = $relatorio_envolvidos->relatorio;   
				$chapas = $relatorio_envolvidos->chapas; 
				$nomes = $relatorio_envolvidos->nomes;        
          
		  		print $rel_envolvidos;

			  ?>
        	</b></font></td>
      </tr>
    </table>
    
          	<?php
			
				$relatorio_placas_datafato = $relatorios->placasDatafato($acidente_id);
				
				$placas = $relatorio_placas_datafato->placas; 
				$data_fato = $relatorio_placas_datafato->datafato;   				         
          
			
           ?>    
    
    <table width="871" border="0">
    	<tr>
        	<td>
       	    <input name="investigacao_analise_id" type="hidden" id="investigacao_analise_id" value="<?php print $investigacao_analise_id ?>" size="5" /></td>
        </tr>
    </table>

    <table width="871" border="0">
    	<tr>
        	<td><font color="#0000FF" size="-1"><b>Les&atilde;o</b></font>
        	  
        	</td>
        </tr>
    </table>    
    
    <table width="1150" border="1" frame="box" rules="none">
    	<tr>
        	<td width="6" height="49">&nbsp;</td>
       	  <td width="110"><input type="checkbox" name="sem_lesao" id="sem_lesao" <?PHP if ($sem_lesao == 's') print 'checked'; ?> />
       	    <font size="-1">Sem les&atilde;o</font></td>
        	<td width="286"><input type="checkbox" name="com_lesao" id="com_lesao" <?PHP if ($com_lesao == 's') print 'checked'; ?> />
       	    <font size="-1">Com les&atilde;o - Parte do corpo atingida</font></td>
        	<td width="179"><input type="checkbox" name="sem_afastamento" id="sem_afastamento" <?PHP if ($sem_afastamento == 's') print 'checked'; ?> />
       	    <font size="-1">Sem afastamento</font></td>
        	<td width="535"><input type="checkbox" name="com_afastamento" id="com_afastamento" <?PHP if ($com_afastamento == 's') print 'checked'; ?> />
       	    <font size="-1">Com afastamento - Tempo prev. afastamento
       	    <font size="-1"><?php print $tempo_prev_afastamento ?>"
<label for="tempo_prev_afastamento"></label>
   	      </font></td>
        </tr>
    	<tr>
    	  <td>&nbsp;</td>
    	  <td colspan="4"><font size="-1"><b>Observa&ccedil;&atilde;o:</b></font></td>
   	  </tr>
    	<tr>
    	  <td>&nbsp;</td>
    	  <td colspan="4"><font size="-1">
    	    <?php print utf8_encode($observ_lesao) ?>
    	  </font></td>
  	  </tr>
    	<tr>
    	  <td>&nbsp;</td>
    	  <td colspan="4">&nbsp;</td>
  	  </tr>
   	  </table>
    
    <table width="871" border="0">
    	<tr>
        	<td>&nbsp;</td>
      </tr>
    </table>   
    
    <table width="871" border="0">
    	<tr>
        	<td width="126"><font size="-1">Foi Emitido CAT? </font></td>
        	<td width="735"><input type="radio" name="radio1" id="emitido_cat_sim" value="s" <?php if ($opcao_emitido_cat == 's'){print "checked='checked'";} ?> onclick="javascript:habilita_obs(this,1,'numero_cat')"/>
              <font size="-1">Sim</font> &nbsp;&nbsp;&nbsp;&nbsp;
              <input type="radio" name="radio1" id="emitido_cat_nao" value="n" <?php if ($opcao_emitido_cat == 'n'){print "checked='checked'";} ?> onclick="javascript:habilita_obs(this,1,'numero_cat')"/>
              <font size="-1">N&atilde;o</font> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<font size="-1">N&uacute;mero da CAT:</font>
          <font size="-1"><?php print $numero_cat ?></td>
      </tr>
    </table>   
    
    <table width="871" border="0">
    	<tr>
        	<td>&nbsp;</td>
      </tr>
    </table>   
    
    <table width="1146" border="0">
    	<tr>
        	<td><font size="-1"><strong>Cronologia  do Acidente / Incidente / Desvios</strong>:</font></td>
      </tr>
    	<tr>
    	  <td><font size="-1">
    	    <?php print utf8_encode($cronologia) ?>
    	  </font></td>
  	  </tr>
    </table>   
    
    <table width="871" border="0">
    	<tr>
        	<td>&nbsp;</td>
      </tr>
    </table>   
    
    <table width="1146" border="0">
    	<tr>
        	<td><font size="-1"><strong>Informa&ccedil;&atilde;o e A&ccedil;&otilde;es da chefia imediata:</strong></font></td>
      </tr>
    	<tr>
    	  <td><font size="-1">
    	    <?php print utf8_encode($informacao_acao_chefia) ?>
    	  </font></td>
  	  </tr>
    </table>                            
    
    <table width="871" border="0">
    	<tr>
        	<td>&nbsp;</td>
      </tr>
    </table>   
    
    <table width="871" border="0">
    	<tr>
       	  <td><font color="#0000FF" size="-1"><b>Coleta de Dados e Cronologia dos Eventos:</b></font></td>
      </tr>
    </table>   
    
    <table width="871" border="0">
    	<tr>
        	<td>&nbsp;</td>
      </tr>
    </table>  
    
    <table width="871" border="0">
    	<tr>
        	<td height="14"><p><strong><font size="-1">Jornada  de trabalho na data do evento</strong></p></td>
      </tr>
    </table>  
    
    <table width="1159" border="0">
    	<tr>
        	<td><font size="-1">Total de horas desde o in&iacute;cio do expediente /  jornada at&eacute; a hora do acidente: </font>
        	  <font size="-1"><?php print $desc_jornada_trabalho ?>
        	</td>
      </tr>
    </table>  
    
    <table width="871" border="0">
    	<tr>
        	<td>&nbsp;</td>
      </tr>
    </table>  
    
    <table width="871" border="0">
    	<tr>
        	<td><font size="-1"><strong>M&aacute;quinas,  ferramental, veiculo e/ou equipamentos envolvidos</strong>? 
        	  <input type="radio" name="radio2" id="maquina_ferramental_sim" value="s" <?php if ($opcao_maquina_ferramental == 's'){print "checked='checked'";} ?> onclick="javascript:habilita_obs(this,2,'desc_maquina_ferramental')"/>
              <font size="-1">Sim</font> &nbsp;&nbsp;&nbsp;&nbsp;
              <input type="radio" name="radio2" id="maquina_ferramental_nao" value="n" <?php if ($opcao_maquina_ferramental == 'n'){print "checked='checked'";} ?> onclick="javascript:habilita_obs(this,2,'desc_maquina_ferramental')"/>
            <font size="-1">N&atilde;o</font></td>
      </tr>
    </table>  
                    
    <table width="1143" border="0">
    	<tr>
        	<td><font size="-1">
        	  <?php print utf8_encode($desc_maquina_ferramental) ?>
        	</font></td>
      </tr>
    </table>  
    
    <table width="871" border="0">
    	<tr>
        	<td>&nbsp;</td>
      </tr>
    </table>  
    
    <table width="871" border="0">
    	<tr>
        	<td><font size="-1"><strong>Treinamentos  do funcion&aacute;rio</strong></td>
      </tr>
    </table>  

    <table width="871" border="0">
    	<tr>
        	<td>
            	<?php

                    $relatorio_treinamentos = $relatorios->treinamentosRealizados('F',$chapas,$acidente_id);
                    
                    $rel_treinamentos = $relatorio_treinamentos->relatorio;          
              
                    print $rel_treinamentos;
	
            	?>
          </td>
      </tr>
    </table> 
    
    <table width="871" border="0">
    	<tr>
        	<td>&nbsp;</td>
      </tr>
    </table>    
    
    <table width="1156" border="0">
    	<tr>
        	<td height="30"><font size="-1"><b>Estava encontrando dificuldades para executar o trabalho?</b> 
          &nbsp;&nbsp;&nbsp;&nbsp;
          <input type="radio" name="radio3" id="dificuldade_trabalho_sim" value="s" <?php if ($opcao_dificuldade_trabalho == 's'){print "checked='checked'";} ?> onclick="javascript:habilita_obs(this,3,'desc_dificuldade_trabalho')"/>
          <font size="-1">Sim</font> &nbsp;&nbsp;&nbsp;&nbsp;
          <input type="radio" name="radio3" id="dificuldade_trabalho_nao" value="n" <?php if ($opcao_dificuldade_trabalho == 'n'){print "checked='checked'";} ?> onclick="javascript:habilita_obs(this,3,'desc_dificuldade_trabalho')"/>
          <font size="-1">N&atilde;o</font> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<font size="-1">Obs</font><font size="-1">: 
          <font size="-1"><?php print utf8_encode($desc_dificuldade_trabalho) ?>
          </font></td>
   	  </tr>
    	<tr>
    	  <td height="30"><font size="-1"><b>Houve orienta&ccedil;&atilde;o da chefia ao iniciar o trabalho?</b>          &nbsp;&nbsp;&nbsp;&nbsp;
          <input type="radio" name="radio4" id="orientacao_chefia_sim" value="s" <?php if ($opcao_orientacao_chefia == 's'){print "checked='checked'";} ?> onclick="javascript:habilita_obs(this,4,'desc_orientacao_chefia')"/>
          <font size="-1">Sim</font> &nbsp;&nbsp;&nbsp;&nbsp;
          <input type="radio" name="radio4" id="orientacao_chefia_nao" value="n" <?php if ($opcao_orientacao_chefia == 'n'){print "checked='checked'";} ?> onclick="javascript:habilita_obs(this,4,'desc_orientacao_chefia')"/>
          <font size="-1">N&atilde;o &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<font size="-1">Obs:
          <font size="-1"><?php print utf8_encode($desc_orientacao_chefia) ?>
          </font></font></td>
   	  </tr>
    	<tr>
    	  <td height="30"><font size="-1"><b>Havia outras pessoas trabalhando no mesmo local do incidente/acidente?</b>          &nbsp;&nbsp;&nbsp;&nbsp;
          <input type="radio" name="radio5" id="outras_pessoas_local_sim" value="s" <?php if ($opcao_outras_pessoas_local == 's'){print "checked='checked'";} ?> onclick="javascript:habilita_obs(this,5,'desc_outras_pessoas_local')"/>
          <font size="-1">Sim</font> &nbsp;&nbsp;&nbsp;&nbsp;
          <input type="radio" name="radio5" id="outras_pessoas_local_nao" value="n" <?php if ($opcao_outras_pessoas_local == 'n'){print "checked='checked'";} ?> onclick="javascript:habilita_obs(this,5,'desc_outras_pessoas_local')"/>
          <font size="-1">N&atilde;o &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<font size="-1">Obs:
         <?php print utf8_encode($desc_outras_pessoas_local) ?>
          </font></font></td>
   	  </tr>
    	<tr>
    	  <td height="30"><font size="-1"><b>Comunicou o superior imediato sobre a ocorr&ecirc;ncia do incidente/acidente?</b>          &nbsp;&nbsp;&nbsp;&nbsp;
          <input type="radio" name="radio6" id="comunic_superior_sim" value="s" <?php if ($opcao_comunic_superior == 's'){print "checked='checked'";} ?> onclick="javascript:habilita_obs(this,6,'desc_comunic_superior')"/>
          <font size="-1">Sim</font> &nbsp;&nbsp;&nbsp;&nbsp;
          <input type="radio" name="radio6" id="comunic_superior_nao" value="n" <?php if ($opcao_comunic_superior == 'n'){print "checked='checked'";} ?> onclick="javascript:habilita_obs(this,6,'desc_comunic_superior')"/>
          <font size="-1">N&atilde;o &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<font size="-1">Obs:
          <font size="-1"><?php print utf8_encode($desc_comunic_superior) ?>
          </font></font></td>
   	  </tr>
    	<tr>
    	  <td height="30"><font size="-1"><b>No dia do evento estava bem de sa&uacute;de? </b> 
          &nbsp;&nbsp;&nbsp;&nbsp;
          <input type="radio" name="radio7" id="dia_evento_sim" value="s" <?php if ($opcao_dia_evento == 's'){print "checked='checked'";} ?> onclick="javascript:habilita_obs(this,7,'desc_dia_evento')"/>
          <font size="-1">Sim</font> &nbsp;&nbsp;&nbsp;&nbsp;
          <input type="radio" name="radio7" id="dia_evento_nao" value="n" <?php if ($opcao_dia_evento == 'n'){print "checked='checked'";} ?> onclick="javascript:habilita_obs(this,7,'desc_dia_evento')"/>
          <font size="-1">N&atilde;o</font></b><font size="-1">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<font size="-1">Obs:
          <font size="-1"><?php print utf8_encode($desc_dia_evento) ?>
          </font></font></td>
   	  </tr>
    	<tr>
    	  <td height="30"><font size="-1"><b>Toma algum rem&eacute;dio de uso cont&iacute;nuo?</b>          &nbsp;&nbsp;&nbsp;&nbsp;
          <input type="radio" name="radio8" id="remedio_continuo_sim" value="s" <?php if ($opcao_remedio_continuo == 's'){print "checked='checked'";} ?> onclick="javascript:habilita_obs(this,8,'desc_remedio_continuo')"/>
          <font size="-1">Sim</font> &nbsp;&nbsp;&nbsp;&nbsp;
          <input type="radio" name="radio8" id="remedio_continuo_nao" value="n" <?php if ($opcao_remedio_continuo == 'n'){print "checked='checked'";} ?> onclick="javascript:habilita_obs(this,8,'desc_remedio_continuo')"/>
          <font size="-1">N&atilde;o&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<font size="-1">Obs:
          <font size="-1"><?php print utf8_encode($desc_remedio_continuo) ?>
          </font></font></td>
   	  </tr>
    	<tr>
    	  <td height="30"><font size="-1"><b>Estava usando EPI (Equipamento de Prote&ccedil;&atilde;o Individual)? </b>
          &nbsp;&nbsp;&nbsp;&nbsp;
          <input type="radio" name="radio9" id="usando_epi_sim" value="s" <?php if ($opcao_usando_epi == 's'){print "checked='checked'";} ?> onclick="javascript:habilita_obs(this,9,'desc_usando_epi')"/>
          <font size="-1">Sim</font> &nbsp;&nbsp;&nbsp;&nbsp;
          <input type="radio" name="radio9" id="usando_epi_nao" value="n" <?php if ($opcao_usando_epi == 'n'){print "checked='checked'";} ?> onclick="javascript:habilita_obs(this,9,'desc_usando_epi')"/>
          <font size="-1">N&atilde;o</font><font size="-1">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<font size="-1">Obs:
          <?php print utf8_encode($desc_usando_epi) ?>
          </font></font>&nbsp; &nbsp;</td>
   	  </tr>
    	<tr>
    	  <td height="30"><p><font size="-1"><b>Voc&ecirc; tinha conhecimento do risco que estava exposto?&nbsp;(Checar informa&ccedil;&atilde;o  PGR/OS/F.77)</b>          &nbsp;&nbsp;&nbsp;&nbsp;
          <input type="radio" name="radio10" id="conhecimento_risco_sim" value="s" <?php if ($opcao_conhecimento_risco == 's'){print "checked='checked'";} ?> />
          <font size="-1">Sim</font> &nbsp;&nbsp;&nbsp;&nbsp;
          <input type="radio" name="radio10" id="conhecimento_risco_nao" value="n" <?php if ($opcao_conhecimento_risco == 'n'){print "checked='checked'";} ?> />
          <font size="-1">N&atilde;o</font></p></td>
   	  </tr>
    </table>    
    
    <table width="871" border="0">
    	<tr>
        	<td>&nbsp;</td>
      </tr>
    </table>      
    
    <table width="871" border="0">
    	<tr>
       	  <td width="249"><font size="-1" color="#0000FF"><b>Para poss&iacute;vel acidente de trajeto</b></font></td>
       	  <td width="612"><input type="checkbox" name="acid_traj_nao_aplic" id="acid_traj_nao_aplic" <?PHP if ($acid_traj_nao_aplic == 's') print 'checked'; ?> onclick="javascript:desabilita_acid_traj()"/><font size="-1"><b>
   	      N&atilde;o Aplic&aacute;vel</td>
      </tr>
    </table>      
    
    <table width="1152" border="1" frame="box" rules="none">
    	<tr>
            <td>&nbsp;</td>
        	<td height="30"><font size="-1"><b>O funcion&aacute;rio recebeu vale/transporte no per&iacute;odo?</b>&nbsp;
          &nbsp;&nbsp;&nbsp;&nbsp;
          <input type="radio" name="radio11" id="recebeu_vale_transp_sim" value="s" <?php if ($opcao_recebeu_vale_transp == 's'){print "checked='checked'";} ?> />
          <font size="-1">Sim</font> &nbsp;&nbsp;&nbsp;&nbsp;
          <input type="radio" name="radio11" id="recebeu_vale_transp_nao" value="n" <?php if ($opcao_recebeu_vale_transp == 'n'){print "checked='checked'";} ?> />
          <font size="-1">N&atilde;o</font>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; </td>
      </tr>
    	<tr>
            <td>&nbsp;</td>        
    	  <td height="30"><font size="-1"><b>Foi apresentado pelo funcion&aacute;rio o Boletim de Ocorr&ecirc;ncia?</b>          &nbsp;&nbsp;&nbsp;&nbsp;
          <input type="radio" name="radio12" id="apresentado_bo_sim" value="s" <?php if ($opcao_apresentado_bo == 's'){print "checked='checked'";} ?> onclick="javascript:habilita_obs(this,12,'desc_apresentado_bo')"/>
          <font size="-1">Sim</font> &nbsp;&nbsp;&nbsp;&nbsp;
          <input type="radio" name="radio12" id="apresentado_bo_nao" value="n" <?php if ($opcao_apresentado_bo == 'n'){print "checked='checked'";} ?> onclick="javascript:habilita_obs(this,12,'desc_apresentado_bo')"/>
          <font size="-1">N&atilde;o</font><font size="-1">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<font size="-1">Obs:
          <font size="-1"><?php print utf8_encode($desc_apresentado_bo) ?>
          </font></font>&nbsp; &nbsp;&nbsp; </td>
  	  </tr>
    	<tr>
            <td>&nbsp;</td>        
    	  <td height="50">
          <p>&nbsp;</p>
          <font size="-1"><b>Endere&ccedil;o do envolvido:</b></p>
          		
				<?php
                    $relatorio_endereco = $relatorios->enderecoEnvolvido('F',$chapas,$acidente_id);
                    
                    $rel_endereco = $relatorio_endereco->relatorio;          
              
                    print $rel_endereco;
                ?>             
          		<p>&nbsp;</p>          
          
<b>O trajeto do fato condiz com a rota  empresa/resid&ecirc;ncia, conforme comprovante de endere&ccedil;o no prontu&aacute;rio? </b>
          &nbsp;&nbsp;&nbsp;&nbsp;
          <input type="radio" name="radio13" id="trajeto_rota_sim" value="s" <?php if ($opcao_trajeto_rota == 's'){print "checked='checked'";} ?>/>
          <font size="-1">Sim</font> &nbsp;&nbsp;&nbsp;&nbsp;
          <input type="radio" name="radio13" id="trajeto_rota_nao" value="n" <?php if ($opcao_trajeto_rota == 'n'){print "checked='checked'";} ?>/>
          <font size="-1">N&atilde;o</font></td>
  	  </tr>
    	<tr>
          <td>&nbsp;</td>        
    	  <td height="30"><font size="-1"><b>Hor&aacute;rio do fato  condiz com a jornada de trabalho registrada no prontu&aacute;rio/ponto  eletr&ocirc;nico? </b>
          &nbsp;&nbsp;&nbsp;&nbsp;
          <input type="radio" name="radio14" id="horario_jornada_sim" value="s" <?php if ($opcao_horario_jornada == 's'){print "checked='checked'";} ?> onclick="javascript:habilita_obs(this,14,'desc_horario_jornada')"/>
          <font size="-1">Sim</font> &nbsp;&nbsp;&nbsp;&nbsp;
          <input type="radio" name="radio14" id="horario_jornada_nao" value="n" <?php if ($opcao_horario_jornada == 'n'){print "checked='checked'";} ?> onclick="javascript:habilita_obs(this,14,'desc_horario_jornada')"/>
          <font size="-1">N&atilde;o&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<font size="-1">Obs:
          <?php print utf8_encode($desc_horario_jornada) ?>
          </font></font>&nbsp; &nbsp;&nbsp; </td>
  	  </tr>
    	<tr>
          <td>&nbsp;</td>        
    	  <td height="30"><font size="-1"><b>Diante da an&aacute;lise  acima &eacute; caracterizado Acidente de Trajeto? </b>
          &nbsp;&nbsp;&nbsp;&nbsp;
          <input type="radio" name="radio15" id="acidente_trajeto_sim" value="s" <?php if ($opcao_acidente_trajeto == 's'){print "checked='checked'";} ?> onclick="javascript:habilita_obs(this,15,'desc_acidente_trajeto')"/>
          <font size="-1">Sim</font> &nbsp;&nbsp;&nbsp;&nbsp;
          <input type="radio" name="radio15" id="acidente_trajeto_nao" value="n" <?php if ($opcao_acidente_trajeto == 'n'){print "checked='checked'";} ?> onclick="javascript:habilita_obs(this,15,'desc_acidente_trajeto')"/>
          <font size="-1">N&atilde;o&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<font size="-1">Obs:
          <?php print utf8_encode($desc_acidente_trajeto) ?>
          </font></font></td>
  	  </tr>
   	  </table>      
    
    
    <table width="871" border="0">
    	<tr>
        	<td>&nbsp;</td>
      </tr>
    </table>      
    
    <table width="871" border="0">
    	<tr>
       	  <td width="205"><font size="-1" color="#0000FF"><b>Para acidentes de tr&acirc;nsito</b></font></td>
       	  <td width="656"><input type="checkbox" name="acid_tran_nao_aplic" id="acid_tran_nao_aplic" <?PHP if ($acid_tran_nao_aplic == 's') print 'checked'; ?> onclick="javascript:desabilita_acid_tran()"/><font size="-1"><b>
   	      N&atilde;o Aplic&aacute;vel</td>
      </tr>
    </table>      
    
    <table width="1152" height="304" border="1" frame="box" rules="none">
    	<tr>
            <td>&nbsp;</td>
        	<td height="83"><font size="-1">
        	  <p><b>O disco de tac&oacute;grafo est&aacute; dispon&iacute;vel para an&aacute;lise?</b></p>
        	  <p>
        	    <input type="radio" name="radio16" id="disco_tacografo_disp_sim" value="s" <?php if ($opcao_disco_tacografo_disp == 's'){print "checked='checked'";} ?> onclick="javascript:habilita_obs(this,16,'veloc_evidenc_disco')"/>
       	      <font size="-1">Sim&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</font>&nbsp;
       	      <input type="radio" name="radio16" id="disco_tacografo_disp_nao" value="n" <?php if ($opcao_disco_tacografo_disp == 'n'){print "checked='checked'";} ?> onclick="javascript:habilita_obs(this,16,'veloc_evidenc_disco')"/>
              <font size="-1">N&atilde;o&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</font>&nbsp;&nbsp;Qual a velocidade do  ve&iacute;culo evidenciada no disco de tac&oacute;grafo no momento do ocorrido? <font size="-1">
       	      <font size="-1"><?php print utf8_encode($veloc_evidenc_disco) ?>
       	      </font></p>
        	  <p>&nbsp;Coment&aacute;rios:&nbsp;&nbsp;<font size="-1">
   	      <font size="-1"><?php print utf8_encode($comentario_disco_tacografo) ?>
   	      </font>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</p></td>
      </tr>
    	<tr>
    	  <td>&nbsp;</td>
    	  <td height="16">&nbsp;</td>
  	  </tr>
    	<tr>
          <td>&nbsp;</td>        
    	  <td height="39"><font size="-1">
    	    <p><b>No momento do acidente teve condi&ccedil;&otilde;es adversas?</b>&nbsp;&nbsp;&nbsp;&nbsp;   	        </p>
    	    <p>Tempo:&nbsp;
    	      <input type="radio" name="radio17" id="tempo_sim" value="s" <?php if ($opcao_tempo == 's'){print "checked='checked'";} ?>/>
    	      <font size="-1">Sim</font> &nbsp;&nbsp;&nbsp;&nbsp;
    	      <input type="radio" name="radio17" id="tempo_nao" value="n" <?php if ($opcao_tempo == 'n'){print "checked='checked'";} ?>/>
   	      <font size="-1">N&atilde;o</font>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Luz:&nbsp;
            <input type="radio" name="radio18" id="luz_sim" value="s" <?php if ($opcao_luz == 's'){print "checked='checked'";} ?> />
            <font size="-1">Sim</font> &nbsp;&nbsp;&nbsp;&nbsp;
            <input type="radio" name="radio18" id="luz_nao" value="n" <?php if ($opcao_luz == 'n'){print "checked='checked'";} ?> />
            <font size="-1">N&atilde;o</font>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Rodovia:&nbsp;
            <input type="radio" name="radio19" id="rodovia_sim" value="s" <?php if ($opcao_rodovia == 's'){print "checked='checked'";} ?> />
            <font size="-1">Sim</font> &nbsp;&nbsp;&nbsp;&nbsp;
            <input type="radio" name="radio19" id="rodovia_nao" value="n" <?php if ($opcao_rodovia == 'n'){print "checked='checked'";} ?> />
          <font size="-1">N&atilde;o</font>&nbsp;&nbsp;</p></td>
  	  </tr>
    	<tr>
    	  <td>&nbsp;</td>
    	  <td height="16">&nbsp;</td>
  	  </tr>
    	<tr>
    	  <td>&nbsp;</td>
    	  <td height="22">
    	    <font size="-1">
            <p><font size="-1">Tr&acirc;nsito:
              <input type="radio" name="radio20" id="transito_sim" value="s" <?php if ($opcao_transito == 's'){print "checked='checked'";} ?>/>
              <font size="-1">Sim</font> &nbsp;&nbsp;&nbsp;&nbsp;
              <input type="radio" name="radio20" id="transito_nao" value="n" <?php if ($opcao_transito == 'n'){print "checked='checked'";} ?>/>
              <font size="-1">N&atilde;o</font>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Ve&iacute;culo:
              <input type="radio" name="radio21" id="veiculo_sim" value="s" <?php if ($opcao_veiculo == 's'){print "checked='checked'";} ?>/>
              <font size="-1">Sim</font> &nbsp;&nbsp;&nbsp;&nbsp;
              <input type="radio" name="radio21" id="veiculo_nao" value="n" <?php if ($opcao_veiculo == 'n'){print "checked='checked'";} ?>/>
              <font size="-1">N&atilde;o</font>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Carga:
              <input type="radio" name="radio22" id="carga_sim" value="s" <?php if ($opcao_carga == 's'){print "checked='checked'";} ?>/>
              <font size="-1">Sim</font> &nbsp;&nbsp;&nbsp;&nbsp;
              <input type="radio" name="radio22" id="carga_nao" value="n" <?php if ($opcao_carga == 'n'){print "checked='checked'";} ?>/>
              <font size="-1">N&atilde;o</font>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp; Motorista:
            <input type="radio" name="radio23" id="motorista_sim" value="s" <?php if ($opcao_motorista == 's'){print "checked='checked'";} ?>/>
              <font size="-1">Sim</font> &nbsp;&nbsp;&nbsp;&nbsp;
              <input type="radio" name="radio23" id="motorista_nao" value="n" <?php if ($opcao_motorista == 'n'){print "checked='checked'";} ?>/>
              <font size="-1">N&atilde;o</font>&nbsp;&nbsp;</font></p>
   	      </font></td>
  	  </tr>
    	<tr>
            <td>&nbsp;</td>        
    	  <td height="59"><p><font size="-1">Utilizava cinto de seguran&ccedil;a:
            <input type="radio" name="radio24" id="cinto_seguranca_sim" value="s" <?php if ($opcao_cinto_seguranca == 's'){print "checked='checked'";} ?>/>
            <font size="-1">Sim</font> &nbsp;&nbsp;&nbsp;&nbsp;
            <input type="radio" name="radio24" id="cinto_seguranca_nao" value="n" <?php if ($opcao_cinto_seguranca == 'n'){print "checked='checked'";} ?>/>
            <font size="-1">N&atilde;o</font>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Observa&ccedil;&atilde;o: <font size="-1">
            <font size="-1"><?php print utf8_encode($observacao_investig) ?>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; </font>Limite Velocidade da Pista: <font size="-1">
          <font size="-1"><?php print $limite_veloc_pista ?>
          </font></p></td>
  	  </tr>
    	<tr>
    	  <td>&nbsp;</td>
    	  <td height="19">&nbsp;</td>
  	  </tr>
    	<tr>
          <td>&nbsp;</td>        
    	  <td height="30"><font size="-1"><b>Foi feito boletim de Ocorr&ecirc;ncia? </b>
          &nbsp;&nbsp;&nbsp;&nbsp;
          <input type="radio" name="radio25" id="feito_bo_sim" value="s" <?php if ($opcao_feito_bo == 's'){print "checked='checked'";} ?> onclick="javascript:habilita_obs(this,25,'dados_bo')"/>
          <font size="-1">Sim</font> &nbsp;&nbsp;&nbsp;&nbsp;
          <input type="radio" name="radio25" id="feito_bo_nao" value="n" <?php if ($opcao_feito_bo == 'n'){print "checked='checked'";} ?> onclick="javascript:habilita_obs(this,25,'dados_bo')"/>
          <font size="-1">N&atilde;o &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Dados do boletim:
          <font size="-1"><?php print $dados_bo ?>
          &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Data: 
          <font size="-1"><?php print $data_bo ?>
          </font></td>
  	  </tr>
   	  </table>       
    
    <table width="871" border="0">
    	<tr>
        	<td>&nbsp;</td>
      </tr>
    </table>      


    <table width="871" border="0">
    	<tr>
       	  <td width="493"><font size="-1" color="#0000FF"><b>Para acidentes de tr&acirc;nsito com envolvimento de produto qu&iacute;mico</b></font></td>
       	  <td width="368"><input type="checkbox" name="acid_env_prod_nao_aplic" id="acid_env_prod_nao_aplic" <?PHP if ($acid_env_prod_nao_aplic == 's') print 'checked'; ?> onclick="javascript:desabilita_acid_env()"/><font size="-1"><b>
       	    N&atilde;o Aplic&aacute;vel</td>
      </tr>
    </table>      
    
    <table width="1152" height="34" border="1" frame="box" rules="none">
    	<tr>
            <td width="8">&nbsp;</td>
        	<td width="1128"><font size="-1">
       	    <p><b>Detalhar o perfil do motorista em velocidade</b>:</p>
       	    <p>
       	      <?php
			
				$relatorio_vel_ponta = $relatorios->velocidadePonta('F',$nomes,$data_fato);
				
				$rel_vel_ponta = $relatorio_vel_ponta->relatorio;          
          
		  		print $rel_vel_ponta;
				
            ?>
   	      </p>
       	    <p>&nbsp;</p>
       	    <p><font size="-1"><b>Observa&ccedil;&atilde;o Velocidade/Pontas:</b></font></p>
   	      <p><font size="-1">
   	        <font size="-1"><?php print utf8_encode($desc_vel_pontas) ?>
   	      </font></p>
   	      <p>&nbsp;</p></td>
      </tr>
    	<tr>
    	  <td>&nbsp;</td>
    	  <td><p>
    	  <font size="-1"><b>Registrar &uacute;ltimo per&iacute;odo  de <strong><u>f&eacute;rias gozadas</u></strong> pelo  funcion&aacute;rio:</b>
          	 <p>
          	   <?php
			
				$relatorio_ferias = $relatorios->feriasGozada('F',$chapas,$acidente_id);
				
				$rel_ferias = $relatorio_ferias->relatorio;          
          
		  		print $rel_ferias;
				
               ?>
           </p>
       	    <p>&nbsp;</p>
           <p>&nbsp;</p>    
          </td>
  	  </tr>
    	<tr>
    	  <td>&nbsp;</td>
    	  <td><p><font size="-1"><b>Registrar a data do  &uacute;ltimo <strong><u>exame m&eacute;dico peri&oacute;dico</u></strong> realizado pelo motorista inclusive se houve registro de identifica&ccedil;&atilde;o de  anormalidades em algum dos exames realizados ou recomenda&ccedil;&atilde;o m&eacute;dica:</b></p>
       	    <p>
       	      <?php
                    $relatorio_exame_per = $relatorios->examePeriodico('F',$chapas,$acidente_id);
                    
                    $rel_exame_per = $relatorio_exame_per->relatorio;          
              
                    print $rel_exame_per;
                ?>
           </p>
       	    <p>&nbsp;</p>    
           <p>Observa&ccedil;&atilde;o: <font size="-1">
           <font size="-1"><?php print utf8_encode($observ_exame_per) ?>
           </font></p>  
   	      <p>&nbsp;</p>
          <p>&nbsp;</p> 
         </td>
  	  </tr>
    	<tr>
    	  <td>&nbsp;</td>
    	  <td><p><font size="-1"><b>Registrar a data do  &uacute;ltimo <strong><u>exame psicol&oacute;gico</u></strong> realizado:</b></p>
          	 <p>&nbsp;</p>

				<?php
                    $relatorio_exame_psi = $relatorios->examePsicologico('F',$chapas,$acidente_id);
                    
                    $rel_exame_psi = $relatorio_exame_psi->relatorio;          
              
                    print $rel_exame_psi;
                ?>
           <p>&nbsp;</p>  
   	      <p>&nbsp;</p>          
         </td>
  	  </tr>
    	<tr>
    	  <td>&nbsp;</td>
    	  <td><p><font size="-1"><b>Registrar &uacute;ltima revis&atilde;o  de <strong><u>manuten&ccedil;&atilde;o preventiva</u></strong> realizada antes do acidente e os itens checados conforme D.07:</b></p>
    	    <p>&nbsp;</p>
          	   <?php
			
				$relatorio_manut = $relatorios->manutPreventiva('F',$placas,$data_fato);
				
				$rel_manut = $relatorio_manut->relatorio;          
          
		  		print $rel_manut;
				
               ?>
   	      <p>&nbsp;</p></td>
  	  </tr>
   	  </table> 
    
    <table width="871" border="0">
    	<tr>
        	<td>&nbsp;</td>
      </tr>
    </table>    
    
    <table width="1152" border="0">
    	<tr>
        	<td colspan="2"><font size="-1"><b>Houve testemunha?</b>
          &nbsp;&nbsp;&nbsp;&nbsp;
          <input type="radio" name="radio26" id="testemunha_sim" value="s" <?php if ($opcao_testemunha == 's'){print "checked='checked'";} ?> onclick="javascript:habilita_obs(this,26,'nome_testemunha')"/>
          <font size="-1">Sim</font> &nbsp;&nbsp;&nbsp;&nbsp;
          <input type="radio" name="radio26" id="testemunha_nao" value="n" <?php if ($opcao_testemunha == 'n'){print "checked='checked'";} ?> onclick="javascript:habilita_obs(this,26,'nome_testemunha')"/>
          <font size="-1">N&atilde;o </font></td>
      </tr>
    	<tr>
    	  <td width="528"><font size="-1">Nome:
   	      <font size="-1"><?php print $nome_testemunha ?></td>
    	  <td width="614"><font size="-1">Fun&ccedil;&atilde;o:
          <font size="-1"><?php print $funcao_testemunha ?>
    	  </font></td>
      </tr>
    	<tr>
    	  <td colspan="2">&nbsp;</td>
  	  </tr>
    	<tr>
    	  <td colspan="2"><font size="-1">Relato da testemunha:
    	      <font size="-1"><?php print utf8_encode($relato_testemunha) ?>
          </font></td>
  	  </tr>
   	  </table>    
    
    <table width="871" border="0">
    	<tr>
        	<td>&nbsp;</td>
      </tr>
    </table>
    <p>&nbsp;</p>
</fieldset>    
<?php
}

if ($analise_causa_id != '')
{

?>

<p class="break"></p>    
<p>&nbsp;</p>
<fieldset>
  <center><b><font size="+2" color="#0000CC">An&aacute;lise de Causa</font></b></center>	
  <p>&nbsp;</p>      
 	  <table width="871" border="0">
    	<tr>
        	<td><b><font color="#0000FF" size="-1">Descri&ccedil;&atilde;o do Fato</font></b></td>
        </tr>
    </table>
    <table width="1150" border="1" frame="box" rules="none">
    	<tr>
        	<td bgcolor="#FFFFFF">
            	<?php

					$dados_registro = $dados->dadosRegistro($acidente_id);
					$descricao_fato = $dados_registro->descricao_fato;            
				
					print utf8_encode($descricao_fato);
				?>            
            
            </td>
      </tr>
    </table>
    
	<table width="871" border="0">
    	<tr>
        	<td><input type="hidden" name="analise_causa_id" id="analise_causa_id" value="<?php print $analise_causa_id ?>"/></td>
      </tr>
    </table>   
    
    <table width="871" border="0">
    	<tr>
    	  <td>&nbsp;</td>
  	  </tr>
    	<tr>
        	<td><font size="-1"><strong>Efeito da NC / Acidente / Incidente / Desvio:</strong></font></td>
      </tr>
    	<tr>
    	  <td><font size="-1">
    	    <?php print utf8_encode($efeito_nc) ?>
    	  </font></td>
  	  </tr>
    </table>       
    
    <table width="871" border="0">
    	<tr>
        	<td>&nbsp;</td>
        </tr>
    </table>

    <table width="871" border="0">
    	<tr>
        	<td><font size="-1" color="#0000FF"><b>Porqu&ecirc;</b></font></td>
        </tr>
    </table>    
    
    <table width="1150" border="1" frame="box" rules="none">
    	<tr>
    	  <td width="4">&nbsp;</td>
    	  <td width="10" colspan="2">&nbsp;</td>
  	  </tr>
    	<tr>
    	  <td>&nbsp;</td>
    	  <td colspan="2">
          
                <?php

					$dados_porque = $dados->pqAnCausa($analise_causa_id,1);
					$descricao_porque = $dados_porque->relatorio;            
				
					print utf8_encode($descricao_porque);
					
				?> 
          
          
          </td>
  	  </tr>
    	<tr>
    	  <td>&nbsp;</td>
    	  <td colspan="2">&nbsp;</td>
  	  </tr>
   	  </table>
    
      
	<table width="871" border="0">
    	<tr>
        	<td>&nbsp;</td>
      </tr>
    </table>   
    
    <table width="871" border="0">
    	<tr>
        	<td><font size="-1"><strong>Causa Raiz:</strong></font></td>
      </tr>
    	<tr>
    	  <td><font size="-1">
    	    <?php print utf8_encode($causa_raiz) ?>
    	  </font></td>
  	  </tr>
    </table>   
 
    <table width="871" border="0">
    	<tr>
        	<td>&nbsp;</td>
      </tr>
    </table>
<table width="871" border="0">
    	<tr>
        	<td>&nbsp;</td>
      </tr>
    </table>  
<p>&nbsp;</p>
</fieldset>
<?php
}
//dados do plano de a��o	
$dados_campos_plano = $exibe_atividade->exibeCampos($acidente_id);  

$descricao_tipo = $dados_campos_plano->descricao_tipo; 
$titulo = $dados_campos_plano->titulo; 
$revisao = $dados_campos_plano->revisao; 
$data_emissao = $dados_campos_plano->data_emissao; 
$equipe_envolvida = $dados_campos_plano->equipe_envolvida; 
$lider = $dados_campos_plano->lider; 
$gerente = $dados_campos_plano->gerente; 
$tipo_acesso = $dados_campos_plano->tipo_acesso; 
$secao = $dados_campos_plano->secao; 
$ocorrencia = $dados_campos_plano->ocorrencia; 
$assunto_oc = $dados_campos_plano->assunto_oc; 
$status_oc = $dados_campos_plano->status_oc; 

	
if ($descricao_tipo != '')	
{
	?>

    
<p class="break"></p>    
<p>&nbsp;</p>
<fieldset>
  <center><b><font size="+2" color="#0000CC">Plano de A&ccedil;&atilde;o</font></b></center>	
  <p>&nbsp;</p>   

    <table width="871" border="0">        
    	<tr>
    	  <td>&nbsp;</td>
  	  </tr>
    </table>



<table width="1150" border="0" align="center">
  <tr>
    <td width="167" height="20"><strong><font >Tipo:</font> </strong></td>
    <td width="574"><strong><font >T&iacute;tulo:</font></strong>&nbsp;</td>
    <td width="124"><strong><font >Revis&atilde;o:</font></strong></td>
    <td width="126"><strong><font >Data</font>:</strong></td>
    <td width="137"><strong><font >N&ordm; Ocorr&ecirc;ncia: </font></strong></td>
  <tr>
    <td><font ><?php print $descricao_tipo ?></font></td>
    <td><font ><?php print utf8_encode($titulo) ?></font></td>
    <td><font ><?php print $revisao ?></font></td>
    <td><font ><?php print $data_emissao ?></font></td>
    <td><font ><?php print $acidente_id ?></font></td>
  <tr>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td colspan="2">&nbsp;</td>
    <td>&nbsp;</td>
  </table>
<table width="1150" border="0" align="center">
  <tr>
    <td width="464" height="18"><strong><font >Equipe Envolvida:
      
    </font></strong></td>
    <td width="348"><strong><font >L&iacute;der: 
          
    </font></strong></td>
    <td width="324"><strong><font >Gerente: 
      
    </font></strong></td>
  <tr>
    <td><font ><?php print $equipe_envolvida ?></font></td>
    <td><font ><?php print $lider ?></font></td>
    <td><font ><?php print $gerente ?></font></td>
  </table>  
<table width="1150" border="0" align="center">
  <tr>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td width="784">&nbsp;</td>  
  <tr>
    <td><strong><font >Tipo Acesso: </font></strong></td>
    <td>&nbsp;</td>
    <td width="784"><strong><font  color="#0000FF">Dados Totvs:</font></strong>
      
     
      
    </td>  
  <tr>
    <td width="130"><font ><?php print $tipo_acesso ?></font></td>
    <td width="222">&nbsp;</td>
    <td width="784" rowspan="4">
    
      <table width="785" height="48" border="1" align="left" frame="box" rules="none">
        <tr>
          <td width="8" rowspan="2"><p align="center">&nbsp;</p></td>
          <td width="75">
            <strong><font size="-1" >N&uacute;mero:</font></strong>&nbsp;</td>
          <td width="85"><font size="-1"><?php print $ocorrencia ?></font></td>
          <td width="589" height="20"><font >
          </font><font size="-1" ><b>&nbsp;            <strong>Status<font >:</font></strong>&nbsp;</b></font><font size="-1" ><?php print $status_oc ?></font></td>
          <tr>
            <td height="20"><font size="-1" ><b>
              Assunto:
              </b></font></td>
            <td colspan="2"><font size="-1" ><?php print $assunto_oc ?></font></td>
      </table>    
    </td>
    <tr>
    <td valign="top">&nbsp;</td>
    <td valign="top">&nbsp;</td>
    <tr>
      <td colspan="2" valign="top"><strong><font >&Aacute;rea (Se&ccedil;&atilde;o):</font></strong></td>
      <tr>
      <td colspan="2" valign="top"><font ><?php print $secao ?></font></td>
      <tr>
        <td colspan="2" valign="top">&nbsp;</td>
        <td>&nbsp;</td>
      <tr>
        <td colspan="2" valign="top">&nbsp;</td>
        <td>&nbsp;</td>
      <tr>
        <td colspan="3" align="left">
		<?php
	
		$dados_atividade = $exibe_atividade->exibeAtividades($acidente_id);  
	
		$rel_atividade = $dados_atividade->atividade; 
	
		print $rel_atividade;	
		
		?>
        </td>
      </tr>
    </table>


    <table width="871" border="0">
      <tr>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td colspan="2">&nbsp;</td>
      </tr>
      <tr>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td colspan="2">&nbsp;</td>
      </tr>
      <tr>
        <td><b>Conclus&atilde;o:</b></td>
        <td><input type="radio" name="radio27" id="conclusao_ato_inseguro" value="a" <?php if ($opcao_conclusao == 'a'){print "checked='checked'";} ?> disabled="disabled"/>
        Ato Inseguro</td>
        <td><input type="radio" name="radio27" id="conclusao_condicao_insegura" value="c" <?php if ($opcao_conclusao == 'c'){print "checked='checked'";} ?> disabled="disabled"/>
        Condi&ccedil;&atilde;o Insegura</td>
        <td colspan="2"><input type="radio" name="radio27" id="conclusao_na" value="n" <?php if ($opcao_conclusao == 'n'){print "checked='checked'";} ?> disabled="disabled"/>
        N.A. </td>
      </tr>
      <tr>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td colspan="2">&nbsp;</td>
      </tr>
      <tr>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td colspan="2">&nbsp;</td>
      </tr>   
    </table>
    
    <table width="1161" border="0">
    	<tr>
       	  <td><div align="center"><font size="+2"><b><u>Comiss&atilde;o de Investiga&ccedil;&atilde;o</u></b></font></div></td>
      </tr>
    </table>
    
    <table width="871" border="0">
    	<tr>
        	<td>&nbsp;</td>
      </tr>
    	<tr>
    	  <td>&nbsp;</td>
  	  </tr>
    	<tr>
    	  <td><b>Data da An&aacute;lise: ____ / ____ / ______
   	        <label for="data_1"></label></td>
   	  </tr>
    	<tr>
    	  <td>&nbsp;</td>
  	  </tr>
    </table>
    
    <table width="1159" border="0">
    	<tr>
    	  <td>&nbsp;</td>
    	  <td>&nbsp;</td>
  	  </tr>
    	<tr>
        	<td width="553"><b>____________________________________________</td>
        	<td width="596"><b>____________________________________________</td>
      </tr>
    	<tr>
    	  <td><b>Funcion&aacute;rio/Terceiro Envolvido no Evento</td>
    	  <td><b>T&eacute;cnico Seguran&ccedil;a do Trabalho</td>
      </tr>
    </table>
    
    <table width="871" border="0">
    	<tr>
        	<td>&nbsp;</td>
      </tr>
    </table>
    
    <table width="871" border="0">
    	<tr>
        	<td>&nbsp;</td>
      </tr>
    </table>
    
    <table width="1159" border="0">
    	<tr>
    	  <td>&nbsp;</td>
    	  <td>&nbsp;</td>
  	  </tr>
    	<tr>
        	<td width="553"><b>____________________________________________</td>
        	<td width="596"><b>____________________________________________</td>
      </tr>
    	<tr>
    	  <td><b>Superior Imediato do Funcion&aacute;rio/Terceiro</td>
    	  <td><b>Assinatura da Testemunha do Evento (Quando aplic&aacute;vel)</td>
      </tr>
    </table>
    
    <table width="871" border="0">
    	<tr>
        	<td>&nbsp;</td>
      </tr>
    </table>
    
    <table width="871" border="0">
    	<tr>
        	<td>&nbsp;</td>
      </tr>
    </table>
    
    <table width="1159" border="0">
    	<tr>
    	  <td>&nbsp;</td>
    	  <td>&nbsp;</td>
  	  </tr>
    	<tr>
        	<td width="553"><b>____________________________________________</td>
        	<td width="596"><b>____________________________________________</td>
      </tr>
    	<tr>
    	  <td><b>Presidente da Cipa</td>
    	  <td><b>Vice-Presidente da Cipa</td>
      </tr>
    </table> 
	<table width="871" border="0">
    	<tr>
        	<td>&nbsp;</td>
      </tr>
    </table>
	<table width="871" border="0">
    	<tr>
        	<td>&nbsp;</td>
      </tr>
    </table>
	
	<table width="1159" border="0">
    	<tr>
    	  <td>&nbsp;</td>
  	  </tr>
    	<tr>
        	<td width="596"><b>____________________________________________</td>
      </tr>
    	<tr>
    	  <td><b>Motorista Instrutor (Quando aplicável)</td>
      </tr>
    </table> 

<?php
}
?>
    
    
    
 	<p>&nbsp;</p>
    <center>
    	<div id='impress'>
			<input name='imprimir' type='button' class='botao_site' value=' Imprimir ' id='imprimir' onclick='impressao()'/>
            <input name='fechar' type='submit' class='botao_site' value=' Fechar ' id='fechar' />
        </div>
    </center>   
    
    
    
    
</fieldset>
</form>

</body>
</html>

