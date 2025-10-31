<?php
session_name("covre_ti");
session_start();
require("../SCA/includes/page_func.php");
include("../SCA/includes/conect_sqlserver.php");
require_once "class/Dados.php";
require_once "class/GravaLog.php";
require_once "class/RelatoriosInv.php";

$dados = new Dados;	
$grava_log = new GravaLog;	
$relatorios = new RelatoriosInv;	

$sistema_id				= 97;

$localItem = "../registro_acidentes/investigacao_analise.php";
$logado    = $_SESSION["usuario_logado"];
$acesso  = valida_acesso_popup($conSQL, $sistema_id, $logado);
//$acesso = "permitido";


if($acesso <> "permitido"){
    //grava_acesso($conSQL, $localItem, $logado, 2, $vObservacao);

    print "
        <script language = 'JavaScript'>
           alert('Acesso negado para esta pagina');
           window.location='centro.php';
		</script>
    ";
}//elseif
else{
	// grava_acesso($conSQL, $localItem, $logado, 1, $vObservacao);


	$acidente_id 			= $_GET["id"];
	
	//$acidente_id = 645;
	//$usuario_id = 79;
	
	//$investigacao_analise_id = 7;

	
	$opcao_emitido_cat			= $_POST["radio1"];
	$opcao_maquina_ferramental	= $_POST["radio2"];
	$opcao_dificuldade_trabalho	= $_POST["radio3"];
	$opcao_orientacao_chefia	= $_POST["radio4"];
	$opcao_outras_pessoas_local	= $_POST["radio5"];
	$opcao_comunic_superior		= $_POST["radio6"];
	$opcao_dia_evento			= $_POST["radio7"];
	$opcao_remedio_continuo		= $_POST["radio8"];
	$opcao_usando_epi			= $_POST["radio9"];
	$opcao_conhecimento_risco	= $_POST["radio10"];
	$opcao_recebeu_vale_transp	= $_POST["radio11"];
	$opcao_apresentado_bo		= $_POST["radio12"];
	$opcao_trajeto_rota			= $_POST["radio13"];
	$opcao_horario_jornada		= $_POST["radio14"];
	$opcao_acidente_trajeto		= $_POST["radio15"];
	$opcao_disco_tacografo_disp	= $_POST["radio16"];
	$opcao_tempo				= $_POST["radio17"];
	$opcao_luz					= $_POST["radio18"];
	$opcao_rodovia				= $_POST["radio19"];
	$opcao_transito				= $_POST["radio20"];
	$opcao_veiculo				= $_POST["radio21"];
	$opcao_carga				= $_POST["radio22"];		
	$opcao_motorista			= $_POST["radio23"];	
	$opcao_cinto_seguranca		= $_POST["radio24"];	
	$opcao_feito_bo				= $_POST["radio25"];	
	$opcao_testemunha			= $_POST["radio26"];	
	
	$sem_lesao					= $_POST["sem_lesao"];
	$com_lesao					= $_POST["com_lesao"];
	$sem_afastamento			= $_POST["sem_afastamento"];
	$com_afastamento			= $_POST["com_afastamento"];
	$tempo_prev_afastamento		= $_POST["tempo_prev_afastamento"];
	$observ_lesao				= $_POST["observ_lesao"];
	$numero_cat					= $_POST["numero_cat"];
	$cronologia					= $_POST["cronologia"];
	$informacao_acao_chefia		= $_POST["informacao_acao_chefia"];
	$desc_jornada_trabalho		= $_POST["desc_jornada_trabalho"];
	$desc_maquina_ferramental	= $_POST["desc_maquina_ferramental"];
	$desc_dificuldade_trabalho	= $_POST["desc_dificuldade_trabalho"];
	$desc_orientacao_chefia		= $_POST["desc_orientacao_chefia"];
	$desc_outras_pessoas_local	= $_POST["desc_outras_pessoas_local"];
	$desc_comunic_superior		= $_POST["desc_comunic_superior"];
	$desc_dia_evento			= $_POST["desc_dia_evento"];
	$desc_remedio_continuo		= $_POST["desc_remedio_continuo"];
	$desc_usando_epi			= $_POST["desc_usando_epi"];
	$desc_apresentado_bo		= $_POST["desc_apresentado_bo"];
	$desc_horario_jornada		= $_POST["desc_horario_jornada"];
	$desc_acidente_trajeto		= $_POST["desc_acidente_trajeto"];
	$veloc_evidenc_disco		= $_POST["veloc_evidenc_disco"];
	$comentario_disco_tacografo	= $_POST["comentario_disco_tacografo"];
	$observacao					= $_POST["observacao"];
	$limite_veloc_pista			= $_POST["limite_veloc_pista"];
	$dados_bo					= $_POST["dados_bo"];
	$data_bo					= $_POST["data_1"];
	$desc_vel_pontas			= $_POST["desc_vel_pontas"];
	$nome_testemunha			= $_POST["nome_testemunha"];
	$funcao_testemunha			= $_POST["funcao_testemunha"];
	$relato_testemunha			= $_POST["relato_testemunha"];
	$observ_exame_per			= $_POST["observ_exame_per"];
	$acid_traj_nao_aplic		= $_POST["acid_traj_nao_aplic"];
	$acid_tran_nao_aplic		= $_POST["acid_tran_nao_aplic"];
	$acid_env_prod_nao_aplic	= $_POST["acid_env_prod_nao_aplic"];		
	
	if ($investigacao_analise_id == '')
		$investigacao_analise_id	= $_POST["investigacao_analise_id"];
	
	
	$tempo_prev_afastamento 	= utf8_decode($tempo_prev_afastamento);
	$observ_lesao				= utf8_decode($observ_lesao);
	$numero_cat					= utf8_decode($numero_cat);
	$cronologia					= utf8_decode($cronologia);
	$informacao_acao_chefia		= utf8_decode($informacao_acao_chefia);
	$desc_jornada_trabalho		= utf8_decode($desc_jornada_trabalho);
	$desc_maquina_ferramental	= utf8_decode($desc_maquina_ferramental);
	$desc_dificuldade_trabalho	= utf8_decode($desc_dificuldade_trabalho);
	$desc_orientacao_chefia		= utf8_decode($desc_orientacao_chefia);
	$desc_outras_pessoas_local	= utf8_decode($desc_outras_pessoas_local);
	$desc_comunic_superior		= utf8_decode($desc_comunic_superior);
	$desc_dia_evento			= utf8_decode($desc_dia_evento);
	$desc_remedio_continuo		= utf8_decode($desc_remedio_continuo);
	$desc_usando_epi			= utf8_decode($desc_usando_epi);
	$desc_apresentado_bo		= utf8_decode($desc_apresentado_bo);
	$desc_horario_jornada		= utf8_decode($desc_horario_jornada);
	$desc_acidente_trajeto		= utf8_decode($desc_acidente_trajeto);
	$veloc_evidenc_disco		= utf8_decode($veloc_evidenc_disco);
	$comentario_disco_tacografo	= utf8_decode($comentario_disco_tacografo);
	$observacao					= utf8_decode($observacao);
	$limite_veloc_pista			= utf8_decode($limite_veloc_pista);
	$dados_bo					= utf8_decode($dados_bo);
	$nome_testemunha			= utf8_decode($nome_testemunha);
	$funcao_testemunha			= utf8_decode($funcao_testemunha);
	$relato_testemunha			= utf8_decode($relato_testemunha);	
	$observ_exame_per			= utf8_decode($observ_exame_per);	
	$desc_vel_pontas			= utf8_decode($desc_vel_pontas);	
	

	
	//$chapa = '21630';
	
	if($fechar != ""){
			print"
			<script language='javascript'>
				open(location, '_self').close();
			</script>
		";
	}
	
	
if ($gravar != '')
{

	
	if ($sem_lesao != '')
		$sem_lesao = 's';
	
	if ($com_lesao != '')
		$com_lesao = 's';

	if ($sem_afastamento != '')
		$sem_afastamento = 's';

	if ($com_afastamento != '')
		$com_afastamento = 's';
		
	if ($acid_traj_nao_aplic != '')
		$acid_traj_nao_aplic = 's';
		
	if ($acid_tran_nao_aplic != '')
		$acid_tran_nao_aplic = 's';
		
	if ($acid_env_prod_nao_aplic != '')
		$acid_env_prod_nao_aplic = 's';						
	
	$nova_data_bo = 
	implode(preg_match("~\/~", $data_bo) == 0 ? "/" : "-", 
	array_reverse(explode(preg_match("~\/~", $data_bo) == 0 ? "-" : "/", $data_bo)));		
	
	
	if (($com_afastamento == 's') && ($tempo_prev_afastamento == ''))
	{
		print "<script type='text/javascript'> alert(unescape('Favor preencher o tempo previsto de afastamento'));</script>";
		$erro = 1;
	}	
	else
	if (($opcao_emitido_cat == 's') && ($numero_cat == ''))
	{
		print "<script type='text/javascript'> alert(unescape('Favor preencher o numero do CAT'));</script>";
		$erro = 1;
	}	
	else
	if (($opcao_dificuldade_trabalho == 's') && ($desc_dificuldade_trabalho == ''))
	{
		print "<script type='text/javascript'> alert(unescape('Favor preencher as dificuldades encontradas para executar o trabalho'));</script>";
		$erro = 1;
	}		
	else
	if (($opcao_orientacao_chefia == 'n') && ($desc_orientacao_chefia == ''))
	{
		print "<script type='text/javascript'> alert(unescape('Favor preencher a observa%E7%E3o de orienta%E7%E3o da chefia'));</script>";
		$erro = 1;
	}			
	else
	if (($opcao_outras_pessoas_local == 's') && ($desc_outras_pessoas_local == ''))
	{
		print "<script type='text/javascript'> alert(unescape('Favor preencher as outras pessoas que trabalharam no mesmo local'));</script>";
		$erro = 1;
	}	
	else
	if (($opcao_comunic_superior == 'n') && ($desc_comunic_superior == ''))
	{
		print "<script type='text/javascript'> alert(unescape('Favor preencher o motivo da n%E3o comunica%E7%E3o com o superior'));</script>";
		$erro = 1;
	}				
	else
	if (($opcao_dia_evento == 'n') && ($desc_dia_evento == ''))
	{
		print "<script type='text/javascript'> alert(unescape('Favor preencher o que sentia no dia do evento'));</script>";
		$erro = 1;
	}
	else
	if (($opcao_remedio_continuo == 's') && ($desc_remedio_continuo == ''))
	{
		print "<script type='text/javascript'> alert(unescape('Favor informar o rem%E9dio de uso cont%EDnuo'));</script>";
		$erro = 1;
	}	
	else
	if (($opcao_usando_epi == 's') && ($desc_usando_epi == ''))
	{
		print "<script type='text/javascript'> alert(unescape('Favor informar o EPI utilizado'));</script>";
		$erro = 1;
	}			
	else
	if (($opcao_usando_epi == 's') && ($desc_usando_epi == ''))
	{
		print "<script type='text/javascript'> alert(unescape('Favor informar o EPI utilizado'));</script>";
		$erro = 1;
	}			
	else
	if ((($opcao_apresentado_bo == 's') || ($opcao_apresentado_bo == 'n')) && ($desc_apresentado_bo == ''))
	{
		print "<script type='text/javascript'> alert(unescape('Favor informar dados do BO apresentado pelo funcion%E1rio'));</script>";
		$erro = 1;
	}			
	else
	if (($opcao_horario_jornada == 'n') && ($desc_horario_jornada == ''))
	{
		print "<script type='text/javascript'> alert(unescape('Favor preencher a observa%E7%E3o do hor%E1rio do fato'));</script>";
		$erro = 1;
	}					
	else
	if ((($opcao_acidente_trajeto == 's') || ($opcao_acidente_trajeto == 'n')) && ($desc_acidente_trajeto == ''))
	{
		print "<script type='text/javascript'> alert(unescape('Favor informar se a an%E1lise foi caracterizada como acidente de trajeto'));</script>";
		$erro = 1;
	}		
	else
	if (($opcao_disco_tacografo_disp == 's') && ($veloc_evidenc_disco == ''))
	{
		print "<script type='text/javascript'> alert(unescape('Favor informar a velocidade evidenciada no disco de tac%F3grafo'));</script>";
		$erro = 1;
	}		
	else
	if (($opcao_feito_bo == 's') && ($dados_bo == ''))
	{
		print "<script type='text/javascript'> alert(unescape('Favor informar os dados do boletim de ocorr%EAncia'));</script>";
		$erro = 1;
	}
	else
	if (($opcao_feito_bo == 's') && ($data_bo == ''))
	{
		print "<script type='text/javascript'> alert(unescape('Favor informar a data do boletim de ocorr%EAncia'));</script>";
		$erro = 1;
	}		
	else
	if (($opcao_testemunha == 's') && (($nome_testemunha == '') || ($funcao_testemunha == '') || ($relato_testemunha == '')))
	{
		print "<script type='text/javascript'> alert(unescape('Favor informar o nome, fun%E7%E3o e relato da testemunha'));</script>";
		$erro = 1;
	}			
	else
	{
		if ($investigacao_analise_id == '')
		{
		
			$query1 = "insert into investigacao_analise (acidente_id, sem_lesao, com_lesao, sem_afastamento, com_afastamento, 
						tempo_prev_afastamento, observ_lesao, emitido_cat, numero_cat, cronologia, informacao_acao_chefia, 
						desc_jornada_trabalho, maquina_ferramental, desc_maquina_ferramental, dificuldade_trabalho, 
						desc_dificuldade_trabalho, orientacao_chefia, desc_orientacao_chefia, outras_pessoas_local, 
						desc_outras_pessoas_local, comunic_superior, desc_comunic_superior, dia_evento, desc_dia_evento, 
						remedio_continuo, desc_remedio_continuo, usando_epi, desc_usando_epi, conhecimento_risco, 
						recebeu_vale_transp, apresentado_bo, desc_apresentado_bo, trajeto_rota, horario_jornada, 
						desc_horario_jornada, acidente_trajeto, desc_acidente_trajeto, disco_tacografo_disp, veloc_evidenc_disco,
						comentario_disco_tacografo, tempo, luz, rodovia, transito, veiculo, carga, motorista, cinto_seguranca, 
						observacao, limite_veloc_pista, feito_bo, dados_bo, data_bo, desc_vel_pontas, testemunha, 
						nome_testemunha, funcao_testemunha, relato_testemunha, observ_exame_per, data_hora_gravacao, 
						user_gravacao, status_id, nao_aplica_acid_traj, nao_aplica_acid_tran, nao_aplica_acid_env)
						values ($acidente_id,
						case when '$sem_lesao' = '' then null else '$sem_lesao' end, 		
						case when '$com_lesao' = '' then null else '$com_lesao' end, 
						case when '$sem_afastamento' = '' then null else '$sem_afastamento' end, 		
						case when '$com_afastamento' = '' then null else '$com_afastamento' end,
						case when '$tempo_prev_afastamento' = '' then null else '$tempo_prev_afastamento' end, 
						case when '$observ_lesao' = '' then null else '$observ_lesao' end,
						case when '$opcao_emitido_cat' = '' then null else '$opcao_emitido_cat' end, 
						case when '$numero_cat' = '' then null else '$numero_cat' end,
						case when '$cronologia' = '' then null else '$cronologia' end,		
						case when '$informacao_acao_chefia' = '' then null else '$informacao_acao_chefia' end, 
						case when '$desc_jornada_trabalho' = '' then null else '$desc_jornada_trabalho' end, 						
						case when '$opcao_maquina_ferramental' = '' then null else '$opcao_maquina_ferramental' end,	
						case when '$desc_maquina_ferramental' = '' then null else '$desc_maquina_ferramental' end,	
						case when '$opcao_dificuldade_trabalho' = '' then null else '$opcao_dificuldade_trabalho' end,	
						case when '$desc_dificuldade_trabalho' = '' then null else '$desc_dificuldade_trabalho' end,	
						case when '$opcao_orientacao_chefia' = '' then null else '$opcao_orientacao_chefia' end, 	
						case when '$desc_orientacao_chefia' = '' then null else '$desc_orientacao_chefia' end,	
						case when '$opcao_outras_pessoas_local' = '' then null else '$opcao_outras_pessoas_local' end,	
						case when '$desc_outras_pessoas_local' = '' then null else '$desc_outras_pessoas_local' end,	
						case when '$opcao_comunic_superior' = '' then null else '$opcao_comunic_superior' end,
						case when '$desc_comunic_superior' = '' then null else '$desc_comunic_superior' end,
						case when '$opcao_dia_evento' = '' then null else '$opcao_dia_evento' end, 
						case when '$desc_dia_evento' = '' then null else '$desc_dia_evento' end,
						case when '$opcao_remedio_continuo' = '' then null else '$opcao_remedio_continuo' end,
						case when '$desc_remedio_continuo' = '' then null else '$desc_remedio_continuo' end,
						case when '$opcao_usando_epi' = '' then null else '$opcao_usando_epi' end, 
						case when '$desc_usando_epi' = '' then null else '$desc_usando_epi' end,
						case when '$opcao_conhecimento_risco' = '' then null else '$opcao_conhecimento_risco' end,
						case when '$opcao_recebeu_vale_transp' = '' then null else '$opcao_recebeu_vale_transp' end,
						case when '$opcao_apresentado_bo' = '' then null else '$opcao_apresentado_bo' end, 
						case when '$desc_apresentado_bo' = '' then null else '$desc_apresentado_bo' end, 
						case when '$opcao_trajeto_rota' = '' then null else '$opcao_trajeto_rota' end, 
						case when '$opcao_horario_jornada' = '' then null else '$opcao_horario_jornada' end, 
						case when '$desc_horario_jornada' = '' then null else '$desc_horario_jornada' end, 
						case when '$opcao_acidente_trajeto' = '' then null else '$opcao_acidente_trajeto' end, 																																																		
						case when '$desc_acidente_trajeto' = '' then null else '$desc_acidente_trajeto' end, 
						case when '$opcao_disco_tacografo_disp' = '' then null else '$opcao_disco_tacografo_disp' end, 
						case when '$veloc_evidenc_disco' = '' then null else '$veloc_evidenc_disco' end, 
						case when '$comentario_disco_tacografo' = '' then null else '$comentario_disco_tacografo' end, 
						case when '$opcao_tempo' = '' then null else '$opcao_tempo' end, 
						case when '$opcao_luz' = '' then null else '$opcao_luz' end, 
						case when '$opcao_rodovia' = '' then null else '$opcao_rodovia' end, 
						case when '$opcao_transito' = '' then null else '$opcao_transito' end, 
						case when '$opcao_veiculo' = '' then null else '$opcao_veiculo' end, 
						case when '$opcao_carga' = '' then null else '$opcao_carga' end, 
						case when '$opcao_motorista' = '' then null else '$opcao_motorista' end, 
						case when '$opcao_cinto_seguranca' = '' then null else '$opcao_cinto_seguranca' end, 
						case when '$observacao' = '' then null else '$observacao' end, 
						case when '$limite_veloc_pista' = '' then null else '$limite_veloc_pista' end, 
						case when '$opcao_feito_bo' = '' then null else '$opcao_feito_bo' end, 
						case when '$dados_bo' = '' then null else '$dados_bo' end, 
						case when '$data_bo' = '' then null else '$nova_data_bo' end,
						case when '$desc_vel_pontas' = '' then null else '$desc_vel_pontas' end,
						case when '$opcao_testemunha' = '' then null else '$opcao_testemunha' end, 
						case when '$nome_testemunha' = '' then null else '$nome_testemunha' end, 		
						case when '$funcao_testemunha' = '' then null else '$funcao_testemunha' end, 					
						case when '$relato_testemunha' = '' then null else '$relato_testemunha' end, 
						case when '$observ_exame_per' = '' then null else '$observ_exame_per' end, 											
						getdate(), (select top 1 id from usuario where usuario = '$logado' and status = 'a'), 'a',
						case when '$acid_traj_nao_aplic, ' = '' then null else '$acid_traj_nao_aplic' end,
						case when '$acid_tran_nao_aplic' = '' then null else '$acid_tran_nao_aplic' end,
						case when '$acid_env_prod_nao_aplic' = '' then null else '$acid_env_prod_nao_aplic' end
						)
						";
			print $query1;
			odbc_exec($conSQL, $query1) or die(odbc_errormsg($conSQL));	
			
			$query = "SELECT @@IDENTITY AS Ident";
			odbc_exec($conSQL, $query) or die(odbc_errormsg($conSQL)."<br>Erro ao selecionar o id inserido<br>");
			$result = odbc_exec($conSQL, $query) ;
			$investigacao_analise_id = odbc_result($result, 1);							
		
		}
		else
		{

			//LOG DE ALTERACAO
			$grava_log_alt = $grava_log->gravaLog1($acidente_id,'Sem lesao', 'sem_lesao', $sem_lesao);
			
			$grava_log_alt = $grava_log->gravaLog1($acidente_id,'Com lesao', 'com_lesao', $com_lesao );
			
			$grava_log_alt = $grava_log->gravaLog1($acidente_id,'Sem afastamento', 'sem_afastamento', $sem_afastamento);
			
			$grava_log_alt = $grava_log->gravaLog1($acidente_id,'Com Afastamento ', 'com_afastamento', $com_afastamento);
			
			$grava_log_alt = $grava_log->gravaLog1($acidente_id,'Tempo previsto afastamento', 'tempo_prev_afastamento', $tempo_prev_afastamento);
			
			$grava_log_alt = $grava_log->gravaLog1($acidente_id,'Observa&ccedil;&atilde;o les&atilde;o', 'observ_lesao', $observ_lesao);
			
			$grava_log_alt = $grava_log->gravaLog1($acidente_id,'Foi Emitido CAT', 'emitido_cat', $opcao_emitido_cat);
			
			$grava_log_alt = $grava_log->gravaLog1($acidente_id,'Numero da CAT', 'numero_cat', $numero_cat);
			
			$grava_log_alt = $grava_log->gravaLog1($acidente_id,'Cronologia do Acidente / Incidente / Desvios', 'cronologia', $cronologia);
			
			$grava_log_alt = $grava_log->gravaLog1($acidente_id,'Informa&ccedil;&atilde;o e A&ccedil;&otilde;es da chefia imediata', 'informacao_acao_chefia', $informacao_acao_chefia);
			
			$grava_log_alt = $grava_log->gravaLog1($acidente_id,'Jornada de Trabalho na data do evento', 'desc_jornada_trabalho', $desc_jornada_trabalho);
			
			$grava_log_alt = $grava_log->gravaLog1($acidente_id,'M&aacute;quinas, ferramental, veiculo e/ou equipamentos envolvidos', 'maquina_ferramental', $opcao_maquina_ferramental);
			
			$grava_log_alt = $grava_log->gravaLog1($acidente_id,'Obs. M&aacute;quinas, ferramental, veiculo e/ou equipamentos envolvidos', 'desc_maquina_ferramental', $desc_maquina_ferramental);
			
			$grava_log_alt = $grava_log->gravaLog1($acidente_id,'Estava encontrando dificuldades para executar o trabalho', 'dificuldade_trabalho', $opcao_dificuldade_trabalho);
			
			$grava_log_alt = $grava_log->gravaLog1($acidente_id,'Obs. Estava encontrando dificuldades para executar o trabalho', 'desc_dificuldade_trabalho', $desc_dificuldade_trabalho);
			
			$grava_log_alt = $grava_log->gravaLog1($acidente_id,'Houve orienta&ccedil;&atilde;o da chefia ao iniciar o trabalho', 'orientacao_chefia', $opcao_orientacao_chefia);
			
			$grava_log_alt = $grava_log->gravaLog1($acidente_id,'Obs. Houve orienta&ccedil;&atilde;o da chefia ao iniciar o trabalho', 'desc_orientacao_chefia', $desc_orientacao_chefia);
			
			$grava_log_alt = $grava_log->gravaLog1($acidente_id,'Havia outras pessoas trabalhando no mesmo local', 'outras_pessoas_local', $opcao_outras_pessoas_local);
			
			$grava_log_alt = $grava_log->gravaLog1($acidente_id,'Obs. Havia outras pessoas trabalhando no mesmo local', 'desc_outras_pessoas_local', $desc_outras_pessoas_local);
			
			$grava_log_alt = $grava_log->gravaLog1($acidente_id,'Comunicou o superior imediato sobre a ocorr&ecirc;ncia ', 'comunic_superior', $opcao_comunic_superior);
			
			$grava_log_alt = $grava_log->gravaLog1($acidente_id,'Obs. Comunicou o superior imediato sobre a ocorr&ecirc;ncia ', 'desc_comunic_superior', $desc_comunic_superior);
			
			$grava_log_alt = $grava_log->gravaLog1($acidente_id,'No dia do evento estava bem de sa&uacute;de', 'dia_evento', $opcao_dia_evento);
			
			$grava_log_alt = $grava_log->gravaLog1($acidente_id,'Obs. No dia do evento estava bem de sa&uacute;de', 'desc_dia_evento', $desc_dia_evento);
																																																																					
			$grava_log_alt = $grava_log->gravaLog1($acidente_id,'Toma algum rem&eacute;dio de uso cont&iacute;nuo','remedio_continuo', $opcao_remedio_continuo);
			
			$grava_log_alt = $grava_log->gravaLog1($acidente_id,'Obs. Toma algum rem&eacute;dio de uso cont&iacute;nuo', 'desc_remedio_continuo', $desc_remedio_continuo);
			
			$grava_log_alt = $grava_log->gravaLog1($acidente_id,'Estava usando EPI', 'usando_epi', $opcao_usando_epi);
			
			$grava_log_alt = $grava_log->gravaLog1($acidente_id,'Obs. Estava usando EPI', 'desc_usando_epi', $desc_usando_epi);
			
			$grava_log_alt = $grava_log->gravaLog1($acidente_id,'Voc&ecirc; tinha conhecimento do risco que estava exposto', 'conhecimento_risco', $opcao_conhecimento_risco);
			
			$grava_log_alt = $grava_log->gravaLog1($acidente_id,'O funcion&aacute;rio recebeu vale/transporte no per&iacute;odo', 'recebeu_vale_transp', $opcao_recebeu_vale_transp);
			
			$grava_log_alt = $grava_log->gravaLog1($acidente_id,'Foi apresentado pelo funcion&aacute;rio o Boletim de Ocorr&ecirc;ncia', 'apresentado_bo', $opcao_apresentado_bo);
			
			$grava_log_alt = $grava_log->gravaLog1($acidente_id,'Obs. Foi apresentado pelo funcion&aacute;rio o Boletim de Ocorr&ecirc;ncia', 'desc_apresentado_bo', $desc_apresentado_bo);
			
			$grava_log_alt = $grava_log->gravaLog1($acidente_id,'O trajeto do fato condiz com a rota empresa/resid&ecirc;ncia', 'trajeto_rota', $opcao_trajeto_rota);
			
			$grava_log_alt = $grava_log->gravaLog1($acidente_id,'Hor&aacute;rio do fato condiz com a jornada de trabalho registrada', 'horario_jornada', $opcao_horario_jornada);
			
			$grava_log_alt = $grava_log->gravaLog1($acidente_id,'Obs. Hor&aacute;rio do fato condiz com a jornada de trabalho registrada ', 'desc_horario_jornada', $desc_horario_jornada);
			
			$grava_log_alt = $grava_log->gravaLog1($acidente_id,'Diante da analise acima &eacute; caracterizado Acidente de Trajeto', 'acidente_trajeto', $opcao_acidente_trajeto);		
			
			$grava_log_alt = $grava_log->gravaLog1($acidente_id,'Obs. Diante da analise acima &eacute; caracterizado Acidente de Trajeto', 'desc_acidente_trajeto', $desc_acidente_trajeto);			
			
			$grava_log_alt = $grava_log->gravaLog1($acidente_id,'O disco de tac&oacute;grafo est&aacute; dispon&iacute;vel para an&aacute;lise', 'disco_tacografo_disp', $opcao_disco_tacografo_disp);
			
			$grava_log_alt = $grava_log->gravaLog1($acidente_id,'Qual a velocidade do ve&iacute;culo evidenciada no disco de tac&oacute;grafo', 'veloc_evidenc_disco', $veloc_evidenc_disco);
			
			$grava_log_alt = $grava_log->gravaLog1($acidente_id,'Coment&aacute;rios disco de tac&oacute;grafo', 'comentario_disco_tacografo', $comentario_disco_tacografo);
			
			$grava_log_alt = $grava_log->gravaLog1($acidente_id,'Tempo', 'tempo', $opcao_tempo);
			
			$grava_log_alt = $grava_log->gravaLog1($acidente_id,'Luz', 'luz', $opcao_luz);
			
			$grava_log_alt = $grava_log->gravaLog1($acidente_id,'Rodovia', 'rodovia', $opcao_rodovia);
			
			$grava_log_alt = $grava_log->gravaLog1($acidente_id,'Trânsito', 'transito ', $opcao_transito );
			
			$grava_log_alt = $grava_log->gravaLog1($acidente_id,'Ve&iacute;culo', 'veiculo', $opcao_veiculo);
			
			$grava_log_alt = $grava_log->gravaLog1($acidente_id,'Carga', 'carga', $opcao_carga);
			
			$grava_log_alt = $grava_log->gravaLog1($acidente_id,'Motorista', 'motorista', $opcao_motorista);
			
			$grava_log_alt = $grava_log->gravaLog1($acidente_id,'Utilizava cinto de segurança', 'cinto_seguranca', $opcao_cinto_seguranca);
			
			$grava_log_alt = $grava_log->gravaLog1($acidente_id,'Observa&ccedil;&atilde;o Limite de velocidade', 'observacao', $observacao);
			
			$grava_log_alt = $grava_log->gravaLog1($acidente_id,'Limite Velocidade da Pista', 'limite_veloc_pista', $limite_veloc_pista);
			
			$grava_log_alt = $grava_log->gravaLog1($acidente_id,' Foi feito boletim de ocorr&ecirc;ncia ', 'feito_bo', $opcao_feito_bo);
			
			$grava_log_alt = $grava_log->gravaLog1($acidente_id,'Dados do boletim de ocorr&ecirc;ncia', 'dados_bo', $dados_bo);
			
			$grava_log_alt = $grava_log->gravaLog1($acidente_id,'Data do boletim de ocorr&ecirc;ncia', 'data_bo', $nova_data_bo);
			
			$grava_log_alt = $grava_log->gravaLog1($acidente_id,'Observa&ccedil;&atilde;o Velocidade/Pontas', 'desc_vel_pontas',$desc_vel_pontas);			
						
			$grava_log_alt = $grava_log->gravaLog1($acidente_id,'Houve testemunha', 'testemunha', $opcao_testemunha);
			
			$grava_log_alt = $grava_log->gravaLog1($acidente_id,'Nome da testemunha', 'nome_testemunha', $nome_testemunha);																																																								
			
			$grava_log_alt = $grava_log->gravaLog1($acidente_id,'Funcao da testemunha', 'funcao_testemunha', $funcao_testemunha);																																																								
			
			$grava_log_alt = $grava_log->gravaLog1($acidente_id,'Relato da testemunha', 'relato_testemunha', $relato_testemunha);																																																														
																																				
			$grava_log_alt = $grava_log->gravaLog1($acidente_id,'Observa&ccedil;&atilde;o Exame Peri&oacute;dico', 'observ_exame_per', $observ_exame_per);																																																																
		
			
			$grava_log_alt = $grava_log->gravaLog1($acidente_id,'N&atilde;o aplicavel - Acidente Trajeto', 'nao_aplica_acid_traj', $acid_traj_nao_aplic);
			$grava_log_alt = $grava_log->gravaLog1($acidente_id,'N&atilde;o aplicavel - Acidente Transito', 'nao_aplica_acid_tran', $acid_tran_nao_aplic);
			$grava_log_alt = $grava_log->gravaLog1($acidente_id,'N&atilde;o aplicavel - Acidente Transito Prod. Quimico', 'nao_aplica_acid_env', $acid_env_prod_nao_aplic);
		
		
			$query1 = "update investigacao_analise 
						set sem_lesao = case when '$sem_lesao' = '' then null else '$sem_lesao' end, 		
						com_lesao = case when '$com_lesao' = '' then null else '$com_lesao' end, 
						sem_afastamento = case when '$sem_afastamento' = '' then null else '$sem_afastamento' end, 		
						com_afastamento = case when '$com_afastamento' = '' then null else '$com_afastamento' end,
						tempo_prev_afastamento = case when '$tempo_prev_afastamento' = '' then null else 
						'$tempo_prev_afastamento' end, 
						observ_lesao = case when '$observ_lesao' = '' then null else '$observ_lesao' end,
						emitido_cat = case when '$opcao_emitido_cat' = '' then null else '$opcao_emitido_cat' end, 
						numero_cat = case when '$numero_cat' = '' then null else '$numero_cat' end,
						cronologia = case when '$cronologia' = '' then null else '$cronologia' end,		
						informacao_acao_chefia = case when '$informacao_acao_chefia' = '' then null else 
						'$informacao_acao_chefia' end,
						desc_jornada_trabalho = case when '$desc_jornada_trabalho' = '' then null else '$desc_jornada_trabalho' 
						end,	
						maquina_ferramental = case when '$opcao_maquina_ferramental' = '' then null else 
						'$opcao_maquina_ferramental' end,	
						desc_maquina_ferramental = case when '$desc_maquina_ferramental' = '' then null else 
						'$desc_maquina_ferramental' end,	
						dificuldade_trabalho = case when '$opcao_dificuldade_trabalho' = '' then null else 
						'$opcao_dificuldade_trabalho' end,	
						desc_dificuldade_trabalho = case when '$desc_dificuldade_trabalho' = '' then null else 
						'$desc_dificuldade_trabalho' end,	
						orientacao_chefia = case when '$opcao_orientacao_chefia' = '' then null else '$opcao_orientacao_chefia' 
						end, 	
						desc_orientacao_chefia = case when '$desc_orientacao_chefia' = '' then null else 
						'$desc_orientacao_chefia' end,	
						outras_pessoas_local = case when '$opcao_outras_pessoas_local' = '' then null else 
						'$opcao_outras_pessoas_local' end,	
						desc_outras_pessoas_local = case when '$desc_outras_pessoas_local' = '' then null else 
						'$desc_outras_pessoas_local' end,	
						comunic_superior = case when '$opcao_comunic_superior' = '' then null else '$opcao_comunic_superior' end,
						desc_comunic_superior = case when '$desc_comunic_superior' = '' then null else '$desc_comunic_superior' 
						end,
						dia_evento = case when '$opcao_dia_evento' = '' then null else '$opcao_dia_evento' end, 
						desc_dia_evento = case when '$desc_dia_evento' = '' then null else '$desc_dia_evento' end,
						remedio_continuo = case when '$opcao_remedio_continuo' = '' then null else '$opcao_remedio_continuo' end,
						desc_remedio_continuo = case when '$desc_remedio_continuo' = '' then null else '$desc_remedio_continuo' 
						end,
						usando_epi = case when '$opcao_usando_epi' = '' then null else '$opcao_usando_epi' end, 
						desc_usando_epi = case when '$desc_usando_epi' = '' then null else '$desc_usando_epi' end,
						conhecimento_risco = case when '$opcao_conhecimento_risco' = '' then null else 
						'$opcao_conhecimento_risco' end,
						recebeu_vale_transp = case when '$opcao_recebeu_vale_transp' = '' then null else 
						'$opcao_recebeu_vale_transp' end,
						apresentado_bo = case when '$opcao_apresentado_bo' = '' then null else '$opcao_apresentado_bo' end, 
						desc_apresentado_bo = case when '$desc_apresentado_bo' = '' then null else '$desc_apresentado_bo' end, 
						trajeto_rota = case when '$opcao_trajeto_rota' = '' then null else '$opcao_trajeto_rota' end, 
						horario_jornada = case when '$opcao_horario_jornada' = '' then null else '$opcao_horario_jornada' end, 
						desc_horario_jornada = case when '$desc_horario_jornada' = '' then null else '$desc_horario_jornada' end, 						acidente_trajeto = case when '$opcao_acidente_trajeto' = '' then null else '$opcao_acidente_trajeto' end, 						desc_acidente_trajeto = case when '$desc_acidente_trajeto' = '' then null else '$desc_acidente_trajeto' 
						end, 
						disco_tacografo_disp = case when '$opcao_disco_tacografo_disp' = '' then null else 
						'$opcao_disco_tacografo_disp' end, 
						veloc_evidenc_disco = case when '$veloc_evidenc_disco' = '' then null else '$veloc_evidenc_disco' end, 
						comentario_disco_tacografo= case when '$comentario_disco_tacografo' = '' then null else 
						'$comentario_disco_tacografo' end, 						
						tempo = case when '$opcao_tempo' = '' then null else '$opcao_tempo' end, 
						luz = case when '$opcao_luz' = '' then null else '$opcao_luz' end, 
						rodovia = case when '$opcao_rodovia' = '' then null else '$opcao_rodovia' end, 
						transito = case when '$opcao_transito' = '' then null else '$opcao_transito' end, 
						veiculo = case when '$opcao_veiculo' = '' then null else '$opcao_veiculo' end, 
						carga = case when '$opcao_carga' = '' then null else '$opcao_carga' end, 
						motorista = case when '$opcao_motorista' = '' then null else '$opcao_motorista' end, 
						cinto_seguranca = case when '$opcao_cinto_seguranca' = '' then null else '$opcao_cinto_seguranca' end, 
						observacao = case when '$observacao' = '' then null else '$observacao' end, 
						limite_veloc_pista = case when '$limite_veloc_pista' = '' then null else '$limite_veloc_pista' end, 
						feito_bo = case when '$opcao_feito_bo' = '' then null else '$opcao_feito_bo' end, 
						dados_bo = case when '$dados_bo' = '' then null else '$dados_bo' end, 
						data_bo = case when '$data_bo' = '' then null else '$nova_data_bo' end,
						desc_vel_pontas = case when '$desc_vel_pontas' = '' then null else '$desc_vel_pontas' end,
						testemunha = case when '$opcao_testemunha' = '' then null else '$opcao_testemunha' end, 
						nome_testemunha = case when '$nome_testemunha' = '' then null else '$nome_testemunha' end, 		
						funcao_testemunha = case when '$funcao_testemunha' = '' then null else '$funcao_testemunha' end, 					
						relato_testemunha = case when '$relato_testemunha' = '' then null else '$relato_testemunha' end,
						observ_exame_per = case when '$observ_exame_per' = '' then null else '$observ_exame_per' end,
						nao_aplica_acid_traj = case when '$acid_traj_nao_aplic' = '' then null else '$acid_traj_nao_aplic' end,
						nao_aplica_acid_tran = case when '$acid_tran_nao_aplic' = '' then null else '$acid_tran_nao_aplic' end,
						nao_aplica_acid_env = case when '$acid_env_prod_nao_aplic' = '' then null else '$acid_env_prod_nao_aplic' 						end  											
						where id = $investigacao_analise_id";
			//print $query1;
			odbc_exec($conSQL, $query1) or die(odbc_errormsg($conSQL));				
		
		
		}		
		
	}

}

	if (($acidente_id != '') && ($erro != 1))
	{
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
		$observacao = $dados_invest_an->observacao;
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

<!--
<script language="javascript">
function habilita_obs(elmnt,numero,campo)
{
	//alert(campo);
	
	if (numero == 25)
	{
		if(elmnt.value == 's')
		{
			document.form1.dados_bo.disabled = false;
			document.form1.data_1.disabled = false;
		}
		else
		{
			document.form1.dados_bo.value = '';
			document.form1.dados_bo.disabled = true;		
			document.form1.data_1.value = '';
			document.form1.data_1.disabled = true;	
		}	
	}
	else
		if (numero == 26)
		{
			if(elmnt.value == 's')
			{
				document.form1.nome_testemunha.disabled = false;
				document.form1.funcao_testemunha.disabled = false;
				document.form1.relato_testemunha.disabled = false;		
			}
			else
			{
				document.form1.nome_testemunha.value = '';
				document.form1.nome_testemunha.disabled = true;		
				document.form1.funcao_testemunha.value = '';
				document.form1.funcao_testemunha.disabled = true;	
				document.form1.relato_testemunha.value = '';
				document.form1.relato_testemunha.disabled = true;					
			}	
		}
		else
			if (numero > 0)
			{
				if(elmnt.value == 's')
					document.getElementById(campo).disabled = false;
				else
				{
					document.getElementById(campo).value = '';
					document.getElementById(campo).disabled = true;		
				}
			}
			else
			{
				
				if(document.form1.radio1[0].checked == 1)
					document.form1.numero_cat.disabled = false;
				else
				{
					document.form1.numero_cat.value = '';
					document.form1.numero_cat.disabled = true;		
				}
				
				if(document.form1.radio2[0].checked == 1)
					document.form1.desc_maquina_ferramental.disabled = false;
				else
				{
					document.form1.desc_maquina_ferramental.value = '';
					document.form1.desc_maquina_ferramental.disabled = true;		
				}	
				
				if(document.form1.radio3[0].checked == 1)
					document.form1.desc_dificuldade_trabalho.disabled = false;
				else
				{
					document.form1.desc_dificuldade_trabalho.value = '';
					document.form1.desc_dificuldade_trabalho.disabled = true;		
				}		
		
				if(document.form1.radio4[0].checked == 1)
					document.form1.desc_orientacao_chefia.disabled = false;
				else
				{
					document.form1.desc_orientacao_chefia.value = '';
					document.form1.desc_orientacao_chefia.disabled = true;		
				}		
		
				if(document.form1.radio5[0].checked == 1)
					document.form1.desc_outras_pessoas_local.disabled = false;
				else
				{
					document.form1.desc_outras_pessoas_local.value = '';
					document.form1.desc_outras_pessoas_local.disabled = true;		
				}		
		
				if(document.form1.radio6[0].checked == 1)
					document.form1.desc_comunic_superior.disabled = false;
				else
				{
					document.form1.desc_comunic_superior.value = '';
					document.form1.desc_comunic_superior.disabled = true;		
				}		
		
				if(document.form1.radio7[0].checked == 1)
					document.form1.desc_dia_evento.disabled = false;
				else
				{
					document.form1.desc_dia_evento.value = '';
					document.form1.desc_dia_evento.disabled = true;		
				}		
		
				if(document.form1.radio8[0].checked == 1)
					document.form1.desc_remedio_continuo.disabled = false;
				else
				{
					document.form1.desc_remedio_continuo.value = '';
					document.form1.desc_remedio_continuo.disabled = true;		
				}		
		
				if(document.form1.radio9[0].checked == 1)
					document.form1.desc_usando_epi.disabled = false;
				else
				{
					document.form1.desc_usando_epi.value = '';
					document.form1.desc_usando_epi.disabled = true;		
				}		
		
				if(document.form1.radio12[0].checked == 1)
					document.form1.desc_apresentado_bo.disabled = false;
				else
				{
					document.form1.desc_apresentado_bo.value = '';
					document.form1.desc_apresentado_bo.disabled = true;		
				}		
		
				if(document.form1.radio14[0].checked == 1)
					document.form1.desc_horario_jornada.disabled = false;
				else
				{
					document.form1.desc_horario_jornada.value = '';
					document.form1.desc_horario_jornada.disabled = true;		
				}	
				
				if(document.form1.radio15[0].checked == 1)
					document.form1.desc_acidente_trajeto.disabled = false;
				else
				{
					document.form1.desc_acidente_trajeto.value = '';
					document.form1.desc_acidente_trajeto.disabled = true;		
				}				
		
				if(document.form1.radio16[0].checked == 1)
					document.form1.veloc_evidenc_disco.disabled = false;
				else
				{
					document.form1.veloc_evidenc_disco.value = '';
					document.form1.veloc_evidenc_disco.disabled = true;		
				}
				
				if(document.form1.radio25[0].checked == 1)
				{
					document.form1.dados_bo.disabled = false;
					document.form1.data_1.disabled = false;
				}
				else
				{
					document.form1.dados_bo.value = '';
					document.form1.dados_bo.disabled = true;		
					document.form1.data_1.value = '';
					document.form1.data_1.disabled = true;						
				}	
				
				if(document.form1.radio26[0].checked == 1)
				{
					document.form1.nome_testemunha.disabled = false;
					document.form1.funcao_testemunha.disabled = false;	
					document.form1.relato_testemunha.disabled = false;						
				}
				else
				{
					document.form1.nome_testemunha.value = '';
					document.form1.nome_testemunha.disabled = true;		
					document.form1.funcao_testemunha.value = '';
					document.form1.funcao_testemunha.disabled = true;	
					document.form1.relato_testemunha.value = '';
					document.form1.relato_testemunha.disabled = true;									
				}							
				
			
			}
	
}
</script>

<script language="javascript">
function charLimit(limitField, limitNum) 
{
  if (limitField.value.length > limitNum) 
  {
  	alert(unescape("O Valor m%E1ximo do campo s%E3o "+limitNum+" caracteres."));
    limitField.value = limitField.value.substring(0, limitNum);
  }
}
</script>
 -->
 
<script language="javascript">
function desabilita_acid_traj(){
	//alert(elmnt.checked);

	if(document.form1.acid_traj_nao_aplic.checked == true)
	{
		document.form1.recebeu_vale_transp_sim.disabled = true;
		document.form1.recebeu_vale_transp_nao.disabled = true;
		document.form1.recebeu_vale_transp_nao.disabled = true;
		document.form1.apresentado_bo_sim.disabled = true;
		document.form1.apresentado_bo_nao.disabled = true;
		document.form1.desc_apresentado_bo.disabled = true;
		document.form1.trajeto_rota_sim.disabled = true;
		document.form1.trajeto_rota_nao.disabled = true;
		document.form1.horario_jornada_sim.disabled = true;
		document.form1.horario_jornada_nao.disabled = true;
		document.form1.desc_horario_jornada.disabled = true;
		document.form1.acidente_trajeto_sim.disabled = true;
		document.form1.acidente_trajeto_nao.disabled = true;
		document.form1.desc_acidente_trajeto.disabled = true;
	}
	else
	{
		document.form1.recebeu_vale_transp_sim.disabled = false;
		document.form1.recebeu_vale_transp_nao.disabled = false;
		document.form1.recebeu_vale_transp_nao.disabled = false;
		document.form1.apresentado_bo_sim.disabled = false;
		document.form1.apresentado_bo_nao.disabled = false;
		document.form1.desc_apresentado_bo.disabled = false;
		document.form1.trajeto_rota_sim.disabled = false;
		document.form1.trajeto_rota_nao.disabled = false;
		document.form1.horario_jornada_sim.disabled = false;
		document.form1.horario_jornada_nao.disabled = false;
		document.form1.desc_horario_jornada.disabled = false;
		document.form1.acidente_trajeto_sim.disabled = false;
		document.form1.acidente_trajeto_nao.disabled = false;
		document.form1.desc_acidente_trajeto.disabled = false;		
	}
}
</script>
 
 
<script language="javascript">
function desabilita_acid_tran(){
	//alert(elmnt.checked);

	if(document.form1.acid_tran_nao_aplic.checked == true)
	{
		document.form1.disco_tacografo_disp_sim.disabled = true;
		document.form1.disco_tacografo_disp_nao.disabled = true;
		document.form1.veloc_evidenc_disco.disabled = true;
		document.form1.comentario_disco_tacografo.disabled = true;
		document.form1.tempo_sim.disabled = true;
		document.form1.tempo_nao.disabled = true;
		document.form1.luz_sim.disabled = true;
		document.form1.luz_nao.disabled = true;
		document.form1.rodovia_sim.disabled = true;
		document.form1.rodovia_nao.disabled = true;
		document.form1.transito_sim.disabled = true;
		document.form1.transito_nao.disabled = true;
		document.form1.veiculo_sim.disabled = true;
		document.form1.veiculo_nao.disabled = true;
		document.form1.carga_sim.disabled = true;
		document.form1.carga_nao.disabled = true;
		document.form1.motorista_sim.disabled = true;
		document.form1.motorista_nao.disabled = true;
		document.form1.cinto_seguranca_sim.disabled = true;
		document.form1.cinto_seguranca_nao.disabled = true;
		document.form1.observacao.disabled = true;
		document.form1.limite_veloc_pista.disabled = true;
		document.form1.feito_bo_sim.disabled = true;
		document.form1.feito_bo_nao.disabled = true;
		document.form1.dados_bo.disabled = true;
		document.form1.data_1.disabled = true;
	}
	else
	{
		document.form1.disco_tacografo_disp_sim.disabled = false;
		document.form1.disco_tacografo_disp_nao.disabled = false;
		document.form1.veloc_evidenc_disco.disabled = false;
		document.form1.comentario_disco_tacografo.disabled = false;
		document.form1.tempo_sim.disabled = false;
		document.form1.tempo_nao.disabled = false;
		document.form1.luz_sim.disabled = false;
		document.form1.luz_nao.disabled = false;
		document.form1.rodovia_sim.disabled = false;
		document.form1.rodovia_nao.disabled = false;
		document.form1.transito_sim.disabled = false;
		document.form1.transito_nao.disabled = false;
		document.form1.veiculo_sim.disabled = false;
		document.form1.veiculo_nao.disabled = false;
		document.form1.carga_sim.disabled = false;
		document.form1.carga_nao.disabled = false;
		document.form1.motorista_sim.disabled = false;
		document.form1.motorista_nao.disabled = false;
		document.form1.cinto_seguranca_sim.disabled = false;
		document.form1.cinto_seguranca_nao.disabled = false;
		document.form1.observacao.disabled = false;
		document.form1.limite_veloc_pista.disabled = false;
		document.form1.feito_bo_sim.disabled = false;
		document.form1.feito_bo_nao.disabled = false;
		document.form1.dados_bo.disabled = false;
		document.form1.data_1.disabled = false;
	}
}
</script> 
 
 
 
<script language="javascript">
function desabilita_acid_env(){
	//alert(elmnt.checked);

	if(document.form1.acid_env_prod_nao_aplic.checked == true)
	{
		document.form1.observ_exame_per.disabled = true;
	}
	else
	{
		document.form1.observ_exame_per.disabled = false;
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
 
</head>
<div id="fundo" style="display:none; width:3000px;">&nbsp;</div>
<body>

<form name="form1" method="post" action="" style="width:1200px">
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
        	  <?php print $desc_jornada_trabalho ?>
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
            <font size="-1"><?php print utf8_encode($observacao) ?>
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
   <table width="619" border="0" align="center">        
        <tr>
          <td width="544" class="txt_home">&nbsp;</td>
          <td width="65" class="txt_home">&nbsp;</td>
      </tr>
        <tr>
          <td colspan="2" class="txt_home"><div align="center">
          	<div id='impress'>
           		<input name='imprimir' type='button' class='botao_site' value=' Imprimir ' id='imprimir' onclick='impressao()'/>
            	<input name="fechar" type="submit" class="botao_site" value=" Fechar " id="fechar" />
          	</div>
          </div>
          </td>
        </tr>
	</table>         


	</fieldset>
</form>

</body>
</html>

<?php
	
		print "<script language='javascript'>desabilita_acid_traj()</script>";	
		print "<script language='javascript'>desabilita_acid_tran()</script>";	
		print "<script language='javascript'>desabilita_acid_env()</script>";	

	}
?>