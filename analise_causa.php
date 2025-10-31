<?php
session_name("covre_ti");
session_start();
require("../SCA/includes/page_func.php");
include("../SCA/includes/conect_sqlserver.php");
require_once "class/Dados.php";
require_once "class/GravaLog.php";

$dados = new Dados;	
$grava_log = new GravaLog;	

$sistema_id				= $_GET["si"];

$localItem = "../registro_acidentes/analise_causa.php";
$logado    = $_SESSION["usuario_logado"];
$sistema_id = 97;
$acesso  = valida_acesso_popup($conSQL, $sistema_id, $logado);
//$acesso = "permitido";


if($acesso <> "permitido"){
    //grava_acesso($conSQL, $localItem, $logado, 2, $vObservacao);

    print "
        <script language = 'JavaScript'>
           alert('Acesso negado para esta pagina');
           window.location='centro.php';
		</script>
    ";
}//elseif
else{
	 //grava_acesso($conSQL, $localItem, $logado, 1, $vObservacao);


	$acidente_id 		= $_GET["id"];
	$analise_causa_id	= $_POST["analise_causa_id"];
	$efeito_nc 			= $_POST["efeito_nc"];	
	$causa_raiz 		= $_POST["causa_raiz"];	
	
	if ($analise_causa_id == '')
		$analise_causa_id	= $_POST["analise_causa_id"];	
	
	
	$efeito_nc		= utf8_decode($efeito_nc);
	$causa_raiz		= utf8_decode($causa_raiz);	
	
	
	if($fechar != ""){
			print"
			<script language='javascript'>
				open(location, '_self').close();
			</script>
		";
	}
	

if ($gravar != '')
{

		if ($analise_causa_id == '')
		{
		
			$query1 = "insert into analise_causa (acidente_id, efeito_nc, causa_raiz, data_hora_gravacao, user_gravacao, status_id)
						values ($acidente_id,
						case when '$efeito_nc' = '' then null else '$efeito_nc' end, 		
						case when '$causa_raiz' = '' then null else '$causa_raiz' end, 
						getdate(), (select top 1 id from usuario where usuario = '$logado' and status = 'a'), 'a')
						";
			//print $query1;
			odbc_exec($conSQL, $query1) or die(odbc_errormsg($conSQL));	
			
			$query = "SELECT @@IDENTITY AS Ident";
			odbc_exec($conSQL, $query) or die(odbc_errormsg($conSQL)."<br>Erro ao selecionar o id inserido<br>");
			$result = odbc_exec($conSQL, $query) ;
			$analise_causa_id = odbc_result($result, 1);							
		
		}
		else
		{

			//LOG DE ALTERACAO
			$grava_log_alt = $grava_log->gravaLog2($acidente_id,'Efeito da NC / Acidente / Incidente / Desvio', 'efeito_nc', $efeito_nc);
			
			$grava_log_alt = $grava_log->gravaLog2($acidente_id,'Causa Raiz', 'causa_raiz', $causa_raiz );
		
		
		
			$query1 = "update analise_causa 
						set efeito_nc = case when '$efeito_nc' = '' then null else '$efeito_nc' end, 		
						causa_raiz = case when '$causa_raiz' = '' then null else '$causa_raiz' end 
						where id = $analise_causa_id";
			//print $query1;
			odbc_exec($conSQL, $query1) or die(odbc_errormsg($conSQL));				
		
		}	
	
	
}//gravar


	if (($acidente_id != '') && ($erro != 1))
	{
		$dados_analise_causa = $dados->dadosAnCausa($acidente_id);
		$analise_causa_id = $dados_analise_causa->id;	
		$efeito_nc = $dados_analise_causa->efeito_nc;
		$causa_raiz = $dados_analise_causa->causa_raiz;	
	
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
fieldset { padding: 22px 12px 12px 15px; position: relative; margin: 12px 0 0px 0px; }
</style>

<script language="javascript">
function insere_porque(id,acao)
{	
	var id_pq = document.form1.pq_analise_causa_id.value;
	var desc_porque = document.form1.desc_porque.value;
	
	if (id_pq != '')
	{
		id = id_pq;	
		acao = 4;
	}

	if (id == '')
		alert(unescape('Necess%E1rio gravar o registro para inserir a descri%E7%E3o do porqu%EA'));
	else
	{
		if (desc_porque == "")
			alert(unescape('Favor inserir a descri%E7%E3o do porqu%EA'));
		else		
		{
	
			$.ajax({type: "POST",//define o met�do de passagem de parametros
				url: "includes/insere_porque.php", //chama uma pagina
				data: "id="+id + "&desc_porque="+desc_porque + "&acao="+acao, 
				success: function(msg){  //pega o retorno da pagina chamada
					//alert(msg);
	
						if(msg.indexOf("Ok") == -1)
							alert(unescape('Sessao Expirada'));
						else
						{
							if (acao == 1)
								alert(unescape('Descri%E7%E3o do porqu%EA inserido com sucesso'));
							else
								alert(unescape('Descri%E7%E3o do porqu%EA alterado com sucesso'));
								
							document.form1.gravar.click();
						}
				}
			});
		 }
	}
}
</script>


<script language="javascript">
function altera_porque(id,acao)
{	
	//alert();
	if (id != "")
	{

		$.ajax({type: "POST",//define o met�do de passagem de parametros
			url: "includes/insere_porque.php", //chama uma pagina
			data: "id="+id + "&acao="+acao, 
			success: function(msg){  //pega o retorno da pagina chamada
				//alert(msg);

					if(msg.indexOf("Ok1") == -1)
						alert(unescape('Sessao Expirada'));
					else
					{
						var dados = msg.split("|");

						document.form1.pq_analise_causa_id.value = id;
						document.form1.desc_porque.value = dados[1];
						//document.form1.gravar.click();
					}
			}
		});
	 }
}
</script>

<script language="javascript">
function impressao(id)
{
	pagina('analise_causa_impressao.php?id='+id,'1500','1500','impressao')
}
</script>

<script language="javascript">
function exclui_porque(id,acao)
{	

	if (confirm(unescape("Deseja realmente excluir a descri%E7%E3o do porqu%EA?")))
	{
		
		$.ajax({type: "POST",//define o met�do de passagem de parametros
			url: "includes/insere_porque.php", //chama uma pagina
			data: "id="+id + "&acao="+acao, 
			success: function(msg){  //pega o retorno da pagina chamada
				console.log(msg);

					if(msg.indexOf("Ok") == -1)
						alert(unescape('Sessao Expirada'));
					else
					{
						alert(unescape('Descri%E7%E3o exclu%EDda com sucesso'));
						document.form1.gravar.click();
					}
			}
		});
	 }
}
</script>

</head>
<div id="fundo" style="display:none; width:3000px;">&nbsp;</div>
<body>

<form name="form1" method="post" action="" style="width:1200px">
    
    <fieldset>
		 	<legend>An&aacute;lise de Causa</legend>
 	  <table width="871" border="0">
    	<tr>
        	<td><b><font color="#0000FF" size="-1">Descri&ccedil;&atilde;o do Fato</font></b></td>
        </tr>
    </table>
    <table width="1151" border="1" frame="box" rules="none">
    	<tr>
        	<td bgcolor="#FFFFFF">
            	<?php

					$dados_registro = $dados->dadosRegistro($acidente_id);
					$descricao_fato = $dados_registro->descricao_fato;            
				
					print utf8_encode($descricao_fato);
				?>            
            
            </td>
      </tr>
    </table>
    
	<table width="871" border="0">
    	<tr>
        	<td><input type="hidden" name="analise_causa_id" id="analise_causa_id" value="<?php print $analise_causa_id ?>"/></td>
      </tr>
    </table>   
    
    <table width="871" border="0">
    	<tr>
    	  <td>&nbsp;</td>
  	  </tr>
    	<tr>
        	<td><font size="-1"><strong>Efeito da NC / Acidente / Incidente / Desvio:</strong></font></td>
      </tr>
    	<tr>
    	  <td><font size="-1">
    	    <textarea name="efeito_nc" id="efeito_nc" cols="140" rows="5" value="" onkeyup='charLimit(this, 1000);'><?php print utf8_encode($efeito_nc) ?></textarea>
    	  </font></td>
  	  </tr>
    </table>       
    
    <table width="871" border="0">
    	<tr>
        	<td>&nbsp;</td>
        </tr>
    </table>

    <table width="871" border="0">
    	<tr>
        	<td><font size="-1" color="#0000FF"><b>Porqu&ecirc;</b></font></td>
        </tr>
    </table>    
    
    <table width="1150" border="1" frame="box" rules="none">
    	<tr>
    	  <td width="4">&nbsp;</td>
    	  <td colspan="2"><font size="-1"><b>Descri&ccedil;&atilde;o do Porqu&ecirc;
    	    <input type="hidden" name="pq_analise_causa_id" id="pq_analise_causa_id" />
    	    <label for="pq_analise_causa_id"></label>
    	  </b></font></td>
   	  </tr>
    	<tr>
    	  <td>&nbsp;</td>
    	  <td width="1070" valign="top"><font size="-1">
    	    <textarea name="desc_porque" id="desc_porque" cols="130" rows="4" value="" onkeyup='charLimit(this, 1000);'></textarea>
    	  </font></td>
    	  <td width="54" valign="middle"><font size="-1">
    	    <input name="inserir" type="button" class="botao_site_1" value=" Gravar " id="inserir" onclick="javascript:insere_porque('<?php print $analise_causa_id ?>',1)"/>
    	  </font></td>
      </tr>
    	<tr>
    	  <td>&nbsp;</td>
    	  <td colspan="2">&nbsp;</td>
  	  </tr>
    	<tr>
    	  <td>&nbsp;</td>
    	  <td colspan="2">
          
                <?php

					$dados_porque = $dados->pqAnCausa($analise_causa_id,1);
					$descricao_porque = $dados_porque->relatorio;            
				
					print utf8_encode($descricao_porque);
					
				?> 
          
          
          </td>
  	  </tr>
    	<tr>
    	  <td>&nbsp;</td>
    	  <td colspan="2">&nbsp;</td>
  	  </tr>
   	  </table>
    
      
	<table width="871" border="0">
    	<tr>
        	<td>&nbsp;</td>
      </tr>
    </table>   
    
    <table width="871" border="0">
    	<tr>
        	<td><font size="-1"><strong>Causa Raiz:</strong></font></td>
      </tr>
    	<tr>
    	  <td><font size="-1">
    	    <textarea name="causa_raiz" id="causa_raiz" cols="140" rows="5" value="" onkeyup='charLimit(this, 3000);'><?php print utf8_encode($causa_raiz) ?></textarea>
    	  </font></td>
  	  </tr>
    </table>   
 
    <table width="871" border="0">
    	<tr>
        	<td>&nbsp;</td>
      </tr>
    </table>   
  
    <table width="285" border="1">
      <tr>
        <td width="104" height="22"><div align="center">
          <?php
				if ($acidente_id == '')
					print "<a href=javascript:alert_anexos()><b><center>Anexos</b></center></a>";
				else
					print "<a href=javascript:pagina('upload.php?id=$acidente_id&tela=6','1200','600','Anexos')><b><center>Anexos</center></b></a>";
      		?>
        </div></td>
        <td width="165" height="22">
      		<a href=javascript:pagina('logs_alteracao.php?acidente_id=<?php print $acidente_id ?>&tela=6','1300','400','Logs')><b>
        	<center>Logs de Altera&ccedil;&atilde;o</center></b></a>        
        </td>
      </tr>
    </table>   
    
    <table width="871" border="0">
    	<tr>
        	<td>&nbsp;</td>
      </tr>
    </table>   

    
   <table width="619" border="0" align="center">        
        <tr>
          <td width="544" class="txt_home">&nbsp;</td>
          <td width="65" class="txt_home">&nbsp;</td>
      </tr>
        <tr>
          <td colspan="2" class="txt_home"><div align="center">
            <input name="gravar" type="submit" class="botao_site" value=" Gravar " id="gravar" />
            <input name='imprimir' type='button' class='botao_site' value=' Imprimir ' id='imprimir' onclick='impressao(<?php print $acidente_id ?>)'/>
            <input name="fechar" type="submit" class="botao_site" value=" Fechar " id="fechar" />
          </div></td>
        </tr>
	</table>         


	</fieldset>
</form>

</body>
</html>

<?php

	/*
	if ($analise_causa_id == '')
		print "<script language='javascript'>desabilita_botao('inserir')</script>";		
	*/

	}
?>