<?php
session_name("covre_ti");
session_start();

require("../../SCA/includes/page_func.php");
include("../../SCA/includes/conect_sqlserver.php");
require("../../SCA/includes/phpmailer/class.phpmailer.php");

$logado    = $_SESSION["usuario_logado"];

$acidente_id = $_POST["acidente_id"];

if ($logado != '') {
	//seleciona o usuario
	$query = "select id, nome
			  from usuario
			  Where usuario = '$logado'";
	$result = odbc_exec($conSQL, $query);
	$usuario_id 	= odbc_result($result, 1);
	$nome_usuario 	= odbc_result($result, 2);


	$query = "update analise_ppae
			  set status_id = 'f', data_concl_analise = getdate(), user_concl_analise = $usuario_id
			  Where acidente_id = $acidente_id";
	odbc_exec($conSQL, $query);

	//verifica se o registro foi inativado
	$query = "select status_id
			  from analise_ppae
			  Where acidente_id = $acidente_id";
	$result = odbc_exec($conSQL, $query);
	$status_id = odbc_result($result, 1);

	if ($status_id == 'f') {
		//retorna o status do acidente para pendente
		$query = "update registro_acidente
				  set status_id = 'p'
				  Where acidente_id = $acidente_id";
		odbc_exec($conSQL, $query);


		//enviando email para a qualidade
		$query1 = "select nome, email
						from permissoes_acidente pa with (nolock)
						join usuario with (nolock) on
							usuario.id = pa.usuario_id
							and usuario.status = 'a'
						where area_qualidade = 'S'";
		//print $query1;					
		$result1 = odbc_exec($conSQL, $query1);

		while (odbc_fetch_array($result1)) {
			$nome_destino = odbc_result($result1, 1);
			$email_destino = odbc_result($result1, 2);


			$enviou = enviar_email(
				"helpdesk@covre.com.br",
				"SCA - Registro de Acidentes",
				"$email_destino",
				"Acidente: $acidente_id - Analise PAE Concluida",
				"Email autom&aacute;tico
				<br><br>A an&aacute;lise do Coordenador do PAE Nacional foi conclu&iacute;da para o acidente n&#176; $acidente_id.
				<br><br>An&aacute;lise feita por $nome_usuario
				<br><br><b>SCA - Registro de Acidentes</b>"
			);

			if ($enviou == 1) {
				$query2 = "insert into log_email_acidente (acidente_id, data_envio, motivo, email) 
								values ($acidente_id, getdate(), 'analise ppae','$email_destino')";
				//print $query2;
				odbc_exec($conSQL, $query2);
			}
		}




		//insere no log de alteracao
		$query = "insert into log_alteracao_acidente (acidente_id, data_alteracao, usuario_id, valor_antigo, valor_novo, campo, tela_alteracao)
				  values ($acidente_id, getdate(), $usuario_id, 'An&aacute;lise Aberta', 'An&aacute;lise Conclu&iacute;da', 'Status da An&aacute;lise', 2)";
		//print $query;
		odbc_exec($conSQL, $query);
	}

	print "1|" . $status_id;
}
