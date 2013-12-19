<?php

include_once("config.php");
session_start();

$myrights = $_GET["myrights"];
$idcontact = $_GET["myid"];
$iddossier = $_GET["iddossier"];
$idarticle = $_GET["idami"];
if (($_GET["sens"]==1)||$_GET["sens"]==-1) {
$myprice = floatval($_GET["sens"]) * floatval(str_replace( ",", ".", $_GET["price"] ));
}
else {$myprice=0;}

$comment = ($_GET["comment"]=="Commentaire") ? "" : $_GET["comment"];	

$dbconn = new DbConnection();
$user = new User($dbconn);

//déterminer le numero de ligne	
$sql = "SELECT IFNULL(MAX(Line),0)+1 AS numLine"
	." FROM lgdossier"
	." WHERE IdDossier = ?"
	." AND TypeDossier = ?";
	
	
$user->db -> beginTransaction();	
	$req = $user->db -> select($sql,array($iddossier,'PCEA'));		
	$result = $req->fetch();	
	$numline = $result["numLine"];
	
	$sqlinsert = "INSERT INTO lgdossier (IdContact,IdDossier,TypeDossier,Line,IdArticle,IdAnal,Quantity,Unit,Price,Forfait,Comment,CreationIdUser)"
			." VALUES (:IdContact,:IdDossier,:TypeDossier,:Line,:IdArticle,:IdAnal,:Quantity,:Unit,:Price,:Forfait,:Comment,:CreationIdUser)";
			
	$stmt = $user->db -> prepare($sqlinsert);
		$stmt->bindParam(':IdContact',$idctct);
		$stmt->bindValue(':IdDossier',$iddossier);
		$stmt->bindValue(':TypeDossier','PCEA');
		$stmt->bindValue(':Line',$numline);
		$stmt->bindParam(':IdArticle',$idart);
		$stmt->bindParam(':IdAnal',$idanal);
		$stmt->bindValue(':Quantity',1);
		$stmt->bindValue(':Unit','Ø');
		$stmt->bindParam(':Price',$price);
		$stmt->bindValue(':Forfait',0);
		$stmt->bindValue(':Comment',$comment);
		$stmt->bindValue(':CreationIdUser',$idcontact);
	
	$idctct = $idcontact;
	$idart = $idarticle;
	$idanal = ($_GET["sens"] == 1) ? "601" : (($_GET["sens"] == -1) ? "531" : "0");
	$price = $myprice;
	try {
	$stmt->execute();
	} 
	catch (POException $e) {
		 die( "Erreur : " . $e->getMessage());
		 }
	
	if ($_GET["sens"] != 0)	 {
	
	$idctct = $idarticle;
	$idart = $idcontact;
	$idanal = ($_GET["sens"] == -1) ? "601" : (($_GET["sens"] == 1) ? "531" : "0");
	$price = -1*$myprice;
	
	try {
		$stmt->execute();
	} catch (POException $e) {
		die('Erreur : ' . $e->getMessage());
		}	
	}
		
$user->db -> commit();

echo '{"lgduser" : "' .$idcontact .'"'
	. ', "iddossier" : "'. $iddossier .'"'
	. ', "idami" : "'. $idarticle .'"'
	. ', "myrights" : "'. $myrights 
	. '"}';

?>		