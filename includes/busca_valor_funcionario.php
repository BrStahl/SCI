<?php
session_name("covre_ti");
session_start();

include("../../SCA/includes/conect_sqlserver.php");


$id 			= $_POST["id"];
$funcionario 	= $_POST["funcionario"];
$horario	 	= $_POST["horario"];

// Extrair horas e minutos corretamente, considerando possíveis 3 dígitos
$horas_minutos = explode(':', $horario);
$hora = (int)$horas_minutos[0];
$minuto = isset($horas_minutos[1]) ? (int)$horas_minutos[1] : 0;

// Calcular o total de minutos e depois converter para horas decimais
$total_minutos = ($hora * 60) + $minuto;
$hora_em_minutos = $total_minutos / 60;

// Formatar para garantir 2 casas decimais
$hora_em_minutos_formatada = number_format($hora_em_minutos, 2, '.', '');

$query = "SELECT REPLACE((CAST((SALARIO / (JORNADAMENSAL / 60)) * 2 AS NUMERIC(15,2)) * $hora_em_minutos_formatada),'.',',')
          FROM CORPORE..PFUNC
          WHERE CODSITUACAO <> 'D'
          AND CODCOLIGADA = 1
          AND NOME = '$funcionario'";

// print $query; // Descomente para debug
$result = odbc_exec($conSQL, $query);
$valor_hora = odbc_result($result, 1);

print $valor_hora;


?>

