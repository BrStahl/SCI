<?php
session_name("covre_ti");
session_start();

require("../SCA/includes/page_func.php");
include("../SCA/includes/conect_sqlserver.php");

$localItem = "../registro_acidentes/cadastro_tipo_ocorrencia.php";
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

	$tipo_ocorrencia_id	= $_POST["tipo_ocorrencia_id"];
	$descricao 			= $_POST["descricao"];
	
	$descricao 	= utf8_decode($descricao);
	
	
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
			window.location.href='cadastro_tipo_ocorrencia.php';
		</script>
		";
	}
	
	if ($gravar != '')
	{


		//verifica se a referencia já esta cadastrada
		$query = "select id, descricao
				  from tipo_ocorrencia_acidente
				  where descricao = '$descricao'";
		$result = odbc_exec($conSQL, $query) ;
		$tipo_ocorrencia_id_existente = odbc_result($result,1);	
		$tipo_ocorrencia_existente 		= odbc_result($result,2);				
	
	
		if ($descricao == '')
			print "<script type='text/javascript'> alert(unescape('Favor preencher a descri%E7%E3o'));</script>";	
		else
		if (($tipo_ocorrencia_existente != '') && ($tipo_ocorrencia_id != $tipo_ocorrencia_id_existente))
			print "<script type='text/javascript'> alert(unescape('Descri%E7%E3o j%E1 cadastrada'));</script>";	
		else
		{		
			if ($tipo_ocorrencia_id == '')
			{
				$query = "insert into tipo_ocorrencia_acidente (descricao) values ('$descricao')";
				//print $query;
				odbc_exec($conSQL, $query) or die(odbc_errormsg($conSQL)."<br>Erro ao inserir o tipo da ocorrencia<br>");
											
				$query = "SELECT @@IDENTITY AS Ident";
				$result = odbc_exec($conSQL, $query) or die(odbc_errormsg($conSQL)."<br>Erro ao selecionar o tipo da ocorrencia");
				$tipo_ocorrencia_id = odbc_result($result, 1);							
			}
			else
			{
				$query = "update tipo_ocorrencia_acidente 
						  set descricao = '$descricao'
						  where id = $tipo_ocorrencia_id";
				//print $query;
				odbc_exec($conSQL, $query) or die(odbc_errormsg($conSQL)."<br>Erro ao atualizar o tipo da ocorrencia<br>");							
			}

			print"<script language='javascript'>window.location.href='cadastro_tipo_ocorrencia.php';</script>";
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
function busca_tipo_ocorrencia(id){
	//alert(id);	
	if (id != "")
	{
		//alert(elmnt.value);
		$.ajax({type: "POST",		
			url: "includes/busca_tipo_ocorrencia.php",
			data: "id="+id,
			success: function(msg){  //pega o retorno da pagina chamada
				//alert(msg);
				var dados = msg.split("|");
				document.form1.tipo_ocorrencia_id.value 	= dados[1];			
				document.form1.descricao.value 				= dados[2];			
			}
					
		});
	}//if
}
</script>


</head>
<div id="fundo" style="display:none">&nbsp;</div>
<body>
  <form action="" name="form1" method="post" enctype="multipart/form-data" style="width:500px" display >
  <fieldset> 
    <legend>Cadastro - Tipo da Ocorr&ecirc;ncia</legend>
    <table width="450" border="0" align="center">
    <tr>
      <td width="20%"><font size="-1"><strong>Descri&ccedil;&atilde;o:</strong></font></td>
      <td width="80%"><input name="descricao" type="text" id="descricao" value="<?PHP print utf8_encode($descricao) ?>" size="40" maxlength="100"/>
        <input name="tipo_ocorrencia_id" type="hidden" id="tipo_ocorrencia_id" value="<?PHP print $tipo_ocorrencia_id ?>" size="4" maxlength="8" align="center" /></td>
    </tr>

    </table>
 
 	<p>&nbsp;</p>
 	<table width="450" border="0" align="center">        
        <tr>
          <td class="txt_home">
            <div align="center">
              <input name="gravar" type="submit" class="botao_site" value=" Gravar " id="gravar" />
              <input name="fechar" type="submit" class="botao_site" value=" Fechar " id="fechar" />

          </div></td>
        </tr>
        <tr>
          <td class="txt_home">&nbsp;</td>
        </tr>
      </table>
            
    <p>
      <?php

		$query = "select id, descricao
					FROM tipo_ocorrencia_acidente with (nolock)
					where 1 = 1";
		$result = odbc_exec($conSQL, $query);           

		print"<table width='450' border='1'>
			   <tr>
				 <td bgcolor='#CCCCCC'><b><center><font size='-1'>Descri&ccedil;&atilde;o</center></b></td>
			   </tr>";

			 while(odbc_fetch_row($result))
			   {
				   $tipo_ocorrencia_id_p	= odbc_result($result,1);
				   $descricao_p				= odbc_result($result,2);
				   
				   print"
				   <tr onmouseover=this.bgColor='#89BFF0' onmouseout=this.bgColor=''>
				   		<td bgcolor='#FFFFFF'><center>
							<a href='javascript:busca_tipo_ocorrencia($tipo_ocorrencia_id_p)'>
							<font size='-1'>".utf8_encode($descricao_p)."</a></center></b></td>
				   </tr>";            
			   } 
		print"</table>";	

	?>
    </p>
    <p>&nbsp;</p>
  </fieldset>
  </form>

</body>
</html>  
<?php

	

}
	
?>