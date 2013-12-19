<?php
include_once("config.php");
session_start();

if (isset($_GET["iddossier"])) 
	{
	$iddossier = $_GET["iddossier"];

	if (isset($_GET["myrights"])) 
	{
		$myrights = $_GET["myrights"];		
		if ($myrights < 15) {die("Vous n'avez pas les droits administrateur");};
		
		if (isset($_GET["idami"])) 
			{
			$idami = $_GET["idami"];
			} 
			else {die("IdAmi perdue");};
	
	$sqlusers = "DELETE FROM pceauser WHERE IdDossier = ? AND IdUser = ?";	
	$sqlcommun = "DELETE FROM pceausercommun WHERE (IdUser1 = ? OR IdUser2 = ?) AND IdDossier = ?";	
	$sqllignes = "DELETE FROM lgdossier WHERE TypeDossier = ? AND IdDossier = ? AND (IdContact = ? OR IdArticle = ?)";
	
	$dbconn = new DbConnection();
	$user= new User($dbconn);
	
	$user->db->beginTransaction();	
		$stmt = $user->db->prepare($sqlusers);
		$stmt->execute(array($iddossier, $idami));

	$stmt = $user->db->prepare($sqlcommun);	
	$stmt->execute(array($idami,$idami,$iddossier));
		
	$stmt = $user->db->prepare($sqllignes);	
	$stmt->execute(array("PCEA",$iddossier,$idami,$idami));

	$user->db->commit();
	
	$answer = '{ "myid" : "'.$user->id .'", "iddossier" : "'.$iddossier.'" }';
	
	echo $answer;
		}
	else {die("Droits non reconnus");}
	}	
else {die("IdDossier inconnu dans delete ami");}	
	

?>