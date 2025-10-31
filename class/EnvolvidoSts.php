<?php
session_name("covre_ti");
session_start();

require_once $_SERVER['DOCUMENT_ROOT']."/SCI/controller/ConexaoSCI.php";


class EnvolvidoSts extends ConexaoSCI{


	//BUSCA OS DADOS DA INVESTIGACAO_ANALISE
	public function envolvidos($acidente_id){
		
		$con = new ConexaoSCI;
		
		$logado = $_SESSION['usuario_logado'];
		

		$query = "select top 1 feridos.id
				  from registro_acidente with (nolock)
				  join tipo_registro_acidente tra with (nolock) on
					tra.id = registro_acidente.tipo_registro_id
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
				  AND registro_acidente.acidente_id = $acidente_id
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
				END = 'STS'";
		//print "<pre>$query</pre>";
		$result = $con->executar($query);
		
		$retorno = '';
		
		$retorno->local	= odbc_result($result, 1);

		return $retorno;

	}



	
}




?>