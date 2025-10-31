<?php
session_name("covre_ti");
session_start();

include("../../SCA/includes/conect_sqlserver.php");

$logado = $_SESSION["usuario_logado"];

$id				= $_POST["id"];


if ($logado != '')
{
    
    $query_add = "insert into log_alteracao_acidente (acidente_id, data_alteracao, usuario_id, valor_antigo, valor_novo, 
				  campo, tela_alteracao)
				 values ((SELECT TOP 1 acidente_id FROM responsavel_analise_rms WHERE responsavel_id = $id), getdate(),(select top 1 id from usuario where usuario = '$logado'),
                                 (SELECT TOP 1 pessoa.nome_fantasia FROM responsavel_analise_rms 
                                 join cargosol..pessoa on pessoa.pessoa_id = responsavel_analise_rms.pessoa_id WHERE responsavel_id = $id), null, 'Exclusao Usuario Responsavel',3)
				  ";
                                //print $query_add;
                                odbc_exec($conSQL, $query_add);
    
	//inativa o registro
	$query = "update responsavel_analise_rms
			  set status_id = 'i', data_inativacao = getdate(), user_inativacao = (select id from usuario where usuario = '$logado')
			  where responsavel_id = $id";
        
         
        
	//print $query;
	odbc_exec($conSQL, $query) or die ('erro1 ao inativar');
	
}

?>
