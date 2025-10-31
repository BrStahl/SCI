<?php
session_name("covre_ti");
session_start();
require("../SCA/includes/page_func.php");
include("../SCA/includes/conect_sqlserver.php");
require_once "class/Dados.php";
require_once "class/GravaLog.php";
require_once "class/RelatoriosInv.php";
require_once "class/PlanoAcao.php";


$dados = new Dados;	
$grava_log = new GravaLog;	
$relatorios = new RelatoriosInv;	
$exibe_atividade = new PlanoAcao;	


$localItem = "../registro_acidentes/imp_investigacao_analise.php";
$logado    = $_SESSION["usuario_logado"];
$sistema_id    = 97;
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
	
	
	//if (($acidente_id != '') && ($erro != 1))
	//{
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
	
		$dados_registro = $dados->dadosRegistro($acidente_id);
		$descricao_fato = $dados_registro->descricao_fato;  
		$opcao_conclusao = $dados_registro->conclusao;     	
	
		
		$dados_analise_causa = $dados->dadosAnCausa($acidente_id);
		$analise_causa_id = $dados_analise_causa->id;
		$efeito_nc = $dados_analise_causa->efeito_nc;
		$causa_raiz = $dados_analise_causa->causa_raiz;		
		
		
		$dados_porque = $dados->pqAnCausa($analise_causa_id,2);
		$descricao_porque = $dados_porque->relatorio;   		
		
	//}			
	
	
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

table.bordasimples {border-collapse: collapse;}

