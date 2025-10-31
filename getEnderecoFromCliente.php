<?
include("../SCA/includes/conect_sqlserver.php");

$cliente = strtolower ($_GET["cliente"]);

$query = "SELECT TOP 1
            ISNULL(ep.Logradouro, NULL),
            ISNULL(ep.Bairro, NULL),
            ISNULL(ep.Complemento, NULL),
            ISNULL(CONCAT(m.Municipio COLLATE Latin1_General_CI_AS, '/', m.UF COLLATE Latin1_General_CI_AS), NULL),
            ISNULL(ep.Municipio_Id, NULL)
          FROM
            CARGOSOL.dbo.ENDERECO_PESSOA ep
          JOIN
            CARGOSOL.dbo.Municipio m
          ON ep.Municipio_Id = m.Municipio_Id
          WHERE ep.Tab_Tipo_Endereco_Id = 4
          AND ep.Tab_Status_Id = 1
          AND  ep.PESSOA_ID IN (SELECT TOP 1 PESSOA_ID FROM CARGOSOL.dbo.PESSOA WHERE nome_fantasia LIKE '$cliente')";

$result = odbc_exec($conSQL, $query) ;
if(!$result){
  $endereco['logradouro'] = "";
  $endereco['bairro'] = "";
  $endereco['ponto_referencia'] = "";
  $endereco['municipio_uf'] = "";
  $endereco['municipio_id'] = "";
  echo json_encode($endereco);
  return ;
}

$endereco = array();
if(odbc_result($result,1))
  $endereco['logradouro'] = odbc_result($result,1);

if(odbc_result($result,2))
  $endereco['bairro'] = odbc_result($result,2);

if(odbc_result($result,3))
  $endereco['ponto_referencia'] = odbc_result($result,3);

if(odbc_result($result,4))
  $endereco['municipio_uf'] = odbc_result($result,4);

if(odbc_result($result,5))
  $endereco['municipio_id'] = odbc_result($result,5);

echo json_encode($endereco);
?>
