<?php 

include_once("config.php");
class Creances {

	private $iddossier;
	private $loggeduser;
	private $idcontact;
	
	public $lines = array();
	
	public $nbrelignes;
	
	function __construct($lgdusr, $idd, $idctct) {
			
		$this->loggeduser = $lgdusr;
		$this->iddossier = $idd;
		$this->idcontact = $idctct;
		
			//connection bdd
		try	{
			$bddcoopeshop =new PDO('mysql:host='.$GLOBALS['db_server'].';dbname='.$GLOBALS['db_name'], $GLOBALS['db_user'], $GLOBALS['db_password']);
			}
		catch (Exception $e)
			{
		        die('Erreur : ' . $e->getMessage());
			}
			//préparation requête
			
		$sql = 'SELECT LGD.Line, LGD.Price, IFNULL(LGD.Comment, " ") AS Comment, LGD.CreationDate, LGD.CreationIdUser'.
			' FROM lgdossier LGD ' .
			' WHERE IdDossier =' . $this->iddossier .
			' AND TypeDossier = "PCEA"' .
			' AND IdContact =' . $this->loggeduser .
			' AND IdArticle =' . $this->idcontact .
			' ORDER BY CreationDate DESC';
		//echo($sql);
		$req = $bddcoopeshop -> prepare($sql);
		$req->execute();
		
		$this->nbrelignes = 0;
		
		while ($ligne = $req -> fetch(PDO::FETCH_ASSOC)) {
		
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
			//	. ' , "nbLignes": '.$this->nbrelignes 
				. ' , "listecreances":' . json_encode($this->lines, JSON_FORCE_OBJECT)
						. '}';
						
					return $json;
				}	
	}
		
		
}




?>