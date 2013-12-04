<?php

include_once("config.php");
session_start();
if (($_SESSION["loggeduser"])&&isset($_GET["myid"])&&($_SESSION["loggeduser"]["iduser"]==$_GET["myid"]))
	{$myid = $_GET["myid"];}
else {die("IdUser perdu - impossible de continuer");}


if (isset($_GET["iddossier"])) 
	{
	$iddossier = $_GET["iddossier"];

	if (isset($_GET["myrights"])) 
		{
		$myrights = $_GET["myrights"];
		
		
		if ($myrights < 15) {die("Vous n'avez pas les droits administrateur");};
		
	
	
	
// ouverture bdd 
	try	{
	$bddcoopeshop = new PDO('mysql:host='.$db_server.';dbname='.$db_name, $db_user, $db_password);
		}
	catch (Exception $e)
		{
	        die('Erreur : ' . $e->getMessage());
		}	
// On supprime la ligne sur pceauser et eventuellement sur pceausercommun	




	$sqlusers = "DELETE FROM pceauser WHERE IdDossier = " .$iddossier;
	
	$sqlcommun = "DELETE FROM pceausercommun WHERE IdDossier = " .$iddossier;
	
	$sqllignes = "DELETE FROM lgdossier WHERE TypeDossier = 'PCEA' AND IdDossier = " .$iddossier ;
	
	$sqldossier = "DELETE FROM dossier WHERE TypeDossier = 'PCEA' AND IdDossier = " .$iddossier ;
	
	$bddcoopeshop->beginTransaction();
	
	$stmt = $bddcoopeshop->prepare($sqlusers);
	try 
	{
	$stmt->execute();
	} 
	catch (POException $e) { die( "Erreur : " . $e->getMessage()); }	

	$stmt = $bddcoopeshop->prepare($sqlcommun);	
		try 
		{
		$stmt->execute();
		} 
		catch (POException $e) { die( "Erreur : " . $e->getMessage()); }
	
	$stmt = $bddcoopeshop->prepare($sqllignes);	
				try 
				{
				$stmt->execute();
				} 
				catch (POException $e) { die( "Erreur : " . $e->getMessage()); }
	
	$stmt = $bddcoopeshop->prepare($sqldossier);	
					try 
					{
					$stmt->execute();
					} 
					catch (POException $e) { die( "Erreur : " . $e->getMessage()); }
	
	$bddcoopeshop->commit();
	
	$answer = '{ "myid" : "'.$myid .'" }';
	
	echo $answer;
		}
	else {die("Droits non reconnus");}
	}
else {die("IdDossier inconnu dans delete dossier");}	
	

?>