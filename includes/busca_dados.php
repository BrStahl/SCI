<?php
session_name("covre_ti");
session_start();

include("../../SCA/includes/conect_sqlserver.php");


$codigo = $_POST["codigo"];


$query = "select ara.id, ara.area, usuario.nome, usuario.id, sub1.nome, ltrim(sub1.id), sub2.nome, ltrim(sub2.id)
			FROM area_responsavel_acidente ara with (nolock)
			join usuario with (nolock) on
				usuario.id = ara.responsavel_id
			left join usuario sub1 with (nolock) on
				sub1.id = ara.sub_1_id
			left join usuario sub2 with (nolock) on
				sub2.id = ara.sub_2_id	
			where ara.id = $codigo";
//print $query;
$result = odbc_exec($conSQL, $query);
$registro = "1|".odbc_result($result, 1)."|".odbc_result($result, 2)."|".odbc_result($result, 3)."|".odbc_result($result, 4)."|".odbc_result($result, 5)."|".odbc_result($result, 6)."|".odbc_result($result, 7)."|".odbc_result($result, 8)."|";

print $registro;

?>

