<?php
include_once("config.php");



	try	{
		$bddcoopeshop = new PDO('mysql:host='.$db_server.';dbname='.$db_name, $db_user, $db_password);
		}
	catch (Exception $e)
		{
	        die('Erreur : ' . $e->getMessage());
		}

		$myid = $_GET["myid"];
		$iddossier = $_GET["iddossier"];		
		$mailami = $_GET["mailami"];	
		$myrights = $_GET["myrights"];
		
	$mailexploded = explode("@",$mailami);
		
	$namenewcontact = $mailexploded[0];
		
	//vérifier que le mail entré est celui d'un utilisateur enregistré
		
	$sqlcheck = 'SELECT C.Name AS NameContact,C.IdContact AS IdContact, D.Name AS NameDossier'
		.' FROM contact as C'
		.' LEFT JOIN dossier as D'		
		.' ON D.IdDossier=' . $iddossier 
		.' AND D.TypeDossier="PCEA"'
		.' WHERE EMail="'.$mailami.'"';
		
	$bddcoopeshop -> beginTransaction();	
	$req = $bddcoopeshop -> prepare($sqlcheck);
			$req->execute();
				
	$result = $req->fetch(PDO::FETCH_ASSOC);
	
	$bddcoopeshop -> commit();
	
	if (empty($result)) {
		
		
		if (isset($mailexploded[1])&&count($mailexploded)==2) {	//à approfondir pour tester adresse mail valide ?
		
			//créer un nouveau contact
			
			$bddcoopeshop -> beginTransaction();
			
			//déterminer le numero de ligne	
					
			$sqlnumid = "SELECT IFNULL(MAX(IdContact),0)+1 AS numId"
				." FROM contact"
				;		
			$req = $bddcoopeshop -> prepare($sqlnumid);
					$req->execute();		
			$rslt = $req->fetch(PDO::FETCH_ASSOC);
			
			$numId = $rslt["numId"];
				
			//inserer dans contact 
			$sqlinsert = "INSERT INTO contact (IdContact,IdContactRef,Name,EMail,Enabled)"
							." VALUES (?,?,?,?,?)";
			
			
			$stmtinsert = $bddcoopeshop->prepare($sqlinsert);
			
			$params = array($numId,$numId,$namenewcontact,$mailami,1);
			try {
			$stmtinsert->execute($params);
			} 
			catch (POException $e) { die( "Erreur : " . $e->getMessage()); }
			
			
			//get myinfo
			$sqlmymail = 'SELECT C.EMail AS mymail, C.Name AS myname'
					.' FROM contact as C'
					.' WHERE IdContact='.$myid;
			$stmtmail =  $bddcoopeshop->prepare($sqlmymail);
			try {
				$stmtmail->execute();
				} 
			catch (POException $e) { die( "Erreur : " . $e->getMessage()); }
			
			$res = $stmtmail->fetch(PDO::FETCH_ASSOC);
			
			
			
			$bddcoopeshop -> commit();	
			
			$msg=  '{"isknownuser" : "no"'
				.' , "validmail" : "yes"'
				.' , "idcontact" : "'.$numId .'"'
				.' , "namecontact" : "' .$namenewcontact.'"'
				.' , "myrights" : "' .$myrights .'"'
				.' , "mymail" : "' .$res["mymail"].'"'
				.' , "myname" : "' .$res["myname"].'"'
				.' , "mailcontact" : "' .$mailami.'"'
				.' , "iddossier" : "' .$iddossier .'"'
				.' , "myid" : "' .$myid .'"'
				. '}';
			echo $msg;
	
			} // fin de if mailami est une adresse mail valide
		
		else {
		
			$msg=  '{"isknownuser" : "no"'
				.' , "validmail" : "no"'
				. '}';	
			echo $msg;
	
		}
		
		}

	else {
		$msg= '{"isknownuser" : "yes"'
			.' , "idcontact" : "' .$result["IdContact"] .'"'
			.' , "namecontact" : "' .$result["NameContact"] .'"'
			.' , "namedossier" : "' .$result["NameDossier"] .'"'
			.' , "myrights" : "' .$myrights .'"'
			.' , "iddossier" : "' .$iddossier .'"'
			.' , "myid" : "' .$myid .'"'
			. '}';
		echo $msg;
		
	}
	
?>