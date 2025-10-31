<?
include("../SCA/includes/conect_sqlserver.php");



$q=strtolower ($_GET["q"]);

$query = "SELECT nome_fantasia NOME
          FROM cargosol..pessoa with (nolock)
          JOIN cargosol..cliente with (nolock) on
			cliente.PESSOA_ID = pessoa.pessoa_id
          WHERE nome_fantasia like '$q%'
		  and pessoa.tab_status_id in (1)
		  ORDER BY NOME";

$result = odbc_exec($conSQL, $query) ;


while(odbc_fetch_row($result))
{
    //if (srtpos(strtolower($reg['nom_lista']),$q !== false){
	// echo $result["nome"]."|".$reg["nome"]."\n";
	print odbc_result($result,1)."\n";
}

?>
