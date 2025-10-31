<?
include("../SCA/includes/conect_sqlserver.php");

$q=strtolower ($_GET["q"]);

$query = "SELECT  top 10
                NOME
            FROM CARGOSOL..PESSOA WITH(NOLOCK)

            LEFT JOIN CARGOSOL..COLABORADOR WITH (NOLOCK) 
            ON COLABORADOR.PESSOA_ID = PESSOA.PESSOA_ID

            WHERE PESSOA.TAB_STATUS_ID <> 2
            AND COLABORADOR.TAB_TIPO_COLABORADOR_ID = 3
            and pessoa.nome_fantasia like '$q%'

            ORDER BY PESSOA.NOME";


$result = odbc_exec($conSQL, $query);

while(odbc_fetch_row($result)){
	print utf8_encode(odbc_result($result,1))."\n";
}

?>
