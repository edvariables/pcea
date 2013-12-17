
<?php

include_once("config.php");
session_start();

if (isset($_GET["namedossier"])) {
	$namedossier = $_GET["namedossier"];}
	
if (isset($_GET["iddossier"])) {
		$iddossier = $_GET["iddossier"];		
		if (isset($_GET["myrights"])) { 		
			$myrights = $_GET["myrights"]; 
			
			//requête sql pour récupérer la liste des amis sur ce compte et les infos de  myid
			//connection bdd
		
			$bdd = new DbConnection();
			$user = new User( $bdd );

			$sql = 'SELECT  D.IdDossier,P.Status AS AmiStatus,P.Rights AS AmiRights, C.IdContact, C.Name AS CName, C.EMail, P.Comment AS CommentUser' .
				' FROM pceauser P' .			
					' INNER JOIN contact C' .
						' ON P.IdUser = C.IdContact' .		
					' INNER JOIN dossier D' .
						' ON P.IdDossier = D.IdDossier' .
						' AND D.TypeDossier = ?' .			
				' WHERE P.IdDossier = ? ' .
				' GROUP BY D.IdDossier, P.Status, P.Rights, C.IdContact, C.Name, C.EMail'
				;
			$req = $user->db -> select($sql, array( "PCEA", $iddossier ));	
						
			$tableauidname = array();
			$k=0;
				
			while ($donnees = $req -> fetch()) {				
				if ($donnees["IdContact"] == $user -> id) 
					$jsomyinfo = $donnees;
				else 
					$tableauidname[$donnees["IdContact"]] = $donnees["CName"];
				$k++;
			}
		
			$k--;
		
		$answer =  '{'
			. '"myrights":"'.$jsomyinfo["AmiRights"] .'"'
			. ' , "myid":"'.$user -> id .'"'
			. ' , "iddossier":"'.$iddossier .'"'
			. ' , "namedossier":"'.$namedossier .'"'
			. ' , "nbreAmis": '. $k
			. ' , "jsomyinfo": '. json_encode($jsomyinfo, JSON_FORCE_OBJECT)
			. ' , "tableauidname":' . json_encode($tableauidname, JSON_FORCE_OBJECT)
			. '}';
		echo  $answer;
	}
}
?>