<?php

include_once("config.php");
session_start();

if (isset($_GET["iddossier"])) {
	$iddossier = $_GET["iddossier"];
	}
	else {die("Problème de connection : perte de l'iddossier...");}

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
				$sqlupdate = " UPDATE dossier SET Name = ?"
						.", Comment = ?"
						.", IdUser = ?"
						." WHERE IdDossier = ?"
						." AND TypeDossier = ?"
						;				
				$dbconn = new DbConnection();
				$user = new User($dbconn);						
				$req = $user->db->prepare($sqlupdate);
				$req->execute(array($namedossier,$comment,$user->id,$iddossier,'PCEA'));
			
				$ans = '{"myid" : "'.$user->id.'"'
					.', "iddossier": "'.$iddossier.'"'
					.', "update" : "ok" }';					
				}//fin de if rights==15
			}//fin de if rights>3
		} //fin de if myrights										
	} // fin de if namedossier
	 
else { 	$ans = '{"myid" : "'.$user->id.'"'
			.', "iddossier": "'.$iddossier.'"'
			.', "update" : "prbm" }'
			;} 
	 
echo $ans;	 
