<?php
include_once("cls_creances.php");
include_once("config.php");


$creances = new Creances($_GET["myid"],$_GET["iddossier"],$_GET["idami"]);
//$answerbase = $creances->serialize('json');
$jsocreancescomm = '{';
if (isset($_GET["NbCommuns"])&&$_GET["NbCommuns"]>0) {
	$NbCommuns = $_GET["NbCommuns"];
	
	for ($k=0;$k<$NbCommuns;$k++) {
		$commk = "comm" . $k;
		$creancescomm = new Creances($_GET[$commk]["IdComm"],$_GET["iddossier"],$_GET["idami"]);
		if ($k!=0) {$jsocreancescomm .=' , ';}
		$jsocreancescomm .= '"comm' . $k .'" : '. json_encode($creancescomm->lines, JSON_FORCE_OBJECT);	
		}	
	}
$jsocreancescomm .=' }';

$answer = '{'								
		. '"myId":"'.$_GET["myid"] .'"'
	. ' , "idDossier":"'.$_GET["iddossier"] .'"'
	. ' , "idAmi": "'.$_GET["idami"].'"' 
	. ' , "NbCommuns" : "'. $_GET["NbCommuns"].'"'
	. ' , "listecreancescommuns" :'.$jsocreancescomm
	. ' , "listecreances":' . json_encode($creances->lines, JSON_FORCE_OBJECT)
	
	. '}';

echo $answer;
?>
