<?php
include_once("config.php");
session_start();

if (isset($_GET["iddossier"])) 
	{
	$iddossier = $_GET["iddossier"];
	
	$sqluser = "DELETE FROM pceauser WHERE IdUser = ? AND IdDossier = ?";	
	$sqlcommun1 = "DELETE FROM pceausercommun WHERE IdUser1 = ? AND IdDossier = ?";
	$sqlcommun2 = "DELETE FROM pceausercommun WHERE IdUser2 = ? AND IdDossier = ?";
	
	
	$dbconn = new DbConnection();
	$user = new User($dbconn);
	$params = array($user->id,$iddossier);
	
	$user->db->beginTransaction();
	
	$stmt = $user->db->prepare($sqluser);
	$stmt->execute($params);

	$stmt = $user->db->prepare($sqlcommun1);	
		$stmt->execute($params);

	$stmt = $user->db->prepare($sqlcommun2);	
	$stmt->execute($params);	
	$user->db->commit();
	
	$answer = '{ "myid" : "'.$user->id .'" }';
	
	echo $answer;
}

else {die("IdDossier inconnu dans decline invit");}	
	

?>