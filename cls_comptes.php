<?php 

	include_once('config.php');
	
class Comptes {
			
	private $loggeduser;
	public $listecomptes = array(); //Tableau de tableaux ([IdDossier]=>xxx,[Name]=>NomDossier,[Status]=>StringDeStatut ...)
	private $nbrecomptes = 0;
	
	
	var $db_server;
	var $db_name;		
	var $db_user;	
	var $db_password;
		
	
	
	function __construct($lgdusr) {

			//connection bdd
		try	{
			$bddcoopeshop = new PDO('mysql:host='.$GLOBALS['db_server'].';dbname='.$GLOBALS['db_name'], $GLOBALS['db_user'], $GLOBALS['db_password']);
			}
		catch (Exception $e)
			{
		        die('Erreur : ' . $e->getMessage());
			}
			
		$this->loggeduser = $lgdusr;	
			
			
			
			//préparation requête
		$sql = "SELECT D.IdDossier, D.Status, D.Name, D.Comment" .
			", P.Status AS PCEAStatus, S.Label AS PCEAStatusLabel, P.Rights" .
			", A2.NbAmis" .
			",SUM(LGD.Price) AS Solde" .
			" FROM pceauser P" .
			" INNER JOIN dossier D" .
				" ON P.IdDossier = D.IdDossier" .
				" AND D.TypeDossier = 'PCEA'" .
			" INNER JOIN parameter S" .
				" ON S.Domain = 'PCEA.USER.STATUS'" .			
				" AND P.Status = S.IdParam" .
			" INNER JOIN (SELECT IdDossier, COUNT(IdUser) AS NbAmis FROM pceauser A" .
				" GROUP BY A.IdDossier) AS A2" .
				" ON P.IdDossier = A2.IdDossier" .
			" LEFT JOIN pceausers PUC" .
				" ON PUC.IdDossier = D.IdDossier" .
				" AND PUC.IdUserC = P.IdUser" .				
			" LEFT JOIN lgdossier LGD" .
				" ON LGD.IdDossier = D.IdDossier" .
				" AND LGD.TypeDossier = D.TypeDossier" .
				" AND LGD.IdContact = PUC.IdUser" .
			" WHERE P.IdUser = " . $this->loggeduser .
			" AND P.Rights > 0" .
			" GROUP BY D.IdDossier"
			.", D.Name, D.Status, D.Comment, P.Status, P.Rights, S.Label, A2.NbAmis "		
			;
	
		$bddcoopeshop -> beginTransaction();
	
	
		$req = $bddcoopeshop -> prepare($sql);
		$req->execute();
		
		while ($donnees = $req -> fetch(PDO::FETCH_ASSOC)) {
			$comptenumber = "cpte".$this->nbrecomptes;
		 	$this->listecomptes[$comptenumber]=$donnees;
			 
	//déterminer les amis en compte communs dans ce compte		 
			 
			 $iddoss = $this->listecomptes[$comptenumber]["IdDossier"];
			 
			 
			 $sqlcommun = 'SELECT PUC.IdUser2 AS IdComm, C.Name AS NameComm' .
				' FROM pceausercommun AS PUC' .
				' INNER JOIN contact AS C' .
				' ON C.IdContact = PUC.IdUser2' .
				' WHERE IdDossier = "' . $iddoss .'"'.
				' AND IdUser1 = ' . $lgdusr .
				' AND IdUser2 <> ' .  $lgdusr;
			 
			 $rq = $bddcoopeshop -> prepare($sqlcommun);
			 		$rq->execute();
			 
			 $nbrecommuns = 0;
			 while ($commun = $rq -> fetch(PDO::FETCH_ASSOC)) {
			 			$strkey = "comm".$nbrecommuns;
			 		 	$this->listecomptes[$comptenumber][$strkey]=$commun;
			 			$nbrecommuns ++;		 
			 			}
			 $this->listecomptes[$comptenumber]["NbCommuns"]=$nbrecommuns;
			$this->nbrecomptes++;						 
			 }
		}

	public function serialize($format){
		   	
			if (!isset($format)) {$format = 'json';}  
			if ($format=='json') {
				$json = '{'
					. '"myId":"'.$this->loggeduser .'"'
					. ' , "nbreCptes":"'.$this->nbrecomptes .'"'
					. ' , "listecomptes":' . json_encode($this->listecomptes, JSON_FORCE_OBJECT)
					. '}';
				
				return $json;
				}	
			}
			
		
		
}




?>