<?php
session_name("covre_ti");
session_start();

require_once $_SERVER['DOCUMENT_ROOT']."/SCI/controller/ConexaoSCI.php";


class Dados extends ConexaoSCI{


	//BUSCA OS DADOS DA INVESTIGACAO_ANALISE
	public function dadosRegistro($acidente_id){
		
		$con = new ConexaoSCI;
		
		$logado = $_SESSION['usuario_logado'];
		

		$query = "select observacao, conclusao
				  from registro_acidente with (nolock)
				  where acidente_id = $acidente_id";
		//print $query;
		$result = $con->executar($query);
		
	 
		$retorno = '';
		
		$retorno->descricao_fato			= odbc_result($result, 1);
		$retorno->conclusao					= odbc_result($result, 2);


		return $retorno;

	}


	
	//BUSCA OS DADOS DA INVESTIGACAO_ANALISE
	public function dadosInvAn($acidente_id){
		
		$con = new ConexaoSCI;
		
		$logado = $_SESSION['usuario_logado'];
		

		$query = "select sem_lesao, com_lesao, sem_afastamento, com_afastamento, 
						tempo_prev_afastamento, observ_lesao, emitido_cat, numero_cat, cronologia, informacao_acao_chefia, 
						maquina_ferramental, desc_maquina_ferramental, dificuldade_trabalho, desc_dificuldade_trabalho, 
						orientacao_chefia, desc_orientacao_chefia, outras_pessoas_local, desc_outras_pessoas_local, 
						comunic_superior, desc_comunic_superior, dia_evento, desc_dia_evento, remedio_continuo, 
						desc_remedio_continuo, usando_epi, desc_usando_epi, conhecimento_risco, recebeu_vale_transp, 
						apresentado_bo, desc_apresentado_bo, trajeto_rota, horario_jornada, desc_horario_jornada, 
						acidente_trajeto, desc_acidente_trajeto, disco_tacografo_disp, veloc_evidenc_disco, 
						comentario_disco_tacografo, tempo, luz, rodovia, transito, veiculo, carga, motorista, cinto_seguranca, 
						observacao, limite_veloc_pista, feito_bo, dados_bo, convert(varchar(10),data_bo,103), desc_vel_pontas, 
						testemunha, nome_testemunha, funcao_testemunha, relato_testemunha, observ_exame_per, id, 
						desc_jornada_trabalho, nao_aplica_acid_traj, nao_aplica_acid_tran, nao_aplica_acid_env
				 from investigacao_analise with (nolock)
				 where acidente_id = $acidente_id";
		//print $query;
		$result = $con->executar($query);
		
	 
		$retorno = '';
		
		$retorno->sem_lesao						= odbc_result($result, 1);
		$retorno->com_lesao						= odbc_result($result, 2);
		$retorno->sem_afastamento				= odbc_result($result, 3);
		$retorno->com_afastamento				= odbc_result($result, 4);
		$retorno->tempo_prev_afastamento		= odbc_result($result, 5);
		$retorno->observ_lesao					= odbc_result($result, 6);
		$retorno->emitido_cat					= odbc_result($result, 7);
		$retorno->numero_cat					= odbc_result($result, 8);		
		$retorno->cronologia					= odbc_result($result, 9);
		$retorno->informacao_acao_chefia		= odbc_result($result, 10);
		$retorno->maquina_ferramental			= odbc_result($result, 11);
		$retorno->desc_maquina_ferramental		= odbc_result($result, 12);
		$retorno->dificuldade_trabalho			= odbc_result($result, 13);
		$retorno->desc_dificuldade_trabalho		= odbc_result($result, 14);
		$retorno->orientacao_chefia				= odbc_result($result, 15);
		$retorno->desc_orientacao_chefia		= odbc_result($result, 16);
		$retorno->outras_pessoas_local			= odbc_result($result, 17);
		$retorno->desc_outras_pessoas_local		= odbc_result($result, 18);
		$retorno->comunic_superior				= odbc_result($result, 19);
		$retorno->desc_comunic_superior			= odbc_result($result, 20);
		$retorno->dia_evento					= odbc_result($result, 21);
		$retorno->desc_dia_evento				= odbc_result($result, 22);
		$retorno->remedio_continuo				= odbc_result($result, 23);
		$retorno->desc_remedio_continuo			= odbc_result($result, 24);																																								
		$retorno->usando_epi					= odbc_result($result, 25);
		$retorno->desc_usando_epi				= odbc_result($result, 26);
		$retorno->conhecimento_risco			= odbc_result($result, 27);
		$retorno->recebeu_vale_transp			= odbc_result($result, 28);
		$retorno->apresentado_bo				= odbc_result($result, 29);
		$retorno->desc_apresentado_bo			= odbc_result($result, 30);
		$retorno->trajeto_rota					= odbc_result($result, 31);
		$retorno->horario_jornada				= odbc_result($result, 32);
		$retorno->desc_horario_jornada			= odbc_result($result, 33);
		$retorno->acidente_trajeto				= odbc_result($result, 34);
		$retorno->desc_acidente_trajeto			= odbc_result($result, 35);
		$retorno->disco_tacografo_disp			= odbc_result($result, 36);
		$retorno->veloc_evidenc_disco			= odbc_result($result, 37);
		$retorno->comentario_disco_tacografo	= odbc_result($result, 38);
		$retorno->tempo							= odbc_result($result, 39);
		$retorno->luz							= odbc_result($result, 40);
		$retorno->rodovia						= odbc_result($result, 41);
		$retorno->transito						= odbc_result($result, 42);
		$retorno->veiculo						= odbc_result($result, 43);
		$retorno->carga							= odbc_result($result, 44);
		$retorno->motorista						= odbc_result($result, 45);
		$retorno->cinto_seguranca				= odbc_result($result, 46);
		$retorno->observacao					= odbc_result($result, 47);
		$retorno->limite_veloc_pista			= odbc_result($result, 48);
		$retorno->feito_bo						= odbc_result($result, 49);
		$retorno->dados_bo						= odbc_result($result, 50);
		$retorno->data_bo						= odbc_result($result, 51);	
		$retorno->desc_vel_pontas				= odbc_result($result, 52);		
		$retorno->testemunha					= odbc_result($result, 53);
		$retorno->nome_testemunha				= odbc_result($result, 54);
		$retorno->funcao_testemunha				= odbc_result($result, 55);
		$retorno->relato_testemunha				= odbc_result($result, 56);
		$retorno->observ_exame_per				= odbc_result($result, 57);		
		$retorno->id							= odbc_result($result, 58);
		$retorno->desc_jornada_trabalho			= odbc_result($result, 59);
		$retorno->acid_traj_nao_aplic			= odbc_result($result, 60);
		$retorno->acid_tran_nao_aplic			= odbc_result($result, 61);
		$retorno->acid_env_prod_nao_aplic		= odbc_result($result, 62);

		return $retorno;

	}	



