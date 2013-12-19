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

	$dbconn = new DbConnection();
	$user = new User($dbconn) ;
	
	$sqlusers = "DELETE FROM pceauser WHERE IdDossier = ?";	
	$sqlcommun = "DELETE FROM pceausercommun WHERE IdDossier = ?";	
	$sqllignes = "DELETE FROM lgdossier WHERE TypeDossier = 'PCEA' AND IdDossier = ?";	
	$sqldossier = "DELETE FROM dossier WHERE TypeDossier = 'PCEA' AND IdDossier = ?";
	
	$user->db->beginTransaction();
	
	$stmt = $user->db->prepare($sqlusers);
	$stmt->execute(array($iddossier));

	$stmt = $user->db->prepare($sqlcommun);	
	$stmt->execute(array($iddossier));
	
	$stmt = $user->db->prepare($sqllignes);	
	$stmt->execute(array($iddossier));
		
	$stmt = $user->db->prepare($sqldossier);	
	$stmt->execute(array($iddossier));				
	
	$user->db->commit();
	
	$answer = '{ "myid" : "'.$user->id .'" }';	
	echo $answer;
		}
	else {die("Droits non reconnus");}
	}
else {die("IdDossier inconnu dans delete dossier");}	
	

?>