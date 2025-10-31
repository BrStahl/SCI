<?php
session_name("covre_ti");
session_start();

include("../../SCA/includes/conect_sqlserver.php");


$area_id = $_POST["area_id"];


$query = "update area_responsavel_acidente
		  set status_id = 'i'
		  where id = $area_id";
//print $query;
$result = odbc_exec($conSQL, $query);


$query = "select status_id
		  from area_responsavel_acidente
		  where id = $area_id";
//print $query;
$result = odbc_exec($conSQL, $query);
$status_id = odbc_result($result, 1);

if ($status_id == 'i')
	print "ok";
else
	print "erro";


?>

