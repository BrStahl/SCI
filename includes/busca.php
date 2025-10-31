






<?php
session_name("covre_ti");
session_start();

include("../../SCA/includes/conect_sqlserver.php");


$codigo = $_POST["codigo"];


$query = "select tipo.id, tipo_despesa, referencia_despesa_id, area_responsavel_id, base, prazo, reembolso, isnull(area.area, 'Todas as &aacute;reas')
		  from tipo_despesa_acidente tipo with (nolock)
		  left join area_responsavel_acidente area with (nolock) on
			area.id = tipo.area_responsavel_id
		  where tipo.id = $codigo";
//print $query;
$result = odbc_exec($conSQL, $query);

$combo = "<option value='" . odbc_result($result, 4) . "'>" . odbc_result($result, 8) . "</option>";

$registro = "1|" . odbc_result($result, 1) . "|" . odbc_result($result, 2) . "|" . odbc_result($result, 3) . "|" . $combo . "|" . odbc_result($result, 5) . "|" . odbc_result($result, 6) . "|" . odbc_result($result, 7) . "|";

print $registro;

?>

