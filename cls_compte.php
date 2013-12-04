<?php 

class Compte {
	private $myid;
	private $iddossier;
	
	public $amis = array(); // (Entêtes à revoir) Tableau ([NameDossier]=>String, [EMail]=> ami@hgfh, [Solde]=>xxxØ) [IsCommun]=>Array([0]=>IdContactCommun1,...)
				//				[Lignes] => Array ((A VOIR ds cls_lignescompte)) [IdContact]=>id, ...)
	
	public $lignescpte;
	
		
	function __construct($lgdusr,$idd) {
			
		$this->myid = $lgdusr;
		$this->iddossier = $idd;
		
		
		//requête sql pour récupérer la liste des amis sur ce compte et leur solde
			//connection bdd
		try	{
			$bddcoopeshop = new PDO('mysql:host='.$db_server.';dbname='.$db_name, $db_user, $db_password);
			}
		catch (Exception $e)
			{
		        die('Erreur : ' . $e->getMessage());
			}
			//préparation requête
		$sql = 'SELECT  D.Name AS NameDossier, D.IdDossier, C.IdContact, C.Name AS CName, C.EMail, LGD.IdArticle, P.IdUser' .
			',-1*SUM(LGD.Price) AS Solde, MAX(PUC.IdUser) AS IsCommunMax, MIN(PUC.IdUser) AS IsCommunMin' .
			' FROM pceauser P' .			
				' INNER JOIN contact C' .
					' ON P.IdUser = C.IdContact' .		
				' INNER JOIN dossier D' .
					' ON P.IdDossier = D.IdDossier' .
					' AND D.TypeDossier = "PCEA"' .
				' LEFT JOIN pceausers PUC' .
					' ON PUC.IdDossier = D.IdDossier' .
					' AND PUC.IdUserC = P.IdUser' .
				' LEFT JOIN lgdossier AS LGD' .
					' ON LGD.IdDossier = P.IdDossier' .
					' AND LGD.TypeDossier = "PCEA"' .
					' AND LGD.IdArticle = PUC.IdUser' . 			
			' WHERE P.IdDossier = ' . $this->iddossier .
			' AND P.IdUser <> ' . $this->myid .				
			' GROUP BY P.IdUser, D.Name, D.IdDossier, C.IdContact, C.Name, C.EMail, LGD.IdArticle, P.IdUser '
			;
	//	echo $sql;	
			
		$req = $bddcoopeshop -> prepare($sql);
		$req->execute();
		while ($donnees = $req -> fetch(PDO::FETCH_ASSOC)) {
				 	array_push($this->amis,$donnees);						 
					 }
					 
		$i=0; //index de row dans la boucle qui suit
		foreach ($this->amis as $row) {
			/* Première version : établit si IdContact est en compte commun avec myId et affecte un boolean.
			$row["IsCommun"]=(bool) FALSE;
						if ($row["IsCommunMin"]<>$row["IsCommunMax"]) {
							if (($row["IsCommunMin"] == $this->myid) || ($row["IsCommunMax"] == $this->myid)) {
								$row["IsCommun"]=(bool) TRUE;
									}
							else {
								$sqltest = 'SELECT *' .
									' FROM pceausercommun' .
									' WHERE IdDossier = ' . $row["IdDossier"] .
									' AND IdUser1 = ' . $row["IdContact"] .
									' AND IdUser2 = ' . $this->myid;
								$reqtest = $bddcoopeshop -> prepare($sqltest);
								$reqtest->execute();
								if ($restest = $reqtest->fetch()) {
									$row["IsCommun"]=(bool) TRUE;
									}		
								}
							}*/
			// Seconde version : établit la liste des users en compte commun avec  IdContact et remplit un tableau indexé de ces contacts
			$row["IsCommun"]=array();
				if ($row["IsCommunMin"]<>$row["IsCommunMax"]) {
					$sqltest = 'SELECT IdUser2' .
						' FROM pceausercommun' .
						' WHERE IdDossier = ' . $row["IdDossier"] .
						' AND IdUser1 = ' . $row["IdContact"] .
						' AND IdUser2 <> ' .  $row["IdContact"];
												
					$reqtest = $bddcoopeshop -> prepare($sqltest);
					$reqtest->execute();

					while ($listecomm = $reqtest -> fetch(PDO::FETCH_ASSOC)) {
						array_push($row["IsCommun"],$listecomm["IdUser2"]);					 
										 }
					}
				
			$this->amis[$i]["IsCommun"]=$row["IsCommun"];
				
		//	$lignescpte = new LignesCompte($this->myid,$row["IdDossier"],$row["IdContact"]);
			
		//	$this->amis[$i]["Lignes"] = $lignescpte->lines;
			
			$i++;	
			}
				
		print_r($this->amis);	
		}

}


?>