<?php
session_name("covre_ti");
session_start();

require("../../SCA/includes/page_func.php");
include("../../SCA/includes/conect_sqlserver.php");
require("../../SCA/includes/phpmailer/class.phpmailer.php");

$logado = $_SESSION["usuario_logado"];

$area			= $_POST["area"];
$acidente_id	= $_POST["acidente_id"];


if ($logado != '')
{
	$query = "select id
				from usuario with (nolock)
				where usuario = '$logado'";
	//print $query;
	$result = odbc_exec($conSQL, $query) ;
	$usuario_id = odbc_result($result, 1);	


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



	$query = "insert into despesa_area_acidente (acidente_id, area_id, data_gravacao, usuario_id, status_id) 
			  values ($acidente_id, $area, getdate(), $usuario_id, 'a')";
	//print $query;
	odbc_exec($conSQL, $query) or die ('erro1 ao inserir');
	
	
	//seleciona os responsaveis da area
	$query = "select nome, email, area
				from area_responsavel_acidente ara with (nolock)
				join usuario with (nolock) on
					(usuario.id = ara.responsavel_id) or (usuario.id = ara.sub_1_id) or (usuario.id = ara.sub_2_id)
				where ara.id = $area";
	//print $query;
	$result = odbc_exec($conSQL, $query) ;
	
	while(odbc_fetch_row($result))
	{
		$nome_responsavel 	= odbc_result($result, 1);		
		$email_responsavel 	= odbc_result($result, 2);		
		$descricao_area 	= odbc_result($result, 3);				

	
		//envia email para responsavel da área
		$enviou = enviar_email("helpdesk@covre.com.br", "SCA - Registro de Acidentes", "$email_responsavel", "Despesas de Acidentes - Area: $descricao_area - Acidente: $acidente_id",
		"<!DOCTYPE html PUBLIC '-//W3C//DTD XHTML 1.0 Transitional//EN' 'http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd'>
		<html xmlns='http://www.w3.org/1999/xhtml'>
		<head>
		<meta http-equiv='Content-Type' content='text/html; charset=iso-8859-1' />
		</head>
		<body>
		Sr(a). $nome_responsavel 
		<br><br>A &aacute;rea $descricao_area foi vinculada ao Registro de Acidente $acidente_id.
		<br><br><b>Ve&iacute;culo(s) envolvido(s) no acidente: </b>$placas
		<br><br>Verifique as despesas previstas no sistema.
		<br><br><br><b>SCA - Registro de Acidentes</b>");
	
		if ($enviou == 1)
		{
			$query2 = "insert into log_email_acidente (acidente_id, data_envio, motivo, email) 
						values ($acidente_id, getdate(), 'area_inserida','$email_responsavel')";
			//print $query2;
			odbc_exec($conSQL, $query2);
		}	
		print $enviou;
	}
	
	
	//selecionar os tipos de acidente do registro
	$query = "select 
				substring(acidente_transito+acidente_trabalho+avaria_carga+atraso_entrega, 
				1,LEN(acidente_transito+acidente_trabalho+avaria_carga+atraso_entrega)-1)
				from (
						select 
						case when acidente_transito = 's' then '1,' else '' end acidente_transito, 
						case when acidente_trabalho = 's' then '2,' else '' end acidente_trabalho, 
						case when avaria_carga = 's' then '3,' else '' end avaria_carga, 
						case when atraso_entrega = 's' then '4,' else '' end atraso_entrega
						from registro_acidente
						where acidente_id = $acidente_id
					)dados";
	//print $query;
	$result = odbc_exec($conSQL, $query) or die("Erro ao selecionar os tipos de acidente<br>");
	$tipos_acidente 	= odbc_result($result,1);


	$query = "select distinct tipo_despesa_acidente.id tipo_despesa_id, DATEADD(dd, prazo, getdate()) prazo
				from tipo_despesa_acidente with (nolock)
				join acidente_tipo_despesas atd with (nolock) on
					atd.tipo_despesa_id = tipo_despesa_acidente.id
					and atd.tipo_acidente_id in ($tipos_acidente)
					and atd.tipo_despesa_id not in (select tipo_despesa_id
													 from lancamento_despesas_acidente
													 where acidente_id = $acidente_id
													 and AREA_ID in (0,$area)
													 and status_id = 'a')
				where status_id = 'a'
				and area_responsavel_id  in (0,$area)";
	//print $query;
	$result = odbc_exec($conSQL, $query);    
            
                  
	 while(odbc_fetch_row($result))
	 {
		$tipo_despesa_id 	= odbc_result($result,1);
		$prazo				= odbc_result($result,2);

		//inserindo na table lancamento_despesas_acidente
		$query = "insert into lancamento_despesas_acidente (acidente_id, tipo_despesa_id, status_despesa_id, prazo, data_inclusao, area_id, status_id) 
				  values ($acidente_id, $tipo_despesa_id, 2, '$prazo', getdate(), $area, 'a')";
		//print $query;
		odbc_exec($conSQL, $query) or die ('erro2 ao inserir');	

	 }	
	
}


?>
