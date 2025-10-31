<?
include("../SCA/includes/conect_sqlserver.php");



$q=strtolower ($_GET["q"]);

$query = "SELECT nome
          FROM cargosol..pessoa with (nolock) 
          join CARGOSOL..COLABORADOR co with (nolock) on
			co.COLABORADOR_ID = pessoa.PESSOA_ID
			and co.tab_status_id in (1,1071,23) --Ativo,Status Funcionário,Demitido
          WHERE nome like '$q%'
		  and pessoa.tab_status_id in (1,1071)
		  
		  UNION

		  SELECT nome COLLATE SQL_Latin1_General_CP1_CI_AS
		  FROM CORPORE..PPESSOA WITH (NOLOCK)
		  WHERE NOME LIKE '$q%'
		  AND NOME NOT IN (SELECT nome COLLATE SQL_Latin1_General_CP1_CI_AI
						  FROM cargosol..pessoa with (nolock) 
						  join CARGOSOL..COLABORADOR co with (nolock) on
							co.COLABORADOR_ID = pessoa.PESSOA_ID
							and co.tab_status_id in (1,1071,23)
						  WHERE nome like '$q%'
						  and pessoa.tab_status_id in (1,1071))
		  ORDER BY NOME";

$result = odbc_exec($conSQL, $query) ;


while(odbc_fetch_row($result))
{
    //if (srtpos(strtolower($reg['nom_lista']),$q !== false){
	// echo $result["nome"]."|".$reg["nome"]."\n";
	print odbc_result($result,1)."|".odbc_result($result,1)."\n";
}

?>
