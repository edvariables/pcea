

<?php
include_once("config.php");

include_once("cls_comptes.php");

session_start();
if (isset($_GET["lgduser"])&&$_GET["lgduser"]==$_SESSION["loggeduser"]["iduser"])
{
	$myid = $_GET["lgduser"];

	$comptes = new Comptes($_GET["lgduser"]);
	$cptesjson = $comptes->serialize('json');
	
	if (isset($_GET["iddossier"])) { //lorsqu'on rafraichit avec un dossier ouvert
	
//aller chercher myrights sur bdd
	
		$idd = $_GET["iddossier"];
		$myid = $_GET["lgduser"];
		
		$data = json_decode($cptesjson,true);
		
		
		
		$sql = "SELECT Rights AS myRights" 
			." FROM pceauser"
			." WHERE IdDossier='".$idd."'"
			." AND IdUser='". $myid."'";
	
		// ouverture bdd
	
		try	{
		$bddcoopeshop = new PDO('mysql:host='.$db_server.';dbname='.$db_name, $db_user, $db_password);
			}
		catch (Exception $e)
			{
		        die('Erreur : ' . $e->getMessage());
			}
	
		$req = $bddcoopeshop->prepare($sql);
		$req->execute();
		

	$rightsarray = $req->fetch(PDO::FETCH_ASSOC);


		if ($rightsarray = $req->fetch(PDO::FETCH_ASSOC))

			{
			
			$data["rightsforselected"] = $rightsarray["myRights"];
					
			}
		
		$data["cpteselected"]=$idd;	
		$cptesjson = json_encode($data);
		}
		
	echo $cptesjson;
}
else {echo var_dump($_SESSION["loggeduser"]);
	echo var_dump($_GET["lgduser"]);
	}
?>
