<?php
session_name("covre_ti");
session_start();

require("../SCA/includes/page_func.php");
include("../SCA/includes/conect_sqlserver.php");

$localItem = "../registro_acidentes/upload.php";
$logado    = $_SESSION["usuario_logado"];
$logado1   = $_SESSION['cpf'];

if ($logado1 != '')
	$acesso = "permitido";
else
	$acesso  = "permitido";//valida_acesso($conSQL, $localItem, $logado);


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

$acidente_id = $_GET["id"];
$tela		 = $_GET["tela"];

$observacao = $_POST["observacao"];

	//busca o status do registro de acidente
	$query = "select status_id
			  from registro_acidente
			  where acidente_id = $acidente_id";
	//print $query;
	$result = odbc_exec($conSQL, $query) ;
	$status_acidente = odbc_result($result, 1);


$diretorio = "arquivos/$acidente_id";

		//pegando o usuario
		$query = "Select id
        	      From usuario
            	  Where usuario = '$logado'";
		$result = odbc_exec($conSQL, $query) or die("Erro ao selecionar o id do usuario<br>");
		$usuario_id = odbc_result($result, 1);
		
		//pegando o usuario caso o registro seja feito pelo terminal
		$query = "select top 1 pessoa.nome
					from registro_acidente ra with (nolock)
					join cargosol..PESSOA with (nolock) on
						pessoa.pf_cpf = ra.cpf_responsavel collate SQL_Latin1_General_CP1_CI_AS
					where acidente_id = $acidente_id";
		$result = odbc_exec($conSQL, $query) or die("Erro ao selecionar o id do usuario<br>");
		$nome_responsavel = odbc_result($result, 1);		


if($fechar != ""){
		print"
		<script language='javascript'>
			open(location, '_self').close();
		</script>
	";
}


	if($_POST['anexar'] <> "")
	{
		
		
		mkdir($diretorio);
		
		
	/*	if(mkdir($diretorio))
    		echo "Diretório criado com sucesso.";
	    else
    		echo "Não foi possível criar o diretório.";
    */
		$dir = "arquivos/$acidente_id";
		
			
		if (is_dir($dir)) {
		   if ($dh = opendir($dir)) {
			   while (($file = readdir($dh)) !== false) {
				   //unlink($dir."/".$file) ;
			   }//while
		   }//if
		}//if

		
		$workDir = "arquivos/$acidente_id"; // define this as per local system
		
		// get temporary file name for the uploaded file
		//$data_upload = date("Y_m_d_H_i_s");
		
		$tmpName = basename($_FILES['file']['tmp_name']);
		$name 	 = basename($_FILES['file']['name']);
		
		
		$name1 = utf8_decode($name);
		
		$nome_arquivo = $name1;
		$nome_fantasia = "anexotela_".$tela."_".$name;
		$nome_fantasia_link = "anexotela_".$tela."_".$name1;		
		
		$nome_fantasia1 = utf8_decode($nome_fantasia);

		
		if ($nome_arquivo == '')
			print "<script language = 'JavaScript'>alert(unescape('Favor selecionar o arquivo'));</script>";
		else
		{
			$query  = "select nome_fantasia 
					   from anexo_arquivo_acidente 
					   where rtrim(ltrim(nome_fantasia)) = rtrim(ltrim('$nome_fantasia1'))
					   and acidente_id = $acidente_id
					   and status_id = 'a'";
			//print $query;
			$result = odbc_exec($conSQL, $query) or die(odbc_errormsg($conSQL)."<br>Erro ao consultar se o arquivo já foi importado<br>");
			$arquivo_encontrado = odbc_result($result, 1);

			
			if($arquivo_encontrado == "")
			{
			
				// copy uploaded file into current directory
							
				move_uploaded_file($_FILES['file']['tmp_name'], $workDir."/".$tmpName) or die("Cannot move uploaded file to working directory");
				
				copy ($workDir."/".$tmpName, $workDir."/".$nome_fantasia);
				
	
				$query  = "insert into anexo_arquivo_acidente (acidente_id, nome_arquivo, nome_fantasia, observacao, data_gravacao, user_gravacao, 
							tela, status_id) 
							values ($acidente_id,(rtrim(ltrim('$nome_arquivo'))), (rtrim(ltrim('$nome_fantasia_link'))), 
							case when '$observacao' = '' then null else '$observacao' end, 
							getdate(), case when '$usuario_id' = '' then 499 else '$usuario_id' end, $tela,'a')";
				//print $query;
				odbc_exec($conSQL, $query) or die(odbc_errormsg($conSQL)."<br>Erro ao salvar os dados do arquivo<br>");
			
				
				print "
					<script language = 'JavaScript'>
					   alert('Arquivo incluido com sucesso!!!');
					   //window.location='importa_edi.php';
					</script>
				";
				
				unlink($workDir."/".$tmpName) or die("Cannot delete uploaded file from working directory -- manual deletion recommended");
				
				
			}//if
			else
				print "<script language = 'JavaScript'>alert(unescape('Este arquivo j%E1 est%E1 anexado. Por favor, selecione outro arquivo!'));</script>";
		}
		
	}


?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/199/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<link href="../SCA/includes/estilo.css" rel="stylesheet" type="text/css">
<style type="text/css">
fieldset { padding: 22px 17px 12px 17px; position: relative; margin: 12px 0 34px 0; }
</style>


<script language="javascript">
function exclui_arquivo(anexo_id, acidente_id, tela)
{	
	if (anexo_id != "")
	{
	 $("#img_exclusao").hide();
		$.ajax({type: "POST",//define o metódo de passagem de parametros
			url: "includes/exclui_arquivo.php", //chama uma pagina
			data: "anexo_id="+anexo_id + "&acidente_id="+acidente_id + "&tela="+tela, //passa os parametros, se necessário
			success: function(msg){  //pega o retorno da pagina chamada
            	//alert (msg);
            		$("#img_exclusao").html(msg);
                    $("#img_exclusao").show();
            		
		    }
		});
	 }
}

