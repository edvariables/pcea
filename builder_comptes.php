

<?php
include_once("config.php");
session_start();

$user = new User();
$comptes = $user -> dossiers("pcea");


$cptesjson = $comptes->serialize('json');

if (isset($_GET["iddossier"])) { //lorsqu'on rafraichit avec un dossier ouvert
	$data = json_decode($cptesjson, true);	
	$data["cpteselected"] = $_GET["iddossier"];
	if (isset($_GET["amiexpanded"])) {
		$data["amiexpanded"] = $_GET["amiexpanded"];
	}
	$cptesjson = json_encode($data);
}	
echo $cptesjson;
?>
