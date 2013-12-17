<?php 
//include_once('config.php');
	
class Comptespcea {
			
	private $loggeduser;
	public $items = array(); //Tableau de tableau ([IdDossier]=>xxx,[Name]=>NomDossier,[Status]=>StringDeStatut ...)
	private $nbrecomptes = 0;
	
	private $mypdo;
	
	function __construct($lgdusr) {
			//connection bdd
		try	{
			$bdd = new DbConnection();
			}
		catch (Exception $e)
			{
		        die('Erreur : ' . $e->getMessage());
			}			
		$this->loggeduser = $lgdusr;	
		$this->mypdo = $bdd;						
			//préparation requête
		$sql = "SELECT D.IdDossier, D.Status, D.Name, D.Comment" .
			", P.Status AS PCEAStatus, S.Label AS PCEAStatusLabel, P.Rights" .
			", A2.NbUsers" .
			",IFNULL(SUM(LGD.Price),0) AS Solde" .
			" FROM pceauser P" .
			" INNER JOIN dossier D" .
				" ON P.IdDossier = D.IdDossier" .
				" AND D.TypeDossier = 'PCEA'" .
			" INNER JOIN parameter S" .
				" ON S.Domain = 'PCEA.USER.STATUS'" .			
				" AND P.Status = S.IdParam" .
			" INNER JOIN (SELECT IdDossier, COUNT(IdUser) AS NbUsers FROM pceauser A" .
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
			.", D.Name, D.Status, D.Comment, P.Status, P.Rights, S.Label, A2.NbUsers "		
			;
	
		$this->mypdo -> beginTransaction();
		
		$req = $this->mypdo->select($sql);
		
		while ($donnees = $req -> fetch()) {
			$comptenumber = "cpte".$this->nbrecomptes;
		 	$this->items[$comptenumber]=$donnees;
			 
	//déterminer les amis en compte communs dans ce compte		 
			 
			 $iddoss = $this->items[$comptenumber]["IdDossier"];
			 
			 
			 $sqlcommun = 'SELECT PUC.IdUser2 AS IdComm, C.Name AS NameComm' .
				' FROM pceausercommun AS PUC' .
				' INNER JOIN contact AS C' .
				' ON C.IdContact = PUC.IdUser2' .
				' WHERE IdDossier = "' . $iddoss .'"'.
				' AND IdUser1 = ' . $lgdusr .
				' AND IdUser2 <> ' .  $lgdusr;
			 
			 $rq = $this->mypdo -> select($sqlcommun);
			 
			 $nbrecommuns = 0;
			 while ($commun = $rq -> fetch()) {
			 			$strkey = "comm".$nbrecommuns;
			 		 	$this->items[$comptenumber][$strkey]=$commun;
			 			$nbrecommuns ++;		 
			 			}
			 $this->items[$comptenumber]["NbCommuns"]=$nbrecommuns;
			$this->nbrecomptes++;						 
			 }
		$this->mypdo -> commit();
	}// fin de construct

	public function item($idd) {
	$found = false;
	$rights = "0";
	$listcomm=array();
	foreach ($this->items as $key=>$value) {
		if ($value["IdDossier"]==$idd) {
			$rights = $value["Rights"];
			if ($value["NbCommuns"]>0) {
				for ($k=0;$k<$value["NbCommuns"];$k++) {
					$listcomm[]=$value["comm".$k]["IdComm"];
				}
				}
			$found = true;	
		}
	}
	if ($found) {
		$amis = new Amis($this->loggeduser,$idd,$rights,$listcomm);
		return $amis;
		}
	else return $found;
	}	
			
