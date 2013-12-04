<?php
include_once("config.php");

session_start();
if (($_SESSION["loggeduser"])&&isset($_GET["myid"])&&($_SESSION["loggeduser"]["iduser"]==$_GET["myid"]))
	{$myid = $_GET["myid"];}
else {die("IdUser perdu - impossible de continuer");}


if (isset($_GET["iddossier"])) 
	{
	$iddossier = $_GET["iddossier"];


// ouverture bdd 
	try	{
	$bddcoopeshop = new PDO('mysql:host='.$db_server.';dbname='.$db_name, $db_user, $db_password);
		}
	catch (Exception $e)
		{
	        die('Erreur : ' . $e->getMessage());
		}	
// On supprime la ligne sur pceauser et eventuellement sur pceausercommun	

	$sqluser = "DELETE FROM pceauser WHERE IdUser = ".$myid." AND IdDossier = " .$iddossier;
	
	$sqlcommun1 = "DELETE FROM pceausercommun WHERE IdUser1 = ".$myid." AND IdDossier = " .$iddossier;
	$sqlcommun2 = "DELETE FROM pceausercommun WHERE IdUser2 = ".$myid." AND IdDossier = " .$iddossier;
	
	$bddcoopeshop->beginTransaction();
	
	$stmt = $bddcoopeshop->prepare($sqluser);
	try 
	{
	$stmt->execute();
	} 
	catch (POException $e) { die( "Erreur : " . $e->getMessage()); }	

	$stmt = $bddcoopeshop->prepare($sqlcommun1);	
		try 
		{
		$stmt->execute();
		} 
		catch (POException $e) { die( "Erreur : " . $e->getMessage()); }
	
	$stmt = $bddcoopeshop->prepare($sqlcommun2);	
				try 
				{
				$stmt->execute();
				} 
				catch (POException $e) { die( "Erreur : " . $e->getMessage()); }
	
	
	
	$bddcoopeshop->commit();
	
	$answer = '{ "myid" : "'.$myid .'" }';
	
	echo $answer;
}

else {die("IdDossier inconnu dans decline invit");}	
	

?>