<?PHP

include('../class/Porque.php');

$insere_porque = new Porque();


$id 			= $_POST['id']; 
$desc_porque 	= $_POST['desc_porque']; 
$acao 			= $_POST['acao']; 


if ($acao == 1)
	$dados = $insere_porque->inserePorque($id,$desc_porque);  
else
	if ($acao == 2)
	{
		$dados_busca = $insere_porque->buscaPorque($id);  
		
		$dados = $dados_busca->desc_porque; 
	}
	else
		if ($acao == 3)
			$dados = $insere_porque->excluiPorque($id);  	
		else
			if ($acao == 4)
				$dados = $insere_porque->alteraPorque($id,$desc_porque);  		


print $dados;






?>