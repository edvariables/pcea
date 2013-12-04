<?php

include_once("config.php");

session_start();
if (($_SESSION["loggeduser"])&&isset($_GET["iddossier"])&&isset($_GET["myid"])&&($_SESSION["loggeduser"]["iduser"]==$_GET["myid"]))
	{
	$iddossier = $_GET["iddossier"];	
	$myid = $_GET["myid"];
	if (isset($_GET["myrights"])) {
			$myrights = $_GET["myrights"];
					}
	else {$myrights = "";}

	}
else {
	die("Pertes de données - Pas de requête");
	}


// ouverture bdd 
try	{
	$bddcoopeshop = new PDO('mysql:host='.$db_server.';dbname='.$db_name, $db_user, $db_password);
}
catch (Exception $e)
		{
	        die('Erreur : ' . $e->getMessage());
		}
		
//changer Status en 'OK' dans pceasuer 

 $sqlupdate = "UPDATE pceauser "  
 		." SET Status = 'OK'" 
		." WHERE IdUser = ". $myid
		." AND IdDossier = ". $iddossier;
		
$req = $bddcoopeshop->prepare($sqlupdate);

$req->execute();

//reponse 
echo '{ "myid" : "'.$myid.'" , "iddossier" : "' . $iddossier .'" , "myrights" : "' . $myrights .'" }';


?>