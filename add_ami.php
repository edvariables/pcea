
<?php

include_once("config.php");
session_start();

$iddossier = $_GET["iddossier"];
$idinvite = $_GET["idinvite"];
$rightsinvite = $_GET["droits"];
$comment = $_GET["comment"];
$myrights = $_GET["myrights"];
$nbreamis = $_GET['totalpresents'];

date_default_timezone_set('Europe/Paris');
$nowsql = date('Y-m-d H:i:s');

$bdd = new DbConnection();
$user = new User($bdd);

$sqlinsert = "INSERT INTO pceauser (IdUser,IdDossier,CreationDate,CreationIdUser,Rights,Status,Comment)"
				." VALUES (?,?,?,?,?,?,?)";

$user->db -> beginTransaction();
	$stmtinsert = $user->db->prepare($sqlinsert);
	$params = array($idinvite,$iddossier,$nowsql,$user->id,$rightsinvite,'INVIT',$comment);
	$stmtinsert->execute($params);	

	//construction d'un tableau communs contenant les ids des compte en commun
	$communs = array();
	$k=0;
	for ($i=0; $i<$nbreamis;$i++) {
		if ($_GET['ami'.$i]!=0 && $_GET['ami'.$i]!="0") {		
			$communs[$k]=$_GET['ami'.$i];
			$k++;
			}
		}		
	if (count($communs)>0) {
		$sqlinsertcommun = "INSERT INTO pceausercommun (IdDossier,IdUser1,IdUser2)"
			." VALUES (:IdDossier,:IdUser1,:IdUser2)";
							
		$stmt = $user->db -> prepare($sqlinsertcommun);	
		
		$stmt->bindValue(':IdDossier',$iddossier);	
		$stmt->bindParam(':IdUser1',$iduser1);
		$stmt->bindParam(':IdUser2',$iduser2);	
		
		 for ($i=0; $i<count($communs); $i++) {
			
			$iduser1 = $communs[$i];
			$iduser2 = $idinvite;
			try {$stmt->execute();} 
			catch (POException $e) { die( "Erreur : " . $e->getMessage()); }
					 
			 $iduser1= $idinvite;
			 $iduser2 = $communs[$i];
			try {$stmt->execute();} 
			catch (POException $e) { die( "Erreur : " . $e->getMessage()); }
		}	
	} // fin de if communs>0			
$user->db -> commit();

$answer = '{"myid" : "'.$user->id.'"'
	.', "iddossier": "'.$iddossier.'" }';
			
echo($answer);

?>