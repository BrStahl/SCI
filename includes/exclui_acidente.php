<?php
session_name("covre_ti");
session_start();

include("../../SCA/includes/conect_sqlserver.php");


$acidente_id = $_POST["acidente_id"];


	$query = "update registro_acidente
			  set status_id = 'i'
			  Where acidente_id = '$acidente_id'";
	$result = odbc_exec($conSQL, $query) ;
	
	//verifica se o registro foi inativado
	$query = "select status_id
			  from registro_acidente
			  Where acidente_id = '$acidente_id'";
	$result = odbc_exec($conSQL, $query) ;
	$status_id = odbc_result($result,1);
	
	if ($status_id == 'i')
	    print "Acidente excluido com sucesso";
	else
		print "Acidente nao excluido";





?>

