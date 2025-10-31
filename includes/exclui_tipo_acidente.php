<?php
session_name("covre_ti");
session_start();

include("../../SCA/includes/conect_sqlserver.php");


$tipo_acidente_id = $_POST["tipo_acidente_id"];


$query = "update tipo_acidente
		  set status_id = 'i'
		  where id = $tipo_acidente_id";
//print $query;
$result = odbc_exec($conSQL, $query);


$query = "select status_id
		  from tipo_acidente
		  where id = $tipo_acidente_id";
//print $query;
$result = odbc_exec($conSQL, $query);
$status_id = odbc_result($result, 1);

if ($status_id == 'i')
	print "ok";
else
	print "erro";


?>