table.bordasimples tr td {border:1px solid #000000;}

</style>


		<style>
			table.comBordaSimples 
			{
				border-collapse: collapse; /* CSS2 */
				background: #FFF;
			}
			table.comBordaSimples td 
			{
				border: 1px solid #333;
			} 
			table.comBordaSimples th 
			{
				border: 1px solid 333;
				background: #F0FFF0;
			}
			.font 
			{
				color:#333;
				
			}
</style>

<style type='text/css'>
  .pagina{ page-break-after: always; margin-left:7px; }
</style>


<script language="javascript">
function exibe_atividades(rms_processos_id){
	//alert(rms_processos_id);	

	//var rms_processos_id = 129;

	//alert("entrou na exclusão");
	if (rms_processos_id != ""){
		$.ajax({type: "POST",//define o metódo de passagem de parametros
			url: "../RMS_Projetos/includes/exibe_atividades.php", //chama uma pagina
			data: "rms_processos_id="+rms_processos_id, //passa os parametros, se necessário
			success: function(msg){  //pega o retorno da pagina chamada
				//alert(msg);
				
				$("#atividades").html(msg);
				$("#atividades").show();
				
				//document.form1.gravar.click();
		
			}
		});
	}

}
</script>

<script language='javascript'>
function imprimi_some()
{
	//alert(1);
	$('#botao_imprimir').hide(); 
	$('#botao_fechar').hide(); 

	self.print();
	open(location, '_self').close();
}
</script>	
 
</head>
<div id="fundo" style="display:none; width:3000px;">&nbsp;</div>
<body>

<form name="form1" method="post" action="" style="width:1200px">
  <fieldset>
    
 	  <table width="1150" border="1">
    	<tr>
        	<td bgcolor="#CCCCCC">
            	<center><font color="#0000FF" size="+3"><b>Investiga&ccedil;&atilde;o e An&aacute;lise de Acidente / Incidente / Desvios</b></font>
            </td>
        </tr>
   	  </table>


 	  <table width="871" border="0">
    	<tr>
    	  <td>&nbsp;</td>
  	  </tr>
    	<tr>
        	<td><font color="#0000FF"><b>Dados do Funcion&aacute;rio
        	  
        	</b></font></td>
        </tr>
    </table>
    <table width="871" border="0">
    	<tr>
        	<td><font color="#0000FF" ><b>
        	  <?PHP
    
				$relatorio_envolvidos = $relatorios->dadosEnvolvidos('I',$acidente_id);
				
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
        	<td><font color="#0000FF"><b>Les&atilde;o</b></font>
        	  
        	</td>
        </tr>
    </table>    
    
    <table width="1150" border=1 cellspacing=0 cellpadding=1  frame="box" rules="none">
    	<tr>
        	<td width="4" height="29">&nbsp;</td>
       	  <td width="203"><input type="checkbox" name="sem_lesao" id="sem_lesao" <?PHP if ($sem_lesao == 's') print 'checked'; ?> disabled/>
       	    <font><strong>Sem les&atilde;o</strong></font></td>
       	  <td width="18"><input type="checkbox" name="com_lesao" id="com_lesao" <?PHP if ($com_lesao == 's') print 'checked'; ?> disabled /></td>
        	<td width="440"><font><strong>Com les&atilde;o - Parte do corpo atingida</strong></font></td>
        	<td width="266"><input type="checkbox" name="sem_afastamento" id="sem_afastamento" <?PHP if ($sem_afastamento == 's') print 'checked'; ?> disabled />
       	    <font><strong>Sem afastamento</strong></font></td>
       	  <td width="193"><input type="checkbox" name="com_afastamento" id="com_afastamento" <?PHP if ($com_afastamento == 's') print 'checked'; ?> disabled />
       	    <font><strong>Com afastamento</strong> </font></td>
        </tr>
    	<tr>
    	  <td>&nbsp;</td>
    	  <td colspan="2"><font> <strong>Tempo Prev. Afastamento:</strong> 
    	  </font></td>
    	  <td colspan="3">
          
				  <table width="500" border=1 cellspacing=0 cellpadding=1>
                      <tr>
                          <td bgcolor="#FFFFFF" valign="top">
                              <font >&nbsp;<?php print utf8_encode($tempo_prev_afastamento); ?></font>
                          </td>
                      </tr>
                  </table>           
          
          </td>
   	  </tr>
    	<tr>
    	  <td>&nbsp;</td>
    	  <td colspan="5">&nbsp;</td>
  	  </tr>
    	<tr>
    	  <td>&nbsp;</td>
    	  <td colspan="5"><font ><b>Observa&ccedil;&atilde;o:</b></font></td>
   	  </tr>
    	<tr>
    	  <td>&nbsp;</td>
    	  <td colspan="5">
                <table width="1130" border=1 cellspacing=0 cellpadding=1>
                    <tr>
                        <td bgcolor="#FFFFFF" height="60" valign="top">
                        	<font>&nbsp;<?php print utf8_encode($observ_lesao); ?></font></td>
                    </tr>
                </table>
          </td>  
  	  </tr>
    	<tr>
    	  <td>&nbsp;</td>
    	  <td colspan="5">&nbsp;</td>
  	  </tr>
   	  </table>
    
    <table width="871" border="0">
    	<tr>
        	<td>&nbsp;</td>
      </tr>
    </table>   
    
    <table width="1118" border="0">
    	<tr>
        	<td width="175"><font><strong>Foi Emitido CAT?</strong></font></td>
        	<td width="237"><input type="radio" name="radio1" id="emitido_cat_sim" value="s" <?php if ($opcao_emitido_cat == 's'){print "checked='checked'";} ?> onclick="javascript:habilita_obs(this,1,'numero_cat')" disabled="disabled"/>
              <strong><font>Sim</font></strong> &nbsp;&nbsp;&nbsp;&nbsp;
              <input type="radio" name="radio1" id="emitido_cat_nao" value="n" <?php if ($opcao_emitido_cat == 'n'){print "checked='checked'";} ?> onclick="javascript:habilita_obs(this,1,'numero_cat')" disabled="disabled"/>
              <font ><strong>N&atilde;o</strong></font> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<strong>&nbsp;</strong></td>
        	<td width="165"><strong><font>N&uacute;mero da CAT:</font></strong><font >&nbsp;&nbsp;</font></td>
        	<td width="523">
                  <table width="300" border=1 cellspacing=0 cellpadding=1>
                      <tr>
                          <td bgcolor="#FFFFFF" valign="top">
                              <font >&nbsp;<?php print utf8_encode($numero_cat); ?></font>
                          </td>
                      </tr>
                  </table>              
            </td>
      </tr>
    </table>   
    
    <table width="871" border="0">
    	<tr>
        	<td>&nbsp;</td>
      </tr>
    </table>   
    
    <table width="1146" border="0">
    	<tr>
        	<td><font ><strong>Cronologia  do Acidente / Incidente / Desvios</strong>:</font></td>
      </tr>
    	<tr>
    	  <td>
            <table width="1150" border=1 cellspacing=0 cellpadding=1>
                    <tr>
                        <td bgcolor="#FFFFFF" height="60" valign="top"><font >&nbsp;<?php print utf8_encode($cronologia); ?></font></td>
                    </tr>
            </table>          
	      </td>
  	  </tr>
    </table>   
    
    <table width="871" border="0">
    	<tr>
        	<td>&nbsp;</td>
      </tr>
    </table>   
    
    <table width="1146" border="0">
    	<tr>
        	<td><font ><strong>Informa&ccedil;&atilde;o e A&ccedil;&otilde;es da chefia imediata:</strong></font></td>
      </tr>
    	<tr>
    	  <td>
          
                <table width="1150" border=1 cellspacing=0 cellpadding=1>
                    <tr>
                        <td bgcolor="#FFFFFF" height="60" valign="top">
                       	  <font >&nbsp;<?php print utf8_encode($informacao_acao_chefia); ?></font>
                        </td>
                    </tr>
                </table>              
          
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
       	  <td><font color="#0000FF" ><b>Coleta de Dados e Cronologia dos Eventos:</b></font></td>
      </tr>
    </table>   
    
    <table width="871" border="0">
    	<tr>
        	<td>&nbsp;</td>
      </tr>
    </table>  
    
    <table width="871" border="0">
    	<tr>
        	<td height="14"><p><strong><font >Jornada  de trabalho na data do evento</strong></p></td>
      </tr>
    </table>  
    
    <table width="1159" border="0">
    	<tr>
        	<td width="635"><font >Total de horas desde o in&iacute;cio do expediente /  jornada at&eacute; a hora do acidente: 
        	</td>
        	<td width="514">
                  <table width="70" border=1 cellspacing=0 cellpadding=1>
                      <tr>
                          <td bgcolor="#FFFFFF" valign="top">
                              <font >&nbsp;<?php print utf8_encode($desc_jornada_trabalho); ?></font>
                          </td>
                      </tr>
                  </table>               
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
        	<td><font ><strong>M&aacute;quinas,  ferramental, veiculo e/ou equipamentos envolvidos</strong>? 
        	  <input type="radio" name="radio2" id="maquina_ferramental_sim" value="s" <?php if ($opcao_maquina_ferramental == 's'){print "checked='checked'";} ?> onclick="javascript:habilita_obs(this,2,'desc_maquina_ferramental')" disabled="disabled"/>
              <font >Sim</font> &nbsp;&nbsp;&nbsp;&nbsp;
              <input type="radio" name="radio2" id="maquina_ferramental_nao" value="n" <?php if ($opcao_maquina_ferramental == 'n'){print "checked='checked'";} ?> onclick="javascript:habilita_obs(this,2,'desc_maquina_ferramental')" disabled="disabled"/>
            <font >N&atilde;o</font></td>
      </tr>
    </table>  
                    
    <table width="1143" border="0">
    	<tr>
        	<td>
            
                <table width="1150" border=1 cellspacing=0 cellpadding=1>
                    <tr>
                        <td bgcolor="#FFFFFF" height="60" valign="top">
                       	  <font >&nbsp;<?php print utf8_encode($desc_maquina_ferramental); ?></font>
                        </td>
                    </tr>
                </table>                
            
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
        	<td><font ><strong>Treinamentos  do funcion&aacute;rio</strong></td>
      </tr>
    </table>  

    <table width="871" border="0">
    	<tr>
        	<td>
            	<?php

                    $relatorio_treinamentos = $relatorios->treinamentosRealizados('I',$chapas,$acidente_id);
                    
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
        	<td height="30"><font >
        	  <p><b>Estava encontrando dificuldades para executar o trabalho?</b> 
        	    &nbsp;&nbsp;&nbsp;&nbsp;
        	    <input type="radio" name="radio3" id="dificuldade_trabalho_sim" value="s" <?php if ($opcao_dificuldade_trabalho == 's'){print "checked='checked'";} ?> onclick="javascript:habilita_obs(this,3,'desc_dificuldade_trabalho')" disabled="disabled"/>
        	    <font >Sim</font> &nbsp;&nbsp;&nbsp;&nbsp;
        	    <input type="radio" name="radio3" id="dificuldade_trabalho_nao" value="n" <?php if ($opcao_dificuldade_trabalho == 'n'){print "checked='checked'";} ?> onclick="javascript:habilita_obs(this,3,'desc_dificuldade_trabalho')" disabled="disabled"/>
       	      <font >N&atilde;o</font>
              <table width="1150" border=1 cellspacing=0 cellpadding=1>
                  <tr>
                      <td bgcolor="#FFFFFF" valign="top">
                       	  <font >&nbsp;<?php print utf8_encode($desc_dificuldade_trabalho); ?></font>
                      </td>
                  </tr>
            </table>               
              </font></p><p>&nbsp;</p>
            </td>
   	  </tr>
    	<tr>
    	  <td height="30"><font ><b>Houve orienta&ccedil;&atilde;o da chefia ao iniciar o trabalho?</b>          &nbsp;&nbsp;&nbsp;&nbsp;
          <input type="radio" name="radio4" id="orientacao_chefia_sim" value="s" <?php if ($opcao_orientacao_chefia == 's'){print "checked='checked'";} ?> onclick="javascript:habilita_obs(this,4,'desc_orientacao_chefia')" disabled="disabled"/>
          <font >Sim</font> &nbsp;&nbsp;&nbsp;&nbsp;
          <input type="radio" name="radio4" id="orientacao_chefia_nao" value="n" <?php if ($opcao_orientacao_chefia == 'n'){print "checked='checked'";} ?> onclick="javascript:habilita_obs(this,4,'desc_orientacao_chefia')" disabled="disabled"/>
          <font >N&atilde;o</font>
              <table width="1150" border=1 cellspacing=0 cellpadding=1>
                  <tr>
                      <td bgcolor="#FFFFFF" valign="top">
                       	  <font >&nbsp;<?php print utf8_encode($desc_orientacao_chefia); ?></font>
                      </td>
                  </tr>
          </table>               
              </font></p><p>&nbsp;</p>
          </td>
   	  </tr>
    	<tr>
    	  <td height="30"><font ><b>Havia outras pessoas trabalhando no mesmo local do incidente/acidente?</b>          &nbsp;&nbsp;&nbsp;&nbsp;
          <input type="radio" name="radio5" id="outras_pessoas_local_sim" value="s" <?php if ($opcao_outras_pessoas_local == 's'){print "checked='checked'";} ?> onclick="javascript:habilita_obs(this,5,'desc_outras_pessoas_local')" disabled="disabled"/>
          <font >Sim</font> &nbsp;&nbsp;&nbsp;&nbsp;
          <input type="radio" name="radio5" id="outras_pessoas_local_nao" value="n" <?php if ($opcao_outras_pessoas_local == 'n'){print "checked='checked'";} ?> onclick="javascript:habilita_obs(this,5,'desc_outras_pessoas_local')" disabled="disabled"/>
          <font >N&atilde;o</font>
              <table width="1150" border=1 cellspacing=0 cellpadding=1>
                  <tr>
                      <td bgcolor="#FFFFFF" valign="top">
                       	  <font >&nbsp;<?php print utf8_encode($desc_outras_pessoas_local); ?></font>
                      </td>
                  </tr>
          </table>               
              </font></p><p>&nbsp;</p>
          </td>
   	  </tr>
    	<tr>
    	  <td height="30"><font ><b>Comunicou o superior imediato sobre a ocorr&ecirc;ncia do incidente/acidente?</b>          &nbsp;&nbsp;&nbsp;&nbsp;
          <input type="radio" name="radio6" id="comunic_superior_sim" value="s" <?php if ($opcao_comunic_superior == 's'){print "checked='checked'";} ?> onclick="javascript:habilita_obs(this,6,'desc_comunic_superior')" disabled="disabled"/>
          <font >Sim</font> &nbsp;&nbsp;&nbsp;&nbsp;
          <input type="radio" name="radio6" id="comunic_superior_nao" value="n" <?php if ($opcao_comunic_superior == 'n'){print "checked='checked'";} ?> onclick="javascript:habilita_obs(this,6,'desc_comunic_superior')" disabled="disabled"/>
          <font >N&atilde;o</font>
              <table width="1150" border=1 cellspacing=0 cellpadding=1>
                  <tr>
                      <td bgcolor="#FFFFFF" valign="top">
                       	  <font >&nbsp;<?php print utf8_encode($desc_comunic_superior); ?></font>
                      </td>
                  </tr>
          </table>               
              </font></p><p>&nbsp;</p>
          </td>
   	  </tr>
    	<tr>
    	  <td height="30"><font ><b>No dia do evento estava bem de sa&uacute;de? </b> 
          &nbsp;&nbsp;&nbsp;&nbsp;
          <input type="radio" name="radio7" id="dia_evento_sim" value="s" <?php if ($opcao_dia_evento == 's'){print "checked='checked'";} ?> onclick="javascript:habilita_obs(this,7,'desc_dia_evento')" disabled="disabled"/>
          <font >Sim</font> &nbsp;&nbsp;&nbsp;&nbsp;
          <input type="radio" name="radio7" id="dia_evento_nao" value="n" <?php if ($opcao_dia_evento == 'n'){print "checked='checked'";} ?> onclick="javascript:habilita_obs(this,7,'desc_dia_evento')" disabled="disabled"/>
          <font >N&atilde;o</font>
              <table width="1150" border=1 cellspacing=0 cellpadding=1>
                  <tr>
                      <td bgcolor="#FFFFFF" valign="top">
                       	  <font >&nbsp;<?php print utf8_encode($desc_dia_evento); ?></font>
                      </td>
                  </tr>
          </table>               
              </font></p><p>&nbsp;</p>
          </td>
      </tr>
    	<tr>
    	  <td height="30"><font ><b>Toma algum rem&eacute;dio de uso cont&iacute;nuo?</b>          &nbsp;&nbsp;&nbsp;&nbsp;
          <input type="radio" name="radio8" id="remedio_continuo_sim" value="s" <?php if ($opcao_remedio_continuo == 's'){print "checked='checked'";} ?> onclick="javascript:habilita_obs(this,8,'desc_remedio_continuo')" disabled="disabled"/>
          <font >Sim</font> &nbsp;&nbsp;&nbsp;&nbsp;
          <input type="radio" name="radio8" id="remedio_continuo_nao" value="n" <?php if ($opcao_remedio_continuo == 'n'){print "checked='checked'";} ?> onclick="javascript:habilita_obs(this,8,'desc_remedio_continuo')" disabled="disabled"/>
          <font >N&atilde;o</font>
              <table width="1150" border=1 cellspacing=0 cellpadding=1>
                  <tr>
                      <td bgcolor="#FFFFFF" valign="top">
                       	  <font >&nbsp;<?php print utf8_encode($desc_remedio_continuo); ?></font>
                      </td>
                  </tr>
          </table>               
              </font></p><p>&nbsp;</p>
          </td>
   	  </tr>
    	<tr>
    	  <td height="30"><font ><b>Estava usando EPI (Equipamento de Prote&ccedil;&atilde;o Individual)? </b>
          &nbsp;&nbsp;&nbsp;&nbsp;
          <input type="radio" name="radio9" id="usando_epi_sim" value="s" <?php if ($opcao_usando_epi == 's'){print "checked='checked'";} ?> onclick="javascript:habilita_obs(this,9,'desc_usando_epi')" disabled="disabled"/>
          <font >Sim</font> &nbsp;&nbsp;&nbsp;&nbsp;
          <input type="radio" name="radio9" id="usando_epi_nao" value="n" <?php if ($opcao_usando_epi == 'n'){print "checked='checked'";} ?> onclick="javascript:habilita_obs(this,9,'desc_usando_epi')" disabled="disabled"/>
          <font >N&atilde;o</font>
              <table width="1150" border=1 cellspacing=0 cellpadding=1>
                  <tr>
                      <td bgcolor="#FFFFFF" valign="top">
                       	  <font >&nbsp;<?php print utf8_encode($desc_usando_epi); ?></font>
                      </td>
                  </tr>
          </table>               
              </font></p><p>&nbsp;</p>
          </td>
   	  </tr>
    	<tr>
    	  <td height="30"><p><font ><b>Voc&ecirc; tinha conhecimento do risco que estava exposto?&nbsp;(Checar informa&ccedil;&atilde;o  PGR/OS/F.77)</b>          &nbsp;&nbsp;&nbsp;&nbsp;
          <input type="radio" name="radio10" id="conhecimento_risco_sim" value="s" <?php if ($opcao_conhecimento_risco == 's'){print "checked='checked'";} ?> disabled="disabled" />
          <font >Sim</font> &nbsp;&nbsp;&nbsp;&nbsp;
          <input type="radio" name="radio10" id="conhecimento_risco_nao" value="n" <?php if ($opcao_conhecimento_risco == 'n'){print "checked='checked'";} ?> disabled="disabled" />
          <font >N&atilde;o</font></p></td>
   	  </tr>
    </table>    
    
    <table width="871" border="0">
    	<tr>
        	<td>&nbsp;</td>
      </tr>
    </table>      
   
   <?php 
   
   	if ($acid_traj_nao_aplic == '')
	{
 
   ?>
    
    <table width="871" border="0">
    	<tr>
       	  <td><font  color="#0000FF"><b>Para poss&iacute;vel acidente de trajeto</b></font></td>
      </tr>
    </table>      
    
    <table width="1152" border="1" frame="box" rules="none">
    	<tr>
            <td>&nbsp;</td>
        	<td height="30"><font ><b>O funcion&aacute;rio recebeu vale/transporte no per&iacute;odo?</b>&nbsp;
          &nbsp;&nbsp;&nbsp;&nbsp;
          <input type="radio" name="radio11" id="recebeu_vale_transp_sim" value="s" <?php if ($opcao_recebeu_vale_transp == 's'){print "checked='checked'";} ?> disabled="disabled" />
          <font >Sim</font> &nbsp;&nbsp;&nbsp;&nbsp;
          <input type="radio" name="radio11" id="recebeu_vale_transp_nao" value="n" <?php if ($opcao_recebeu_vale_transp == 'n'){print "checked='checked'";} ?> disabled="disabled"/>
          <font >N&atilde;o</font>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; </td>
      </tr>
    	<tr>
            <td>&nbsp;</td>        
    	  <td height="30"><font ><b>Foi apresentado pelo funcion&aacute;rio o Boletim de Ocorr&ecirc;ncia?</b>          &nbsp;&nbsp;&nbsp;&nbsp;
          <input type="radio" name="radio12" id="apresentado_bo_sim" value="s" <?php if ($opcao_apresentado_bo == 's'){print "checked='checked'";} ?> onclick="javascript:habilita_obs(this,12,'desc_apresentado_bo')" disabled="disabled"/>
          <font >Sim</font> &nbsp;&nbsp;&nbsp;&nbsp;
          <input type="radio" name="radio12" id="apresentado_bo_nao" value="n" <?php if ($opcao_apresentado_bo == 'n'){print "checked='checked'";} ?> onclick="javascript:habilita_obs(this,12,'desc_apresentado_bo')" disabled="disabled"/>
          <font >N&atilde;o</font>
              <table width="1130" border=1 cellspacing=0 cellpadding=1>
                  <tr>
                      <td bgcolor="#FFFFFF" valign="top">
                       	  <font >&nbsp;<?php print utf8_encode($desc_apresentado_bo); ?></font>
                      </td>
                  </tr>
          </table>               
              </font></p>
          </td>
  	  </tr>
    	<tr>
            <td>&nbsp;</td>        
    	  <td height="50">
          <p>&nbsp;</p>
          <font ><b>Endere&ccedil;o do envolvido:</b></p>
          		
				<?php
                    $relatorio_endereco = $relatorios->enderecoEnvolvido('I',$chapas,$acidente_id);
                    
                    $rel_endereco = $relatorio_endereco->relatorio;          
              
                    print $rel_endereco;
                ?>             
          		<p>&nbsp;</p>          
          
<b>O trajeto do fato condiz com a rota  empresa/resid&ecirc;ncia, conforme comprovante de endere&ccedil;o no prontu&aacute;rio? </b>
          <p></p>
          <input type="radio" name="radio13" id="trajeto_rota_sim" value="s" <?php if ($opcao_trajeto_rota == 's'){print "checked='checked'";} ?> disabled="disabled"/>
          <font >Sim</font> &nbsp;&nbsp;&nbsp;&nbsp;
          <input type="radio" name="radio13" id="trajeto_rota_nao" value="n" <?php if ($opcao_trajeto_rota == 'n'){print "checked='checked'";} ?> disabled="disabled"/>
          <font >N&atilde;o</font>
          <p>&nbsp;</p>
          </td>
  	  </tr>
    	<tr>
          <td>&nbsp;</td>        
    	  <td height="30"><font ><b>Hor&aacute;rio do fato condiz com a jornada de trabalho registrada no prontu&aacute;rio/ponto  eletr&ocirc;nico? </b>
          &nbsp;&nbsp;&nbsp;&nbsp;
          <input type="radio" name="radio14" id="horario_jornada_sim" value="s" <?php if ($opcao_horario_jornada == 's'){print "checked='checked'";} ?> onclick="javascript:habilita_obs(this,14,'desc_horario_jornada')" disabled="disabled"/>
          <font >Sim</font> &nbsp;&nbsp;&nbsp;&nbsp;
          <input type="radio" name="radio14" id="horario_jornada_nao" value="n" <?php if ($opcao_horario_jornada == 'n'){print "checked='checked'";} ?> onclick="javascript:habilita_obs(this,14,'desc_horario_jornada')" disabled="disabled"/>
          <font >N&atilde;o</font>
              <table width="1130" border=1 cellspacing=0 cellpadding=1>
                  <tr>
                      <td bgcolor="#FFFFFF" valign="top">
                       	  <font >&nbsp;<?php print utf8_encode($desc_horario_jornada); ?></font>
                      </td>
                  </tr>
          </table>               
              </font></p><p>&nbsp;</p>
          </td>
  	  </tr>
    	<tr>
          <td>&nbsp;</td>        
    	  <td height="30"><font ><b>Diante da an&aacute;lise  acima &eacute; caracterizado Acidente de Trajeto? </b>
          &nbsp;&nbsp;&nbsp;&nbsp;
          <input type="radio" name="radio15" id="acidente_trajeto_sim" value="s" <?php if ($opcao_acidente_trajeto == 's'){print "checked='checked'";} ?> onclick="javascript:habilita_obs(this,15,'desc_acidente_trajeto')" disabled="disabled"/>
          <font >Sim</font> &nbsp;&nbsp;&nbsp;&nbsp;
          <input type="radio" name="radio15" id="acidente_trajeto_nao" value="n" <?php if ($opcao_acidente_trajeto == 'n'){print "checked='checked'";} ?> onclick="javascript:habilita_obs(this,15,'desc_acidente_trajeto')" disabled="disabled"/>
          <font >N&atilde;o</font>
              <table width="1130" border=1 cellspacing=0 cellpadding=1>
                  <tr>
                      <td bgcolor="#FFFFFF" valign="top">
                       	  <font >&nbsp;<?php print utf8_encode($desc_acidente_trajeto); ?></font>
                      </td>
                  </tr>
          </table>               
              </font></p><p>&nbsp;</p>
          </td>
  	  </tr>
    </table>      

    <table width="871" border="0">
    	<tr>
        	<td>&nbsp;</td>
      </tr>
    </table>      

   <?php 
	}
	
   	if ($acid_tran_nao_aplic == '')
	{
	
   ?>    
       
    <table width="871" border="0">
    	<tr>
       	  <td><font  color="#0000FF"><b>Para acidentes de tr&acirc;nsito</b></font></td>
      </tr>
    </table>      
    
    <table width="1152" height="425" border="1" frame="box" rules="none">
    	<tr>
            <td>&nbsp;</td>
        	<td height="83"><font >
        	  <p><b>O disco de tac&oacute;grafo est&aacute; dispon&iacute;vel para an&aacute;lise?</b></p>
        	  <p>
        	    <input type="radio" name="radio16" id="disco_tacografo_disp_sim" value="s" <?php if ($opcao_disco_tacografo_disp == 's'){print "checked='checked'";} ?> onclick="javascript:habilita_obs(this,16,'veloc_evidenc_disco')" disabled="disabled"/>
       	      <font >Sim&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</font>&nbsp;
       	      <input type="radio" name="radio16" id="disco_tacografo_disp_nao" value="n" <?php if ($opcao_disco_tacografo_disp == 'n'){print "checked='checked'";} ?> onclick="javascript:habilita_obs(this,16,'veloc_evidenc_disco')" disabled="disabled"/>
              <font >N&atilde;o&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</font>&nbsp;&nbsp;<strong>
              <p></p>Qual a velocidade do  ve&iacute;culo evidenciada no disco de tac&oacute;grafo no momento do ocorrido?</strong> <font ><?php print utf8_encode($veloc_evidenc_disco) ?>
       	      </font></p><p>
              <table width="100" border="0">
              	<tr>
                	<td><font ><strong>&nbsp;Coment&aacute;rios:&nbsp;</strong>&nbsp;</font></td>
                    <td>
                      <table width="950" border=1 cellspacing=0 cellpadding=1>
                          <tr>
                              <td bgcolor="#FFFFFF" valign="top">
                                  <font >&nbsp;<?php print utf8_encode($comentario_disco_tacografo); ?></font>
                              </td>
                          </tr>
                      </table>                 
                   	</td>
                </tr>
            </table>

   	      </font>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</p></td>
      </tr>
    	<tr>
    	  <td>&nbsp;</td>
    	  <td height="16">&nbsp;</td>
  	  </tr>
    	<tr>
          <td>&nbsp;</td>        
    	  <td height="39"><font >
    	    <p><b>No momento do acidente teve condi&ccedil;&otilde;es adversas?</b>&nbsp;&nbsp;&nbsp;&nbsp;   	        </p>
    	    <p>&nbsp;Tempo:&nbsp;
    	      <input type="radio" name="radio17" id="tempo_sim" value="s" <?php if ($opcao_tempo == 's'){print "checked='checked'";} ?> disabled="disabled"/>
    	      <font >Sim</font> &nbsp;&nbsp;&nbsp;&nbsp;
    	      <input type="radio" name="radio17" id="tempo_nao" value="n" <?php if ($opcao_tempo == 'n'){print "checked='checked'";} ?> disabled="disabled"/>
   	      <font >N&atilde;o</font>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Luz:&nbsp;
            <input type="radio" name="radio18" id="luz_sim" value="s" <?php if ($opcao_luz == 's'){print "checked='checked'";} ?> disabled="disabled" />
            <font >Sim</font> &nbsp;&nbsp;&nbsp;&nbsp;
            <input type="radio" name="radio18" id="luz_nao" value="n" <?php if ($opcao_luz == 'n'){print "checked='checked'";} ?> disabled="disabled" />
            <font >N&atilde;o</font>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Rodovia:&nbsp;
            <input type="radio" name="radio19" id="rodovia_sim" value="s" <?php if ($opcao_rodovia == 's'){print "checked='checked'";} ?> disabled="disabled" />
            <font >Sim</font> &nbsp;&nbsp;&nbsp;&nbsp;
            <input type="radio" name="radio19" id="rodovia_nao" value="n" <?php if ($opcao_rodovia == 'n'){print "checked='checked'";} ?> disabled="disabled" />
          <font >N&atilde;o</font>&nbsp;&nbsp;</p></td>
  	  </tr>
    	<tr>
    	  <td>&nbsp;</td>
    	  <td height="16">&nbsp;</td>
  	  </tr>
    	<tr>
    	  <td>&nbsp;</td>
    	  <td height="62"><p><font ><b>Limite de velocidade sinalizado no local</b>&nbsp;&nbsp;&nbsp;&nbsp; </font></p>
    	    <font >
            <p><font >Tr&acirc;nsito:
              <input type="radio" name="radio20" id="transito_sim" value="s" <?php if ($opcao_transito == 's'){print "checked='checked'";} ?> disabled="disabled"/>
              <font >Sim</font> &nbsp;&nbsp;&nbsp;&nbsp;
              <input type="radio" name="radio20" id="transito_nao" value="n" <?php if ($opcao_transito == 'n'){print "checked='checked'";} ?> disabled="disabled"/>
              <font >N&atilde;o</font>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Ve&iacute;culo:
              <input type="radio" name="radio21" id="veiculo_sim" value="s" <?php if ($opcao_veiculo == 's'){print "checked='checked'";} ?> disabled="disabled"/>
              <font >Sim</font> &nbsp;&nbsp;&nbsp;&nbsp;
              <input type="radio" name="radio21" id="veiculo_nao" value="n" <?php if ($opcao_veiculo == 'n'){print "checked='checked'";} ?> disabled="disabled"/>
              <font >N&atilde;o</font>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Carga:
              <input type="radio" name="radio22" id="carga_sim" value="s" <?php if ($opcao_carga == 's'){print "checked='checked'";} ?> disabled="disabled"/>
              <font >Sim</font> &nbsp;&nbsp;&nbsp;&nbsp;
              <input type="radio" name="radio22" id="carga_nao" value="n" <?php if ($opcao_carga == 'n'){print "checked='checked'";} ?> disabled="disabled"/>
              <font >N&atilde;o</font>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp;</font></p>
   	      </font></td>
  	  </tr>
    	<tr>
            <td>&nbsp;</td>        
    	  <td height="24">
          
              <table width="1131" border="0">
              	<tr>
              	  <td height="49" colspan="4"><font ><font >Motorista:
                        <input type="radio" name="radio23" id="motorista_sim" value="s" <?php if ($opcao_motorista == 's'){print "checked='checked'";} ?> disabled="disabled"/>
                        <font >Sim</font> &nbsp;&nbsp;&nbsp;&nbsp;
                        <input type="radio" name="radio23" id="motorista_nao" value="n" <?php if ($opcao_motorista == 'n'){print "checked='checked'";} ?> disabled="disabled"/>
                  <font >N&atilde;o</font></font></font><font >&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Utilizava cinto de seguran&ccedil;a:
              	    <input type="radio" name="radio24" id="cinto_seguranca_sim" value="s" <?php if ($opcao_cinto_seguranca == 's'){print "checked='checked'";} ?> disabled="disabled"/>
                    <font >Sim</font> &nbsp;&nbsp;&nbsp;&nbsp;
                    <input type="radio" name="radio24" id="cinto_seguranca_nao" value="n" <?php if ($opcao_cinto_seguranca == 'n'){print "checked='checked'";} ?> disabled="disabled"/>
                  <font >N&atilde;o</font></font></td>
           	    </tr>
              	<tr>
                	<td width="106" height="38"><font >Observa&ccedil;&atilde;o:<font >&nbsp;</font>
                                         	</td>
                	<td width="688">
                    
					<table width="500" border=1 cellspacing=0 cellpadding=1>
                          <tr>
                              <td bgcolor="#FFFFFF" valign="top">
                                  <font >&nbsp;<?php print utf8_encode($observacao); ?></font>
                              </td>
                          </tr>
                      </table>                    
                    
                    </td>
                    <td width="220">
                    	<font >Limite Velocidade da Pista:
                    </td>
		            <td width="99">
                      <table width="50" border=1 cellspacing=0 cellpadding=1>
                          <tr>
                              <td bgcolor="#FFFFFF" valign="top">
                                  <font >&nbsp;<?php print utf8_encode($limite_veloc_pista); ?></font>
                              </td>
                          </tr>
                      </table>                 
                   	</td>                    
                </tr>
            </table>          
         </td>
  	  </tr>
    	<tr>
    	  <td>&nbsp;</td>
    	  <td height="30">&nbsp;</td>
  	  </tr>
    	<tr>
          <td>&nbsp;</td>        
    	  <td height="30">
          
            <table width="1131" border="0">
              	<tr>
                	<td width="309" height="38"><font ><b>Foi feito boletim de Ocorr&ecirc;ncia? </b></td>
                    <td width="208"><input type="radio" name="radio25" id="feito_bo_sim" value="s" <?php if ($opcao_feito_bo == 's'){print "checked='checked'";} ?> onclick="javascript:habilita_obs(this,25,'dados_bo')" disabled="disabled"/>
          <font >Sim</font> &nbsp;&nbsp;&nbsp;&nbsp;
          <input type="radio" name="radio25" id="feito_bo_nao" value="n" <?php if ($opcao_feito_bo == 'n'){print "checked='checked'";} ?> onclick="javascript:habilita_obs(this,25,'dados_bo')" disabled="disabled"/>
          <font >N&atilde;o</td>
                    <td width="131"><font >Dados  boletim:<font >&nbsp;</font></td>
		            <td width="311">
                      <table width="250" border=1 cellspacing=0 cellpadding=1>
                          <tr>
                              <td bgcolor="#FFFFFF" valign="top">
                                  <font >&nbsp;<?php print utf8_encode($dados_bo); ?></font>
                              </td>
                          </tr>
                      </table>                 
                   	</td>
                    <td width="38">
                    	<font >Data:
                    </td>
		            <td width="108">
                      <table width="100" border=1 cellspacing=0 cellpadding=1>
                          <tr>
                              <td bgcolor="#FFFFFF" valign="top">
                                  <font >&nbsp;<?php print utf8_encode($data_bo); ?></font>
                              </td>
                          </tr>
                      </table>                 
                   	</td>                    
              </tr>
            </table>            
   		  </td>
  	  </tr>
    </table>       
    
    <table width="871" border="0">
    	<tr>
        	<td>&nbsp;</td>
      </tr>
    </table>   
       
   <?php 
	}
	
   	if ($acid_env_prod_nao_aplic == '')
	{
	
   ?>    

    <table width="871" border="0">
    	<tr>
       	  <td><font  color="#0000FF"><b>Para acidentes de tr&acirc;nsito com envolvimento de produto qu&iacute;mico</b></font></td>
      </tr>
    </table>      
    
    <table width="1152" height="34" border="1" frame="box" rules="none">
    	<tr>
            <td width="8">&nbsp;</td>
        	<td width="1128"><font >
       	    <p><b>Detalhar o perfil do motorista em velocidade</b>:</p>
       	    <p>
       	      <?php
			
				$relatorio_vel_ponta = $relatorios->velocidadePonta('I',$nomes,$data_fato);
				
				$rel_vel_ponta = $relatorio_vel_ponta->relatorio;          
          
		  		print $rel_vel_ponta;
				
            ?>
   	      </p>
       	    <p>&nbsp;</p>
       	    <p>Observa&ccedil;&atilde;o Velocidade/Pontas:</p>
   	      <p><font >
   	            <table width="1100" border=1 cellspacing=0 cellpadding=1>
                    <tr>
                        <td bgcolor="#FFFFFF" height="60" valign="top">
                        	<font >&nbsp;<?php print utf8_encode($desc_vel_pontas); ?></font>
                        </td>
                    </tr>
                </table>   
   	      </font></p>
   	      <p>&nbsp;</p></td>
      </tr>
    	<tr>
    	  <td>&nbsp;</td>
    	  <td><p>
    	  <font ><b>Registrar &uacute;ltimo per&iacute;odo  de <strong><u>f&eacute;rias gozadas</u></strong> pelo  funcion&aacute;rio:</b>
          	 <p>
          	   <?php
			
				$relatorio_ferias = $relatorios->feriasGozada('I',$chapas,$acidente_id);
				
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
    	  <td><p><font ><b>Registrar a data do  &uacute;ltimo <strong><u>exame m&eacute;dico peri&oacute;dico</u></strong> realizado pelo motorista inclusive se houve registro de identifica&ccedil;&atilde;o de  anormalidades em algum dos exames realizados ou recomenda&ccedil;&atilde;o m&eacute;dica:</b></p>
       	    <p>
       	      <?php
                    $relatorio_exame_per = $relatorios->examePeriodico('I',$chapas,$acidente_id);
                    
                    $rel_exame_per = $relatorio_exame_per->relatorio;          
              
                    print $rel_exame_per;
                ?>
           </p>
       	    <p>&nbsp;</p>    
            <table width="1100" border=0 cellspacing=0 cellpadding=1>
                    <tr>
                        <td width="95" height="29" valign="top">
                       	  <font >&nbsp;Observa&ccedil;&atilde;o:</font>
                        </td>
                        <td width="1051" height="29" valign="top">
                        	<table width="991" border=1 cellspacing=0 cellpadding=1>
                                <tr>
                                    <td valign="top" bgcolor="#FFFFFF">
                                      <font >&nbsp;<?php print utf8_encode($observ_exame_per); ?></font>
                                    </td>
                                </tr>
                            </table> 
                        </td>
                    </tr>
            </table>              
           </p>  
   	      <p>&nbsp;</p>
          <p>&nbsp;</p> 
         </td>
  	  </tr>
    	<tr>
    	  <td>&nbsp;</td>
    	  <td><p><font ><b>Registrar a data do  &uacute;ltimo <strong><u>exame psicol&oacute;gico</u></strong> realizado:</b></p>
          	 <p>&nbsp;</p>

				<?php
                    $relatorio_exame_psi = $relatorios->examePsicologico('I',$chapas,$acidente_id);
                    
                    $rel_exame_psi = $relatorio_exame_psi->relatorio;          
              
                    print $rel_exame_psi;
                ?>
           <p>&nbsp;</p>  
   	      <p>&nbsp;</p>          
         </td>
  	  </tr>
    	<tr>
    	  <td>&nbsp;</td>
    	  <td><p><font ><b>Registrar &uacute;ltima revis&atilde;o  de <strong><u>manuten&ccedil;&atilde;o preventiva</u></strong> realizada antes do acidente e os itens checados conforme D.07:</b></p>
    	    <p>&nbsp;</p>
          	   <?php
			
				$relatorio_manut = $relatorios->manutPreventiva('I',$placas,$data_fato);
				
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

   <?php 
	}
   ?>    
    
    <table width="1152" border="0">
    	<tr>
        	<td colspan="2"><font ><b>Houve testemunha?</b>
          &nbsp;&nbsp;&nbsp;&nbsp;
          <input type="radio" name="radio26" id="testemunha_sim" value="s" <?php if ($opcao_testemunha == 's'){print "checked='checked'";} ?> onclick="javascript:habilita_obs(this,26,'nome_testemunha')" disabled="disabled"/>
          <font >Sim</font> &nbsp;&nbsp;&nbsp;&nbsp;
          <input type="radio" name="radio26" id="testemunha_nao" value="n" <?php if ($opcao_testemunha == 'n'){print "checked='checked'";} ?> onclick="javascript:habilita_obs(this,26,'nome_testemunha')" disabled="disabled"/>
          <font >N&atilde;o </font></td>
      </tr>
    	<tr>
        	<td>
              <table width="1142" border="0">
              	<tr>
                	<td width="62" height="22"><font >Nome:</td>
                    <td width="586">
					
                      <table width="500" border=1 cellspacing=0 cellpadding=1>
                          <tr>
                              <td bgcolor="#FFFFFF" valign="top">
                                <font >&nbsp;<?php print utf8_encode($nome_testemunha); ?></font>
                              </td>
                          </tr>
                      </table>                      
                    
                    </td>
                    <td width="62"><font >Fun&ccedil;&atilde;o:&nbsp;</font></td>
		            <td width="403">
                      <table width="370" border=1 cellspacing=0 cellpadding=1>
                          <tr>
                              <td bgcolor="#FFFFFF" valign="top">
                                <font >&nbsp;<?php print utf8_encode($funcao_testemunha); ?></font>
                              </td>
                          </tr>
                      </table>  					
                    </td>
                </tr>
              </table>           
        	</td>
      </tr>
    	<tr>
    	  <td colspan="2">&nbsp;</td>
  	  </tr>
    	<tr>
    	  <td colspan="2"><font >Relato da testemunha:
                <table width="1150" border=1 cellspacing=0 cellpadding=1>
                    <tr>
                        <td bgcolor="#FFFFFF" height="60" valign="top">
                        	<font >&nbsp;<?php print utf8_encode($relato_testemunha); ?></font>
                        </td>
                    </tr>
                </table>   
          </font></td>
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
    	  <td>&nbsp;</td>
  	  </tr>
    </table>                                                                               
 
    <p>&nbsp;</p>

	  <table width="1150" border="1">
    	<tr>
        	<td bgcolor="#CCCCCC">
            	<center><font color="#0000FF" size="+3"><b>An&aacute;lise de Causa</b></font>
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
        	<td><font ><b>Descri&ccedil;&atilde;o do Fato</b></font></td>
      </tr>
    	<tr>
        	<td>
                <table width="1150" border=1 cellspacing=0 cellpadding=1>
                    <tr>
                        <td bgcolor="#FFFFFF" height="60" valign="top">
                       	  <font >&nbsp;<?php print utf8_encode($descricao_fato); ?></font></td>
                    </tr>
                </table>            
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
        	<td><table cellspacing="0" cellpadding="0">
        	  <tr>
        	    <td></td>
      	    </tr>
        	  <tr>
        	    <td><font ><strong>Efeito da NC / Acidente / Incidente / Desvio</strong></font></td>
      	    </tr>
      	  </table></td>
        </tr>
    	<tr>
        	<td>
                <table width="1150" border=1 cellspacing=0 cellpadding=1>
                    <tr>
                        <td bgcolor="#FFFFFF" height="60" valign="top">
                       	  <font >&nbsp;<?php print utf8_encode($efeito_nc); ?></font></td>
                    </tr>
                </table>            
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
        	<td><table cellspacing="0" cellpadding="0">
        	  <tr>
        	    <td></td>
      	    </tr>
        	  <tr>
        	    <td><font ><strong>Porqu&ecirc;</strong></font></td>
      	    </tr>
      	  </table></td>
        </tr>
    	<tr>
        	<td>
            	<?php
					print utf8_encode($descricao_porque);           
				?>
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
        	<td><table cellspacing="0" cellpadding="0">
        	  <tr>
        	    <td></td>
      	    </tr>
        	  <tr>
        	    <td><font ><strong>Causa Raiz</strong></font></td>
      	    </tr>
      	  </table></td>
        </tr>
    	<tr>
        	<td>
                <table width="1150" border=1 cellspacing=0 cellpadding=1>
                    <tr>
                        <td bgcolor="#FFFFFF" height="60" valign="top">
                       	  <font >&nbsp;<?php print utf8_encode($causa_raiz); ?></font></td>
                    </tr>
                </table>            
            </td>
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
    	  <td>&nbsp;</td>
  	  </tr>
    </table>                                                                               
 
    <p>&nbsp;</p>

	<table width="1150" border="1">
    	<tr>
        	<td bgcolor="#CCCCCC">
            	<center><font color="#0000FF" size="+3"><b>Plano de A&ccedil;&atilde;o</b></font>
            </td>
        </tr>
    </table>

    <table width="871" border="0">        
    	<tr>
    	  <td>&nbsp;</td>
  	  </tr>
    </table>

    <?php
	
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

	?>



<table width="1150" border="0" align="center">
  <tr>
    <td width="167" height="20"><strong><font >Tipo:</font> </strong></td>
    <td width="574"><strong><font >T&iacute;tulo:</font></strong>&nbsp;</td>
    <td width="124"><strong><font >Revis&atilde;o:</font></strong></td>
    <td width="126"><strong><font >Data</font>:</strong></td>
    <td width="137"><strong><font >N&ordm; Ocorr&ecirc;ncia: </font></strong></td>
  <tr>
    <td><font ><?php print $descricao_tipo ?></font></td>
    <td><font ><?php print $titulo  ?></font></td>
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
            
            
    <div id='botao_imprimir'>
    <table border='0' align='center'>
        <tr>
          <td><input name="imprimir" type="button" class="botao_site_2" value=" Imprimir " id="imprimir" onclick='imprimi_some()'/>
                <input name="fechar" type="submit" class="botao_site_2" value=" Fechar " id="fechar" />
                
            </td>			
        </tr>
    </table>
    </div>        
                
  </fieldset>
</form>




</body>
</html>

<?php
		
		print "<script language='javascript'>exibe_atividades(129)</script>";	

	}
?>