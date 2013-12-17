<?php
include_once("config.php");

session_start();

if (isset($_GET["idami"])) {
	$idami = $_GET["idami"];
	}
	else {die("Problème de connection : perte de l'idami...");}

$dbconn = new DbConnection();
$user = new User($dbconn);

if (isset($_GET["iddossier"])) {
		$iddossier = $_GET["iddossier"];	
		if (isset($_GET["myrights"])) { 		
			$myrights = $_GET["myrights"];} else {die("Problème de connection : perte de vos myrights...");}
		
			if (isset($_GET["droits"])) { 	
				$rightsami = $_GET["droits"];
					if (isset($_GET["comment"])) { 	
							$commentuser = $_GET["comment"];
							}
			//updatesql rights dans pceauser iddossier = idd, iduser=idami			
			$sqlupdate = " UPDATE pceauser SET Rights = ?"
					.", Comment = ?"
					." WHERE IdDossier = ?"
					." AND IdUser = ?";
			
					
			$req = $user->db->prepare($sqlupdate);	
			$req->execute(array($rightsami,$commentuser,$iddossier,$idami));								
			} // fin de if droits
			
			else {if (isset($_GET["comment"])) { 	
				$commentuser = $_GET["comment"];
				$sqlupdate = " UPDATE pceauser SET Comment = ?"
								." WHERE IdDossier = ?"
								." AND IdUser = ?";
				$req = $user->db->prepare($sqlupdate);	
				$req->execute(array($commentuser,$iddossier,$idami));
					}
				}
			
			if (($myrights==15) || ($myrights == 7) && ($idami == $user->id)) {
						
			if (isset($_GET['nombreamis'])) 			
			{
			$nbreamis = $_GET['nombreamis'];}
			$newcommuns = array();
			$k=0;
			for ($i=0;$i<$nbreamis;$i++) {
				if ($_GET['ami'.$i]!=0 && $_GET['ami'.$i]!="0") {
				$newcommuns[$k] = $_GET['ami'.$i];
				$k++;
				}
			}		
			//effacer les anciennes données de compte commun
			$sqldelete = "DELETE FROM pceausercommun WHERE (IdUser1 = ? OR  IdUser2 = ?) AND IdDossier = ?";					
			$user->db->beginTransaction();								
				$stmt = $user->db->prepare($sqldelete);	
				$stmt->execute(array($idami,$idami,$iddossier));		
				// inserer les nouvelles		
				$sqlinsert = "INSERT INTO pceausercommun (IdDossier,IdUser1,IdUser2)"
					." VALUES (:IdDossier,:IdUser1,:IdUser2)";									
				$stmt = $user->db -> prepare($sqlinsert);					
				$stmt->bindValue(':IdDossier',$iddossier);	
				$stmt->bindParam(':IdUser1',$iduser1);
				$stmt->bindParam(':IdUser2',$iduser2);					
				//boucle sur les cases cochées userscommun
				 for ($i=0; $i<count($newcommuns); $i++) {				
					$iduser1 = $newcommuns[$i];
					$iduser2 = $idami;
					try {$stmt->execute();} 
					catch (POException $e) { die( "Erreur : " . $e->getMessage()); }						 
					 $iduser1= $idami;
					 $iduser2 = $newcommuns[$i];
					try {$stmt->execute();} 
					catch (POException $e) { die( "Erreur : " . $e->getMessage()); }
					}				
			$user->db->commit();						
			} // fin de droits a changer les comptes commun
		$ans = '{"myid" : "'.$user->id.'"'
			.', "iddossier": "'.$iddossier.'" }';									
		} //fin de if iddossier	 		
		else	{ $ans = '{"myid" : "'.$user->id.'" }';} 
	 
echo $ans;	 
	 