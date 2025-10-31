<?php
session_name("covre_ti");
session_start();

require("../SCA/includes/page_func.php");
include("../SCA/includes/conect_sqlserver.php");
require("../SCA/includes/phpmailer/class.phpmailer.php");

$localItem = "../registro_acidentes/emails_automaticos.php";
$logado    = $_SESSION["usuario_logado"];
//$acesso	   = valida_acesso($conSQL, $localItem, $logado);
$acesso = "permitido";

if($acesso <> "permitido"){
    grava_acesso($conSQL, $localItem, $logado, 2, $vObservacao);

    print "
        <script language = 'JavaScript'>
           alert('Acesso negado para esta página');
           window.location='centro.php';
		</script>
    ";
}//elseif
else{
	 grava_acesso($conSQL, $localItem, $logado, 1, $vObservacao);


	//EMAILS PARA RESPONSAVEL DA AREA COM LANCAMENTOS DE DESPESAS PENDENTES (toda segunda-feira)
	$query = "select distinct usuario.email, lda.ACIDENTE_ID, VF.placa
				from LANCAMENTO_DESPESAS_ACIDENTE lda with (nolock)
				join tipo_despesa_acidente tda with (nolock) on
					tda.id = lda.TIPO_DESPESA_ID
				join area_responsavel_acidente ara with (nolock) on
					ara.id = lda.AREA_ID
				join usuario with (nolock) on
					(usuario.id = ara.responsavel_id) or (usuario.id = ara.sub_1_id) or (usuario.id = ara.sub_2_id)
				left join veiculos_acidente VA with (nolock) on
					VA.acidente_id = lda.ACIDENTE_ID
					and VA.status_id = 'a'
				left join CARGOSOL..VEICULO_FORNECEDOR VF with (nolock) on
					VF.VEICULO_FORNECEDOR_ID = VA.veiculo_fornecedor_id		
				join registro_acidente ra with (nolock) on
					ra.acidente_id = lda.ACIDENTE_ID
					and ra.status_id NOT IN ('i','f')								
				where STATUS_DESPESA_ID = 2
				and (DATEPART(DW,getdate())) = 2
				and lda.STATUS_ID = 'a'";
	//print $query;
	$result = odbc_exec($conSQL, $query);
	
				  
	 while(odbc_fetch_row($result))
	 {	
	 
		$email_destino	= odbc_result($result, 1);
		$acidente_id	= odbc_result($result, 2);
		
		
		//BUSCA AS PLACAS DOS REGISTROS
		$query1 = "select vf.placa
					from veiculos_acidente va with (nolock)
					join CARGOSOL..VEICULO_FORNECEDOR VF with (nolock) on
						VF.VEICULO_FORNECEDOR_ID = VA.veiculo_fornecedor_id		
					where acidente_id = $acidente_id
					and va.status_id = 'a'";
		//print $query1;
		$result1 = odbc_exec($conSQL, $query1);
		
		$placas = '';
		$conta_placa = 1;			  
		while(odbc_fetch_row($result1))
		{			
			$placa			= odbc_result($result1, 1);
			
			if ($conta_placa == 1)			
				$placas = $placa;
			else
				$placas .= ', '.$placa;
			
			$conta_placa++;
			
		}
		
		//$email_destino	= 'vinicius.figueiredo@covre.com.br';
		
		$enviou = enviar_email("helpdesk@covre.com.br", "SCA - Registro de Acidentes", "$email_destino", "Acidente: $acidente_id - Despesas Pendentes",
		"Email autom&aacute;tico
		<br><br>O acidente n&#176; $acidente_id possui despesas pendentes sob a sua responsabilidade.
		<br><br>Placa(s): $placas
		<br><br>Favor verificar.
		<br><br><b>SCA - Registro de Acidentes</b>");
		
		if ($enviou == 1)
		{
			$query2 = "insert into log_email_acidente (acidente_id, data_envio, motivo, email) 
						values ($acidente_id, getdate(), 'despesas pendentes','$email_destino')";
			//print $query2;
			odbc_exec($conSQL, $query2);
		}

	 }//fim while
	


	//EMAILS PARA QUALIDADE COM LANCAMENTO DE DESPESAS PENDENTES COM O PRAZO VENCIDO
	$query = "select distinct LANCAMENTO_DESPESAS_ACIDENTE.ACIDENTE_ID
				from LANCAMENTO_DESPESAS_ACIDENTE with (nolock)
				left join veiculos_acidente VA with (nolock) on
					VA.acidente_id = LANCAMENTO_DESPESAS_ACIDENTE.ACIDENTE_ID
					and VA.status_id = 'a'
				join registro_acidente ra with (nolock) on
					ra.acidente_id = LANCAMENTO_DESPESAS_ACIDENTE.ACIDENTE_ID
					and ra.status_id NOT IN ('i','f')					
				where STATUS_DESPESA_ID = 2
				AND LANCAMENTO_DESPESAS_ACIDENTE.STATUS_ID = 'a'
				and PRAZO < CONVERT(VARCHAR(10), GetDate(), 120)
				and LANCAMENTO_DESPESAS_ACIDENTE.AREA_ID > 0
				";
	//print $query;
	$result = odbc_exec($conSQL, $query);
	
				  
	while(odbc_fetch_row($result))
	{	
	 	$acidente_id = odbc_result($result, 1);	


		//BUSCA AS PLACAS DOS REGISTROS
		$query1 = "select vf.placa
					from veiculos_acidente va with (nolock)
					join CARGOSOL..VEICULO_FORNECEDOR VF with (nolock) on
						VF.VEICULO_FORNECEDOR_ID = VA.veiculo_fornecedor_id		
					where acidente_id = $acidente_id
					and va.status_id = 'a'";
		//print $query1;
		$result1 = odbc_exec($conSQL, $query1);
		
		$placas = '';
		$conta_placa = 1;			  
		while(odbc_fetch_row($result1))
		{			
			$placa	= odbc_result($result1, 1);
			
			if ($conta_placa == 1)			
				$placas = $placa;
			else
				$placas .= ', '.$placa;
			
			$conta_placa++;
			
		}


		//enviando email para a qualidade
		$query1 = "select nome, email
					from permissoes_acidente pa with (nolock)
					join usuario with (nolock) on
						usuario.id = pa.usuario_id
						and usuario.status = 'a'
					where area_qualidade = 'S'";	
		//print $query1;					
		$result1 = odbc_exec($conSQL, $query1);
		
		while(odbc_fetch_array($result1))
		{
			$nome_destino = odbc_result($result1, 1);
			$email_destino = odbc_result($result1, 2);	
	
			
			$enviou = enviar_email("helpdesk@covre.com.br", "SCA - Registro de Acidentes", "$email_destino", "Acidente: $acidente_id - Despesas pendentes fora do prazo",
			"Email autom&aacute;tico
			<br><br>O acidente n&#176; $acidente_id possui despesas pendentes cujo prazo já venceu.
			<br><br>Placa(s): $placas
			<br><br>Favor verificar.
			<br><br><b>SCA - Registro de Acidentes</b>");
			
			if ($enviou == 1)
			{
				$query2 = "insert into log_email_acidente (acidente_id, data_envio, motivo, email) 
							values ($acidente_id, getdate(), 'prazo vencido','$email_destino')";
				//print $query2;
				odbc_exec($conSQL, $query2);
			}

		}
	

	}//fim while




}//else
?>
