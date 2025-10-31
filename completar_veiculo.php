<?
include("../SCA/includes/conect_sqlserver.php");



$q=strtolower ($_GET["q"]);

$query = "SELECT placa
FROM veiculo_pgr with (nolock) 
WHERE placa like '$q%'
and status_id ='a'
ORDER BY placa";

$result = odbc_exec($conSQL, $query) ;


while(odbc_fetch_row($result))
{
    //if (srtpos(strtolower($reg['nom_lista']),$q !== false){
	// echo $result["nome"]."|".$reg["nome"]."\n";
	print odbc_result($result,1)."|".odbc_result($result,1)."\n";
}

?>
