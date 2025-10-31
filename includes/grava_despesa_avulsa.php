<?php
session_name("covre_ti");
session_start();

include("../../SCA/includes/conect_sqlserver.php");
require_once "../class/GravaDados.php";

$dados = new GravaDados;

$acidente_id 				= $_POST["acidente_id"];
$area_id			 		= $_POST["area_id"];
$tipo_despesa		 		= $_POST["tipo_despesa"];
$referencia_despesa_id 		= $_POST["referencia_despesa_id"];
$funcionario				= $_POST["funcionario"];
$horas						= $_POST["horas"];
$dt_despesa					= $_POST["data_despesa"];
$valor						= $_POST["valor"];
$observacao					= $_POST["observacao"];
$valor_abat = $_POST["valor_abat"];


$data_despesa =
	implode(
		preg_match("~\/~", $dt_despesa) == 0 ? "/" : "-",
		array_reverse(explode(preg_match("~\/~", $dt_despesa) == 0 ? "-" : "/", $dt_despesa))
	);


$gravacao = $dados->gravaDespesaAvulsa($acidente_id, $area_id, $tipo_despesa, $referencia_despesa_id, $funcionario, $horas, $data_despesa, $valor, $observacao, $valor_abat);

$retorno = $gravacao->ret_gravacao;

print $retorno;
