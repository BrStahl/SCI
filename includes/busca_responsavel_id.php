<?php
session_name("covre_ti");
session_start();

include("../../SCA/includes/conect_sqlserver.php");


$nome = $_POST["nome"];


$query = "SELECT id, rtrim(usuario)
          FROM usuario with (nolock) 
          WHERE nome = '$nome'
          and status = 'a' ";
//print $query;
$result = odbc_exec($conSQL, $query) or die('erro') ;

$registro = "1|".odbc_result($result, 1)."|".odbc_result($result, 2)."|";

print $registro;

?>

