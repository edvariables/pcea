<?php 

include_once("config.php");
class Creances {

	private $iddossier;
	private $loggeduser;
	private $idcontact;
	
	public $lines = array();
	
	public $nbrelignes;
	
	function __construct($lgdusr, $idd, $idctct, $arrcomm) {
			
		$this->loggeduser = $lgdusr;
		$this->iddossier = $idd;
		$this->idcontact = $idctct;

		$dbconn = new DbConnection();
			
		$sql = 'SELECT LGD.Line, LGD.IdContact, LGD.Price, IFNULL(LGD.Comment, " ") AS Comment, LGD.IdAnal, LGD.CreationDate, LGD.CreationIdUser'.
			' FROM lgdossier LGD' .
			' WHERE IdDossier =' . $this->iddossier .
			' AND TypeDossier = "PCEA"' .
			' AND (IdContact =' . $this->loggeduser;
			
		if (!empty($arrcomm)){
			foreach ($arrcomm as $idcomm) {
			$sql  .= ' OR IdContact ='. $idcomm;
			}
		}
		$sql  .=') AND IdArticle =' . $this->idcontact .
			' ORDER BY CreationDate DESC';

		$req = $dbconn -> select($sql);
				
		$this->nbrelignes = 0;
		
		while ($ligne = $req -> fetch()) {		
			$linenum = "line".$this->nbrelignes;
		 	$this->lines[$linenum] = $ligne;	
			 $this->nbrelignes++;
		}			
		$this->lines["nbLignes"] = $this->nbrelignes;
	}
		
	public function serialize($format){
	
				if (!isset($format)) {$format = 'json';}  
				if ($format=='json') {
					$json = '{'								
					. '"myId":"'.$this->loggeduser .'"'
				. ' , "idDossier":"'.$this->iddossier .'"'
				. ' , "idAmi": '.$this->idcontact 
				. ' , "listecreances":' . json_encode($this->lines, JSON_FORCE_OBJECT)
						. '}';
						
					return $json;
				}	
	}				
}
?>