<?php
session_name("covre_ti");
session_start();

require_once $_SERVER['DOCUMENT_ROOT']."/SCI/controller/ConexaoSCI.php";


class RelatoriosInv extends ConexaoSCI{


	//BUSCA DADOS DOS ENVOLVIDOS
	public function dadosEnvolvidos($tipo_form,$acidente_id){
		
		$con = new ConexaoSCI;
		
		$logado = $_SESSION['usuario_logado'];
		

		$query = "SELECT DISTINCT
					DADOS.NOME,
					DADOS.DATA_ADMISSAO,
					CASE WHEN DADOS.MESES = 0
							THEN CAST(DADOS.ANOS AS VARCHAR)+' ANOS'
							ELSE CASE WHEN DADOS.MESES = 1
										THEN CAST(DADOS.ANOS AS VARCHAR)+' ANOS E '+CAST(DADOS.MESES AS VARCHAR)+' MES'
										ELSE CAST(DADOS.ANOS AS VARCHAR)+' ANOS E '+CAST(DADOS.MESES AS VARCHAR)+' MESES'
								 END
					END,
					DADOS.IDADE,
					(SELECT 
					CASE WHEN DADOS.MESES = 0
							THEN CAST(DADOS.ANOS AS VARCHAR)+' ANOS'
							ELSE CASE WHEN DADOS.MESES = 1
										THEN CAST(DADOS.ANOS AS VARCHAR)+' ANOS E '+CAST(DADOS.MESES AS VARCHAR)+' MES'
										ELSE CAST(DADOS.ANOS AS VARCHAR)+' ANOS E '+CAST(DADOS.MESES AS VARCHAR)+' MESES'
								 END
					END
					FROM (
							SELECT TOP 1 
							DateDiff(day, DTMUDANCA, GetDate())/365 as Anos,
							DateDiff(Month,DTMUDANCA,GetDate()) - (Case when day(GetDate()) < day(DTMUDANCA) Then 1 Else 0 end) - (12 * (DateDiff(day, DTMUDANCA, GetDate())/365))as Meses
							FROM CORPORE..PFHSTFCO WITH (NOLOCK)
							JOIN CORPORE..PFUNCAO WITH (NOLOCK) ON
								PFUNCAO.CODIGO = PFHSTFCO.CODFUNCAO
								AND PFUNCAO.CODCOLIGADA = 1
							WHERE CHAPA = DADOS.CHAPA
							AND PFHSTFCO.CODCOLIGADA = 1
							ORDER BY DTMUDANCA DESC
						)DADOS
					)TEMPO_ULTIMA_FUNCAO,
					DADOS.ENDERECO,
					DADOS.CHAPA
					FROM (SELECT PESSOA.NOME, 
							CONVERT(VARCHAR(10),PFUNC.DATAADMISSAO, 103) DATA_ADMISSAO, 
							--DATEDIFF(M, PFUNC.DATAADMISSAO, GETDATE()),
							DateDiff(day, PFUNC.DATAADMISSAO, GetDate())/365 as Anos,
							DateDiff(Month,PFUNC.DATAADMISSAO,GetDate()) - (Case when day(GetDate()) < day(PFUNC.DATAADMISSAO) Then 1 Else 0 end) - (12 * (DateDiff(day, PFUNC.DATAADMISSAO, GetDate())/365))as Meses,
							FLOOR(DATEDIFF(DAY, PPESSOA.DTNASCIMENTO, GETDATE()) / 365.25) IDADE,
							PFUNC.CHAPA,
							PPESSOA.RUA+', '+PPESSOA.NUMERO+', '+PPESSOA.BAIRRO+' - '+PPESSOA.CIDADE+'/'+PPESSOA.ESTADO ENDERECO
							FROM cargosol..PESSOA with (nolock)
							LEFT join corpore..PPESSOA with (nolock) on
								PPESSOA.CPF collate SQL_Latin1_General_CP1_CI_AS = PESSOA.pf_cpf
							LEFT JOIN CORPORE..PFUNC WITH (NOLOCK) ON
								PFUNC.CODPESSOA = PPESSOA.CODIGO
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
							JOIN CARGOSOL..COLABORADOR CO WITH (NOLOCK) ON
								CO.COLABORADOR_ID = PESSOA.PESSOA_ID
							where FA.ACIDENTE_ID = '$acidente_id'
							and FA.status_id = 'a'
						)DADOS";
		//print $query;
		$result = $con->executar($query);
		
		$chapa = '';
		$retorno = '';


		if ($tipo_form == 'F')
			$borda = "border='1'";
		else
			$borda = "class='bordasimples'";
			

		$table = "<table width='1150' $borda>
				   <tr>
						 <td bgcolor='#CCCCCC' width='400'><b><center><font size='-1'>Nome</center></b></td>
						 <td bgcolor='#CCCCCC' width='150'><b><center><font size='-1'>Data Admiss&atilde;o</center></b></td>
						 <td bgcolor='#CCCCCC' width='200'><b><center><font size='-1'>Tempo &uacute;ltimo Cargo</center></b></td>
						 <td bgcolor='#CCCCCC' width='250'><b><center><font size='-1'>Tempo de Empresa</center></b></td>
						 <td bgcolor='#CCCCCC' width='100'><b><center><font size='-1'>Idade</center></b></td>				 				 				
				   </tr>";
	
		  $conta_chapa = 1;
			 
		  while(odbc_fetch_row($result))
		  {
				$nome			 		= odbc_result($result,1);				   
				$data_admissao	 		= odbc_result($result,2);				   
				$tempo_empresa	 		= odbc_result($result,3);				   
				$idade			 		= odbc_result($result,4);				   
				$tempo_ultimo_cargo 	= odbc_result($result,5);	
				$chapa			 		= odbc_result($result,7);	
				
				if ($conta_chapa == 1)
				{
					$chapas = "'".$chapa."'";
					$nomes = "'".$nome."'";
				}
				else
				{
					$chapas .= ",'".$chapa."'";	
					$nomes .= ",'".$nome."'";	
				}
			
				$table .= "<tr>
								<td bgcolor='#FFFFFF'><center><font size='-1'>".$nome."</center></b></td>
								<td bgcolor='#FFFFFF'><center><font size='-1'>".$data_admissao."</center></b></td>
								<td bgcolor='#FFFFFF'><center><font size='-1'>".$tempo_ultimo_cargo."</center></b></td>						
								<td bgcolor='#FFFFFF'><center><font size='-1'>".$tempo_empresa."</center></b></td>
								<td bgcolor='#FFFFFF'><center><font size='-1'>".$idade." anos</center></b></td>																			
						   </tr>";   
						   
				$conta_chapa++;		  
						 
		  } 
   
		  $table .= "</table>";	

		$retorno->relatorio = $table;
		$retorno->chapas = $chapas;
		$retorno->nomes = $nomes;

		return $retorno;

	}	


