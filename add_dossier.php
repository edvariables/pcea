<?php

include_once("config.php");
session_start();
if($_SESSION["loggeduser"]) {
	$myid = $_SESSION["loggeduser"]["iduser"];
}

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

// ouverture bdd 

try	{
	$bddcoopeshop = new PDO('mysql:host='.$db_server.';dbname='.$db_name, $db_user, $db_password);
		}
catch (Exception $e)
		{
	        die('Erreur : ' . $e->getMessage());
		}
//Requete pour déterminer le numero de dossier

$sql = "SELECT IFNULL(MAX(IdDossier),0) AS numLastDossier"
	." FROM dossier"
	." WHERE TypeDossier = 'PCEA'"
	." AND DateDossier BETWEEN '". $todaymorning . "' AND '" . $todaynight."'";

$numstmt = $bddcoopeshop->prepare($sql);

$numstmt->execute();

$result = $numstmt->fetch(PDO::FETCH_ASSOC);

if ($result["numLastDossier"] && $result["numLastDossier"]!= 0) {
	$newnum = $result["numLastDossier"] + 1;	
	 }
else {
	$newnum = date("ymd"). "001";
	}
//echo var_dump($sql);
//echo var_dump($result);

// Requete pour inserer dans table dossier 

$sqlinsert = "INSERT INTO dossier (IdContact,IdDossier,TypeDossier,DateDossier,Name,Status,Comment)"
			." VALUES (?,?,?,?,?,?,?)";

$bddcoopeshop->beginTransaction();
$stmt = $bddcoopeshop->prepare($sqlinsert);
$params = Array($myid,$newnum,'PCEA',$nowsql,$nmdossier,'OK',$commentdossier);
try 
{
$stmt->execute($params);
} 
catch (POException $e) { die( "Erreur : " . $e->getMessage()); }

//DEBUG
//echo var_dump($sqlinsert);
//echo var_dump($stmt);

	
// Requete pour inserer dans table pceauser
$sqlpcea = "INSERT INTO pceauser (IdUser,IdDossier,CreationDate,CreationIdUser,Rights,Status,Comment)"
			." VALUES (?,?,?,?,?,?,?)";
$stmt = $bddcoopeshop->prepare($sqlpcea);
$params = Array($myid,$newnum,$nowsql,$myid,$rights,'OK',$commentuser);
try 
{
$stmt->execute($params);
} 
catch (POException $e) { die( "Erreur : " . $e->getMessage()); }

//DEBUG
//echo var_dump($sqlpcea);
//echo var_dump($stmt);


$bddcoopeshop->commit();

$stranswer =  '{ "myid" : "'. $myid.'"'
		.', "iddossier" :"'. $newnum.'"}';
echo $stranswer;

?>