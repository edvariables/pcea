<?php

include_once("config.php");

$user = new User();
$creances = $user -> dossiers("pcea")->item($_GET["iddossier"])->item($_GET["idami"]);

$answer = '{'								
	. '"myId":"'. $user->id .'"'
	. ' , "idDossier":"'.$_GET["iddossier"] .'"'
	. ' , "idAmi": "'.$_GET["idami"].'"' 
	. ' , "listecreances":' . json_encode($creances->lines, JSON_FORCE_OBJECT)	
	. '}';

echo $answer;
?>