	//BUSCA PLACAS E DATA/HORA FATO
	public function placasDatafato($acidente_id){
		
		$con = new ConexaoSCI;
		
		$logado = $_SESSION['usuario_logado'];
		

		$query = "SELECT PLACA, CAST(CONVERT(VARCHAR(10), data_fato, 120)+' '+hora_fato AS DATETIME)
					FROM VEICULOS_ACIDENTE VA WITH (NOLOCK)
					JOIN CARGOSOL..VEICULO_FORNECEDOR VF WITH (NOLOCK) ON
						VF.VEICULO_FORNECEDOR_ID = VA.VEICULO_FORNECEDOR_ID
					JOIN REGISTRO_ACIDENTE RA WITH (NOLOCK) ON
						RA.ACIDENTE_ID = VA.ACIDENTE_ID
					WHERE VA.STATUS_ID = 'A'
					AND VA.ACIDENTE_ID IN ($acidente_id)";
		//print $query;
		$result = $con->executar($query);
		
		$retorno = '';
		$conta_placa = 1;

		while(odbc_fetch_row($result))
		{
			$placa			= odbc_result($result,1);
			$data_fato	 	= odbc_result($result,2);
			
			
			if ($conta_placa == 1)
				$placas = "'".$placa."'";
			else
				$placas .= ",'".$placa."'";	
			
			$conta_placa++;
		} 

		$retorno->placas = $placas;
		$retorno->datafato = $data_fato;		

		return $retorno;

	}		
	



