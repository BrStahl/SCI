<?php
session_name("covre_ti");
session_start();

include("../../SCA/includes/conect_sqlserver.php");


$onu = $_POST["onu"];


$query = "SELECT TOP 1
num_onu onu,
CASE WHEN PATINDEX('%.%',classe_risco) > 0
		THEN SUBSTRING(classe_risco, 0,PATINDEX('%.%',classe_risco))
		ELSE CASE WHEN PATINDEX('%,%',classe_risco) > 0
					THEN SUBSTRING(classe_risco, 0,PATINDEX('%,%',classe_risco))
					ELSE classe_risco
			 END
END num_classe_risco,
classe_risco,
nome_aprop_embarque collate SQL_Latin1_General_Cp1251_CS_AS,
grupo_epi.descricao grupo_epi
from dados_planilha_fispq dados_planilha with (nolock)
join grupo_epi with (nolock) on
	grupo_epi.id = dados_planilha.grupo_epi
	and grupo_epi.status_id = 'a'
where dados_planilha.status_id = 'a'
and num_onu = '$onu'";
//print $query;
$result = odbc_exec($conSQL, $query);



print  odbc_result($result, 1) . '|' . odbc_result($result, 2) . '|' . odbc_result($result, 3) . '|' . odbc_result($result, 4) . '|' . odbc_result($result, 5);
