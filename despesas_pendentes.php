<?php
session_name("covre_ti");
session_start();

require("../SCA/includes/page_func.php");
include("../SCA/includes/conect_sqlserver.php");

$localItem = "../registro_acidentes/logs_alteracao.php";
$logado    = $_SESSION["usuario_logado"];
//$acesso	   = valida_acesso($conSQL, $localItem, $logado);
$acesso = "permitido";

if($acesso <> "permitido"){
    grava_acesso($conSQL, $localItem, $logado, 2, $vObservacao);

    print "
        <script language = 'JavaScript'>
           alert('Acesso negado para esta página');
           window.location='centro.php';
		</script>
    ";
}//elseif
else{
	 grava_acesso($conSQL, $localItem, $logado, 1, $vObservacao);

$acidente_id 	= $_GET["acidente_id"];


if($fechar != ""){
		print"
		<script language='javascript'>
			open(location, '_self').close();
		</script>
	";
}
	
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<link href="../SCA/includes/estilo.css" rel="stylesheet" type="text/css">
<style type="text/css">
fieldset { padding: 22px 17px 12px 17px; position: relative; margin: 12px 0 34px 0; }


</style>
</head>
<body>
<form action="" method="post" enctype="multipart/form-data" style="width:1050px">
<fieldset>
<legend>Despesas Pendentes</legend> 

<?php


	 $query = "	select distinct ara.area, tda.tipo_despesa, convert(varchar(10), lda.PRAZO, 103) prazo
				from LANCAMENTO_DESPESAS_ACIDENTE lda with (nolock)
				join registro_acidente ra with (nolock) on
					ra.acidente_id = lda.acidente_id 
					and ra.status_id not in ('i','f')
				join despesa_area_acidente daa with (nolock) on
					daa.area_id = lda.AREA_ID
					and daa.status_id = 'a'
				join area_responsavel_acidente ara with (nolock) on
					ara.id = lda.AREA_ID
				join tipo_despesa_acidente tda with (nolock) on
					tda.id = lda.TIPO_DESPESA_ID
				where lda.status_id = 'a'
				and status_despesa_id = 2
				and lda.area_id > 0
				and lda.ACIDENTE_ID = $acidente_id
				order by ara.area
				 ";
	 //print $query;
	 odbc_exec($conSQL, $query) or die(odbc_errormsg($conSQL)."<br>Erro ao consultar<br>");	 
     
	 $result = odbc_exec($conSQL, $query);           
				
	 print"<table width='1000' border='1'>
	           <tr>
 			     <td bgcolor='#CCCCCC' width='250'><font size='-1'><b><center>&Aacute;rea</center></b></td>
			     <td bgcolor='#CCCCCC' width='650'><font size='-1'><b><center>Descri&ccedil;&atilde;o</center></b></td>
			     <td bgcolor='#CCCCCC' width='100'><font size='-1'><b><center>Prazo</center></b></td>				 
						 				 
			   </tr>";

	         while(odbc_fetch_row($result))
	         {
	        	$area 			= odbc_result($result,1);
	        	$descricao		= odbc_result($result,2);
	        	$prazo			= odbc_result($result,3);	
			
			
			    print"
					 <td bgcolor='#FFFFFF'><center><font size='-2'>".$area."</center></td>
 					 <td bgcolor='#FFFFFF'><center><font size='-2'>".$descricao."</center></td>
 					 <td bgcolor='#FFFFFF'><center><font size='-2'>".$prazo."</center></td>					 
	               </tr>";            
	         } 
		print"</table>";


?>

<table width="878" border="0" align="center">
<tr>
           <td width="739">&nbsp;</td>
      
      </tr>        
        <tr>
          <td class="txt_home"><center><input name="fechar" type="submit" class="botao_site_1" value="fechar" /></center></td>
        </tr>
</table>


 </fieldset>
</form>

</body>
</html>
<?php
}//else
?>
