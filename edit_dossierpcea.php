<?php


//TODO 
include_once("config.php");

session_start();

if(isset($_SESSION["loggeduser"]) && (isset($_GET["myid"]))&& ($_GET["myid"]==$_SESSION["loggeduser"]["iduser"])) {
	$myid = $_SESSION["loggeduser"]["iduser"];}
else {die("Problème de connection : perte de l'iduser...");}


if (isset($_GET["iddossier"])) {
	$iddossier = $_GET["iddossier"];
	}
	else {die("Problème de connection : perte de l'iddossier...");}

	
// ouverture bdd
try	{
	$bddcoopeshop = new PDO('mysql:host='.$db_server.';dbname='.$db_name, $db_user, $db_password);
		}
catch (Exception $e)
		{
	        die('Erreur : ' . $e->getMessage());
		}
		

if (isset($_GET["namedossier"])) {

		$namedossier = $_GET["namedossier"];
		
		if (isset($_GET["myrights"])) { 		
			$myrights = $_GET["myrights"]; //A vérifier/comparer dans la base de données ??
			
		if ($myrights > 3) {	
		
			if ($myrights == 15) {
								
						if (isset($_GET["comment"])) { 	
							$comment = $_GET["comment"];
							}
							else {$comment = "";}
						//updatesql name and comment dans dossier
						
						$sqlupdate = " UPDATE dossier SET Name = '".$namedossier."'"
								.", Comment = '".$comment."'"
								.", IdUser = ".$myid
								." WHERE IdDossier = ".$iddossier
								." AND TypeDossier = 'PCEA'"
								;
						
						$req = $bddcoopeshop->prepare($sqlupdate);	
										try 
										{
										$req->execute();
										} 
										catch (POException $e) { die( "Erreur : " . $e->getMessage()); }									
						
						$ans = '{"myid" : "'.$myid.'"'
							.', "iddossier": "'.$iddossier.'"'
							.', "update" : "ok" }';					
						}//fin de if rights==15
			}//fin de if rights>3
		} //fin de if myrights										
	} // fin de if namedossier
	 
else { 	$ans = '{"myid" : "'.$myid.'"'
			.', "iddossier": "'.$iddossier.'"'
			.', "update" : "prbm" }'
			;} 
	 
echo $ans;	 
