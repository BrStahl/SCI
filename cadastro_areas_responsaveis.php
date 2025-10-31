<?php
session_name("covre_ti");
session_start();

require("../SCA/includes/page_func.php");
include("../SCA/includes/conect_sqlserver.php");

$localItem = "../registro_acidentes/cadastro_areas_responsaveis.php";
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

	
	$area 				= $_POST["area"];
	$responsavel 		= $_POST["responsavel"];
	$substituto_1 		= $_POST["substituto_1"];
	$substituto_2 		= $_POST["substituto_2"];
	$area_id	 		= $_POST["area_id"];
	$responsavel_id	 	= $_POST["responsavel_id"];
	$sub_1_id	 		= $_POST["sub_1_id"];
	$sub_2_id	 		= $_POST["sub_2_id"];
	
	
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
			window.location.href='cadastro_areas_responsaveis.php';
		</script>
		";
	}
	
	if ($gravar != '')
	{
		if ($substituto_1 == '')
			$sub_1_id = '';
			
		if ($substituto_2 == '')
			$sub_2_id = '';		

		//verifica se o item já esta cadastrado
		$query = "select area
				  from area_responsavel_acidente
				  where area = '$area'
				  and status_id = 'a'";
		$result = odbc_exec($conSQL, $query) ;
		$area_existente = odbc_result($result,1);		
	
	
		if ($area == '')
			print "<script type='text/javascript'> alert(unescape('Favor preencher a %e1rea'));</script>";	
		else
			if ($responsavel == '')
				print "<script type='text/javascript'> alert(unescape('Favor preencher o nome do respons%e1vel'));</script>";
			else
				if ($responsavel_id == '')
					print "<script type='text/javascript'> alert(unescape('Respons%e1vel Inv%e1lido'));</script>";	
				else
					if (($substituto_1 != '') && ($sub_1_id == ''))
						print "<script type='text/javascript'> alert(unescape('Substituto 1 Inv%e1lido'));</script>";	
					else
						if (($substituto_2 != '') && ($sub_2_id == ''))
							print "<script type='text/javascript'> alert(unescape('Substituto 2 Inv%e1lido'));</script>";	
						else
							if (($area == $area_existente) && ($area_id == ''))
								print "<script type='text/javascript'> alert(unescape('%C1rea j%E1 cadastrada'));</script>";				
							else						
								if ($area_id == '')
								{
									$query = "insert into area_responsavel_acidente (area, responsavel_id, sub_1_id, sub_2_id, status_id)
											  values ('$area', $responsavel_id, case when '$sub_1_id' = '' then null else '$sub_1_id' end,
											  case when '$sub_2_id' = '' then null else '$sub_2_id' end, 'a')";
									//print $query;
									odbc_exec($conSQL, $query) or die(odbc_errormsg($conSQL)."<br>Erro ao inserir a area<br>");
																
									$query = "SELECT @@IDENTITY AS Ident";
									$result = odbc_exec($conSQL, $query) or die(odbc_errormsg($conSQL)."<br>Erro ao selecionar o area_id");
									$area_id = odbc_result($result, 1);							
								}
								else
								{
									$query = "update area_responsavel_acidente 
											  set area = '$area', responsavel_id = $responsavel_id, 
											  sub_1_id = case when '$sub_1_id' = '' then null else '$sub_1_id' end, 
											  sub_2_id = case when '$sub_2_id' = '' then null else '$sub_2_id' end
											  where id = $area_id";
									//print $query;
									odbc_exec($conSQL, $query) or die(odbc_errormsg($conSQL)."<br>Erro ao atualizar a area<br>");							
									
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
 	$(document).ready(function(){
		$("#responsavel").autocomplete("completar_usuario.php", {
			width:600,
			selectFirst: false
		});
		//document.form1.observacao_multa_inadequada.focus();
	});
</script>

<script type="text/javascript">
 	$(document).ready(function(){
		$("#substituto_1").autocomplete("completar_usuario.php", {
			width:600,
			selectFirst: false
		});
		//document.form1.observacao_multa_inadequada.focus();
	});
</script>


<script type="text/javascript">
 	$(document).ready(function(){
		$("#substituto_2").autocomplete("completar_usuario.php", {
			width:600,
			selectFirst: false
		});
		//document.form1.observacao_multa_inadequada.focus();
	});
</script>

<script type="text/javascript">
function busca_usuario_id(elmnt, numero){
	
	if (elmnt.value != "")
	{
		//alert(elmnt.value);
		$.ajax({type: "POST",		
			url: "includes/busca_usuario_id.php",
			data: "nome="+elmnt.value,
			success: function(msg){  //pega o retorno da pagina chamada
				//alert(msg);
				
				var dados = msg.split("|");
				
				if(dados[1].indexOf("invalido") == -1)
				{
				
					if (numero == 1)
						document.form1.responsavel_id.value = dados[1];
					else
						if (numero == 2)
							document.form1.sub_1_id.value = dados[1];
						else
							document.form1.sub_2_id.value = dados[1];
				}
				else
				{
					if (numero == 1)
						document.form1.responsavel_id.value = '';
					else
						if (numero == 2)
							document.form1.sub_1_id.value = '';
						else
							document.form1.sub_2_id.value = '';
				}				
			}
					
		});
	}//if
}
</script>

<script type="text/javascript">
function busca_dados(codigo){
	
	if (codigo != "")
	{
		//alert(elmnt.value);
		$.ajax({type: "POST",		
			url: "includes/busca_dados.php",
			data: "codigo="+codigo,
			success: function(msg){  //pega o retorno da pagina chamada
				//alert(msg);
				var dados = msg.split("|");
				document.form1.area_id.value 		= dados[1];			
				document.form1.area.value 			= dados[2];			
				document.form1.responsavel.value 	= dados[3];			
				document.form1.responsavel_id.value = dados[4];			
				document.form1.substituto_1.value 	= dados[5];			
				document.form1.sub_1_id.value		= dados[6];			
				document.form1.substituto_2.value 	= dados[7];			
				document.form1.sub_2_id.value		= dados[8];	
				
				habilita_botao('excluir');																						
			}
					
		});
	}//if
}
</script>

<script language="javascript">
function exclui_area(elmnt){	
	if (confirm(unescape("Deseja realmente excluir a %E1rea?")))
	{
			//alert("entrou na exclusão");
			if (elmnt != ""){
				$.ajax({type: "POST",//define o metódo de passagem de parametros
					url: "includes/exclui_area.php", //chama uma pagina
					data: "area_id="+elmnt, //passa os parametros, se necessário
					success: function(msg){  //pega o retorno da pagina chamada
						//alert(msg);
						
						if(msg.indexOf("erro") == -1)
						{
							alert(unescape("%C1rea exclu%EDda com sucesso"));
							window.location.href='cadastro_areas_responsaveis.php';
						}
						else
							alert(unescape("%C1rea n%E3o exclu%EDda"));
						
						
				
					}
				});
			}
		
	}
	
}
</script>

</head>
<div id="fundo" style="display:none">&nbsp;</div>
<body>
  <form action="" name="form1" method="post" enctype="multipart/form-data" style="width:500px" display >
  <fieldset> 
    <legend>Cadastro - &Aacute;reas e Respons&aacute;veis</legend>
    <table width="450" border="0" align="center">
    <tr>
        <td width="23%" height="23">
        <font size="-1"><strong>C&oacute;digo:</strong></font><td width="77%">
        <input name="area_id" type="text" id="area_id" value="<?PHP print $area_id ?>" size="4" maxlength="8" align="center" readonly="readonly" style="background: #DCDCDC"/>
        </center></td>
       
    </tr>
    <tr>
       <td><font size="-1"><strong>&Aacute;rea:</strong></font></td>
       <td><input name="area" type="text" id="area" value="<?PHP print $area ?>" size="20" maxlength="200"/>
        <input name="responsavel_id" type="hidden" id="responsavel_id" value="<?PHP print $responsavel_id ?>" size="3" align="center"/>
        <input name="sub_1_id" type="hidden" id="sub_1_id" value="<?PHP print $sub_1_id ?>" size="3" align="center"/>
        <input name="sub_2_id" type="hidden" id="sub_2_id" value="<?PHP print $sub_2_id ?>" size="3" align="center"/></td>
    </tr>
    <tr>
       <td height="22"><font size="-1"><strong>Respons&aacute;vel: </strong></font></td>
       <td><input name="responsavel" type="text" id="responsavel" value="<?PHP print $responsavel ?>" size="50" 
       style="background: #FFFACD" onblur="javasctipt: busca_usuario_id(this, 1)"/></td>
      </tr>
  <tr>
       <td height="22"><font size="-1"><strong>Substituto 1: </strong></font></td>
       <td><input name="substituto_1" type="text" id="substituto_1" value="<?PHP print $substituto_1 ?>" size="50" 
       style="background: #FFFACD" onblur="javasctipt: busca_usuario_id(this, 2)"/></td>
       </tr>
 <tr>
       <td height="22"><font size="-1"><strong>Substituto 2:</strong></font></td>
       <td><input name="substituto_2" type="text" id="substituto_2" value="<?PHP print $substituto_2 ?>" size="50"
       style="background: #FFFACD" onblur="javasctipt: busca_usuario_id(this, 3)"/></td>
      </tr>



    </table>
 
 	<p>&nbsp;</p>
 	<table width="450" border="0" align="center">        
        <tr>
          <td class="txt_home">
            <div align="center">
              <input name="novo" type="submit" class="botao_site" value=" Novo " id="novo" /> 
              <input name="gravar" type="submit" class="botao_site" value=" Gravar " id="gravar" />
              <input name="excluir" type="button" class="botao_site" value=" Excluir " id="excluir" disabled="disabled"
              onclick="javascript:exclui_area(document.form1.area_id.value)"/>
              <input name="fechar" type="submit" class="botao_site" value=" Fechar " id="fechar" />

          </div></td>
        </tr>
        <tr>
          <td class="txt_home">&nbsp;</td>
        </tr>
      </table>
            
    <p>
      <?php

		$query = "select ara.id, ara.area, usuario.nome
					FROM area_responsavel_acidente ara with (nolock)
					join usuario with (nolock) on
						usuario.id = ara.responsavel_id
					where ara.status_id = 'a'";
		$result = odbc_exec($conSQL, $query);           

		print"<table width='450' border='1'>
			   <tr>
				 <td bgcolor='#CCCCCC'><b><center><font size='-1'>C&oacute;digo</center></b></td>
				 <td bgcolor='#CCCCCC'><b><center><font size='-1'>&Aacute;rea</center></b></td>
				 <td bgcolor='#CCCCCC'><b><center><font size='-1'>Respons&aacute;vel</center></b></td>
			   </tr>";

			 while(odbc_fetch_row($result))
			   {
				   $codigo		 	= odbc_result($result,1);
				   $area_p 			= odbc_result($result,2);
				   $responsavel_p 	= odbc_result($result,3);				   
				   
				print"
				   <tr onmouseover=this.bgColor='#89BFF0' onmouseout=this.bgColor=''>";
				   

		   		print "<td bgcolor='#FFFFFF'><center>
					 	<a href='javascript:busca_dados($codigo)'>
						<font size='-1'>".$codigo."</a></center></b>
					   </td>
					   <td bgcolor='#FFFFFF'><center><font size='-1'>".$area_p."</center></b></td>
					   <td bgcolor='#FFFFFF'><center><font size='-1'>".$responsavel_p."</center></b></td>
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

	if ($area_id != '')
		print "<script language='javascript'>habilita_botao('excluir')</script>";			

}
	
?>