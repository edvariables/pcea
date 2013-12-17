<?php
include_once("config.php");

$user = new User($_GET["lgduser"]);
$amis = $user -> dossiers("pcea")->item($_GET["idDossier"]);

//$amis = new Amis($_GET["lgduser"],$_GET["idDossier"],$_GET["myRights"],null);
if (isset($_GET["amiexpanded"])) $amis->amiexpanded=$_GET["amiexpanded"];
$amisjson = $amis->serialize('json');

echo $amisjson;
?>