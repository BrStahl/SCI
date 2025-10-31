<?php
session_name("covre_ti");
session_start();


require("../SCA/includes/page_func.php");
include("../SCA/includes/conect_sqlserver.php");

$localItem = "../registro_acidentes/tipo_acidente.php";
$logado    = $_SESSION["usuario_logado"];
$acesso	   = valida_acesso($conSQL, $localItem, $logado);
//$acesso = "permitido";

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


	$tipo_acidente_id	= $_POST["tipo_acidente_id"];
	$tipo_acidente 		= $_POST["tipo_acidente"];	
	
	
	if($fechar != "")
	{
		print"
			<script language='javascript'>
				open(location, '_self').close();
			</script>
		";
	}
	
	if($novo != "")
	{
		print"
		<script language='javascript'>
			window.location.href='tipo_acidente.php';
		</script>
		";
	}
	
	if ($gravar != '')
	{
	
	
		if ($tipo_acidente == '')
			print "<script type='text/javascript'> alert(unescape('Favor preencher o tipo de acidente'));</script>";	
		else
			if ($tipo_acidente_id == '')
			{
				$query = "insert into tipo_acidente (tipo_acidente, status_id) values ('$tipo_acidente', 'a')";
				//print $query;
				odbc_exec($conSQL, $query) or die(odbc_errormsg($conSQL)."<br>Erro ao inserir o tipo de acidente<br>");
											
				$query = "SELECT @@IDENTITY AS Ident";
				$result = odbc_exec($conSQL, $query) or die(odbc_errormsg($conSQL)."<br>Erro ao selecionar tipo_acidente_id");
				$tipo_acidente_id = odbc_result($result, 1);							
			}
			else
			{
				$query = "update tipo_acidente 
						  set tipo_acidente = '$tipo_acidente'
						  where id = $tipo_acidente_id";
				//print $query;
				odbc_exec($conSQL, $query) or die(odbc_errormsg($conSQL)."<br>Erro ao atualizar tipo_acidente_id<br>");							
				
			}
		
	}//fim gravar

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<link href="../SCA/includes/estilo.css" rel="stylesheet" type="text/css">

<script type="text/javascript" src="../SCA/includes/js/jquery-autocomplete/lib/jquery.js"></script>
<script type="text/javascript" src="../SCA/includes/js/jquery-autocomplete/lib/jquery.bgiframe.min.js"></script>
<script type="text/javascript" src="../SCA/includes/js/jquery-autocomplete/lib/jquery.ajaxQueue.js"></script>
<script type="text/javascript" src="../SCA/includes/js/jquery-autocomplete/lib/thickbox-compressed.js"></script>
<script type="text/javascript" src="../SCA/includes/js/jquery-autocomplete/jquery.autocomplete.js"></script>

<script type="text/javascript" src="../SCA/includes/calendario/_scripts/jquery.click-calendario-1.0-min.js"></script>		
<script type="text/javascript" src="../SCA/includes/calendario/_scripts/exemplo-calendario.js"></script>

<link href="../SCA/includes/calendario/_style/jquery.click-calendario-1.0.css" rel="stylesheet" type="text/css"/>

<link rel="stylesheet" type="text/css" href="../SCA/includes/js/jquery-autocomplete/jquery.autocomplete.css"/>
<link rel="stylesheet" type="text/css" href="../SCA/includes/js/jquery-autocomplete/lib/thickbox.css"/>  

<style type="text/css">
fieldset {padding: 22px 17px 12px 17px; position: relative; margin: 12px 0 34px 0;}
</style>


<script type="text/javascript">
function busca_dados_tipo_acidente(codigo){
	
	if (codigo != "")
	{
		//alert(elmnt.value);
		$.ajax({type: "POST",		
			url: "includes/busca_dados_tipo_acidente.php",
			data: "codigo="+codigo,
			success: function(msg){  //pega o retorno da pagina chamada
				//alert(msg);
				var dados = msg.split("|");
				document.form1.tipo_acidente_id.value 	= dados[1];			
				document.form1.tipo_acidente.value 		= dados[2];			
				
				habilita_botao('excluir');
			}
					
		});
	}//if
}
</script>

<script language="javascript">
function exclui_tipo_acidente(elmnt){	
	if (confirm(unescape("Deseja realmente excluir o tipo de acidente?")))
	{
			//alert("entrou na exclusão");
			if (elmnt != ""){
				$.ajax({type: "POST",//define o metódo de passagem de parametros
					url: "includes/exclui_tipo_acidente.php", //chama uma pagina
					data: "tipo_acidente_id="+elmnt, //passa os parametros, se necessário
					success: function(msg){  //pega o retorno da pagina chamada
						//alert(msg);
						
						if(msg.indexOf("erro") == -1)
						{
							alert(unescape("Tipo de acidente exclu%EDdo com sucesso"));
							window.location.href='tipo_acidente.php';
						}
						else
							alert(unescape("Tipo de acidente n%E3o exclu%EDdo"));
						
					}
				});
			}
		
	}
	
}
</script>


