<?php

include_once("config.php");

session_start();

$iddossier = $_GET["iddossier"];	
$myrights = $_GET["myrights"];
	
$dbconn = new DbConnection();
$user= new User($dbconn);	
//changer Status en 'OK' dans pceasuer 
$sqlupdate = "UPDATE pceauser "  
 		." SET Status = 'OK'" 
		." WHERE IdUser = ?"
		." AND IdDossier = ?";
		
$req = $user->db->prepare($sqlupdate);
$req->execute(array($user->id,$iddossier));

echo '{ "myid" : "'.$user->id.'" , "iddossier" : "' . $iddossier .'" , "myrights" : "' . $myrights .'" }';

?>