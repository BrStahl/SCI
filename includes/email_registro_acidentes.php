<?php
session_name("covre_ti");
session_start();

include("../../SCA/includes/conect_sqlserver.php");

$logado    = $_SESSION["usuario_logado"];

$email = $_POST["email"];
$id    = $_POST["id"];


if ($logado != '')
{

	if($id == 0){//SE ID = 0, INSERE NOVO EMAIL
	
		$format_email = str_replace(" ", "", $email);//Retira os espaços do e-mail
		$format_email = str_replace(",", ";", $format_email);//Troca qualquer virgula que tiver por ponto e virgula
		$multiplo_email = explode(";", $format_email);//Separa os e-mails por ponto e virgula
		$count_multiplo = count($multiplo_email);
		
		$verifica_email = strpos($email, '@covre.com.br');
			
		for($i=0; $i < $count_multiplo; $i++){
			//echo "Nome: ".$multiplo_email[$i]." - Contador: ".$i." | ";
			
			$verifica_email = strpos($multiplo_email[$i], '@covre.com.br');
			
			if($verifica_email != true){
				echo "pertence :".$multiplo_email[$i]."|";
			}else{
				$query_verif_email = "SELECT id FROM email_registro_acidentes WITH(NOLOCK) WHERE email = '$multiplo_email[$i]'";
				$result_verif_email = odbc_exec($conSQL, $query_verif_email) or die("Erro ao verificar email");
				$verifica_email = odbc_result($result_verif_email, 1);
				
				if($verifica_email == ""){
					$insert_email = "insert into email_registro_acidentes (email) values ('$multiplo_email[$i]')";
					odbc_exec($conSQL, $insert_email) or die("Erro ao inserir e-mail");
				}else{
					echo "inserido :".$multiplo_email[$i]."|";
					
				}
			}
		}
		
		
		
	}else
		if($id > 0){
			$delete_email = "delete email_registro_acidentes where id = $id";
			odbc_exec($conSQL, $delete_email) or die("Erro ao deletar e-mail");
			echo "ok"; 
		}
	
}else{
	print "sessao expirada";
}




?>