</script> 

</head>
<body>
<form action="" method="post" enctype="multipart/form-data" style="width:1100px">
<fieldset>
<legend>Anexo de Arquivos</legend>
<table width="685" border="0">
  <tr>
        <td width="13%" class="txt_home"><div align="right" class="txt_home">
            <p align="left"><strong>Arquivo:</strong>          </p>
        </div></td>
        <td width="87%" class="subtitulo"><input name="file" type="file" class="inp-text" id="file" size="40" /></td>
        </tr>
	<tr>
	  <td><font size="-1"><strong>Observa&ccedil;&atilde;o:</strong></td>
	  <td><input name="observacao" type="text" id="observacao" size="52" maxlength="100" /></td>
	</tr>
	<tr>
	  <td colspan="2">&nbsp;</td>
	  </tr>
</table>
<!-- </fieldset>
</form> -->

<?php

//if
  //  ($anexar != "")
//	{
	
print "<div id='img_exclusao'>";
	    $query = "Select anexo_id, nome_arquivo, nome_fantasia, CONVERT(varchar(10), data_gravacao, 103),  CONVERT(varchar(5), data_gravacao, 108), 
				  case when user_gravacao = 499
						then '$nome_responsavel'
						else cast(usuario.nome as varchar)
				  end,
				  isnull(observacao,'&nbsp;'), user_gravacao,
				  case when data_gravacao >= '2017-04-28'
						then 'link_novo'
						else 'link_antigo'
				  end
				  From anexo_arquivo_acidente aaa with (nolock)
				  JOIN usuario with (nolock) on 
				  	usuario.id = aaa.user_gravacao
				  Where acidente_id = $acidente_id
				  and aaa.tela = $tela
				  and status_id = 'a'";
		//print $query;
        $result = odbc_exec($conSQL, $query) or die ("erro ao exibir os arquivos");           


		     print"<table width='1050' border='1'>
				   <tr>
					 <td bgcolor='#CCCCCC'><font size='-1'><b><center>Nome do Arquivo</center></b></td>
					 <td bgcolor='#CCCCCC'><font size='-1'><b><center>Data</center></b></td>
 					 <td bgcolor='#CCCCCC'><font size='-1'><b><center>Hora</center></b></td>
					 <td bgcolor='#CCCCCC'><font size='-1'><b><center>Usu&aacute;rio</center></b></td>				 
					 <td bgcolor='#CCCCCC'><font size='-1'><b><center>Observa&ccedil;&atilde;o</center></b></td>
					 <td bgcolor='#CCCCCC'><font size='-1'><b><center>Excluir</center></b></td>
				   </tr>";

	         while(odbc_fetch_row($result))
	         {
					$anexo_id 			= odbc_result($result,1);
					$nome_arquivo_p 	= odbc_result($result,2);
					$nome_fantasia_p 	= odbc_result($result,3);					
					$data_p 			= odbc_result($result,4);
					$hora_p 			= odbc_result($result,5);
					$usuario_p 			= odbc_result($result,6);
					$observacao_p 		= odbc_result($result,7);	
					$user_gravacao 		= odbc_result($result,8);	
					$status_link 		= odbc_result($result,9);		
									
					if ($status_link == 'link_novo')
					{
						$nome_arquivo_p = utf8_encode($nome_arquivo_p);																													
						$nome_fantasia_p = utf8_encode($nome_fantasia_p);																													
					}

	            print"
	               <tr onmouseover=this.bgColor='#89BFF0' onmouseout=this.bgColor=''>
	                 <td bgcolor='#FFFFFF'><font size='-1'><center>
					 	<a href='arquivos/$acidente_id/".$nome_fantasia_p."' target='_blank'>".$nome_arquivo_p."</center></a></td>
					 <td bgcolor='#FFFFFF'><font size='-1'><center>".$data_p."</center></td>
 					 <td bgcolor='#FFFFFF'><font size='-1'><center>".$hora_p."</center></td>
 					 <td bgcolor='#FFFFFF'><font size='-1'><center>".$usuario_p."</center></td>					 
					 <td bgcolor='#FFFFFF'><font size='-1'><center>".$observacao_p."</center></td>";

					 if (($status_acidente == 'p') || ($status_acidente == 'r'))
					 {					 
						 if ($usuario_id == $user_gravacao)
							print "
								<td bgcolor='#FFFFFF'><center><a href='javascript:exclui_arquivo($anexo_id, $acidente_id, $tela);'>
								<img src='../../SCA/images/excluir1.jpg' style='border:none' width='30' height='25'></center></a></td>";
						 else
							print "
								<td bgcolor='#FFFFFF'><center> - </center></td>";
					 }
					 else
					 	print "
					 		<td bgcolor='#FFFFFF'><center> - </center></td>";
						
						 
					 print "</tr>";            
	         } 
		      print"</table>	

</div>";
     






?>

<table width="1050" border="0" align="center">
        <tr>
         <td>&nbsp;</td>
        </tr>
        <tr>
         <td>&nbsp;</td>
        </tr>        
        <tr>
          <td colspan="2" class="txt_home"><div align="center">
            <input name="anexar" type="submit" class="botao_site" value=" Inserir " />            
            <input name="fechar" type="submit" class="botao_site" value=" Fechar " />
          </div></td>
        </tr>
  </table>


 </fieldset>
</form>

</body>
</html>
<?php

	if (($status_acidente != 'p') && ($status_acidente != 'r'))
		print "<script language='javascript'>desabilita_botao('anexar')</script>";

}//else
?>
