<?php
session_name("covre_ti");
session_start();

require_once $_SERVER['DOCUMENT_ROOT']."/SCI/controller/ConexaoSCI.php";



class PlanoAcao extends ConexaoSCI{


	public function exibeCampos($acidente_id){
		
		$con = new ConexaoSCI;
		
		$logado = $_SESSION['usuario_logado'];
		
		
		if ($logado != '')
		{
	

			$table = '';

			$query = "select tipo_projeto.descricao_tipo, titulo, revisao, convert(varchar(10),data_emissao,103), equipe_envolvida, lider, gerente,
						case tipo_acesso_id	
							when 'R' then 'Restrito'
							when 'P' then 'Publico'
							else null
						end tipo_acesso,
						psecao.DESCRICAO psecao,
						rms_processos.codatendimento, hatendimentobase.assuntooc  collate sql_latin1_general_cp1251_ci_as, 
						CASE hatendimentobase.CODSTATUS
											 WHEN 'G' THEN 'Agendado a Responder'
											 WHEN 'E' THEN 'Agendado Respondido'
											 WHEN 'F' THEN 'Concluido Confirmado'
											 WHEN 'A' THEN 'Em Andamento'
											 WHEN 'C' THEN 'Cancelada'
											 WHEN 'O' THEN 'Concluido Respondido'
											 WHEN 'R' THEN 'Concluido a Responder'
											 WHEN 'T' THEN 'Aguardando Terceiros'
						END AS STATUS
						from rms_processos with (nolock)
						join tipo_projeto with (nolock) on
							tipo_projeto.tipo_id = rms_processos.tipo_id
						left join CORPORE..PSECAO psecao with (nolock) on
							psecao.CODIGO = rms_processos.area_secao_id collate SQL_Latin1_General_CP1_CI_AI
						left join corpore..hatendimentobase with (nolock) on 
							hatendimentobase.codatendimento = rms_processos.codatendimento
						left join corpore..hclassifatendimento with (nolock) on 
							hclassifatendimento.codclassificacao = hatendimentobase.codclassificacao
						left join corpore..htipoatendimento with (nolock) on 
							htipoatendimento.codtipoatendimento = hatendimentobase.codtipoatendimento	
						where num_ocorrencia = $acidente_id
						and rms_processos.status = 'a'";
			//print $query;
			$result = $con->executar($query);

			$retorno = '';
		
				
			$descricao_tipo 	= odbc_result($result,1);
			$titulo 			= odbc_result($result,2);
			$revisao	 		= odbc_result($result,3);
			$data_emissao		= odbc_result($result,4);
			$equipe_envolvida 	= odbc_result($result,5);
			$lider	 			= odbc_result($result,6);
			$gerente 			= odbc_result($result,7);
			$tipo_acesso 		= odbc_result($result,8);
			$secao 				= odbc_result($result,9);
			$ocorrencia 		= odbc_result($result,10);
			$assunto_oc			= odbc_result($result,11);
			$status_oc			= odbc_result($result,12);			

				  
			$retorno->descricao_tipo = $descricao_tipo;
			$retorno->titulo = $titulo;
			$retorno->revisao = $revisao;
			$retorno->data_emissao = $data_emissao;
			$retorno->equipe_envolvida = $equipe_envolvida;
			$retorno->lider = $lider;
			$retorno->gerente = $gerente;
			$retorno->tipo_acesso = $tipo_acesso;
			$retorno->secao = $secao;
			$retorno->ocorrencia = $ocorrencia;
			$retorno->assunto_oc = $assunto_oc;
			$retorno->status_oc = $status_oc;

			return $retorno;
		}
		else
			return 'Erro';
		
	}	

	
	public function exibeAtividades($acidente_id){
		
		$con = new ConexaoSCI;
		
		$logado = $_SESSION['usuario_logado'];
		
		
		if ($logado != '')
		{
	

			$table = '';

			$query = "select distinct itens_rms_processos.item_id, atividade, 
						isnull(DBO.FN_RESPONSAVEL_ATIVIDADE(itens_rms_processos.item_id),'VAZIO'),  
						isnull(convert(char, prazo_novo ,103), convert(char, prazo ,103)), isnull(convert(char, data_conclusao, 103), null), 
						isnull(status_item.descricao, '&nbsp'), ordem, 
						codsecao, isnull(psecao.descricao, '&nbsp;'),
						CONVERT(varchar(10), data_inicio, 103), CONVERT(varchar(10), data_fim, 103)	
						from itens_rms_processos with (nolock)
						join rms_processos with (nolock) on
							rms_processos.rms_processos_id = itens_rms_processos.rms_processos_id
							and rms_processos.status = 'a'
						left join status_item with (nolock) on 
							status_item.status_id = itens_rms_processos.status_id
						left join corpore..PSECAO with (nolock) on 
							PSECAO.CODIGO = itens_rms_processos.codsecao collate SQL_Latin1_General_CP1_CI_AI
						and PSECAO.CODCOLIGADA = 1
						left join responsavel_atividade ra with (nolock) on
							ra.item_id = itens_rms_processos.item_id
							and ra.status_id = 'a'	
						where rms_processos.num_ocorrencia = $acidente_id
						and itens_rms_processos.status = 'a'
						order by ordem";
			//print $query;
			$result = $con->executar($query);
		

			$retorno = '';
		
			$table .= "<table width='1140' border=1 cellspacing=0 cellpadding=1>
			   <tr>
					<td width='45' bgcolor='#CCCCCC'><div align='center'><strong><FONT size='-1'>ITEM</FONT></strong></div></td>
					<td width='150' bgcolor='#CCCCCC'>
						<div align='center'><strong><FONT size='-1'>ATIVIDADE <p>(O QUE FAZER?)</FONT></strong></div>
					</td>
					<td bgcolor='#CCCCCC'><div align='center'><strong><FONT size='-1'>RESPONS&Aacute;VEL (QUEM?)</FONT></strong></div></td>
					<td bgcolor='#CCCCCC'><div align='center'><strong><FONT size='-1'>&Aacute;REA</FONT></strong></div></td>			
					<td width='80' bgcolor='#CCCCCC'><div align='center'><strong><FONT size='-1'>DATA IN&Iacute;CIO</FONT></strong></div></td>
					<td width='80' bgcolor='#CCCCCC'><div align='center'><strong><FONT size='-1'>DATA FIM</FONT></strong></div></td>
					<td width='80' bgcolor='#CCCCCC'><div align='center'><strong><FONT size='-1'>PRAZO (QUANDO?)</FONT></strong></div></td>
					<td width='80' bgcolor='#CCCCCC'><div align='center'><strong><FONT size='-1'>DATA CONCLUS&Atilde;O</FONT></strong></div></td>
					<td width='150' bgcolor='#CCCCCC'><div align='center'><strong><FONT size='-1'>STATUS </FONT></strong></div></td>
			   </tr>";
		

					
				 while(odbc_fetch_row($result))
				 {
				
					$item_id_p 			= odbc_result($result,1);
					$descr_atividade 	= odbc_result($result,2);
					$relacao_resp 		= odbc_result($result,3);
					$prazo_p 			= odbc_result($result,4);
					$data_conclusao 	= odbc_result($result,5);
					$status_p 			= odbc_result($result,6);
					$ordem_p 			= odbc_result($result,7);

					$codsecao_p 		= odbc_result($result,8);
					$descr_secao_p 		= odbc_result($result,9);
					$data_inicio_p 		= odbc_result($result,10);
					$data_fim_p			= odbc_result($result,11);

					$descr_atividade = utf8_encode($descr_atividade);
		
					$table .= "
						  <tr onmouseover=this.bgColor='#89BFF0' onmouseout=this.bgColor=''>
							<td bgcolor='#FFFFFF'><center><font size='-1'>".$ordem_p."</font></a></center></td>
							<td bgcolor='#FFFFFF'><center><font size='-1'>".$descr_atividade."</font></a></center></td>
							<td bgcolor='#FFFFFF'><center><font size='-1'>".$relacao_resp."</font></a></center></td>
							<td bgcolor='#FFFFFF'><center><font size='-1'>".$descr_secao_p."</font></a></center></td>
							<td bgcolor='#FFFFFF'><center><font size='-1'>".$data_inicio_p."</font></a></center></td>
							<td bgcolor='#FFFFFF'><center><font size='-1'>".$data_fim_p."</font></a></center></td>
							<td bgcolor='#FFFFFF'><center><font size='-1'>".$prazo_p."</font></a></center></td>
							<td bgcolor='#FFFFFF'><center><font size='-1'>".$data_conclusao."</font></a></center></td>
							<td bgcolor='#FFFFFF'><center><font size='-1'>".$status_p."</font></a></center></td>							
						  </tr>"; 
				         
				 } 
				 
				  $table .= "</table>"; 
				  
				  $retorno->atividade = $table;

				  return $retorno;
		}
		else
			return 'Erro';
		
	}	


	
}


?>