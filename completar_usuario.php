<?
include("../SCA/includes/conect_sqlserver.php");



$q = strtolower($_GET["q"]);


$query = "SELECT PPESSOA.NOME
				FROM CORPORE..PPESSOA WITH (NOLOCK) 
				LEFT JOIN CORPORE..PFUNC ON PFUNC.CODPESSOA = PPESSOA.CODIGO

				LEFT JOIN CORPORE..VPCOMPL ON VPCOMPL.CODPESSOA = PPESSOA.CODIGO

				JOIN USUARIO ON USUARIO.registro = PFUNC.CHAPA
				AND USUARIO.STATUS = 'A'

				WHERE PPESSOA.NOME LIKE '$q%'
				AND (PFUNC.CODSITUACAO <> 'D' OR VPCOMPL.DTDES IS NULL)";

$result = odbc_exec($conSQL, $query);


while (odbc_fetch_row($result)) {
	//if (srtpos(strtolower($reg['nom_lista']),$q !== false){
	// echo $result["nome"]."|".$reg["nome"]."\n";
	print odbc_result($result, 1) . "|" . odbc_result($result, 1) . "\n";
}
