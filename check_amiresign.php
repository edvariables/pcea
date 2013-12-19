<?php
include_once("config.php");

if (isset($_GET["idami"])&&isset($_GET["iddossier"])) {
	$id = $_GET["idami"];
	$dos = $_GET["iddossier"];
//interroger bdd sur nbre de ligne dans dossier pour cet ami
	$bdd = new DbConnection();
	//préparation requête
	$sql = "SELECT COUNT(Line) AS NbLignes, IdContact"
		." FROM lgdossier"
		." WHERE TypeDossier = 'PCEA'"
		." AND IdDossier = ?"
		." AND IdContact = ?"
		." GROUP BY IdContact" ;
	$req = $bdd->select($sql,array($dos,$id));
		
	if ($result = $req->fetch()) {
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