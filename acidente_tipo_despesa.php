<?php


require("../SCA/includes/page_func.php");
include("../SCA/includes/conect_sqlserver.php");

$localItem = "../registro_acidentes/acidente_tipo_despesa.php";
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


	$tipo_acidente_id	= $_GET["cod"];

	
	$query = "select tipo_acidente collate sql_latin1_general_cp1251_ci_as
			  from tipo_acidente
			  where id = $tipo_acidente_id";
	$result = odbc_exec($conSQL, $query);  
	$tipo_acidente = odbc_result($result,1);	
	
	
	if($fechar != "")
	{
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
function busca_dados_tipo(codigo){
	
	if (codigo != "")
	{
		//alert(elmnt.value);
		$.ajax({type: "POST",		
			url: "includes/busca_dados_tipo.php",
			data: "codigo="+codigo,
			success: function(msg){  //pega o retorno da pagina chamada
				//alert(msg);
				var dados = msg.split("|");
				document.form1.tipo_despesa_id.value 	= dados[1];			
				document.form1.tipo_despesa.value 		= dados[2];			
				document.form1.referencia.value 		= dados[3];			
				document.form1.area_responsavel.value 	= dados[4];			
				document.form1.base.value 				= dados[5];			
				document.form1.prazo.value				= dados[6];		
				
				habilita_botao('excluir');
			}
					
		});
	}//if
}
</script>

<script language="javascript">
function exclui_tipo_despesa(elmnt){	
	if (confirm(unescape("Deseja realmente excluir o tipo de despesa?")))
	{
			//alert("entrou na exclusão");
			if (elmnt != ""){
				$.ajax({type: "POST",//define o metódo de passagem de parametros
					url: "includes/exclui_tipo_despesa.php", //chama uma pagina
					data: "tipo_despesa_id="+elmnt, //passa os parametros, se necessário
					success: function(msg){  //pega o retorno da pagina chamada
						//alert(msg);
						
						if(msg.indexOf("erro") == -1)
						{
							alert(unescape("Tipo de despesa exclu%EDdo com sucesso"));
							window.location.href='tipo_despesa.php';
						}
						else
							alert(unescape("Tipo de despesa n%E3o exclu%EDdo"));
						
						
				
					}
				});
			}
		
	}
	
}
</script>

<script type="text/javascript">
function insere_despesa(elmnt,id,tipo_acidente_id){
	//alert();	
	var valor = "";
	
	if($(elmnt).is(":checked"))
		valor = "S";
	else
		valor = "N";	
		
	//alert(valor);
	
	if (id != '')	
	{	
		$.ajax({type: "POST",//define o metódo de passagem de parametros
			url: "includes/insere_despesa.php", //chama uma pagina
			data: "valor="+valor+"&tipo_despesa_id="+id+"&tipo_acidente_id="+tipo_acidente_id, //parametros
			success: function(msg){  //pega o retorno da pagina chamada
				//alert(msg);
				if(msg.indexOf("Erro") > -1){
					
					//alert(msg);				
				}
			}
		});
	}
}
</script>

</head>
<div id="fundo" style="display:none">&nbsp;</div>
<body>
  <form action="" name="form1" method="post" enctype="multipart/form-data" style="width:1000px" display >
  <fieldset> 
    <legend> Tipos de Despesas</legend>
    <table width="950" border="0" align="center">
    <tr>
        <td width="18%" height="23"><font size="-1"><strong>Tipo de Acidente: </strong></font></td>
        <td width="82%"><font size="-1"><?php print $tipo_acidente ?></font></td>
       
    </tr>
    <tr>
      <td height="23">&nbsp;</td>
      <td>&nbsp;</td>
    </tr>



    </table>
 
 	<p>
 	  <?php

		$query = "SELECT tipo.id, tipo_despesa, referencia.descricao, isnull(area.area, 'Todas as &aacute;reas'), base, prazo,
					CASE WHEN atd.id IS NOT NULL
							THEN 'CHECKED'
							ELSE ''
					END CHECKED	
					FROM tipo_despesa_acidente tipo with (nolock)
					join referencia_despesa referencia with (nolock) on
						referencia.id = tipo.referencia_despesa_id
					left join area_responsavel_acidente area with (nolock) on
						area.id = tipo.area_responsavel_id
					left join tipo_acidente with (nolock) on
						tipo_acidente.id = $tipo_acidente_id
					left join acidente_tipo_despesas atd with (nolock) on
						atd.tipo_acidente_id = tipo_acidente.id
						and atd.tipo_despesa_id = tipo.id
					where tipo.status_id = 'a'";
		$result = odbc_exec($conSQL, $query);           

		print"<table width='950' border='1'>
			   <tr>
				 <td bgcolor='#CCCCCC'><b><center><font size='-1'>&nbsp;</center></b></td>
				 <td bgcolor='#CCCCCC'><b><center><font size='-1'>Tipo Despesa</center></b></td>
				 <td bgcolor='#CCCCCC'><b><center><font size='-1'>Refer&ecirc;ncia</center></b></td>				 
				 <td bgcolor='#CCCCCC'><b><center><font size='-1'>Area Respons&aacute;vel</center></b></td>
				 <td bgcolor='#CCCCCC'><b><center><font size='-1'>Base</center></b></td>
				 <td bgcolor='#CCCCCC'><b><center><font size='-1'>Prazo (Dias)</center></b></td>				 				 
			   </tr>";

			 while(odbc_fetch_row($result))
			   {
				   $id			 	= odbc_result($result,1);
				   $tipo_despesa_p	= odbc_result($result,2);
				   $referencia_p 	= odbc_result($result,3);				   
				   $area_p 			= odbc_result($result,4);				   
				   $base_p 			= odbc_result($result,5);				   
				   $prazo_p 		= odbc_result($result,6);	
				   $checked 		= odbc_result($result,7);					   			   				   				   				   
				   
				print"
				   <tr onmouseover=this.bgColor='#89BFF0' onmouseout=this.bgColor=''>";
				   

		   		print "<td bgcolor='#FFFFFF'><center>
							<input name='despesa_id$id' type='checkbox' id='despesa_id$id' value='1' 
								onclick='javascript:insere_despesa(this,$id,$tipo_acidente_id)' $checked />					 	
						</td>
					   <td bgcolor='#FFFFFF'><center><font size='-1'>".$tipo_despesa_p."</center></b></td>
					   <td bgcolor='#FFFFFF'><center><font size='-1'>".$referencia_p."</center></b></td>
					   <td bgcolor='#FFFFFF'><center><font size='-1'>".$area_p."</center></b></td>
					   <td bgcolor='#FFFFFF'><center><font size='-1'>".$base_p."</center></b></td>
					   <td bgcolor='#FFFFFF'><center><font size='-1'>".$prazo_p."</center></b></td>					   					   					   
				   </tr>";            
			   } 
		print"</table>";	

	?>
 	</p>
 	<table width="950" border="0" align="center">        
        <tr>
          <td height="23" class="txt_home">
            <div align="center"></div></td>
        </tr>
        <tr>
          <td class="txt_home"><div align="center">
            <input name="fechar" type="submit" class="botao_site" value=" Fechar " id="fechar" />
          </div></td>
        </tr>
      </table>
            
    <p>&nbsp;</p>
    <p>&nbsp;</p>
  </fieldset>
  </form>

</body>
</html>  
<?php

	if ($tipo_despesa_id != '')
		print "<script language='javascript'>habilita_botao('excluir')</script>";			

}
	
?>