	//BUSCA OS DADOS DA ANALISE_CAUSA
	public function dadosAnCausa($acidente_id){
		
		$con = new ConexaoSCI;
		
		$logado = $_SESSION['usuario_logado'];
		

		$query = "select id, efeito_nc, causa_raiz
				  from analise_causa with (nolock)
				  where acidente_id = $acidente_id";
		//print $query;
		$result = $con->executar($query);
		
	 
		$retorno = '';
		
		$retorno->id				= odbc_result($result, 1);
		$retorno->efeito_nc			= odbc_result($result, 2);
		$retorno->causa_raiz		= odbc_result($result, 3);

		return $retorno;

	}


	//BUSCA OS PORQUES DA ANALISE_CAUSA
	public function pqAnCausa($analise_causa_id, $tipo_acao){
		
		$con = new ConexaoSCI;
		
		$logado = $_SESSION['usuario_logado'];
		

		$query = "select id, desc_porque
				  from pq_analise_causa with (nolock)
				  where analise_causa_id = '$analise_causa_id'
				  and status_id = 'a'";
		//print $query;
		$result = $con->executar($query);
		
	 
		$retorno = '';
		$cont_pq = 1;
		
		$table = "<table width='1140' border=1 cellspacing=0 cellpadding=1>
				   <tr>
						 <td bgcolor='#CCCCCC' width='20'><b><center><font >&nbsp;</center></b></td>
						 <td bgcolor='#CCCCCC'><b><center><font >Descri&ccedil;&atilde;o Porqu&ecirc;</center></b></td>";
						 
		if ($tipo_acao == 1)//inserçao
			$table .= "<td bgcolor='#CCCCCC' width='50'><b><center><font >Excluir</center></b></td>";
			
			$table .= "</tr>";		
		
		while(odbc_fetch_row($result))
		{			$id				= odbc_result($result,1);
			$desc_porque	= odbc_result($result,2);
			
			
			$table .= "<tr>
							<td bgcolor='#FFFFFF'><center><font ><font color='#0000FF'>".$cont_pq."</font></center></b></td>
							<td bgcolor='#FFFFFF'><center><font >
								<a href='javascript:altera_porque($id,2);'>".$desc_porque."</a></center></b></td>";
							
			if ($tipo_acao == 1)//inserçao				
				$table .= "<td bgcolor='#FFFFFF'><center><font >
							<a href='javascript:exclui_porque($id,3);'><img src='../SCA/images/excluir1.jpg' width='25' heigth='25' 
							style='border:none'></a></center></b></td>";							
				
				$table .= "</tr>"; 			
			
			$cont_pq++;
		}
		
		$table .= "</table>";			
		

		$retorno->relatorio = $table;

		return $retorno;

	}



