<?php
session_name("covre_ti");
session_start();

include("../../SCA/includes/conect_sqlserver.php");


$nome = $_POST["nome"];
$corpore = $_POST["corpore"];

if ($corpore == '1') {
	$query1 = "select CODIGO
				from CORPORE..PPESSOA (NOLOCK)
				WHERE PPESSOA.NOME = '$nome'";
	//print $query1;
	$result1 = odbc_exec($conSQL, $query1);

	$registro1 = odbc_result($result1, 1);

	if ($registro1 != '')
		print "2|" . $registro1;
	else
		print "3|invalido";
	exit();
}

$query = "select PESSOA_ID
			from CARGOSOL..PESSOA (NOLOCK)
			WHERE PESSOA.nome_fantasia = '$nome'
			and Tab_Status_Id <> 2";
//print $query;
$result = odbc_exec($conSQL, $query);

$registro = odbc_result($result, 1);

if ($registro != '')
	print "1|" . $registro;
else {

	$query1 = "select CODIGO
				from CORPORE..PPESSOA (NOLOCK)
				WHERE PPESSOA.NOME = '$nome'";
	//print $query1;
	$result1 = odbc_exec($conSQL, $query1);

	$registro1 = odbc_result($result1, 1);

	if ($registro1 != '')
		print "2|" . $registro1;
	else
		print "3|invalido";
}
