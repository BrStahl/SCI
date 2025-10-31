<?php
session_name("covre_ti");
session_start();

require("../SCA/includes/page_func.php");
include("../SCA/includes/conect_sqlserver.php");


$localItem = "../registro_acidentes/lancamento_despesa_avulsa.php";
$logado    = $_SESSION["usuario_logado"];
//$acesso  = valida_acesso($conSQL, $localItem, $logado);
$acesso = "permitido";

if ($acesso <> "permitido") {
	grava_acesso($conSQL, $localItem, $logado, 2, $vObservacao);

	print "
        <script language = 'JavaScript'>
           alert('Acesso negado para esta p�gina');
           window.location='centro.php';
		</script>
    ";
} //elseif
else {
	grava_acesso($conSQL, $localItem, $logado, 1, $vObservacao);


	$acidente_id 					= $_GET["acidente_id"];
	$area_id 						= $_GET["area_id"];

	$query = "select pa.area_rh, pa.area_qualidade, usuario.id
			  from usuario with (nolock)
			  left join permissoes_acidente pa with (nolock) on
				  pa.usuario_id = usuario.id
			  where usuario = '$logado'";
	//print $query;
	$result = odbc_exec($conSQL, $query);
	$area_rh 			= odbc_result($result, 1);
	$area_qualidade		= odbc_result($result, 2);
	$usuario_id			= odbc_result($result, 3);


	$query = "select top 1 id, area
				from area_responsavel_acidente area with (nolock)
				where area.status_id = 'a'
				and ((responsavel_id = $usuario_id) or (sub_1_id = $usuario_id) or (sub_2_id = $usuario_id))
				order by area";
	//print $query;
	$result = odbc_exec($conSQL, $query);
	$responsavel_area 	= odbc_result($result, 1);


	if ($fechar != "") {
		print "
		<script language='javascript'>
			open(location, '_self').close();
		</script>
	";
	}




?>

	<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
	<html xmlns="http://www.w3.org/1999/xhtml">

	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
		<link href="../SCA/includes/estilo.css" rel="stylesheet" type="text/css">

		<script type="text/javascript" src="../SCA/includes/js/jquery-autocomplete/lib/jquery.bgiframe.min.js"></script>
		<script type="text/javascript" src="../SCA/includes/js/jquery-autocomplete/lib/jquery.ajaxQueue.js"></script>
		<script type="text/javascript" src="../SCA/includes/js/jquery-autocomplete/lib/thickbox-compressed.js"></script>
		<script type="text/javascript" src="../SCA/includes/js/jquery-autocomplete/jquery.autocomplete.js"></script>

		<script type="text/javascript" src="../SCA/includes/calendario/_scripts/jquery.click-calendario-1.0-min.js"></script>
		<script type="text/javascript" src="../SCA/includes/calendario/_scripts/exemplo-calendario.js"></script>

		<link href="../SCA/includes/calendario/_style/jquery.click-calendario-1.0.css" rel="stylesheet" type="text/css" />

		<link rel="stylesheet" type="text/css" href="../SCA/includes/js/jquery-autocomplete/jquery.autocomplete.css" />
		<link rel="stylesheet" type="text/css" href="../SCA/includes/js/jquery-autocomplete/lib/thickbox.css" />

		<style type="text/css">
			fieldset {
				padding: 22px 12px 12px 15px;
				position: relative;
				margin: 12px 0 0px 0px;
			}
		</style>

		<script>
			function atualiza() {
				this.opener.location = "lancamento_despesas.php?acidente_id=<?php print $acidente_id ?>&area_id=<?php print $area_id ?>";
			}
		</script>


		<script>
			function fecha() {
				this.opener.location = "lancamento_despesas.php?acidente_id=<?php print $acidente_id ?>&area_id=<?php print $area_id ?>";
				window.setInterval("self.close();window.opener.focus();", 1000);
			}
		</script>

		<script type="text/javascript">
			$(document).ready(function() {
				$("#funcionario").autocomplete("completar_funcionario.php", {
					width: 600,
					selectFirst: false
				});
				//document.form1.usuario.focus();
			});
		</script>


		<script language="javascript">
			function pesquisa_despesas(acidente_id) {
				//alert(acidente_id);


				if (acidente_id == '')
					alert(unescape('Favor inserir o n%FAmero do registro de ocorr%EAncia'));
				else {

					$.ajax({
						type: "POST", //define o met?do de passagem de parametros
						url: "includes/busca_dados_despesas.php", //chama uma pagina
						data: "acidente_id=" + acidente_id,
						success: function(msg) {
							//alert(msg);

							if (msg.indexOf("NAO_ENCONTRADO") == -1) {
								if (msg.indexOf("SEM_CLASSIFICACAO") == -1) {
									habilita_botao('gravar');
									$("#exibe_resultado").html(msg);
								} else {
									desabilita_botao('gravar');
									$("#exibe_resultado").html("<CENTER><font color='red' size='+1'><b>NECESS&Aacute;RIO CLASSIFICA&Ccedil;&Atilde;O DO SETOR QSMA PARA INSERIR DESPESAS</b></font>");
								}
							} else {
								desabilita_botao('gravar');
								$("#exibe_resultado").html("<CENTER><font color='red' size='+1'><b>REGISTRO N&Atilde;O ENCONTRADO</b></font>");
							}


						}
					});

				}

			}
		</script>


		<script language="javascript">
			function altera_campo(referencia_id) {
				//alert();
				if (referencia_id.value == 3) {
					document.getElementById('valor_desp').value = '*****';
					document.getElementById('valor_desp').readOnly = true;
				} else {
					document.getElementById('valor_desp').value = '';
					document.getElementById('valor_desp').readOnly = false;
				}
			}
		</script>

		<script language="javascript">
			function grava_registro(acidente_id) {
				//alert();

				var area_id = document.getElementById('area_id').value;
				var tipo_despesa = document.getElementById('tipo_despesa').value;
				var referencia_despesa_id = document.getElementById('referencia_despesa_id').value;
				var funcionario = document.getElementById('funcionario').value;
				var horas = document.getElementById('horas').value;
				var data_despesa = document.getElementById('data_1').value;
				var valor_desp = document.getElementById('valor_desp').value;
				var valor_abat = document.getElementById('valor_abat').value;

				if (valor_desp == '*****')
					var valor = document.getElementById('valor').value;
				else
					var valor = valor_desp;

				var observacao = document.getElementById('observacao').value;
				//alert();


				if (area_id == '')
					alert(unescape('Favor inserir a %E1rea'));
				else
				if (tipo_despesa == '')
					alert(unescape('Favor inserir o tipo da despesa'));
				else
				if (referencia_despesa_id == '')
					alert(unescape('Favor inserir a refer%EAncia'));
				else
				if ((referencia_despesa_id == 3) && (funcionario == ''))
					alert(unescape('Favor inserir o funcion%E1rio'));
				else
				if ((referencia_despesa_id == 3) && (horas == ''))
					alert(unescape('Favor inserir a hora'));
				else
				if (data_despesa == '')
					alert(unescape('Favor inserir a data do cre&eacute;dito'));
				else {

					$.ajax({
						type: "POST", //define o met?do de passagem de parametros
						url: "includes/grava_despesa_avulsa.php", //chama uma pagina
						data: "tipo_despesa=" + tipo_despesa + "&referencia_despesa_id=" + referencia_despesa_id + "&funcionario=" + funcionario + "&horas=" + horas + "&data_despesa=" + data_despesa + "&valor=" + valor + "&observacao=" + observacao + "&area_id=" + area_id + "&acidente_id=" + acidente_id + "&valor_abat=" + valor_abat,
						success: function(msg) {
							//alert(msg);

							if (msg.indexOf("ok") == -1)
								alert(unescape(msg));
							else {
								//document.form1.submit();
								//document.getElementById('area_id').value = '';
								document.getElementById('tipo_despesa').value = '';
								document.getElementById('referencia_despesa_id').value = '';
								document.getElementById('funcionario').value = '';
								document.getElementById('horas').value = '';
								document.getElementById('data_1').value = '';
								document.getElementById('valor_desp').value = '';
								document.getElementById('valor_abat').value = '';
								document.getElementById('valor').value = '';
								document.getElementById('observacao').value = '';

								pesquisa_despesas(<?php print $acidente_id ?>)
								atualiza();
							}

						}
					});

				}

			}
		</script>

<script type="text/javascript">
    // FORMATAÇÃO (insere ":" antes dos últimos 2 dígitos)
    function mascara_hora1(elmnt) {
        // Remove tudo que não for número
        let valor = elmnt.value.replace(/\D/g, '');
        
        // Insere ":" antes dos últimos 2 dígitos (formato XXXX:XX)
        if (valor.length >= 2) {
            const posicaoDoisPontos = valor.length - 2;
            valor = valor.substring(0, posicaoDoisPontos) + ':' + valor.substring(posicaoDoisPontos);
        }
        
        elmnt.value = valor;
        
        // Valida minutos quando tiver ":"
        if (valor.includes(':')) {
            verifica_minutos(elmnt);
        }
    }

    // VALIDAÇÃO DOS MINUTOS (00-59)
    function verifica_hora1(elmnt) {
        const valor = elmnt.value;
        const minutos = parseInt(valor.split(':')[1] || '0', 10);
        
        if (minutos < 0 || minutos > 59) {
            alert("Minutos inválidos! Devem ser entre 00 e 59.");
            elmnt.value = valor.split(':')[0] + ':'; // Mantém apenas as horas + ":"
            elmnt.focus();
        }
    }
</script>
	</head>

	<body>
		<form action="" name="form1" method="post" enctype="multipart/form-data" style="width:95%">
			<fieldset style="width:95%">
				<legend>Lan&ccedil;amentos de Despesas Avulsas</legend>
				<table>
					<tr>
						<td><b>
								<font size="-1">N&ordm; Registro Ocorr&ecirc;ncia:
							</b> <?php print $acidente_id ?></font>
						</td>
					</tr>
				</table>

				<p>&nbsp;</p>
				<p>
					<?php

					if ($responsavel_area == '') {
						print "<script language='javascript'>desabilita_botao('pesquisar')</script>";
						print "<center><FONT color='red' size='+1'><b>VOC&Ecirc; N&Atilde;O &Eacute; RESPONS&Aacute;VEL POR NENHUMA &Aacute;REA</b></font>";
					}

					?>
				<div id="exibe_resultado"></div>

				</p>



				<p>&nbsp;</p>
				<p>
					<font size="-1" color="#0000FF"><b>Inserir Despesa</b></font>
				</p>
				<table width="95%" border="1" frame="box" rules="none">
					<tr>
						<td height="22">&nbsp;</td>
						<td height="22" colspan="4">
							<font size="-1"><b>Tipo Despesa:</b></font>
						</td>
						<td height="22" colspan="3">
							<font size="-1"><b>&Aacute;rea:</b></font>
						</td>
					</tr>
					<tr>
						<td height="22">&nbsp;</td>
						<td height="22" colspan="4"><input name="tipo_despesa" type="text" id="tipo_despesa" size="135" /></td>
						<td height="22" colspan="3"><?php



													$query = "select id, area
								from area_responsavel_acidente area with (nolock)
								where area.status_id = 'a'
								and ((responsavel_id = $usuario_id) or (sub_1_id = $usuario_id) or (sub_2_id = $usuario_id))
								order by area";
													$result = odbc_exec($conSQL, $query);
													$result_cont = odbc_exec($conSQL, $query);
													$cont = 0;
													while (odbc_fetch_row($result_cont)) {
														$cont++;
													}

													if ($cont <= 1)
														$option = "";
													else
														$option = "<option value=''></option>";


													print "<select name='area_id' id='area_id' class='lista' >$option";

													while (odbc_fetch_array($result)) {
														print "<option value='" . odbc_result($result, 1) . "'$selected>" . odbc_result($result, 2) . "</option>";
													}
													print "</select>";
													?></td>
					</tr>
					<tr>
						<td height="22">&nbsp;</td>
						<td height="22" colspan="5">&nbsp;</td>
					</tr>
					<tr>
						<td width="2" height="22">
							<font size="-1"><b>&nbsp;</b></font>
						</td>
						<td width="198" height="22">
							<font size="-1"><b>Refer&ecirc;ncia:</b></font>
						</td>
						<td width="207" height="22">
							<font size="-1"><b>Funcion&aacute;rio:</b></font>
						</td>
						<td width="80">
							<font size="-1"><b>Horas</b></font>:
						</td>
						<td width="132">
							<font size="-1"><b>Valor D&eacute;bito:</b></font>
						</td>
						<td width="132">
							<font size="-1"><b>Valor Cr&eacute;dito:</b></font>
						</td>
						<td width="225">
							<font size="-1"><b>Data Cr&eacute;dito:</b></font>
						</td>
					</tr>
					<tr>
						<td height="22">&nbsp;</td>
						<td height="22"><?php
										$query = "SELECT id, descricao
							  FROM referencia_despesa
							  order by descricao";
										$result = odbc_exec($conSQL, $query);

										print "<select style='width: 300px;' name='referencia_despesa_id' id='referencia_despesa_id' class='lista' onchange='javascript:altera_campo(this)'><option value=''></option>";

										while (odbc_fetch_array($result)) {
											if (odbc_result($result, 1) == $referencia)
												$selected = "selected='selected'";
											else
												$selected = "";

											print "<option value='" . odbc_result($result, 1) . "'$selected>" . odbc_result($result, 2) . "</option>";
										}
										print "</select>";
										?></td>
						<td height="22"><input name="funcionario" type="text" id="funcionario" style="width: 300px;" size="65" onfocus='javascript:completa_nome(this)' /></td>
						<td height="22">
							<font size="-1">
								<input align="center" name="horas" type="text" id="horas" size="1" maxlength="6"  onblur='javascript:mascara_hora1(this)' />
							</font>
						</td>
						<td height="22">
							<font size="-1">
								<input name='valor_desp' type='text' id='valor_desp' value='' size='7' onkeypress='valida_dinheiro(this)' />
								<input name='valor' type='hidden' id='valor' value='' size='7' />
							</font>
						</td>
						<td height="22">
							<font size="-1">
								<input name='valor_abat' type='text' id='valor_abat' value='' size='7' onkeypress='valida_dinheiro(this)' />
								<!--<input name='valor' type='hidden' id='valor' value='' size='7' />-->
							</font>
						</td>
						<td height="22">
							<font size="-1">
								<input name="data_1" type="text" id="data_1" value="<?php print $data_despesa ?>" size="07" maxlength="10" align="center" onkeypress="valida_conteudo_data(this)" onblur="javascript:verifica_data_1(this)" />
							</font>
						</td>
					</tr>
					<tr>
						<td height="22">&nbsp;</td>
						<td height="22" colspan="5">&nbsp;</td>
					</tr>
					<tr>
						<td height="22">&nbsp;</td>
						<td height="22" colspan="5">
							<font size="-1"><b>Observa&ccedil;&atilde;o:</b></font>
						</td>
					</tr>
					<tr>
						<td height="22">&nbsp;</td>
						<td height="22" colspan="5"><input name="observacao" type="text" id="observacao" size="198" maxlength="200" /></td>
					</tr>
					<tr>
						<td height="22">&nbsp;</td>
						<td height="22" colspan="5">&nbsp;</td>
					</tr>
					<tr>
						<td height="22">&nbsp;</td>
						<td height="22" colspan="5">
							<div align="center">
								<input name="gravar" type="button" class="botao_site" value=" Gravar " id="gravar" onClick='javascript:grava_registro(<?php print $acidente_id ?>)' />
							</div>
						</td>
					</tr>
				</table>



				<p>&nbsp;</p>
				<table width="1200" border="0" align="center">
					<tr>
						<td width="1115" class="txt_home">
							<div align="center">
								<input name="fechar" type="submit" class="botao_site" value=" Fechar " id="fechar" />
							</div>
						</td>
						<td width="75" class="txt_home">
							<?php

							if ($botao_voltar == 1) {
								print "
      	<a href='registro_acidente_parte1.php?id=$acidente_id'><img src='../SCA/images/botao_voltar.png' alt='Retorna para a p&aacute;gina principal' width='77' height='28' border='0'/></a>";
							}
							?>

						</td>
					</tr>
					<tr>
						<td colspan="2" class="txt_home">&nbsp;</td>
					</tr>
				</table>
			</fieldset>
		</form>

		<p>&nbsp;</p>
	</body>
<?php

	if ($acidente_id == '')
		print "<script language='javascript'>desabilita_botao('gravar')</script>";
	else
		print "<script language='javascript'>pesquisa_despesas($acidente_id)</script>";
}
?>

	</html>