<?php 
include_once("config.php");
class Amis {
	private $myid;
	private $iddossier;
	
	public $amis = array(); // (Entêtes à revoir) Tableau ([IdDossier]=>String, [EMail]=> ami@hgfh, [Solde]=>xxxØ) [IsCommun]=>Array([comm0]=>IdContactCommun1,...)
				//				[Lignes] => Array ((A VOIR ds cls_lignescompte)) [IdContact]=>id, ...)
	
	public $nbreamis;
	
	public $lignescpte;
	
		
	function __construct($lgdusr,$idd,$lgdrights) {
			
		$this->myid = $lgdusr;
		$this->iddossier = $idd;
		$this->myrights = $lgdrights;
		
		//requête sql pour récupérer la liste des amis sur ce compte et leur solde
			//connection bdd
		try	{
			$bddcoopeshop =new PDO('mysql:host='.$GLOBALS['db_server'].';dbname='.$GLOBALS['db_name'], $GLOBALS['db_user'], $GLOBALS['db_password']);
			}
		catch (Exception $e)
			{
		        die('Erreur : ' . $e->getMessage());
			}
			//préparation requête
		$sql = 'SELECT  D.IdDossier,P.Status AS AmiStatus,P.Rights AS AmiRights, C.IdContact, C.Name AS CName, C.EMail, P.Comment AS CommentUser' .
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
			' GROUP BY D.IdDossier, P.Status, P.Rights, C.IdContact, C.Name, C.EMail'
			;
	//	echo $sql;	
			
		$req = $bddcoopeshop -> prepare($sql);
		$req->execute();
		
		$this->nbreamis = 0;
		
		while ($donnees = $req -> fetch(PDO::FETCH_ASSOC)) {
		
					$aminumber = "ami".$this->nbreamis;
				 	$this->amis[$aminumber]=$donnees;
					 $this->nbreamis ++;
					  
					 }
					 
		$i=0; //index de row dans la boucle qui suit
		foreach ($this->amis as $row) {
		
			//  établit la liste des users en compte commun avec  IdContact et remplit un tableau indexé de ces contacts
			$row["IsCommun"]=array();
			$nbrecommun = 0;
				if ($row["IsCommunMin"]<>$row["IsCommunMax"]) {
					$sqltest = 'SELECT IdUser2' .
						' FROM pceausercommun' .
						' WHERE IdDossier = ' . $row["IdDossier"] .
						' AND IdUser1 = ' . $row["IdContact"] .
						' AND IdUser2 <> ' .  $row["IdContact"];
												
					$reqtest = $bddcoopeshop -> prepare($sqltest);
					$reqtest->execute();
					
					while ($listecomm = $reqtest -> fetch(PDO::FETCH_ASSOC)) {				
						$row["IsCommun"]["com".$nbrecommun]=$listecomm["IdUser2"];
						$nbrecommun ++;					 
						}
									 									 
					}
			$this->amis["ami".$i]["nbreCommun"] = $nbrecommun;
			$this->amis["ami".$i]["isCommun"]=$row["IsCommun"];
				
			
			$i++;	
			}				
		}
	public function serialize($format){
				   	
					if (!isset($format)) {$format = 'json';}  
					if ($format=='json') {
						$json = '{'
							. '"myRights":"'.$this->myrights .'"'
							. ' , "myId":"'.$this->myid .'"'
							. ' , "idDossier":"'.$this->iddossier .'"'
							. ' , "nbreAmis": '.$this->nbreamis 
							. ' , "listeamis":' . json_encode($this->amis, JSON_FORCE_OBJECT)
							. '}';						
						return $json;
						}	
					}		
}


?>