	//VERIFICA SE O USUARIO LOGADO TEM PERMISSAO PARA ANALISE
	public function permissaoAnalise($acidente_id){
		
		$con = new ConexaoSCI;
		
		$logado = $_SESSION['usuario_logado'];
		

		$query = "SELECT responsavel.responsavel_id, ra.analise_rms
					FROM responsavel_analise_rms responsavel with (nolock)
					join usuario with (nolock) on
						usuario.pessoa_id = responsavel.pessoa_id
						and usuario.status = 'a'
					join registro_acidente ra with (nolock) on
						ra.acidente_id = responsavel.acidente_id						
					where responsavel.status_id = 'a'
					and usuario.usuario = '$logado'
					and responsavel.acidente_id = $acidente_id";
		//print $query;
		$result = $con->executar($query);
		
		$retorno = '';
		
		$retorno->responsavel_id	= odbc_result($result, 1);
		$retorno->analise_rms		= odbc_result($result, 2);


		return $retorno;

	}
        
        //VERIFICA SE O USUARIO LOGADO É O CRIADOR DA OCORRÊNCIA
	public function permissaoAnaliseCriador($acidente_id){
		
		$con = new ConexaoSCI;
		
		$logado = $_SESSION['usuario_logado'];
		

		$query = "
SELECT ra.acidente_id FROM registro_acidente ra
                                                
                                                JOIN CARGOSOL..PESSOA WITH(NOLOCK)
                                                ON PESSOA.pf_cpf collate SQL_Latin1_General_CP1_CI_AS = ra.CPF_RESPONSAVEL

                                                join usuario with (nolock) on
						usuario.pessoa_id = PESSOA.pessoa_id
						and usuario.status = 'a'
                                                  
					where usuario.usuario = '$logado'
					and ra.acidente_id = $acidente_id                    
";
		//print $query;
		$result = $con->executar($query);
		
		$retorno = '';
		
		$retorno->acidente_id	= odbc_result($result, 1);


		return $retorno;

	}


