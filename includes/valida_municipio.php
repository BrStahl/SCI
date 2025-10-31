<?php
session_name("covre_ti");
session_start();

include("../../SCA/includes/conect_sqlserver.php");


$municipio = $_POST["municipio"];


$query = "select MUNICIPIO_ID
		  from CARGOSOL..municipio with (nolock)
		  where municipio+'/'+uf = '$municipio'";
//print $query;
$result = odbc_exec($conSQL, $query) ;

$registro = odbc_result($result, 1);

if ($registro != '')
	print "1|".$registro;
else
	print "2|invalido";





?>

