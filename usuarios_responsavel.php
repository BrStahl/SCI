<?php
session_name("covre_ti");
session_start();

require("../SCA/includes/page_func.php");
include("../SCA/includes/conect_sqlserver.php");

$localItem = "../registro_acidentes/usuarios_responsavel.php";
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

$id 			= $_GET["id"];



if($fechar != "")
{
		print"
		<script language='javascript'>
			open(location, '_self').close();
		</script>";
}


if ($gravar != "")
{

if(isset($_POST['usuario'])) $usuario_ins = $_POST['usuario'];

$query = "SELECT id,email FROM usuario WHERE nome LIKE '$usuario_ins'
					AND STATUS = 'A'";
$result = odbc_exec($conSQL,$query);
$id_ins = odbc_result($result,1); 
$email_ins = odbc_result($result,2); 

if(!$id_ins){
  echo "<script>alert('Selecionar Usuario')</script>";
}else{

$query_verifica = "select id 
                   from responsavel_po_macro_email
                   where status = 'a'
                   and responsavel_po_macro_id = $id and usuario_id = $id_ins";
// echo $query_verifica;
$result_ver = odbc_exec($conSQL,$query_verifica);
$existe = odbc_result($result_ver,1);

    if(!$existe){
		$query = "INSERT INTO responsavel_po_macro_email (responsavel_po_macro_id, usuario_id, email,status)
							VALUES ($id,$id_ins,'$email_ins','a')";
		// print $query;					
		odbc_exec($conSQL, $query) or die(odbc_errormsg($conSQL)."<br>Erro1 ao inserir o usuario<br>");

    print"<script language='javascript'>window.location.href='usuarios_responsavel.php?id=$id';</script>";
    }else{
      echo "<script>alert('Ja existe usuario cadastrado para o Ponto de Operacao')</script>";
    }
  }
}

if($_POST['excluir'] != ""){
			$query = "UPDATE responsavel_po_macro_email set status = 'i' where id =  ".$_POST['excluir']."";
		odbc_exec($conSQL, $query) or die(odbc_errormsg($conSQL)."<br>Erro1 ao excluir o usuario<br>");
}


	
?>


<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<link href="../SCA/includes/estilo.css" rel="stylesheet" type="text/css">

<!--<script type="text/javascript" src="jquery-autocomplete/lib/jquery.js"></script> -->
<script type="text/javascript" src="../SCA/includes/js/jquery-autocomplete/lib/jquery.bgiframe.min.js"></script>
<script type="text/javascript" src="../SCA/includes/js/jquery-autocomplete/lib/jquery.ajaxQueue.js"></script>
<script type="text/javascript" src="../SCA/includes/js/jquery-autocomplete/lib/thickbox-compressed.js"></script>
<script type="text/javascript" src="../SCA/includes/js/jquery-autocomplete/jquery.autocomplete.js"></script>

<script type="text/javascript" src="_scripts/jquery.click-calendario-1.0-min.js"></script>		
<script type="text/javascript" src="_scripts/exemplo-calendario.js"></script>

<link href="_style/jquery.click-calendario-1.0.css" rel="stylesheet" type="text/css"/>

<link rel="stylesheet" type="text/css" href="../SCA/includes/js/jquery-autocomplete/jquery.autocomplete.css"/>
<link rel="stylesheet" type="text/css" href="../SCA/includes/js/jquery-autocomplete/lib/thickbox.css"/> 

<style type="text/css">
fieldset { padding: 22px 17px 12px 17px; position: relative; margin: 12px 0 34px 0; }

</style>

<script language="javascript">

  $(document).ready(function(){
    $("#usuario").autocomplete("completar_usuario.php", {
      width:600,
      selectFirst: false
    });
  });
</script> 
<script language="javascript">
function excluir(elmnt){
  if(confirm('Deseja realmente excluir?')){
	document.getElementById('excluir').value = elmnt;
	document.getElementById('form1').submit();
  }
}
</script>



</head>
<body>
  <form action="" name="form1" method="post" enctype="multipart/form-data" style="width:500px" >
  <fieldset> 
    <legend>Emails Cadastrados</legend> 
      
    <table width="450" border="0" align="center">
        <tr>
								<td width='450'>
                    Usu&aacute;rio: <input type='text' name='usuario' id='usuario' size='40' value='<? echo $usuario ?>'>
                </td>
        </tr>
        <tr>
          <td height="22">&nbsp;</td>
        </tr>
        <tr>
          <td height="14">&nbsp;</td>
        </tr>
      </table>
<table width="447" border="0" align="center">
        <tr>
          <td width="441"><div align="center">
            <input name="gravar" type="submit" class="botao_site" value=" Gravar " id="gravar" />
            <input name="fechar" type="submit" class="botao_site" value=" Fechar " id="fechar" />
          </div></td>
      </tr>
        <tr>
          <td>&nbsp;</td>
        </tr>
</table>
<p>
  

	<table width='450' border='1'>
				<tr>
				<td bgcolor='#CCCCCC' width='400'><b><center><font size='-1'>Usuario</center></b></td>
				<td bgcolor='#CCCCCC' width='400'><b><center><font size='-1'>Email</center></b></td>
				<td bgcolor='#CCCCCC' width='50'><b><center><font size='-1'>Excluir</center></b></td>				 
				</tr>
 <?php





			 $query = "SELECT usu.id, usuario.NOME, usu.email 
			 				   FROM responsavel_po_macro_email USU(NOLOCK)
								 JOIN usuario (NOLOCK)
										ON usuario.id = usu.usuario_id
								 WHERE responsavel_po_macro_id = $id
								 	and usu.status = 'a'";
			 $result = odbc_exec($conSQL,$query) or die(odbc_errormsg($conSQL));
			 while(odbc_fetch_row($result)){
				//  echo "aqui";
				   $id_registro	= odbc_result($result,1);
					 $nome				= odbc_result($result,2);	
					 $email				= odbc_result($result,3);	
					 				 
				   print"
				   <tr>
							 <td bgcolor='#FFFFFF'><center><font size='-1'>".$nome."</center></td>
							 <td bgcolor='#FFFFFF'><center><font size='-1'>".$email."</center></td>
				   		<td bgcolor='#FFFFFF'><center>
							<a href='javascript:excluir($id_registro)'><img src='../SCA/images/excluir1.jpg' width='25' heigth='25' style='border:none'></a></center></b></td>				
				   </tr>";            
			  }  	 

	?>
	</table>
</p>
<table width="450" border="0" align="center">        
        <tr>
          <td class="txt_home">
            <div align="center"></div></td>
          <!--          <td class="txt_home"><input name="alterar" type="submit" class="botao_site" value=" Alterar " id="alterar" /></td> -->
      </tr>
    </table>
<p>&nbsp;</p>
       <input type="hidden" name="excluir" id="excluir" value="">
    </fieldset>
  </form>


</body>
</html>
<?php

	
}//else
?>