	//VERIFICA SE O USUARIO LOGADO TEM PERMISSAO PARA ANALISE
	public function permissaoPlanoAcao($acidente_id){
		
		$con = new ConexaoSCI;
		
		$logado = $_SESSION['usuario_logado'];
		

		$query = "select top 1 itens.item_id
					from registro_acidente ra with (nolock)
					join rms_processos with (nolock) on
						rms_processos.num_ocorrencia = ra.acidente_id
						and rms_processos.status = 'a'
					join itens_rms_processos itens with (nolock) on
						itens.rms_processos_id = rms_processos.rms_processos_id
						and itens.status = 'a'
					join responsavel_atividade responsavel with (nolock) on
						responsavel.item_id = itens.item_id
						and responsavel.status_id = 'a'
					join usuario with (nolock) on
						((usuario.pessoa_id = responsavel.pessoa_id) or (usuario.id = rms_processos.usuario_id) or 
						(usuario.id = rms_processos.lider_id) or (usuario.id = rms_processos.gerente_id)) 
						and usuario.status = 'a'
					where ra.acidente_id = $acidente_id
					and usuario.usuario = '$logado'
					
					UNION

					select top 1 rms_processos.rms_processos_id
					from registro_acidente ra with (nolock)
					join rms_processos with (nolock) on
						rms_processos.num_ocorrencia = ra.acidente_id
						and rms_processos.status = 'a'
					join responsavel_analise_rms rar with (nolock) on
						rar.acidente_id = ra.acidente_id
						and rar.status_id = 'a'
					join usuario with (nolock) on
						usuario.pessoa_id = rar.pessoa_id
						and usuario.status = 'a'
					where ra.acidente_id = $acidente_id
					and usuario.usuario = '$logado'					
					
					";
		//print $query;
		$result = $con->executar($query);
		
		$retorno = '';
		
		$retorno->responsavel_item_id	= odbc_result($result, 1);

		return $retorno;

	}



	//VERIFICA SE O PLANO É DE QSMA - SANTOS
	public function ocorrenciaQSMASantos($acidente_id){
		
		$con = new ConexaoSCI;
		
		$logado = $_SESSION['usuario_logado'];
		

		$query = "SELECT TOP 1 'S'
				  from registro_acidente with (nolock)
				  join tipo_registro_acidente tra with (nolock) on
					tra.id = registro_acidente.tipo_registro_id
				  join feridos_acidente feridos with (nolock) on
					feridos.acidente_id = registro_acidente.acidente_id
					and feridos.status_id = 'a'
				  JOIN CARGOSOL..COLABORADOR CO WITH (NOLOCK) ON
					CO.COLABORADOR_ID = feridos.PESSOA_ID
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
				  AND registro_acidente.acidente_id = $acidente_id
				  AND (((registro_acidente.classificacao is not null) and (registro_acidente.classificacao <> 'o')) OR
					((registro_acidente.classificacao = 'o') and ((select rar.responsavel_id
																	from responsavel_analise_rms rar with (nolock)
																	join usuario with (nolock) on
																		usuario.pessoa_id = rar.pessoa_id
																		and usuario.status = 'a'
																	where acidente_id = registro_acidente.acidente_id
																	and usuario.id = (select id 
																					  from usuario with (nolock) 
																					  where usuario = '$logado'
																					  and status = 'a')
																	and rar.status_id = 'a') > 0)))
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
																					and motorista_pessoa_id = feridos.PESSOA_ID
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
				  
				  SELECT TOP 1 'S'
				  from registro_acidente with (nolock)
				  join tipo_registro_acidente tra with (nolock) on
					tra.id = registro_acidente.tipo_registro_id
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
				  AND registro_acidente.acidente_id = $acidente_id
				  AND (((registro_acidente.classificacao is not null) and (registro_acidente.classificacao <> 'o')) OR
					((registro_acidente.classificacao = 'o') and ((select rar.responsavel_id
																	from responsavel_analise_rms rar with (nolock)
																	join usuario with (nolock) on
																		usuario.pessoa_id = rar.pessoa_id
																		and usuario.status = 'a'
																	where acidente_id = registro_acidente.acidente_id
																	and usuario.id = (select id 
																					  from usuario with (nolock) 
																					  where usuario = '$logado'
																					  and status = 'a')
																	and rar.status_id = 'a') > 0)))
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
		$result = $con->executar($query);
	 
		$retorno = '';
		
		$retorno->ocorrencia_sts	= odbc_result($result, 1);

		return $retorno;

	}



