<?php
session_name("covre_ti");
session_start();

include("../../SCA/includes/conect_sqlserver.php");


$id = $_POST["id"];


$query = "select id, descricao
			FROM local_ocorrencia_acidente with (nolock)
			where id = $id";
//print $query;
$result = odbc_exec($conSQL, $query);
$registro = "1|".odbc_result($result, 1)."|".utf8_encode(odbc_result($result, 2))."|";

print $registro;

?>