	//BUSCA ULTIMOS TREINAMENTOS (12 MESES)
	public function treinamentosRealizados($tipo_form,$chapas,$acidente_id){
		
		$con = new ConexaoSCI;
		
		$logado = $_SESSION['usuario_logado'];
		
		if ($chapas == '')
			$chapas = "''";
		

		$query = "SELECT DISTINCT
					CONVERT(VARCHAR(10), VTURMAS.DTTERMINO, 103),
					VTURMAS.NOME collate sql_latin1_general_cp1251_ci_as TURMA,
					DBO.MINTOTIME(VTURMAS.CARGAHORARIA,':') CARGAHORARIA,
					ISNULL(PFUNC.CHAPA,PPESSOA.CODIGO) CODIGO,
					PPESSOA.NOME NOME,
					CASE WHEN COLABORADOR.HOMOLOGADO_SASSMAQ = 'S'
						 THEN 'SIM'
						 ELSE 'NAO'
					END HOMOLOGADO,
				   (SELECT DESCRICAO FROM CORPORE..PSECAO (NOLOCK)
					WHERE CODCOLIGADA = 1 AND CODIGO = 
				   (SELECT TOP 1 CODSECAO
					FROM CORPORE..PFHSTSEC (NOLOCK)
					WHERE PFHSTSEC.CODCOLIGADA = PFUNC.CODCOLIGADA AND 
					PFHSTSEC.CHAPA = PFUNC.CHAPA
					AND PFHSTSEC.DTMUDANCA <= VTURMAS.DTTERMINO
					ORDER BY PFHSTSEC.DTMUDANCA DESC)) SECAO,
					CASE WHEN INSTRUTOR.NOME IS NULL
						 THEN UPPER(VTURMAS.NOMEINSTRUTOR)
						 ELSE INSTRUTOR.NOME
					END INSTRUTOR,
					VTURMAS.CARGAHORARIA,
					VTURMAS.DTTERMINO DATA_TERMINO
				FROM
					CORPORE..VTURMAS (NOLOCK)
				JOIN CORPORE..VTURMA (NOLOCK) ON
					VTURMAS.CODTURMA = VTURMA.CODTURMA
				JOIN CORPORE..PPESSOA (NOLOCK) ON
					PPESSOA.CODIGO = VTURMA.CODPESSOA
				LEFT JOIN CORPORE..PPESSOA INSTRUTOR (NOLOCK) ON
					INSTRUTOR.CODIGO = VTURMAS.CODINSTRUTOR
				JOIN CORPORE..PFUNC (NOLOCK) ON
					PFUNC.CODCOLIGADA = VTURMA.CODCOLIGADA
				AND PFUNC.CODPESSOA = VTURMA.CODPESSOA
				AND PFUNC.CODTIPO <> 'A'
				AND ISNULL(PFUNC.TIPODEMISSAO,'0') <> '5'
				AND PFUNC.DATAADMISSAO <= VTURMAS.DTTERMINO
				AND(PFUNC.DATADEMISSAO >= VTURMAS.DTTERMINO
				 OR PFUNC.DATADEMISSAO IS NULL)
				LEFT JOIN CORPORE..VENTIDADES (NOLOCK) ON
					VENTIDADES.CODENTIDADE = VTURMAS.CODENTIDADE
				LEFT JOIN CARGOSOL..COLABORADOR COLABORADOR (NOLOCK) ON
					COLABORADOR.NUM_MATRICULA COLLATE SQL_Latin1_General_CP1_CI_AI = PFUNC.CHAPA
				WHERE
					VTURMAS.CODCOLIGADA = 1
				AND VTURMAS.DTTERMINO BETWEEN (SELECT CAST(DATA_FATO AS DATETIME) - 365
											   FROM REGISTRO_ACIDENTE WITH (NOLOCK)
											   WHERE ACIDENTE_ID = $acidente_id) AND (SELECT CAST(DATA_FATO AS DATETIME)
																					   FROM REGISTRO_ACIDENTE WITH (NOLOCK)
																					   WHERE ACIDENTE_ID = $acidente_id)
				AND VTURMAS.ATIVO = 0
				AND PFUNC.CHAPA IN ($chapas)
				
				UNION ALL
				
				SELECT DISTINCT
					CONVERT(VARCHAR(10), VTURMAS.DTTERMINO, 103),
					VTURMAS.NOME collate sql_latin1_general_cp1251_ci_as TURMA,
					DBO.MINTOTIME(VTURMAS.CARGAHORARIA,':') CARGAHORARIA,
					PPESSOA.CODIGO CODIGO,
					PPESSOA.NOME,
					'' HOMOLOGADO,
					'' SECAO,
					CASE WHEN INSTRUTOR.NOME IS NULL
						 THEN UPPER(VTURMAS.NOMEINSTRUTOR)
						 ELSE INSTRUTOR.NOME
					END INSTRUTOR,
					VTURMAS.CARGAHORARIA,
					VTURMAS.DTTERMINO
				FROM
					CORPORE..VTURMAS WITH (NOLOCK)
				JOIN CORPORE..VTURMA (NOLOCK) ON
					VTURMAS.CODTURMA = VTURMA.CODTURMA
				JOIN CORPORE..PPESSOA (NOLOCK) ON
					PPESSOA.CODIGO = VTURMA.CODPESSOA
				JOIN CORPORE..PFUNC (NOLOCK) ON
					PFUNC.CODPESSOA = PPESSOA.CODIGO
				LEFT JOIN CORPORE..PPESSOA INSTRUTOR (NOLOCK) ON
					INSTRUTOR.CODIGO = VTURMAS.CODINSTRUTOR
				JOIN CORPORE..VPCOMPL (NOLOCK) ON
					VPCOMPL.CODPESSOA = PPESSOA.CODIGO
				AND VPCOMPL.TIPO IN ('1','3')
				LEFT JOIN CORPORE..VENTIDADES (NOLOCK) ON
					VENTIDADES.CODENTIDADE = VTURMAS.CODENTIDADE
				WHERE
					VTURMAS.CODCOLIGADA = 1
				AND VTURMAS.DTTERMINO BETWEEN (SELECT CAST(DATA_FATO AS DATETIME) - 365
											   FROM REGISTRO_ACIDENTE WITH (NOLOCK)
											   WHERE ACIDENTE_ID = $acidente_id) AND (SELECT CAST(DATA_FATO AS DATETIME)
																					   FROM REGISTRO_ACIDENTE WITH (NOLOCK)
																					   WHERE ACIDENTE_ID = $acidente_id)		
				AND VTURMAS.ATIVO = 0
				AND PFUNC.CHAPA IN ($chapas)
				ORDER BY NOME, DATA_TERMINO ";
		//print $query;
		$result = $con->executar($query);
		
		$retorno = '';
		
		if ($tipo_form == 'F')
			$borda = "border='1'";
		else
			$borda = "class='bordasimples'";
					

		$table = "<table width='1150' $borda>
				   <tr>
					 <td width='400' bgcolor='#CCCCCC'><font size='-1'><b><center>Funcionario</center></b></td>
					 <td width='150' bgcolor='#CCCCCC'><font size='-1'><b><center>Data</center></b></td>
					 <td width='600' bgcolor='#CCCCCC'><font size='-1'><b><center>Treinamento</center></b></td>
				   </tr>";
	
		 while(odbc_fetch_row($result))
		 {
				$data_curso			= odbc_result($result,1);
				$turma 				= odbc_result($result,2);
				$carga_horaria 		= odbc_result($result,3);
				$nome_func_trein	= odbc_result($result,5);
				$instutor 			= odbc_result($result,8);			
			
				$table .= "<tr>
								<td bgcolor='#FFFFFF'><center><font size='-1'>".$nome_func_trein."<font></center></td>
								<td bgcolor='#FFFFFF'><center><font size='-1'>".$data_curso."<font></center></td>
								<td bgcolor='#FFFFFF'><center><font size='-1'>".$turma."<font></center></td>
						   </tr>";   
		   } 
   
		  $table .= "</table>";	

		$retorno->relatorio = $table;

		return $retorno;

	}	



