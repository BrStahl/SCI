<?php
session_name("covre_ti");
session_start();

include("../../SCA/includes/conect_sqlserver.php");


$responsavel_id = $_POST["responsavel_id"];

$query = "select pa.id, nome, usuario, coordenador_ppae, area_qualidade, area_rh, area_qualidade_sts, area_ti, sac, 		
		  somente_leitura, area_id_leitura, coordenador_ppae_sts
			from permissoes_acidente pa (nolock)
			join usuario (nolock) on
				usuario.id = pa.usuario_id
			where pa.id = $responsavel_id";
//print $query;
$result = odbc_exec($conSQL, $query) ;


$registro = "1|".odbc_result($result, 1)."|".odbc_result($result, 2)."|".odbc_result($result, 3)."|".odbc_result($result, 4)."|".odbc_result($result, 5)."|".odbc_result($result, 6)."|".odbc_result($result, 7)."|".odbc_result($result, 8)."|".odbc_result($result, 9)."|".odbc_result($result, 10)."|".odbc_result($result, 11)."|".odbc_result($result, 12)."|";

print $registro;



?>

