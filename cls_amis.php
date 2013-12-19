<?php 
include_once("config.php");

class Amis {
	private $myid;
	private $iddossier;
	private $myrights;
	
	public $items = array(); // (Entêtes à revoir) Tableau ([IdDossier]=>String, [EMail]=> ami@hgfh, [Solde]=>xxxØ) [IsCommun]=>Array([comm0]=>IdContactCommun1,...)
				//				[Lignes] => Array ((A VOIR ds cls_lignescompte)) [IdContact]=>id, ...)
	
	public $nbreamis;
	
	public $communs = array();
		
	function __construct($lgdusr,$idd,$lgdrights,$comms) {
			
		$this->myid = $lgdusr;
		$this->iddossier = $idd;
		$this->myrights = $lgdrights;
		$this->communs = $comms;
		//requête sql pour récupérer la liste des amis sur ce compte et leur solde
			//connection bdd
		try	{
			$bdd = new DbConnection();
			}
		catch (Exception $e)
			{
		        die('Erreur : ' . $e->getMessage());
			}
			//préparation requête
		$sql = 'SELECT  D.IdDossier,P.Status AS AmiStatus,P.Rights AS AmiRights, C.IdContact, C.Name AS CName, C.EMail, P.Comment AS CommentUser' .
			', IFNULL(-1*SUM(LGD.Price),0) AS Solde, IFNULL(MAX(PUC.IdUser), 0) AS IsCommunMax, IFNULL(MIN(PUC.IdUser), 0) AS IsCommunMin' .		
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
			' WHERE P.IdDossier = ?' .
			' AND P.IdUser <> ?' .				
			' GROUP BY D.IdDossier, P.Status, P.Rights, C.IdContact, C.Name, C.EMail'
			;
		//echo $sql;	
			
		$req = $bdd -> select( $sql, array($this->iddossier, $this->myid ));
		
		$this->nbreamis = 0;
		
		while ($donnees = $req -> fetch()) {
		
			$aminumber = "ami".$this->nbreamis;
			$this->items[$aminumber] = $donnees;
			$this->nbreamis ++;
			  
		}
		
		$i = 0; //index de row dans la boucle qui suit
		foreach ($this->items as $row) {
		
			//  établit la liste des users en compte commun avec  IdContact et remplit un tableau indexé de ces contacts
			$row["IsCommun"]=array();
			$nbrecommun = 0;
			if ($row["IsCommunMin"] != $row["IsCommunMax"]) {
				$sqltest = 'SELECT IdUser2' .
					' FROM pceausercommun' .
					' WHERE IdDossier = ?' . 
					' AND IdUser1 = ?' . 
					' AND IdUser2 <> ?';
											
				$reqtest = $bdd -> select($sqltest, array($row["IdDossier"], $row["IdContact"], $row["IdContact"]));
				
				while ($listecomm = $reqtest -> fetch()) {				
					$row["IsCommun"]["com".$nbrecommun] = $listecomm["IdUser2"];
					$nbrecommun ++;					 
				}
																 
			}
			$this->items["ami".$i]["nbreCommun"] = $nbrecommun;
			$this->items["ami".$i]["isCommun"] = $row["IsCommun"];
			
			$i++;	
		}				
	}
	
	public function item($idami) {
		$found = false;
		$amidata;
		foreach ($this->items as $key=>$value) {
			if ($value["IdContact"] == $idami) {
				$amidata = $value;
				$found = true;	
				break;
			}
		}
		
		if ($found)
			return new Creances($this->myid, $this->iddossier, $idami, $this->communs);
			
		else return $found;
	}
		
	public function serialize($format){
				   	
		if (!isset($format)) {$format = 'json';}  
		if ($format=='json') {
			if (!isset($this->amiexpanded)) $this->amiexpanded = 0;
			$json = '{'
				. '"myRights":"'.$this->myrights .'"'
				. ' , "myId":"'.$this->myid .'"'
				. ' , "amiexpanded":"'.$this->amiexpanded .'"'
				. ' , "idDossier":"'.$this->iddossier .'"'
				. ' , "nbreAmis": '.$this->nbreamis 
				. ' , "listeamis":' . json_encode($this->items, JSON_FORCE_OBJECT)
				. '}';						
			return $json;
		}	
	}
			
}


?>