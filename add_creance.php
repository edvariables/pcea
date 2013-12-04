<?php

include_once("config.php");
session_start();
if (($_SESSION["loggeduser"])&&($_SESSION["loggeduser"]["iduser"])&&($_SESSION["loggeduser"]["iduser"]==$_GET["myid"]))

{


	try	{
		$bddcoopeshop = new PDO('mysql:host='.$db_server.';dbname='.$db_name, $db_user, $db_password);
		}
	catch (Exception $e)
		{
	        die('Erreur : ' . $e->getMessage());
		}
		$myrights = $_GET["myrights"];
		$idcontact = $_GET["myid"];
		$iddossier = $_GET["iddossier"];
		$idarticle = $_GET["idami"];
		$myprice = floatval($_GET["sens"])*floatval($_GET["price"]);
		$comment = ($_GET["comment"]=="Commentaire") ? "" : $_GET["comment"];	
	
	//déterminer le numero de ligne	
		
	$sql = "SELECT IFNULL(MAX(Line),0)+1 AS numLine"
		." FROM lgdossier"
		." WHERE IdDossier = ".$iddossier
		." AND TypeDossier = 'PCEA'";
		
	$req = $bddcoopeshop -> prepare($sql);
			$req->execute();
			
	$result = $req->fetch(PDO::FETCH_ASSOC);
	
	$numline = $result["numLine"];
	
	$sqlinsert = "INSERT INTO lgdossier (IdContact,IdDossier,TypeDossier,Line,IdArticle,IdAnal,Quantity,Unit,Price,Forfait,Comment,CreationIdUser)"
			." VALUES (:IdContact,:IdDossier,:TypeDossier,:Line,:IdArticle,:IdAnal,:Quantity,:Unit,:Price,:Forfait,:Comment,:CreationIdUser)";
	
	$bddcoopeshop -> beginTransaction();
			
	$stmt = $bddcoopeshop -> prepare($sqlinsert);
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
	$idanal = ($_GET["sens"] == 1) ? "601" : "531";
	$price = $myprice;
	try {
	$stmt->execute();
	} 
	catch (POException $e) {
		 die( "Erreur : " . $e->getMessage());
		 }
	$idctct = $idarticle;
	$idart = $idcontact;
	$idanal = ($_GET["sens"] != 1) ? "601" : "531";
	$price = -1*$myprice;

	try {
		$stmt->execute();
	} catch (POException $e) {
		die('Erreur : ' . $e->getMessage());
		}
		
	$bddcoopeshop -> commit();

	echo '{"lgduser" : "' .$idcontact .'"'
		. ', "iddossier" : "'. $iddossier .'"'
		. ', "myrights" : "'. $myrights 
		. '"}';
		}
else {

echo "Problème de connection: iduser != _session_iduser à l'appel de add_creance.php";

};		

?>		