<?php
session_name("covre_ti");
session_start();

require_once $_SERVER['DOCUMENT_ROOT']."/SCI/controller/ConexaoSCI.php";
require_once "../class/GravaLog.php";


class Porque extends ConexaoSCI{

	
	public function inserePorque($id,$desc_porque){
		
		$con = new ConexaoSCI;
		
		$logado = $_SESSION['usuario_logado'];
		
		
		if ($logado != '')
		{

			$desc_porque = utf8_decode($desc_porque);

			$query = "insert into pq_analise_causa (analise_causa_id, desc_porque, data_hora_gravacao, user_gravacao, status_id) 
					  values ($id, '$desc_porque', getdate(), (select id from usuario where usuario = '$logado' and status = 'a'), 'a')";
			//print $query;
			$result = $con->executar($query);		
			
			return 'Ok';
		}
		else
			return 'Erro';
		
	}	
	
	
	public function alteraPorque($id,$desc_porque){
		
		$con = new ConexaoSCI;
		$grava_log = new GravaLog;
		
		$logado = $_SESSION['usuario_logado'];
		
		
		if ($logado != '')
		{
			$desc_porque = utf8_decode($desc_porque);

			$grava_log_alt = $grava_log->gravaLog3($id,'Porque', 'desc_porque', $desc_porque );

			$query = "update pq_analise_causa
					  set desc_porque = '$desc_porque'
					  where id = $id;";
			//print $query;
			$result = $con->executar($query);		
			
			return 'Ok';
		}
		else
			return 'Erro';
		
	}		
	
	public function excluiPorque($id){
		
		$con = new ConexaoSCI;
		$grava_log = new GravaLog;
		
		$logado = $_SESSION['usuario_logado'];
		
		
		
		if ($logado != '')
		{
			//SELECIONA OS DADOS DO USUARIO QUE FARÁ A EXCLUSAO
			$query = "select id, UPPER(nome) 
					  from usuario WITH (NOLOCK)
					  where usuario = '$logado' 
					  and status = 'a';";
			//print $query;
			$result = $con->executar($query);	
			
			$usuario_id		= odbc_result($result,1);				
			$nome_usuario	= odbc_result($result,2);							
			
			
			//GRAVA O LOG DA EXCLUSAO
			$grava_log_alt = $grava_log->gravaLog3($id,'Porque', 'desc_porque', '[REGISTRO EXCLUIDO POR '.$nome_usuario.']' );			
			

			$query = "update pq_analise_causa
					  set data_hora_inativacao = getdate(), 
					  user_inativacao = $usuario_id, 
					  status_id = 'i'
					  where id = $id;";
			//return $query;
			$result = $con->executar($query);		
			
			
			
			return 'Ok';
		}
		else
			return 'Erro';
		
	}		
	
	
	public function buscaPorque($id){
		
		$con = new ConexaoSCI;
		
		$logado = $_SESSION['usuario_logado'];
		
		$retorno = '';
		
		if ($logado != '')
		{
			
			$query = "select desc_porque
					  from pq_analise_causa with (nolock)
					  where id = $id
					  and status_id = 'a';";
			//print $query;
			$result = $con->executar($query);	
			
			$desc_porque	= odbc_result($result,1);	
			
			$retorno->desc_porque = 'Ok1|'.utf8_encode($desc_porque);
	
			return $retorno;

		}
		else
			return 'Erro';
		
	}		
	
	
	
}


?>