</head>
<div id="fundo" style="display:none">&nbsp;</div>
<body>
  <form action="" name="form1" method="post" enctype="multipart/form-data" style="width:600px" display >
  <fieldset> 
    <legend>Filtro Ocorr&ecirc;ncias / Despesas</legend>
<div id="campos" style="display:none">
<table width="550" border="0" align="center">
    <tr>
        <td width="22%" height="23">
        <font size="-1"><strong>C&oacute;digo:</strong></font><td width="78%">
        <input name="tipo_acidente_id" type="text" id="tipo_acidente_id" value="<?PHP print $tipo_acidente_id ?>" size="4" maxlength="8" align="center" 
        readonly="readonly" style="background: #DCDCDC"/>
        </center></td>
       
    </tr>
    <tr>
      <td><font size="-1"><strong>Tipo Acidente:</strong></font></td>
      <td><input name="tipo_acidente" type="text" id="tipo_acidente" value="<?PHP print $tipo_acidente ?>" size="50" maxlength="200"/></td>
    </tr>
</table>

 	<p>&nbsp;</p>
 	<table width="550" border="0" align="center">        
        <tr>
          <td class="txt_home">
            <div align="center">
              <input name="novo" type="submit" class="botao_site" value=" Novo " id="novo" /> 
              <input name="gravar" type="submit" class="botao_site" value=" Gravar " id="gravar" />
              <input name="excluir" type="button" class="botao_site" value=" Excluir " id="excluir" disabled="disabled"
              onclick="javascript:exclui_tipo_acidente(document.form1.tipo_acidente_id.value)"/>
			  <input name="fechar" type="submit" class="botao_site" value=" Fechar " id="fechar" />

          </div></td>
        </tr>
        <tr>
          <td class="txt_home">&nbsp;</td>
        </tr>
      </table>
</div>             
    <p>
      <?php

		$query = "select id, tipo_acidente collate sql_latin1_general_cp1251_ci_as, tela_secao
				  from tipo_acidente
				  where status_id = 'a'";
		$result = odbc_exec($conSQL, $query);           

		print"<table width='550' border='1'>
			   <tr>
				 <td bgcolor='#CCCCCC'><b><center><font size='-1'>C&oacute;digo</center></b></td>
				 <td bgcolor='#CCCCCC'><b><center><font size='-1'>Filtro Ocorr&ecirc;ncia / Despesa</center></b></td>
				 <td bgcolor='#CCCCCC'><b><center><font size='-1'>Tipo Despesa</center></b></td>				 
			   </tr>";

			 while(odbc_fetch_row($result))
			   {
				   $codigo		 	= odbc_result($result,1);
				   $tipo_acidente_p	= odbc_result($result,2);
				   $tela_secao		= odbc_result($result,3);
				   
				print"
				   <tr onmouseover=this.bgColor='#89BFF0' onmouseout=this.bgColor=''>";
				   

		   		print "<td bgcolor='#FFFFFF' width='70'><center><font size='-1'>".$codigo."</center></b></td>";
				
				if ($tela_secao != '')
					print "<td bgcolor='#FFFFFF'><center><a href=javascript:pagina('secao_filtro.php?cod=".$codigo."','800','500','Secao')><font size='-1'><u>".$tipo_acidente_p."</u></center></b></a></td>";
				else
					print "<td bgcolor='#FFFFFF'><center><font size='-1'>".$tipo_acidente_p."</center></b></td>";				
				
				print "
					   <td bgcolor='#FFFFFF' width='110'><center>
					   	<a href=javascript:pagina('acidente_tipo_despesa.php?cod=".$codigo."','800','400','Tipo_despesa')>
					   <img src='../SCA/images/sifrao.png' style='border:none' width='15' height='20'></center></b></a></td>				   
				   </tr>";            
			   } 
		print"</table>";	

	?>
    </p>
 	<table width="450" border="0" align="center">        
        <tr>
          <td class="txt_home">&nbsp;</td>
        </tr>
        <tr>
          <td class="txt_home">
            <div align="center">
              <input name="fechar" type="submit" class="botao_site" value=" Fechar " id="fechar" />

          </div></td>
        </tr>
      </table>    
  </fieldset>
  </form>

</body>
</html>  
<?php

	if ($tipo_acidente_id != '')
		print "<script language='javascript'>habilita_botao('excluir')</script>";			

}
	
?>