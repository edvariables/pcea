<?php
include_once("config.php");

session_start();
if(isset($_SESSION["loggeduser"]) && (isset($_GET["myid"]))&& ($_GET["myid"]==$_SESSION["loggeduser"]["iduser"])) {
	$myid = $_SESSION["loggeduser"]["iduser"];}
else {die("Problème de connection : perte de l'iduser...");}


if (isset($_GET["idami"])) {
	$idami = $_GET["idami"];
	}
	else {die("Problème de connection : perte de l'idami...");}

	
// ouverture bdd
try	{
	$bddcoopeshop = new PDO('mysql:host='.$db_server.';dbname='.$db_name, $db_user, $db_password);
		}
catch (Exception $e)
		{
	        die('Erreur : ' . $e->getMessage());
		}
		

if (isset($_GET["iddossier"])) {

		$iddossier = $_GET["iddossier"];
		
		if (isset($_GET["myrights"])) { 		
			$myrights = $_GET["myrights"]; //A vérifier/comparer dans la base de données ??
			
		if ($myrights == 15) {
					
			if (isset($_GET["droits"])) { 	
				$rightsami = $_GET["droits"];
			
				if (isset($_GET["comment"])) { 	
							$commentuser = "'".$_GET["comment"]."'";
							}
					else {$commentuser="''";}
				
			//updatesql rights dans pceauser iddossier = idd, iduser=idami
			
			$sqlupdate = " UPDATE pceauser SET Rights = ".$rightsami
					.", Comment = ".$commentuser
					." WHERE IdDossier = ".$iddossier
					." AND IdUser = ".$idami;
			
		
					
			$req = $bddcoopeshop->prepare($sqlupdate);	
							try 
							{
							$req->execute();
							} 
							catch (POException $e) { die( "Erreur : " . $e->getMessage()); }									
				} // fin de if droits
			
			
				
						
			if (isset($_GET['nombreamis'])) 
			
			{
			//effacer les anciennes données de compte commun
			$sqldelete = "DELETE FROM pceausercommun WHERE (IdUser1 = ".$idami." OR  IdUser2 = ".$idami.") AND IdDossier = " .$iddossier;
			
				$nbreamis = $_GET['nombreamis'];
				$newcommuns = array();
				$k=0;
				for ($i=0;$i<$nbreamis;$i++) {
					if ($_GET['ami'.$i]!=0 && $_GET['ami'.$i]!="0") {
					$newcommuns[$k] = $_GET['ami'.$i];
					$k++;
					}
				}
			$bddcoopeshop->beginTransaction();
			
					
			$stmt = $bddcoopeshop->prepare($sqldelete);	
				try 
				{
				$stmt->execute();
				} 
				catch (POException $e) { die( "Erreur : " . $e->getMessage()); }

			
				
			// inserer les nouvelles		
			$sqlinsert = "INSERT INTO pceausercommun (IdDossier,IdUser1,IdUser2)"
				." VALUES (:IdDossier,:IdUser1,:IdUser2)";
								
			$stmt = $bddcoopeshop -> prepare($sqlinsert);	
			
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
			
			$bddcoopeshop->commit();
							
			} // fin de if checkcommuns
							
			}//fin de if rights==15

		$ans = '{"myid" : "'.$myid.'"'
			.', "iddossier": "'.$iddossier.'" }';
									
		} //fin de if myrights
	 		
		else	{ $ans = '{"myid" : "'.$myid.'" }';} 
		
	 	 	 
	} // fin de if iddossier
	 
else { $ans = '{"myid" : "'.$myid.'" }';} 
	 
echo $ans;	 
	 