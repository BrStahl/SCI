<?php
session_name("covre_ti");
session_start();

include("../../SCA/includes/conect_sqlserver.php");

$logado = $_SESSION["usuario_logado"];


$contato_id	= $_POST["contato_id"];


if ($logado != '')
{

		//inativa o registro
		$query = "update parametros_contato_acidente
				  set status_id = 'i',
				  data_hora_ult_gravacao = getdate(),
				  user_ult_gravacao = (select top 1 id from usuario where usuario = '$logado' and status = 'a')
				  where id = $contato_id";
		//print $query;
		odbc_exec($conSQL, $query);	

	
}


?>