	public function edit($idd,$params) {	//$params   = array("comment"=>truc,"namedossier"=>jolinom)	
		foreach ($this->items as $key=>$value) {
			if ($value["IdDossier"]==$idd) {
				$myrights = $value["Rights"];
				}
			}
		// ouverture bdd
		//try	{$bdd = new DbConnection();	}
		//catch (Exception $e)	{die('Erreur : ' . $e->getMessage());}	
					
		if ($myrights > 3) {	
			$comment = $params["comment"];				
			if ($myrights == 15) {		
				$namedossier = $params["namedossier"];
				//updatesql name and comment dans dossier		
				$sqlupdate = " UPDATE dossier SET Name = '".$namedossier."'"
						.", Comment = '".$comment."'"
						.", IdUser = ".$myid
						." WHERE IdDossier = ".$iddossier
						." AND TypeDossier = 'PCEA'"
						;				
				$req = $this->mypdo->prepare($sqlupdate);	
				try {$req->execute();} 
				catch (POException $e) { die( "Erreur : " . $e->getMessage()); }																			
			}//fin de if rights==15
			else {$comment = $params["comment"];
				$sqlupdate = " UPDATE dossier SET Comment = '".$comment."'"
						.", IdUser = ".$myid
						." WHERE IdDossier = ".$iddossier
						." AND TypeDossier = 'PCEA'"
						;
				$req = $this->mypdo->prepare($sqlupdate);	
				try {$req->execute();} 
				catch (POException $e) { die( "Erreur : " . $e->getMessage()); }
				}
		}//fin de if rights>3
	}	
	
	public function create($params) {
	if (isset($params["namedossier"]) && $params["namedossier"] != "")
		 {$nmdossier = $params["namedossier"];}
	else 	{$nmdossier = "Nouveau dossier du ". date("d-m-Y");}
	
	if (isset($params["commentuser"])) {$commentuser = $params["commentuser"];} else {$commentuser = "";}
	if (isset($params["commentdossier"])) {$commentdossier = $params["commentdossier"];} else {$commentdossier = "";}
	if (isset($params["rights"])) {$rights = $params["rights"];} else {$rights = 15;}
	
	date_default_timezone_set('Europe/Paris');
	$todaysql = date("Y-m-d");
	$todaymorning = $todaysql . " 00:00:00";
	$todaynight = $todaysql . " 23:59:59";
	
	$nowsql = date('Y-m-d H:i:s');
	
	// ouverture transac
	$this->mypdo->beginTransaction();		
			
	//Requete pour déterminer le numero de dossier
	
	$sql = "SELECT IFNULL(MAX(IdDossier),0) AS numLastDossier"
		." FROM dossier"
		." WHERE TypeDossier = 'PCEA'"
		." AND DateDossier BETWEEN '". $todaymorning . "' AND '" . $todaynight."'";
	
	
	$numstmt = $this->mypdo->prepare($sql);
	try {$numstmt->execute();} 
		catch (POException $e) { die( "Erreur : " . $e->getMessage()); }
	$result = $numstmt->fetch(PDO::FETCH_ASSOC);
	
	if ($result["numLastDossier"] && $result["numLastDossier"]!= 0) {
		$newnum = $result["numLastDossier"] + 1;	
		 }
	else {
		$newnum = date("ymd"). "001";
		}

	// Requete pour inserer dans table dossier 
	
	$sqlinsert = "INSERT INTO dossier (IdContact,IdDossier,TypeDossier,DateDossier,Name,Status,Comment)"
				." VALUES (?,?,?,?,?,?,?)";
		
	$stmt = $this->mypdo->prepare($sqlinsert);
	$params = Array($myid,$newnum,'PCEA',$nowsql,$nmdossier,'OK',$commentdossier);
	try {$stmt->execute($params);} 
	catch (POException $e) { die( "Erreur : " . $e->getMessage()); }	
		
	// Requete pour inserer dans table pceauser
	$sqlpcea = "INSERT INTO pceauser (IdUser,IdDossier,CreationDate,CreationIdUser,Rights,Status,Comment)"
				." VALUES (?,?,?,?,?,?,?)";
	$stmt = $this->mypdo->prepare($sqlpcea);
	$params = Array($myid,$newnum,$nowsql,$myid,$rights,'OK',$commentuser);
	try {$stmt->execute($params);} 
	catch (POException $e) { die( "Erreur : " . $e->getMessage()); }

	$this->mypdo->commit();
		
	}	
		
	public function serialize($format){
		   	
			if (!isset($format)) {$format = 'json';}  
			if ($format=='json') {
				$json = '{'
					. '"myId":"'.$this->loggeduser .'"'
					. ' , "nbreCptes":"'.$this->nbrecomptes .'"'
					. ' , "listecomptes":' . json_encode($this->items, JSON_FORCE_OBJECT)
					. '}';
				
				return $json;
				}	
	}				
}




?>