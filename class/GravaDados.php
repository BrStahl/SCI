<?php
session_name("covre_ti");
session_start();

require_once $_SERVER['DOCUMENT_ROOT'] . "/SCI/controller/ConexaoSCI.php";


class GravaDados extends ConexaoSCI
{


	public function gravaDespesaAvulsa($acidente_id, $area_id, $tipo_despesa, $referencia_despesa_id, $funcionario, $horas, $data_despesa, $valor, $observacao, $valor_abat)
	{

		$con = new ConexaoSCI;

		$logado = $_SESSION['usuario_logado'];

		$retorno = '';


		if ($logado != '') {

			$valor = str_replace('.', '', $valor);
			$valor = str_replace(',', '.', $valor);
			$valor = ltrim($valor);
			$valor = rtrim($valor);

			$valor_abat = str_replace('.', '', $valor_abat);
			$valor_abat = str_replace(',', '.', $valor_abat);
			$valor_abat = ltrim($valor_abat);
			$valor_abat = rtrim($valor_abat);

			$tipo_despesa = utf8_decode($tipo_despesa);
			$observacao = utf8_decode($observacao);


			$query = "INSERT INTO despesa_area_acidente (acidente_id, area_id, data_gravacao, usuario_id, status_id)
						select $acidente_id, area.id, GETDATE(), (select id from usuario where usuario = '$logado' and status = 'a'), 'a'
						from area_responsavel_acidente area with (nolock)
						where status_id = 'a'
						and id = $area_id
						and area.id not in (select top 1 area_id
											from despesa_area_acidente with (nolock)
											WHERE acidente_id = $acidente_id
											and status_id = 'a')";
			//print $query;
			$con->executar($query);



			$query = "insert into tipo_despesa_acidente (tipo_despesa, referencia_despesa_id, area_responsavel_id, base, prazo, data_gravacao, 
					  user_gravacao, status_id) 
					  values ('$tipo_despesa', $referencia_despesa_id, $area_id, 'DESPESA MANUAL', 0, getdate(), 
					  (select id from usuario where usuario = '$logado' and status = 'a'), 'm')";
			//print $query;
			$con->executar($query);

			$query = "SELECT @@IDENTITY AS Ident";
			$result = $con->executar($query);

			$tipo_despesa_id = odbc_result($result, 1);


			$query = "insert into LANCAMENTO_DESPESAS_ACIDENTE (acidente_id, tipo_despesa_id, funcionario, horas, status_despesa_id, 
					  data_despesa, observacao, valor, prazo, data_inclusao, area_id, status_id, valor_abatido) 
					  values ($acidente_id, $tipo_despesa_id, 
					  case when '$funcionario' = '' then null else '$funcionario' end, 
					  case when '$horas' = '' then null else '$horas' end, 
					  1, '$data_despesa',
					  case when '$observacao' = '' then null else '$observacao' end, 
					  case when '$valor' = '' then null else '$valor' end, '$data_despesa', getdate(), $area_id, 'm'
					  ,case when '$valor_abat' = '' then null else '$valor_abat' end)";
			//print $query;
			$con->executar($query);

			$ret_gravacao = "ok";
		} else
			$ret_gravacao = "Sessão expirada, favor logar novamente";

		$retorno->ret_gravacao = $ret_gravacao;

		return $retorno;
	}
}
