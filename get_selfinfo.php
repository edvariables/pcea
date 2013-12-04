
<?php

include_once("config.php");

session_start();
if(isset($_SESSION["loggeduser"]) && (isset($_GET["myid"]))&& ($_GET["myid"]==$_SESSION["loggeduser"]["iduser"])) {
	$myid = $_SESSION["loggeduser"]["iduser"];}
else {die("Problème de connection : perte de l'iduser...");}

if (isset($_GET["namedossier"])) {
	$namedossier = $_GET["namedossier"];}
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




	//requête sql pour récupérer la liste des amis sur ce compte et les infos de  myid
			//connection bdd
	
	
	
	
	
		try	{
			$bddcoopeshop = new PDO('mysql:host='.$db_server.';dbname='.$db_name, $db_user, $db_password);
			}
		catch (Exception $e)
			{
		        die('Erreur : ' . $e->getMessage());
			}
			//préparation requête
		$sql = 'SELECT  D.IdDossier,P.Status AS AmiStatus,P.Rights AS AmiRights, C.IdContact, C.Name AS CName, C.EMail, P.Comment AS CommentUser' .
			' FROM pceauser P' .			
				' INNER JOIN contact C' .
					' ON P.IdUser = C.IdContact' .		
				' INNER JOIN dossier D' .
					' ON P.IdDossier = D.IdDossier' .
					' AND D.TypeDossier = "PCEA"' .			
			' WHERE P.IdDossier = ' . $iddossier .
	//		' AND P.IdUser <> ' . $this->myid .				
			' GROUP BY D.IdDossier, P.Status, P.Rights, C.IdContact, C.Name, C.EMail'
			;
	//	echo $sql;

		$req = $bddcoopeshop -> prepare($sql);
			$req->execute();
			
		$tableauidname = array();
		$k=0;
			
		while ($donnees = $req -> fetch(PDO::FETCH_ASSOC)) {
			
			if ($donnees["IdContact"] == $myid) {
				$jsomyinfo = $donnees;		
				}
			else {$tableauidname[$donnees["IdContact"]]=$donnees["CName"];}
			$k++;
		}
	
	$k--;
	
	$answer =  '{'
		. '"myrights":"'.$myrights .'"'
		. ' , "myid":"'.$myid .'"'
		. ' , "iddossier":"'.$iddossier .'"'
		. ' , "namedossier":"'.$namedossier .'"'
		. ' , "nbreAmis": '. $k
		. ' , "jsomyinfo": '. json_encode($jsomyinfo, JSON_FORCE_OBJECT)
		. ' , "tableauidname":' . json_encode($tableauidname, JSON_FORCE_OBJECT)
		. '}';
	echo  $answer;
		}
}
?>