	//ENDERECO DOS ENVOLVIDOS
	public function enderecoEnvolvido($tipo_form,$chapas,$acidente_id){
		
		$con = new ConexaoSCI;
		
		$logado = $_SESSION['usuario_logado'];
		
		if ($chapas == '')
			$chapas = "''";
				

		$query = "SELECT PFUNC.NOME, PPESSOA.RUA+', '+PPESSOA.NUMERO+', '+PPESSOA.BAIRRO+' - '+UPPER(PPESSOA.CIDADE)+'/'+PPESSOA.ESTADO 
					collate sql_latin1_general_cp1251_ci_as
					FROM CORPORE..PPESSOA WITH (NOLOCK)
					JOIN CORPORE..PFUNC WITH (NOLOCK) ON
						PFUNC.CODPESSOA = PPESSOA.CODIGO
						AND PFUNC.CODCOLIGADA = 1
						AND PFUNC.CODSITUACAO <> 'D'
						AND PFUNC.CODTIPO <> 'A'
					WHERE PFUNC.CHAPA IN ($chapas)";
		//print $query;
		$result = $con->executar($query);
		
		$retorno = '';
		
		if ($tipo_form == 'F')
			$borda = "border='1'";
		else
			$borda = "class='bordasimples'";		


		$table = "<table width='1000' $borda>
					   <tr>
						 <td width='400' bgcolor='#CCCCCC'><font size='-1'><b><center>Funcion&aacute;rio</center></b></td>
						 <td width='600' bgcolor='#CCCCCC'><font size='-1'><b><center>Endere&ccedil;o</center></b></td>
					   </tr>";
	
		 while(odbc_fetch_row($result))
		 {
				$nome			= odbc_result($result,1);
				$endereco	 	= odbc_result($result,2);
			
				$table .= "<tr>
								<td bgcolor='#FFFFFF'><center><font size='-1'>".$nome."<font></center></td>
								<td bgcolor='#FFFFFF'><center><font size='-1'>".$endereco."<font></center></td>
						  </tr>";   
		  } 
   
		  $table .= "</table>";	

		$retorno->relatorio = $table;

		return $retorno;

	}	



