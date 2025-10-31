<?php
session_name("covre_ti");
session_start();

require_once $_SERVER['DOCUMENT_ROOT'] . "/SCI/controller/ConexaoSCI.php";


class GravaLog extends ConexaoSCI
{


	//GRAVA LOG DA INVESTIGACAO_ANALISE
	public function gravaLog1($acidente_id, $informacao, $nome_campo, $dado)
	{

		$con = new ConexaoSCI;

		$logado = $_SESSION['usuario_logado'];


		$query = "insert into log_alteracao_acidente (acidente_id, data_alteracao, usuario_id, valor_antigo, valor_novo, 
				  campo, tela_alteracao)
				 
				  select acidente_id, getdate(), ISNULL((select id from usuario where usuario = '$logado'),
				  (select id from usuario where usuario = 'sca')), $nome_campo,  
				  case when '$dado' = ''
				  		then null
						else '$dado'
				  end, 
				  '$informacao', 5
				  From investigacao_analise with (nolock)
				  Where acidente_id = $acidente_id 
				  and isnull($nome_campo,'') <> '$dado'";
		//print $query;
		$result = $con->executar($query);
	}


	//GRAVA LOG DA ANALISE_CAUSA
	public function gravaLog2($acidente_id, $informacao, $nome_campo, $dado)
	{

		$con = new ConexaoSCI;

		$logado = $_SESSION['usuario_logado'];


		$query = "insert into log_alteracao_acidente (acidente_id, data_alteracao, usuario_id, valor_antigo, valor_novo, 
				  campo, tela_alteracao)
				 
				  select acidente_id, getdate(), ISNULL((select id from usuario where usuario = '$logado'),
				  (select id from usuario where usuario = 'sca')), $nome_campo,  
				  case when '$dado' = ''
				  		then null
						else '$dado'
				  end, 
				  '$informacao', 6
				  From analise_causa with (nolock)
				  Where acidente_id = $acidente_id 
				  and isnull($nome_campo,'') <> '$dado'";
		//print $query;
		$result = $con->executar($query);
	}


	//GRAVA LOG DO PORQUE ANALISE_CAUSA
	public function gravaLog3($id, $informacao, $nome_campo, $dado)
	{

		$con = new ConexaoSCI;

		$logado = $_SESSION['usuario_logado'];


		$query = "insert into log_alteracao_acidente (acidente_id, data_alteracao, usuario_id, valor_antigo, valor_novo, 
				  campo, tela_alteracao)
				  select acidente_id, getdate(), ISNULL((select id from usuario where usuario = '$logado'),
				  (select id from usuario where usuario = 'sca')), $nome_campo,  
				  case when '$dado' = ''
				  		then null
						else '$dado'
				  end, 
				  '$informacao', 6
				  From pq_analise_causa with (nolock)
				  join analise_causa with (nolock) on
					analise_causa.id = pq_analise_causa.analise_causa_id
				  where pq_analise_causa.id = $id
				  and isnull($nome_campo,'') <> '$dado'";
		//print $query;
		$result = $con->executar($query);
	}


	//GRAVA LOG DA TELA QSMA
	public function gravaLog4($acidente_id, $informacao, $nome_campo, $dado)
	{

		$con = new ConexaoSCI;

		$logado = $_SESSION['usuario_logado'];



		$query = "insert into log_alteracao_acidente (acidente_id, data_alteracao, usuario_id, valor_antigo, valor_novo, 
				  campo, tela_alteracao)
				 
				  select acidente_id, getdate(), ISNULL((select id from usuario where usuario = '$logado'),
				  (select id from usuario where usuario = 'sca')), $nome_campo,  
				  case when '$dado' = ''
				  		then null
						else '$dado'
				  end, 
				  '$informacao', 3
				  From registro_acidente with (nolock)
				  Where acidente_id = $acidente_id 
				  and isnull($nome_campo,'') <> '$dado'";
		// print $query;
		$result = $con->executar($query);
	}


	//GRAVA LOG DA TELA PRINCIPAL
	public function gravaLog5($acidente_id, $informacao, $nome_campo, $dado)
	{

		$con = new ConexaoSCI;

		$logado = $_SESSION['usuario_logado'];


		$query = "insert into log_alteracao_acidente (acidente_id, data_alteracao, usuario_id, valor_antigo, valor_novo, 
				  campo, tela_alteracao)
				  select acidente_id, getdate(), ISNULL((select id from usuario where usuario = '$logado'),
				  (select id from usuario where usuario = 'sca')), $nome_campo,  
				  case when '$dado' = ''
				  		then null
						else '$dado'
				  end, 
				  '$informacao', 1
				  From registro_acidente with (nolock)
				  Where acidente_id = $acidente_id 
				  and isnull($nome_campo,'') <> '$dado'";
		//print $query;
		$result = $con->executar($query);
	}


	//GRAVA LOG DA TELA ANALISE PPAE
	public function gravaLog6($acidente_id, $informacao, $nome_campo, $dado)
	{

		$con = new ConexaoSCI;

		$logado = $_SESSION['usuario_logado'];


		$query = "insert into log_alteracao_acidente (acidente_id, data_alteracao, usuario_id, valor_antigo, valor_novo, 
				  campo, tela_alteracao)
				  select acidente_id, getdate(), ISNULL((select id from usuario where usuario = '$logado'),
				  (select id from usuario where usuario = 'sca')), $nome_campo,  
				  case when '$dado' = ''
				  		then null
						else '$dado'
				  end, 
				  '$informacao', 2
				  From analise_ppae with (nolock)
				  Where acidente_id = $acidente_id 
				  and isnull($nome_campo,'') <> '$dado'";
		//print $query;
		$result = $con->executar($query);
	}
}
