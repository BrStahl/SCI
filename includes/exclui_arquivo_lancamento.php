<?php
session_name("covre_ti");
session_start();

include("../../SCA/includes/conect_sqlserver.php");

$logado    = $_SESSION["usuario_logado"];

$anexo_id 		= $_POST["anexo_id"];
$acidente_id	= $_POST["acidente_id"];
$lancamento_id	= $_POST["lancamento_id"];

if ($logado != '')
{

		//pegando o usuario
		$query = "Select id
        	      From usuario
            	  Where usuario = '$logado'";
		$result = odbc_exec($conSQL, $query) or die("Erro ao selecionar o usuario<br>");
		$usuario_id = odbc_result($result, 1);


		$query = "UPDATE anexo_arquivo_acidente 
		 		 SET status_id = 'i', user_exclusao = $usuario_id, data_exclusao = getdate()
		 		 WHERE anexo_id = $anexo_id";
		$result = odbc_exec($conSQL, $query) ;

}

	    $query = "Select anexo_id, nome_arquivo, nome_fantasia, CONVERT(varchar(10), data_gravacao, 103),  CONVERT(varchar(5), data_gravacao, 108), 
				  usuario.nome, isnull(observacao,'&nbsp;'), user_gravacao
				  From anexo_arquivo_acidente aaa with (nolock)
				  JOIN usuario with (nolock) on 
				  	usuario.id = aaa.user_gravacao
				  Where acidente_id = $acidente_id
				  and aaa.tela = 4
				  and aaa.lancamento_id = $lancamento_id
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

	            print"
	               <tr onmouseover=this.bgColor='#89BFF0' onmouseout=this.bgColor=''>
	                 <td bgcolor='#FFFFFF'><font size='-1'><center>
					 	<a href='arquivos/$acidente_id/".$nome_fantasia_p."' target='_blank'>".$nome_arquivo_p."</center></a></td>
					 <td bgcolor='#FFFFFF'><font size='-1'><center>".$data_p."</center></td>
 					 <td bgcolor='#FFFFFF'><font size='-1'><center>".$hora_p."</center></td>
 					 <td bgcolor='#FFFFFF'><font size='-1'><center>".$usuario_p."</center></td>					 
					 <td bgcolor='#FFFFFF'><font size='-1'><center>".$observacao_p."</center></td>";
					 
					 if ($usuario_id == $user_gravacao)
					 	print "
					 		<td bgcolor='#FFFFFF'><center><a href='javascript:exclui_arquivo($anexo_id, $acidente_id, $lancamento_id);'>
					 	 	<img src='../../SCA/images/excluir1.jpg' style='border:none' width='30' height='25'></center></a></td>";
					 else
					 	print "
					 		<td><center> - </center></td>";
					 
					 print "</tr>";            
	           } 
		      print"</table>";




?>