	//BUSCA VELOCIDADE E PONTAS
	public function velocidadePonta($tipo_form,$nomes,$data_fato){
		
		$con = new ConexaoSCI;
		
		$logado = $_SESSION['usuario_logado'];
	
		if ($nomes == '')
			$nomes = "''";
			

		$query = "select distinct upper(nome_motorista_arq), placa, CONVERT(VARCHAR(10),data_hora_inicio, 103) data, velocidade_maxima, tas pontas, 					CONVERT(VARCHAR(10), data_hora_inicio, 120) data_inicio
					from tacografo with (nolock)
					where nome_motorista_arq in ($nomes)
					and data_hora_inicio BETWEEN (CAST('$data_fato' AS DATETIME)-180) AND CAST('$data_fato' AS DATETIME)
					and tas > 0
					order by data_inicio";
		//print $query;
		$result = $con->executar($query);	
		
		$retorno = '';
		
		if ($tipo_form == 'F')
			$borda = "border='1'";
		else
			$borda = "class='bordasimples'";		


		$table = "<table width='1000' $borda>
					   <tr>
						 <td width='500' bgcolor='#CCCCCC'><font size='-1'><b><center>Motorista</center></b></td>
						 <td width='150' bgcolor='#CCCCCC'><font size='-1'><b><center>Placa</center></b></td>
						 <td width='150' bgcolor='#CCCCCC'><font size='-1'><b><center>Data</center></b></td>
						 <td width='100' bgcolor='#CCCCCC'><font size='-1'><b><center>Vel. M&aacute;xima</center></b></td>						 						 						 <td width='100' bgcolor='#CCCCCC'><font size='-1'><b><center>Pontas</center></b></td>						 						
					   </tr>";
	
		 while(odbc_fetch_row($result))
		 {
				$motorista		= odbc_result($result,1);
				$placa 			= odbc_result($result,2);
				$data 			= odbc_result($result,3);
				$vel_maxima		= odbc_result($result,4);
				$pontas			= odbc_result($result,5);								
			
				$table .= "<tr>
								<td bgcolor='#FFFFFF'><center><font size='-1'>".$motorista."<font></center></td>
								<td bgcolor='#FFFFFF'><center><font size='-1'>".$placa."<font></center></td>
								<td bgcolor='#FFFFFF'><center><font size='-1'>".$data."<font></center></td>
								<td bgcolor='#FFFFFF'><center><font size='-1'>".$vel_maxima."<font></center></td>
								<td bgcolor='#FFFFFF'><center><font size='-1'>".$pontas."<font></center></td>																								
						  </tr>";   
		  } 
   
		  $table .= "</table>";	

		$retorno->relatorio = $table;

		return $retorno;

	}	


	
	//BUSCA ULTIMA FERIAS GOZADAS
	public function feriasGozada($tipo_form,$chapas,$acidente_id){
		
		$con = new ConexaoSCI;
		
		$logado = $_SESSION['usuario_logado'];
		
		if ($chapas == '')
			$chapas = "''";
				

		$query = "SELECT PFUNC.NOME, CONVERT(VARCHAR(10),PFUFERIASPER.DATAINICIO,103)DATA_INICIO, 
				CONVERT(VARCHAR(10),PFUFERIASPER.DATAFIM,103)DATA_FIM	
				FROM CORPORE..PFUNC WITH(NOLOCK) 
				JOIN CORPORE..PPESSOA WITH (NOLOCK) ON 
					PFUNC.CODPESSOA = PPESSOA.CODIGO
				LEFT JOIN CORPORE..PFUFERIAS WITH(NOLOCK) ON
					PFUFERIAS.CHAPA = PFUNC.CHAPA
					AND PFUFERIAS.CODCOLIGADA = PFUNC.CODCOLIGADA
				LEFT JOIN CORPORE..PFUFERIASPER WITH(NOLOCK) ON
					PFUFERIASPER.CHAPA = PFUFERIAS.CHAPA
					AND PFUFERIASPER.FIMPERAQUIS = PFUFERIAS.FIMPERAQUIS
					AND PFUFERIASPER.CODCOLIGADA = PFUFERIAS.CODCOLIGADA
				Where pfunc.chapa in ($chapas)
				and PFUFERIASPER.DATAFIM = (SELECT TOP 1 PFUFERIASPER.DATAFIM
											FROM CORPORE..PFUFERIASPER WITH (NOLOCK)
											WHERE CHAPA = PFUNC.CHAPA
											AND PFUFERIASPER.CODCOLIGADA = 1
											AND PFUFERIASPER.DATAFIM <= (SELECT DATA_FATO
																		 FROM REGISTRO_ACIDENTE WITH (NOLOCK)
																		 WHERE ACIDENTE_ID = $acidente_id)
											ORDER BY PFUFERIASPER.DATAFIM DESC)
				and pfunc.codcoligada = 1
				order by pfunc.NOME, PFUFERIASPER.DATAFIM DESC";
		//print $query;
		$result = $con->executar($query);
		
		$retorno = '';
		
		if ($tipo_form == 'F')
			$borda = "border='1'";
		else
			$borda = "class='bordasimples'";		
		

		$table = "<table width='1000' $borda>
					   <tr>
						 <td width='600' bgcolor='#CCCCCC'><font size='-1'><b><center>Funcion&aacute;rio</center></b></td>
						 <td width='400' bgcolor='#CCCCCC'><font size='-1'><b><center>Per&iacute;odo</center></b></td>
					   </tr>";
	
		 while(odbc_fetch_row($result))
		 {
				$nome			= odbc_result($result,1);
				$data_inicio 	= odbc_result($result,2);
				$data_fim 		= odbc_result($result,3);
			
				$table .= "<tr>
								<td bgcolor='#FFFFFF'><center><font size='-1'>".$nome."<font></center></td>
								<td bgcolor='#FFFFFF'><center><font size='-1'>".$data_inicio." &agrave; ".$data_fim."<font></center></td>
						  </tr>";   
		  } 
   
		  $table .= "</table>";	

		$retorno->relatorio = $table;

		return $retorno;

	}	
	

	
	//BUSCA ULTIMO EXAME PERIODICO
	public function examePeriodico($tipo_form,$chapas,$acidente_id){
		
		$con = new ConexaoSCI;
		
		$logado = $_SESSION['usuario_logado'];
		
		if ($chapas == '')
			$chapas = "''";
				

		$query = "SELECT PFUNC.NOME, CONVERT(VARCHAR(10),DATACONSULTA,103) DATA
					FROM CORPORE..VCONSULTASPRONT WITH (NOLOCK)
					JOIN CORPORE..PFUNC WITH (NOLOCK) ON
						PFUNC.CODPESSOA = VCONSULTASPRONT.CODPESSOA
						AND PFUNC.CODCOLIGADA = 1
					where PFUNC.CHAPA IN ($chapas)
					AND CODTIPOCONSULTA = '9002'
					AND DATACONSULTA = (SELECT TOP 1 DATACONSULTA
										FROM CORPORE..VCONSULTASPRONT WITH (NOLOCK)
										WHERE CODPESSOA = PFUNC.CODPESSOA
										AND CODTIPOCONSULTA = '9002'
										AND CODCOLIGADA = 1
										AND DATACONSULTA <= (SELECT DATA_FATO
															 FROM REGISTRO_ACIDENTE WITH (NOLOCK)
															 WHERE ACIDENTE_ID = $acidente_id)										
										ORDER BY DATACONSULTA DESC)";
		//print $query;
		$result = $con->executar($query);
		
		$retorno = '';
		
		if ($tipo_form == 'F')
			$borda = "border='1'";
		else
			$borda = "class='bordasimples'";		
		

		$table = "<table width='1000' $borda>
					   <tr>
						 <td width='600' bgcolor='#CCCCCC'><font size='-1'><b><center>Funcion&aacute;rio</center></b></td>
						 <td width='400' bgcolor='#CCCCCC'><font size='-1'><b><center>Data do Exame</center></b></td>
					   </tr>";
	
		 while(odbc_fetch_row($result))
		 {
				$nome			= odbc_result($result,1);
				$data_exame 	= odbc_result($result,2);
			
				$table .= "<tr>
								<td bgcolor='#FFFFFF'><center><font size='-1'>".$nome."<font></center></td>
								<td bgcolor='#FFFFFF'><center><font size='-1'>".$data_exame."<font></center></td>
						  </tr>";   
		  } 
   
		  $table .= "</table>";	

		$retorno->relatorio = $table;

		return $retorno;

	}	

	
	//BUSCA ULTIMO EXAME PSICOLOGICO
	public function examePsicologico($tipo_form,$chapas,$acidente_id){
		
		$con = new ConexaoSCI;
		
		$logado = $_SESSION['usuario_logado'];
		
		if ($chapas == '')
			$chapas = "''";
				

		$query = "SELECT PFUNC.NOME, CONVERT(VARCHAR(10),DATACONSULTA,103) DATA
					FROM CORPORE..VCONSULTASPRONT WITH (NOLOCK)
					JOIN CORPORE..PFUNC WITH (NOLOCK) ON
						PFUNC.CODPESSOA = VCONSULTASPRONT.CODPESSOA
						AND PFUNC.CODCOLIGADA = 1
					where PFUNC.CHAPA IN ($chapas)
					AND CODTIPOCONSULTA = '9007'
					AND DATACONSULTA = (SELECT TOP 1 DATACONSULTA
										FROM CORPORE..VCONSULTASPRONT WITH (NOLOCK)
										WHERE CODPESSOA = PFUNC.CODPESSOA
										AND CODTIPOCONSULTA = '9007'
										AND CODCOLIGADA = 1
										AND DATACONSULTA <= (SELECT DATA_FATO
															 FROM REGISTRO_ACIDENTE WITH (NOLOCK)
															 WHERE ACIDENTE_ID = $acidente_id)										
										ORDER BY DATACONSULTA DESC)";
		//print $query;
		$result = $con->executar($query);
		
		$retorno = '';
		
		if ($tipo_form == 'F')
			$borda = "border='1'";
		else
			$borda = "class='bordasimples'";		
		

		$table = "<table width='1000' $borda>
					   <tr>
						 <td width='600' bgcolor='#CCCCCC'><font size='-1'><b><center>Funcion&aacute;rio</center></b></td>
						 <td width='400' bgcolor='#CCCCCC'><font size='-1'><b><center>Data do Exame</center></b></td>
					   </tr>";
	
		 while(odbc_fetch_row($result))
		 {
				$nome			= odbc_result($result,1);
				$data_exame 	= odbc_result($result,2);
			
				$table .= "<tr>
								<td bgcolor='#FFFFFF'><center><font size='-1'>".$nome."<font></center></td>
								<td bgcolor='#FFFFFF'><center><font size='-1'>".$data_exame."<font></center></td>
						  </tr>";   
		  } 
   
		  $table .= "</table>";	

		  $retorno->relatorio = $table;

		  return $retorno;

	}
	
	
	
