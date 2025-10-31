<?php
session_name("covre_ti");
session_start();

require("../SCA/includes/page_func.php");
include("../SCA/includes/conect_sqlserver.php");

$localItem = "../registro_acidentes/tipo_despesa.php";
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


	$tipo_despesa 		= $_POST["tipo_despesa"];
	$referencia 		= $_POST["referencia"];
	$area_responsavel 	= $_POST["area_responsavel"];
	$base 				= $_POST["base"];
	$prazo	 			= $_POST["prazo"];
	$tipo_despesa_id	= $_POST["tipo_despesa_id"];
	$reembolso			= $_POST["reembolso"];	
	
	
	
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
			window.location.href='tipo_despesa.php';
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
				//document.form1.area_responsavel.value 	= dados[4];			
				
				$("#area_responsavel").html(dados[4]);
				
				document.form1.base.value 				= dados[5];			
				document.form1.prazo.value				= dados[6];	
				
				if (dados[7] == 'S')
					document.form1.reembolso.checked = 1;
				else
					document.form1.reembolso.checked = 0;

				//document.form1.area_responsavel.disabled = true;				
				
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


</head>
<div id="fundo" style="display:none">&nbsp;</div>
<body>
  <form action="" name="form1" method="post" enctype="multipart/form-data" style="width:1200px" display >
  <fieldset> 
    <legend>Cadastro - Tipos de Despesas</legend>

<?php

	if ($gravar != '')
	{

		//checkbox reembolso
		if ($reembolso != '')
			$reembolso = 'S';
	
	
		if ($tipo_despesa == '')
			print "<script type='text/javascript'> alert(unescape('Favor preencher o tipo de despesa'));</script>";	
		else
			if ($referencia == '')
				print "<script type='text/javascript'> alert(unescape('Favor preencher a refer%EAncia'));</script>";
			else
				if ($area_responsavel == '')
					print "<script type='text/javascript'> alert(unescape('Favor preencher a %e1rea respons%e1vel'));</script>";	
				else
					if ($prazo == '')
						print "<script type='text/javascript'> alert(unescape('Favor preencher o prazo'));</script>";	
					else
						if ($tipo_despesa_id == '')
						{
							$query = "insert into tipo_despesa_acidente (tipo_despesa, referencia_despesa_id, area_responsavel_id, base, 
									  prazo, status_id, reembolso) values ('$tipo_despesa', $referencia, $area_responsavel, 
									  case when '$base' = '' then null else '$base' end, $prazo, 'a', 
									  case when '$reembolso' = '' then null else '$reembolso' end)";
							//print $query;
							odbc_exec($conSQL, $query) or die(odbc_errormsg($conSQL)."<br>Erro ao inserir o tipo de despesa<br>");
														
							$query = "SELECT @@IDENTITY AS Ident";
							$result = odbc_exec($conSQL, $query) or die(odbc_errormsg($conSQL)."<br>Erro ao selecionar tipo_despesa_id");
							$tipo_despesa_id = odbc_result($result, 1);							
						}
						else
						{
							$query = "update tipo_despesa_acidente 
									  set tipo_despesa = '$tipo_despesa', referencia_despesa_id = $referencia, 
									  area_responsavel_id = $area_responsavel, 
									  base = case when '$base' = '' then null else '$base' end, prazo = $prazo,
									  reembolso = case when '$reembolso' = '' then null else '$reembolso' end
									  where id = $tipo_despesa_id";
							//print $query;
							odbc_exec($conSQL, $query) or die(odbc_errormsg($conSQL)."<br>Erro ao atualizar tipo_despesa_id<br>");							
							
							print "<script language='javascript'>busca_dados_tipo($tipo_despesa_id)</script>";
					
						}
	
		
	}//fim gravar


