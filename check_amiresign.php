<?php

include_once("config.php");


if (isset($_GET["idami"])&&isset($_GET["iddossier"])) {

	$id = $_GET["idami"];
	$dos = $_GET["iddossier"];


//interroger bdd sur nbre de ligne dans dossier pour cet ami

try { $bdd = new PDO('mysql:host='.$db_server.';dbname='.$db_name, $db_user, $db_password);
			}
catch (Exception $e)
			{
		        die('Erreur : ' . $e->getMessage());
			}
	//préparation requête
		$sql = "SELECT COUNT(Line) AS NbLignes, IdContact"
			." FROM lgdossier"
			." WHERE TypeDossier = 'PCEA'"
			." AND IdDossier = ".$dos
			." AND IdContact = ".$id
			." GROUP BY IdContact" ;
//
	$req = $bdd->prepare($sql);
	$req->execute();
	
	
	if ($result = $req->fetch(PDO::FETCH_ASSOC)) {
		$nbreligne = $result["NbLignes"];	
		}
	else { $nbreligne = 0;};


 	if ($nbreligne == 0) {
  		$answ = '{ "resign" : "ok"}' ;
 		echo $answ;
 		}
	else {
	$answ = '{ "resign" : "'.$nbreligne.'"}';
	echo $answ;
	}



	}
else {
	$answ = '{ "resign" : "cnxpb"}';
	echo $answ;
	}



?>