	//BUSCA ULTIMA MANUTENCAO PREVENTIVA
	public function manutPreventiva($tipo_form,$placas,$data_fato){
		
		$con = new ConexaoSCI;
		
		$logado = $_SESSION['usuario_logado'];
	
		if ($placas == '')
			$placas = "''";

		$query = "SELECT DISTINCT
					DADOS.PLACA,
					DADOS.PLANO,
					CONVERT(VARCHAR(10),DADOS.DATA_TERMINO,103) DATA_TERMINO,
					PRODUTO.NOMEFANTASIA collate sql_latin1_general_cp1251_ci_as ITEM
					FROM ( SELECT TOP 1 PLANO.IDPLANO, TMOV.DATAEXTRA2 DATA_TERMINO, VPLANOMANUTENCAO.PLANO, TMOV.DATAEXTRA2, 
							VPLANOMANUTENCAO.PLACA PLACA
						   FROM INTEGRADADOS..VPLANOMANUTENCAO WITH (NOLOCK)
						   JOIN CORPORE..OFLOGGERPLANO PLANO WITH (NOLOCK) ON
								PLANO.IDPLANO = VPLANOMANUTENCAO.IDPLANO
							AND PLANO.CODCOLIGADA = 1
							JOIN CORPORE..OFOBJOFICINA OFOBJOFICINA WITH (NOLOCK) ON
							OFOBJOFICINA.IDOBJOF = PLANO.IDOBJOF
							AND OFOBJOFICINA.CODCOLIGADA = PLANO.CODCOLIGADA
							JOIN CORPORE..TMOV TMOV WITH (NOLOCK) ON
								TMOV.IDMOV = PLANO.IDMOV
								AND TMOV.CODCOLIGADA = 1
						   Where VPLANOMANUTENCAO.PLACA IN ($placas)
						   AND PLANO LIKE 'PREV%'
						   AND TMOV.DATAEXTRA2 IS NOT NULL
						   AND TMOV.DATAEXTRA2 <= '$data_fato'
						   ORDER BY TMOV.DATAEXTRA2 DESC
						)DADOS
					JOIN CORPORE..OFITMPLANO ITEM WITH (NOLOCK) ON
						ITEM.IDPLANO = DADOS.IDPLANO
					JOIN CORPORE..TPRODUTO PRODUTO WITH (NOLOCK) ON
						PRODUTO.IDPRD = ITEM.IDPRD
					WHERE 1 = 1	
					AND PRODUTO.CODIGOPRD LIKE '1.90%'
					ORDER BY DADOS.PLACA, ITEM
					";
		//print $query;
		$result = $con->executar($query);	
		
		$retorno = '';
		
		if ($tipo_form == 'F')
			$borda = "border='1'";
		else
			$borda = "class='bordasimples'";		
		

		$table = "<table width='1100' $borda>
					   <tr>
						 <td width='150' bgcolor='#CCCCCC'><font size='-1'><b><center>Placa</center></b></td>
						 <td width='150' bgcolor='#CCCCCC'><font size='-1'><b><center>Data T&eacute;rmino</center></b></td>
						 <td width='800' bgcolor='#CCCCCC'><font size='-1'><b><center>Item</center></b></td>						 						 
					   </tr>";
	
		 while(odbc_fetch_row($result))
		 {
				$placa			= odbc_result($result,1);
				$plano 			= odbc_result($result,2);
				$data 			= odbc_result($result,3);
				$item			= odbc_result($result,4);
			
				$table .= "<tr>
								<td bgcolor='#FFFFFF'><center><font size='-1'>".$placa."<font></center></td>
								<td bgcolor='#FFFFFF'><center><font size='-1'>".$data."<font></center></td>
								<td bgcolor='#FFFFFF'><center><font size='-1'>".$item."<font></center></td>
						  </tr>";   
		  } 
   
		  $table .= "</table>";	

		$retorno->relatorio = $table;

		return $retorno;

	}	
	
	
}


?>