<?php
include_once("config.php");

$iddossier = $_GET["iddossier"];		
$mailami = $_GET["mailami"];	
$myrights = $_GET["myrights"];

$mailexploded = explode("@",$mailami);

$namenewcontact = $mailexploded[0];

//vérifier que le mail entré est celui d'un utilisateur enregistré

$sqlcheck = 'SELECT C.Name AS NameContact,C.IdContact AS IdContact, D.Name AS NameDossier'
.' FROM contact as C'
.' LEFT JOIN dossier as D'		
.' ON D.IdDossier = ?' 
.' AND D.TypeDossier = ?'
.' WHERE EMail = ?';

$bdd = new DbConnection();
$user = new User($bdd);	
$req = $user->db -> select($sqlcheck,array($iddossier,"PCEA",$mailami));		
$result = $req->fetch();
	if (empty($result)) {	
		if (count($mailexploded)==2 && $mailexploded[1]!=""&& $mailexploded[0]!="") {	
			//créer un nouveau contact		
		$user->db -> beginTransaction();													
			//inserer dans contact 
			$sqlinsert = "INSERT INTO contact (IdContactRef,Name,EMail,Enabled)"
							." VALUES (?,?,?,?)";						
			$stmtinsert = $user->db->prepare($sqlinsert);			
			$params = array(0,$namenewcontact,$mailami,1);
			try {
			$stmtinsert->execute($params);
			} 
			catch (POException $e) { die( "Erreur : " . $e->getMessage()); }						
			$numId =  $user->db->lastInsertId();
			
			//inserer dans user, rn ayant généré un password				
			$psswd = "";	
			for($i = 0; $i<8;$i++)
				{
				$d = rand(1,3);
				$psswd .= ($d==1) ? chr(rand(49,57)) : chr(rand(97,122));
				}	
			$sqluser = "INSERT INTO user (IdUser,Enabled,Password)"
					." VALUES (?,1,PASSWORD(?))";
			$stmtuser = $user->db->prepare($sqluser);	
			try {
			$stmtuser->execute(array($numId,$psswd));
			} 
			catch (POException $e) { die( "Erreur : " . $e->getMessage()); }
		
			$sqlrights = "INSERT INTO rights (IdUser, Domain, Rights)"
					. " VALUES(?,?,?)";
			
			$stmt = $user->db->prepare($sqlrights);
			$params = array($numId,".",3);
				try {
					$stmt->execute($params);
				} 
				catch (PDOException $e) { die( "Erreur : " . $e->getMessage()); }
							
		$user->db -> commit();	
			
			$msg=  '{"isknownuser" : "no"'
				.' , "validmail" : "yes"'
				.' , "idcontact" : "'.$numId .'"'
				.' , "namecontact" : "' .$namenewcontact.'"'
				.' , "myrights" : "' .$myrights .'"'
				.' , "mymail" : "' .$user->email.'"'
				.' , "myname" : "' .$user->name.'"'
				.' , "mailcontact" : "' .$mailami.'"'
				.' , "password" : "'. $psswd.'"'
				.' , "iddossier" : "' .$iddossier .'"'
				.' , "myid" : "' .$user->id .'"'
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
			.' , "myid" : "' .$user->id .'"'
			.' , "mymail" : "' .$user->email.'"'
			.' , "myname" : "' .$user->name.'"'
			.' , "mailcontact" : "' .$mailami.'"'
			. '}';
		echo $msg;
		
	}
	
?>