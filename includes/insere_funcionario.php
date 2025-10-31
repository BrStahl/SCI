<?php
session_name("covre_ti");
session_start();

include("../../SCA/includes/conect_sqlserver.php");

$logado = $_SESSION["usuario_logado"];

$pessoa_id		= $_POST["pessoa_id"];
$tipo_envolvido	= $_POST["tipo_envolvido"];
$func_ferido	= $_POST["func_ferido"];
$acidente_id	= $_POST["acidente_id"];
$tipo_ferimento	= $_POST["tipo"];
$hospital		= $_POST["hospital"];
$cpf			= $_POST["cpf"];


if ($acidente_id != '')
{

	//verifica se a pessoa já está inserida no acidente
	$query = "select pessoa_id
			  from feridos_acidente			
			  where pessoa_id = $pessoa_id
			  and status_id = 'a'
			  and acidente_id = $acidente_id";
	//print $query;
	$result = odbc_exec($conSQL, $query) ;
	$pessoa_inserida = odbc_result($result, 1);

	if ($pessoa_inserida == '')
	{
	
		$query = "insert into feridos_acidente (pessoa_id, acidente_id, tipo_envolvido_id, func_ferido, tipo_ferimento, hospital, 
				  status_id) values ($pessoa_id, $acidente_id, $tipo_envolvido, '$func_ferido', 
				  case when '$tipo_ferimento' = '' then null else '$tipo_ferimento' end, 
				  case when '$hospital' = '' then null else '$hospital' end, 'a')";
		//print $query;
		odbc_exec($conSQL, $query) or die ('erro1 ao inserir');
		
		print "ok";
	}
	else
		print "Funcionário Já inserido";	
		

}
else
{
	//insere na tabela temporaria
	$query = "insert into feridos_acidente_temp (pessoa_id, cpf, tipo_envolvido_id, func_ferido, tipo_ferimento, hospital) 
			  values ($pessoa_id, '$cpf', $tipo_envolvido, '$func_ferido', 
			  case when '$tipo_ferimento' = '' then null else '$tipo_ferimento' end,
			  case when '$hospital' = '' then null else '$hospital' end)";
	//print $query;
	odbc_exec($conSQL, $query) or die ('erro2 ao inserir');		
	

   $query = "select DISTINCT PESSOA.NOME collate sql_latin1_general_cp1251_ci_as, 
					CASE CO.TAB_TIPO_VINCULO_ID
						WHEN 1 THEN PFUNCAO.NOME collate sql_latin1_general_cp1251_ci_as 
						WHEN 2 THEN 'MOTORISTA AGREGADO'
						WHEN 3 THEN 'MOTORISTA TERCEIRO'
					END FUNCAO,
					PSECAO.DESCRICAO  collate sql_latin1_general_cp1251_ci_as,
					CASE WHEN FAT.FUNC_FERIDO = 's'
							THEN 'Sim'
							ELSE 'Nao'
					END,
					TIPO_FERIMENTO,
					HOSPITAL,
					CASE WHEN CO.PONTO_OPERACAO_ID IN (15,85,89)
							THEN 'STS'
							ELSE 'COVRE'
					END,
					tipo_envolvido.descricao tipo_envolvido					
					from cargosol..PESSOA with (nolock)
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
					LEFT JOIN FERIDOS_ACIDENTE_TEMP FAT WITH (NOLOCK) ON
						FAT.PESSOA_ID = PESSOA.PESSOA_ID
					LEFT JOIN CORPORE..PFUNCAO WITH (NOLOCK) ON
						PFUNCAO.CODIGO = PFUNC.CODFUNCAO
						AND PFUNCAO.CODCOLIGADA = 1
					JOIN CARGOSOL..COLABORADOR CO WITH (NOLOCK) ON
						CO.COLABORADOR_ID = PESSOA.PESSOA_ID
					LEFT JOIN tipo_envolvido_acidente tipo_envolvido with (nolock) on
						tipo_envolvido.id = FAT.TIPO_ENVOLVIDO_ID
						and tipo_envolvido.status_id = 'a'
				where FAT.CPF = '$cpf'
				";
	//print $query;
	$result = odbc_exec($conSQL, $query);    


	print "<table width='1192' border='1' >
		  <tr>
			<td bgcolor='#CCCCCC'><div align='center'><strong>
				<font size='-2'>FUNCION&Aacute;RIO ENVOLVIDO </font></strong></div></td>
			<td bgcolor='#CCCCCC'>
				<div align='center'><p align='center'><strong><font size='-2'>FUN&Ccedil;&Atilde;O</font></strong></p></div></td>
			<td bgcolor='#CCCCCC'><div align='center'><strong><font size='-2'>SE&Ccedil;&Atilde;O</font></strong></div></td>
			<td bgcolor='#CCCCCC'><div align='center'><p align='center'><strong>
				<font size='-2'>TIPO ENVOLVIDO</font></strong></p></div></td>
			<td bgcolor='#CCCCCC'><div align='center'><p align='center'><strong>
				<font size='-2'>FERIDO</font></strong></p></div></td>
			<td bgcolor='#CCCCCC'><div align='center'><p align='center'><strong>
				<font size='-2'>TIPO FERIMENTO</font></strong></p></div></td>
			<td bgcolor='#CCCCCC'><div align='center'><p align='center'><strong>
				<font size='-2'>HOSPITAL</font></strong></p></div></td>										
		  </tr>";
	  
	  
	  
	 while(odbc_fetch_row($result))
	 {
		   $nome_envolvido 		= odbc_result($result,1);
		   $funcao				= odbc_result($result,2);
		   $secao		 		= odbc_result($result,3);
		   $ferido		 		= odbc_result($result,4);
		   $tipo_ferimento		= odbc_result($result,5);	
		   $hospital			= odbc_result($result,6);		
		   $po_envolvido		= odbc_result($result,7);	
		   $desc_tipo_env		= odbc_result($result,8);	
		   
		   if ($po_envolvido == 'STS')
				$ocorrencia_sts = 'S';		   					   

		  print "<tr>
				 <td bgcolor='#FFFFFF'><center><font size='-2'>".$nome_envolvido."</center></b></td>
				 <td bgcolor='#FFFFFF'><center><font size='-2'>".$funcao."</center></b></td>
				 <td bgcolor='#FFFFFF'><center><font size='-2'>".$secao."</center></b></td>
				 <td bgcolor='#FFFFFF'><center><font size='-2'>".$desc_tipo_env."</center></b></td>
				 <td bgcolor='#FFFFFF'><center><font size='-2'>".$ferido."</center></b></td>
				 <td bgcolor='#FFFFFF'><center><font size='-2'>".$tipo_ferimento."</center></b></td>
				 <td bgcolor='#FFFFFF'><center><font size='-2'>".$hospital."</center></b></td>";							 							
				 
		   print "</tr>";

	 }
	 print "</table>";

	
}



?>
