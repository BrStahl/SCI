<?php
session_name("covre_ti");
session_start();

include("../../SCA/includes/conect_sqlserver.php");


$prox_id = $_POST["prox_id"];


$query = "select acidente_id
			from registro_acidente with (nolock)
			where acidente_id = $prox_id";
//print $query;
$result = odbc_exec($conSQL, $query) ;

$registro = odbc_result($result, 1);

if ($registro != '')
	print "1|ok";
else
	print "2|invalido";





?>

