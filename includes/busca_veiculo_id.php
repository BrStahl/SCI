<?php
session_name("covre_ti");
session_start();

include("../../SCA/includes/conect_sqlserver.php");


$placa = $_POST["placa"];


$query = "select VF.VEICULO_FORNECEDOR_ID
from veiculo_pgr VF (NOLOCK)
WHERE VF.PLACA = '$placa'
and VF.STATUS_ID = 'a'";
//print $query;
$result = odbc_exec($conSQL, $query) ;

$registro = odbc_result($result, 1);

if ($registro != '')
	print "1|".$registro;
else
	print "2|invalido";





?>

