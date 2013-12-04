
<?php

include_once("config.php");

session_start();
if($_SESSION["loggeduser"]) {
	$myid = $_SESSION["loggeduser"]["iduser"];
}


$iddossier = $_GET["iddossier"];
$idinvite = $_GET["idinvite"];
$rightsinvite = $_GET["droits"];
$comment = $_GET["comment"];
$myrights = $_GET["myrights"];

date_default_timezone_set('Europe/Paris');
$nowsql = date('Y-m-d H:i:s');
// ouverture bdd

try	{
	$bddcoopeshop = new PDO('mysql:host='.$db_server.';dbname='.$db_name, $db_user, $db_password);
		}
catch (Exception $e)
		{
	        die('Erreur : ' . $e->getMessage());
		}	
//inserer dans PCEAuser	 
$sqlinsert = "INSERT INTO pceauser (IdUser,IdDossier,CreationDate,CreationIdUser,Rights,Status,Comment)"
				." VALUES (?,?,?,?,?,?,?)";

$bddcoopeshop -> beginTransaction();

$stmtinsert = $bddcoopeshop->prepare($sqlinsert);

$params = array($idinvite,$iddossier,$nowsql,$myid,$rightsinvite,'INVIT',$comment);
try {
$stmtinsert->execute($params);
} 
catch (POException $e) { die( "Erreur : " . $e->getMessage()); }

		
if (isset($_GET['totalpresents'])) 

{	$nbreamis = $_GET['totalpresents'];

	//construction d'un tableau communs contenant les ids des compte en commun
	$communs = array();
	$k=0;
	for ($i=0; $i<$nbreamis;$i++) {
		if ($_GET['ami'.$i]!=0 && $_GET['ami'.$i]!="0") {		
			$communs[$k]=$_GET['ami'.$i];
			$k++;
			}
		}
	 

// inserer dans pceausercommun

	$sqlinsertcommun = "INSERT INTO pceausercommun (IdDossier,IdUser1,IdUser2)"
		." VALUES (:IdDossier,:IdUser1,:IdUser2)";
						
	$stmt = $bddcoopeshop -> prepare($sqlinsertcommun);	
	
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
} // fin de if checkcommuns
		
	
$bddcoopeshop -> commit();

$answer = '{"myid" : "'.$myid.'"'
	.', "iddossier": "'.$iddossier.'" }';
			
echo($answer);

?>