<?php

include_once("config.php");
session_start();

if (isset($_GET["namedossier"]) && $_GET["namedossier"] != "")
	 {$nmdossier = $_GET["namedossier"];}
else 	{$nmdossier = "Nouveau dossier du ". date("d-m-Y");}

if (isset($_GET["commentuser"])) {$commentuser = $_GET["commentuser"];} else {$commentuser = "";}
if (isset($_GET["commentdossier"])) {$commentdossier = $_GET["commentdossier"];} else {$commentdossier = "";}
if (isset($_GET["rights"])) {$rights = $_GET["rights"];} else {$rights = 15;}

date_default_timezone_set('Europe/Paris');
$todaysql = date("Y-m-d");
$todaymorning = $todaysql . " 00:00:00";
$todaynight = $todaysql . " 23:59:59";
$nowsql = date('Y-m-d H:i:s');
$dbconn = new DbConnection();
$user = new User($dbconn);
$user->db->beginTransaction();		
			
	//Requete pour déterminer le numero de dossier
	$sql = "SELECT IFNULL(MAX(IdDossier),0) AS numLastDossier"
		." FROM dossier"
		." WHERE TypeDossier = 'PCEA'"
		." AND DateDossier BETWEEN '". $todaymorning . "' AND '" . $todaynight."'";
	
	$numstmt = $user->db->select($sql);		
	$result = $numstmt->fetch();
	
	if ($result["numLastDossier"] && $result["numLastDossier"]!= 0) {
		$newnum = $result["numLastDossier"] + 1;	
		 }
	else {
		$newnum = date("ymd"). "001";
		}

	// Requete pour inserer dans table dossier 	
	$sqlinsert = "INSERT INTO dossier (IdContact,IdDossier,TypeDossier,DateDossier,Name,Status,Comment)"
				." VALUES (?,?,?,?,?,?,?)";
		
	$stmt = $user->db->prepare($sqlinsert);
	$params = Array($user->id,$newnum,'PCEA',$nowsql,$nmdossier,'OK',$commentdossier);
	try 
	{
	$stmt->execute($params);
	} 
	catch (POException $e) { die( "Erreur : " . $e->getMessage()); }
		
	// Requete pour inserer dans table pceauser
	$sqlpcea = "INSERT INTO pceauser (IdUser,IdDossier,CreationDate,CreationIdUser,Rights,Status,Comment)"
				." VALUES (?,?,?,?,?,?,?)";
	$stmt = $user->db->prepare($sqlpcea);
	$params = Array($user->id,$newnum,$nowsql,$user->id,$rights,'OK',$commentuser);
	try 
	{
	$stmt->execute($params);
	} 
	catch (POException $e) { die( "Erreur : " . $e->getMessage()); }

$user->db->commit();

$stranswer =  '{ "myid" : "'. $user->id.'"'
		.', "iddossier" :"'. $newnum.'"}';
echo $stranswer;

?>