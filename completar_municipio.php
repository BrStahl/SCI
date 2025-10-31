<?
include("../SCA/includes/conect_sqlserver.php");



$q=strtolower ($_GET["q"]);

$query = "select municipio+'/'+uf
		  From cargosol..municipio
		  Where tab_status_id = '1' and municipio like '$q%'
		  ORDER BY municipio";

$result = odbc_exec($conSQL, $query) ;


while(odbc_fetch_row($result))
{
    //if (srtpos(strtolower($reg['nom_lista']),$q !== false){
	// echo $result["nome"]."|".$reg["nome"]."\n";
	print odbc_result($result,1)."\n";
}

?>