	//BUSCA DESPESAS DO REGISTRO
	public function buscaDespesas($acidente_id){
		
		$con = new ConexaoSCI;
		
		$logado = $_SESSION['usuario_logado'];
		

		$retorno = '';
		
		//VERIFICA SE O REGISTRO JA FOI CLASSIFICADO
		$query = "select acidente_id, classificacao
				  from registro_acidente with (nolock)
				  where acidente_id = $acidente_id";
		//print $query;
		$result = $con->executar($query);
		$id_encontrado = odbc_result($result, 1);	
		$classificacao = odbc_result($result, 2);			
		
		if ($id_encontrado == '')
			$retorno->retorno	= 'NAO_ENCONTRADO';
		else
			if ($classificacao == '')
				$retorno->retorno	= 'SEM_CLASSIFICACAO';
			else
			{
				$contador = 0;
	
				$query = "select area.area, tipo_despesa, 
							tipo.base, referencia.descricao referencia, status_despesa.descricao status
							from LANCAMENTO_DESPESAS_ACIDENTE lda with (nolock)
							join area_responsavel_acidente area with (nolock) on
								area.id = lda.AREA_ID
							left join tipo_despesa_acidente tipo with (nolock) on
								tipo.id = lda.tipo_despesa_id
							left join referencia_despesa referencia with (nolock) on
								referencia.id = tipo.referencia_despesa_id
							LEFT JOIN status_despesa_acidente status_despesa with (nolock) on
								status_despesa.id = lda.status_despesa_id
							where ACIDENTE_ID = $acidente_id
							and lda.status_id <> 'i'
							order by area, tipo_despesa";
				//print $query;
				$result = $con->executar($query);  
			
			
				$table = "<font color='#0000FF' size='-1'><b>Despesas Vinculadas</b></font>
				<table width='1200' border='1' >
				  <tr>
					<td bgcolor='#CCCCCC'><div align='center'><strong><font size='-2'>AREA</font></strong></div></td>
					<td bgcolor='#CCCCCC'><div align='center'><strong><font size='-2'>TIPO DE DESPESA</font></strong></div></td>
					<td bgcolor='#CCCCCC'><div align='center'><strong><font size='-2'>BASE</font></strong></div></td>	
					<td bgcolor='#CCCCCC'><div align='center'><strong><font size='-2'>REFERENCIA</font></strong></div></td>						
					<td bgcolor='#CCCCCC'><div align='center'><p align='center'><strong><font size='-2'>STATUS</font></strong></p></div></td>
				  </tr>";
				  
				  
				 while(odbc_fetch_row($result))
				 {
					   $area				= odbc_result($result,1);
					   $tipo_despesa 		= odbc_result($result,2);
					   $base		 		= odbc_result($result,3);			   
					   $referencia	 		= odbc_result($result,4);		
					   $status		 		= odbc_result($result,5);					   		   
					   
					   if ($base == 'DESPESA MANUAL')
					   	$tipo_despesa = utf8_encode($tipo_despesa);
					   
		
					$table .= "<tr>
								 <td bgcolor='#FFFFFF'><center><font size='-2'>".$area."</center></b></td>
								 <td bgcolor='#FFFFFF'><center><font size='-2'>".$tipo_despesa."</center></b></td>
								 <td bgcolor='#FFFFFF'><center><font size='-2'>".$base."</center></b></td>					 
								 <td bgcolor='#FFFFFF'><center><font size='-2'>".$referencia."</center></b></td>					 					 
								 <td bgcolor='#FFFFFF'><center><font size='-2'>".$status."</center></b></td>					 					 
							  </tr>";
					$contador++;
		
				 }
				 $table .= "</table>";
				
				if ($contador > 0)
					$retorno->retorno = $table;
				else
					$retorno->retorno = 'NENHUMA DESPESA VINCULADA';
		
			}
			return $retorno;

	
	}

	
}




?>