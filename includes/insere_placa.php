<?php
session_name("covre_ti");
session_start();

include("../../SCA/includes/conect_sqlserver.php");

$logado = $_SESSION["usuario_logado"];

$veiculo_fornecedor_id	= $_POST["veiculo_fornecedor_id"];
$acidente_id			= $_POST["acidente_id"];
$cpf					= $_POST["cpf"];


if ($acidente_id != '')
{
	//verifica se o veiculo ja está cadastrado
	$query = "select id
			  from veiculos_acidente
			  where acidente_id = $acidente_id
			  and veiculo_fornecedor_id = $veiculo_fornecedor_id
			  and status_id = 'a'";
	//print $query;
	$result = odbc_exec($conSQL, $query) or die ('erro1 ao consultar o veiculo');	
	$id = odbc_result($result,1);
	
	if ($id == '')
	{
		$query = "insert into veiculos_acidente (veiculo_fornecedor_id, acidente_id, status_id) 
				  values ($veiculo_fornecedor_id, $acidente_id, 'a')";
		//print $query;
		odbc_exec($conSQL, $query) or die ('erro1 ao inserir');

		print "ok";
	}
	else
		print "Veiculo já inserido";
}
else
{
	//insere na tabela temporaria
	$query = "insert into veiculos_acidente_temp (veiculo_fornecedor_id, cpf) 
			  values ($veiculo_fornecedor_id, '$cpf')";
	//print $query;
	odbc_exec($conSQL, $query) or die ('erro2 ao inserir');		
	

	$query = "WITH depara_codfil_ponto_operacao AS (
		SELECT * FROM (VALUES
			(1, 1),      -- MATRIZ LIMEIRA
			(10, 1),     -- OPERAÇÃO MATRIZ
			(11, 58),    -- FILIAL AEROPORTO CAMPINAS
			(12, 106),   -- FILIAL APARECIDA DE GOIANIA
			(13, 116),   -- FILIAL ARMAZÉM 2 LIMEIRA (NR)
			(14, 91),    -- FILIAL CAMPOS NOVOS
			(15, 93),    -- FILIAL CUIABÁ
			(16, 16),    -- FILIAL OSASCO
			(17, 60),    -- PS AEROPORTO GUARULHOS
			(19, 71),    -- PS BOSCH CAMPINAS
			(20, 88),    -- PS BOSCH ITUPEVA
			(21, 92),    -- PS BOSCH SOROCABA
			(22, 45),    -- PS DELPHI PIRACICABA
			(23, 43),    -- PS SYNGENTA PAULÍNIA
			(24, 15),    -- SANTOS ADM GONZAGA
			(25, 15),    -- SANTOS ALEMOA
			(26, 112),   -- FILIAL GUARUJÁ
			(27, 81)     -- FILIAL GUAXUPÉ
		) AS mapeamento (codfil, ponto_operacao_id)
	)
	
	
	
	
	SELECT  DISTINCT 
		   VEICULO_PGR.PLACA,
		   dados.DESC_PROP_VEICULO,
		   ISNULL(vponto.nome, '') AS NOME_FANTASIA,
		   DADOS.DESCRICAO DESC_TIPO_VEICULO
		   
			
		 FROM OPENQUERY([BDRODOPAR], '
	SELECT 
	RODCGA.CODCGA,
	RODCLI.NOMEAB AS PROPRI,
	RODVEI.CODVEI AS VEICULO_FORNECEDOR_ID,
	RODVEI.CODVEI AS PLACA,
	RODMOT.NOMMOT  NOME_MOTORISTA,
	CASE
	WHEN RODVEI.PROPRI = ''S'' THEN ''EMPRESA DE TRANSPORTES COVRE LTDA''
	ELSE RODCLI.NOMEAB
	END AS DESC_PROP_VEICULO,
	RODRDI.DESCRI DESCRICAO
	
	FROM db_visual_covre..RODVEI WITH (NOLOCK)
	JOIN db_visual_covre..RODCGA ON
	RODCGA.CODCGA=RODVEI.CODCGA
	LEFT JOIN db_visual_covre..RODRDI ON
	RODRDI.CODRDI=RODVEI.CODRDI
	LEFT JOIN db_visual_covre..RODMCV ON
	RODMCV.CODMCV=RODVEI.CODMCV
	LEFT JOIN db_visual_covre..RODMDV ON 
	RODVEI.CODMDV=RODMDV.CODMDV
	LEFT JOIN db_visual_covre..RODGAS ON
	RODGAS.CODCMB=RODVEI.CODCMB
	LEFT JOIN db_visual_covre..RODMOT ON
	RODMOT.CODMOT=RODVEI.CODMOT
	LEFT JOIN db_visual_covre..RODCLI ON
	RODCLI.CODCLIFOR=RODVEI.CODPRO
	where 1=1')DADOS 	
	 JOIN	VEICULO_PGR WITH(NOLOCK)
		ON VEICULO_PGR.PLACA LIKE DADOS.PLACA COLLATE SQL_Latin1_General_CP1_CI_AS     
	
		JOIN depara_codfil_ponto_operacao AS mapa
		ON dados.CODCGA = mapa.codfil
	
	JOIN cargosol..vponto_operacao vponto WITH (NOLOCK)
		ON mapa.ponto_operacao_id = vponto.ponto_operacao_id
		JOIN VEICULOS_ACIDENTE_TEMP VAT WITH (NOLOCK) ON
						VAT.VEICULO_FORNECEDOR_ID = VEICULO_PGR.VEICULO_FORNECEDOR_ID
						where VAT.CPF = '$cpf'";
	//print $query;
	$result = odbc_exec($conSQL, $query);     


	print "<table width='1192' border='1' >
	  <tr>
		<td width='100' bgcolor='#CCCCCC'><div align='center'><strong><font size='-2'>PLACA</font></strong></div></td>
		<td bgcolor='#CCCCCC'>
			<div align='center'><p align='center'><strong><font size='-2'>PROPRIET&Aacute;RIO</font></strong></p></div></td>
		<td bgcolor='#CCCCCC'><div align='center'><strong><font size='-2'>PONTO OPERA&Ccedil;&Atilde;O</font></strong></div></td>
		<td bgcolor='#CCCCCC'>
			<div align='center'><p align='center'><strong><font size='-2'>TIPO VE&Iacute;CULO</font></strong></p></div></td>
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
				 <td bgcolor='#FFFFFF'><center><font size='-2'>".$tipo_veiculo."</center></b></td>
				 
				 </tr>";

	 }
	 print "</table>";

	
}
?>
