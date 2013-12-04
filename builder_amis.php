<?php

include_once("cls_amis.php");

$amis = new Amis($_GET["lgduser"],$_GET["idDossier"],$_GET["myRights"]);

$amisjson = $amis->serialize('json');

echo $amisjson;
?>