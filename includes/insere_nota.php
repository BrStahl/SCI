<?php
session_name("covre_ti");
session_start();

include("../../SCA/includes/conect_sqlserver.php");

$logado = $_SESSION["usuario_logado"];

$veiculo_fornecedor_id	= $_POST["veiculo_fornecedor_id"];
$acidente_id			= $_POST["acidente_id"];
$cpf					= $_POST["cpf"];


if ($acidente_id != '')
{
	//verifica se a NF esta cadastrada
	$query = "select id
			  from notas_acidente
			  where acidente_id = $acidente_id
			  and nota_fiscal = $nota_fiscal
			  and status_id = 'a'";
	//print $query;
	$result = odbc_exec($conSQL, $query) or die ('erro1 ao consultar a NF');	
	$id = odbc_result($result,1);
	
	if ($id == '')
	{
		$query = "insert into notas_acidente (nota_fiscal, acidente_id, status_id) 
				  values ($nota_fiscal, $acidente_id, 'a')";
		//print $query;
		odbc_exec($conSQL, $query) or die ('erro1 ao inserir');

		print "ok";
	}
	else
		print "Nota Fiscal já inserida";
}
else
{
	//insere na tabela temporaria
	$query = "insert into notas_acidente_temp (nota_fiscal, cpf) 
			  values ($nota_fiscal, '$cpf')";
	//print $query;
	odbc_exec($conSQL, $query) or die ('erro2 ao inserir');		
	

	$query = "SELECT NAT.NOTA_FISCAL
				FROM NOTAS_ACIDENTE_TEMP NAT WITH (NOLOCK)
				where NAT.CPF = '$cpf'";
	//print $query;
	$result = odbc_exec($conSQL, $query);     


	print "<table width='300' border='1' >
	  <tr>
		<td bgcolor='#CCCCCC'><strong><font size='-2'><CENTER>NOTA FISCAL</font></strong></div></td>
	  </tr>";
	  
	  
	  
	 while(odbc_fetch_row($result))
	 {
	
		   $nota_fiscal	 = odbc_result($result,1);

		   print "<tr>
				 	<td bgcolor='#FFFFFF'><center><font size='-2'>".$nota_fiscal."</center></b></td>
				 </tr>";

	 }
	 print "</table>";

	
}
?>
