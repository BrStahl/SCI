<?php
session_name("covre_ti");
session_start();

include("../../SCA/includes/conect_sqlserver.php");


$nome = $_POST["nome"];


$query = "select id
		  From usuario with (nolock)
		  Where nome = '$nome'
		  AND status = 'a'";
//print $query;
$result = odbc_exec($conSQL, $query) ;
$usuario_id = odbc_result($result, 1);

if ($usuario_id != '')
{
	$registro = "1|".$usuario_id."|";
	print $registro;
}
else
{
	$registro = "1|invalido|";
	print $registro;

}



?>

