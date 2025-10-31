<?php
session_name("covre_ti");
session_start();

include("../../SCA/includes/conect_sqlserver.php");


$codigo = $_POST["codigo"];


$query = "select id, tipo_acidente
		  from tipo_acidente	
		  where id = $codigo";
//print $query;
$result = odbc_exec($conSQL, $query);
$registro = "1|".odbc_result($result, 1)."|".odbc_result($result, 2)."|";

print $registro;

?>