?>


    <table width="1020" border="0" >
    <tr>
        <td width="15%" height="23">
        <font size="-1"><strong>C&oacute;digo:</strong></font><td>
        <input name="tipo_despesa_id" type="text" id="tipo_despesa_id" value="<?PHP print $tipo_despesa_id ?>" size="4" maxlength="8" align="center" 
        readonly="readonly" style="background: #DCDCDC"/>
        </center></td>
       
    </tr>
    <tr>
       <td><font size="-1"><strong>Tipo Despesa:</strong></font></td>
       <td><input name="tipo_despesa" type="text" id="tipo_despesa" value="<?PHP print $tipo_despesa ?>" size="100" maxlength="200"/></td>
    </tr>
    <tr>
       <td height="22"><font size="-1"><strong>Refer&ecirc;ncia: </strong></font></td>
       <td><?php
					$query = "SELECT id, descricao
							  FROM referencia_despesa
							  order by descricao";
					$result = odbc_exec($conSQL, $query);           
			  
					print "<select name='referencia' id='referencia' class='lista' ><option value=''></option>";
					
					while(odbc_fetch_array($result))
					{
						if (odbc_result($result, 1) == $referencia)
							$selected = "selected='selected'";
						else
							$selected = "";
						
						 print "<option value='".odbc_result($result, 1)."'$selected>".odbc_result($result, 2)."</option>";
					}     
					print"</select>";
			  ?></td>
      </tr>
  <tr>
       <td height="22"><font size="-1"><strong>&Aacute;rea respons&aacute;vel: </strong></font></td>
       <td><?php
					$query = "SELECT id, area
							  FROM area_responsavel_acidente
							  where status_id = 'a'
							  union
							  select 0, 'Todas as &aacute;reas'
							  order by area";
					$result = odbc_exec($conSQL, $query);           
			  
					print "<select name='area_responsavel' id='area_responsavel' class='lista' ><option value=''></option>";
					
					while(odbc_fetch_array($result))
					{
						if (odbc_result($result, 1) == $area_responsavel)
							$selected = "selected='selected'";
						else
							$selected = "";
						
						 print "<option value='".odbc_result($result, 1)."'$selected>".odbc_result($result, 2)."</option>";
					}     
					print"</select>";
			  ?></td>
       </tr>
 <tr>
       <td height="22"><font size="-1"><strong>Base:</strong></font></td>
       <td><input name="base" type="text" id="base" value="<?PHP print $base ?>" size="100"/></td>
      </tr>
 <tr>
   <td height="22"><font size="-1"><strong>Prazo (dias):</strong></font></td>
   <td><input name="prazo" type="text" id="prazo" value="<?PHP print $prazo ?>" size="2" onKeyPress="return somente_numero(event)"/></td>
 </tr>
 <tr>
   <td height="22"><font size="-1"><strong>Reembolso:</td>
   <td height="22"><input type="checkbox" name="reembolso" id="reembolso"
		<?PHP
              if ($reembolso == 'S')
                  print 'checked';	  	 	  
        ?> />
     <font size="-2" color="#0000FF">(Passivo de Reembolso pela Seguradora Covre/Cliente ou DPVAT)</font></td>
   </tr>



    </table>
 
 	<p>&nbsp;</p>
 	<table width="750" border="0" align="center">        
        <tr>
          <td class="txt_home">
            <div align="center">
              <input name="novo" type="submit" class="botao_site" value=" Novo " id="novo" /> 
              <input name="gravar" type="submit" class="botao_site" value=" Gravar " id="gravar" />
              <input name="excluir" type="button" class="botao_site" value=" Excluir " id="excluir" disabled="disabled"
              onclick="javascript:exclui_tipo_despesa(document.form1.tipo_despesa_id.value)"/>
			  <input name="fechar" type="submit" class="botao_site" value=" Fechar " id="fechar" />

          </div></td>
        </tr>
        <tr>
          <td class="txt_home">&nbsp;</td>
        </tr>
      </table>
            
    <p>
      <?php

		$query = "SELECT tipo.id, tipo_despesa, referencia.descricao, isnull(area.area, 'Todas as &aacute;reas') , base, prazo,
					case when reembolso = 'S'
							then '[X]'
							else null
					end		
					FROM tipo_despesa_acidente tipo with (nolock)
					join referencia_despesa referencia with (nolock) on
						referencia.id = tipo.referencia_despesa_id
					left join area_responsavel_acidente area with (nolock) on
						area.id = tipo.area_responsavel_id
					where tipo.status_id = 'a'";
		$result = odbc_exec($conSQL, $query);           

		print"<table width='1150' border='1'>
			   <tr>
				 <td bgcolor='#CCCCCC'><b><center><font size='-1'>C&oacute;digo</center></b></td>
				 <td bgcolor='#CCCCCC'><b><center><font size='-1'>Tipo Despesa</center></b></td>
				 <td bgcolor='#CCCCCC'><b><center><font size='-1'>Refer&ecirc;ncia</center></b></td>				 
				 <td bgcolor='#CCCCCC'><b><center><font size='-1'>&Aacute;rea Respons&aacute;vel</center></b></td>
				 <td bgcolor='#CCCCCC'><b><center><font size='-1'>Base</center></b></td>
				 <td bgcolor='#CCCCCC'><b><center><font size='-1'>Prazo (Dias)</center></b></td>	
				 <td bgcolor='#CCCCCC'><b><center><font size='-1'>Reembolso</center></b></td>					 			 				 
			   </tr>";

			 while(odbc_fetch_row($result))
			   {
				   $codigo		 	= odbc_result($result,1);
				   $tipo_despesa_p	= odbc_result($result,2);
				   $referencia_p 	= odbc_result($result,3);				   
				   $area_p 			= odbc_result($result,4);				   
				   $base_p 			= odbc_result($result,5);				   
				   $prazo_p 		= odbc_result($result,6);
				   $reembolso_p 	= odbc_result($result,7);				   				   				   				   				   
				   
				print"
				   <tr onmouseover=this.bgColor='#89BFF0' onmouseout=this.bgColor=''>";
				   

		   		print "<td bgcolor='#FFFFFF'><center>
					 	<a href='javascript:busca_dados_tipo($codigo)'><font size='-1'>".$codigo."</a></center></b></td>
					   <td bgcolor='#FFFFFF'><center><font size='-1'>".$tipo_despesa_p."</center></b></td>
					   <td bgcolor='#FFFFFF'><center><font size='-1'>".$referencia_p."</center></b></td>
					   <td bgcolor='#FFFFFF'><center><font size='-1'>".$area_p."</center></b></td>
					   <td bgcolor='#FFFFFF'><center><font size='-1'>".$base_p."</center></b></td>
					   <td bgcolor='#FFFFFF'><center><font size='-1'>".$prazo_p."</center></b></td>	
					   <td bgcolor='#FFFFFF'><center><font size='-1'>".$reembolso_p."</center></b></td>						   				   					   					   
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

	if ($tipo_despesa_id != '')
		print "<script language='javascript'>habilita_botao('excluir')</script>";			

}
	
?>