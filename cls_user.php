<?php

class User {

	public $id;
	public $name;
	public $email;
	public $idFournisseur;
	
	
	function __construct($mykey = 0, $db = null) {
		if(is_a($mykey, "PDO")){
			$db = $mykey;
			$mykey = 0;
		}
		if($db != null)
			$this -> db = $db;
		if($mykey == 0){
			if(isset($_GET["myid"]))
				$mykey = $_GET["myid"];
			else if(isset($_SESSION["loggeduser"]))
				$mykey = $_SESSION["loggeduser"]["iduser"];
			else
				die("Problème de connection : perte de l'iduser...");
		}
		
		if ($mykey) {
			
			$this->id = $mykey;
					
			$this -> refresh();
		
		}
	}
	
	/*  refresh
	*/
	function refresh() {
		$bdd = isset($this->db) ? $this->db : new DBConnection();
		
		$sql = 'SELECT  c.*, f.IdFournisseur' .
			' FROM contact c' .
			' LEFT JOIN fournisseur f' .
				' ON c.IdContact = f.IdContact' .
			' WHERE c.IdContact = ?'
		;
		
		$req = $bdd -> select($sql, array( $this -> id ));
		
	//	$req = $bdd -> prepare($sql);
	//	$req->execute( array( $this -> id ));
		
		if ($donnees = $req -> fetch()) {
			$this -> name = $donnees["Name"];
			$this -> email = $donnees["EMail"];
			$this -> idFournisseur = $donnees["IdFournisseur"] == null ? 0 : $donnees["IdFournisseur"];
			/*
			for( $donnees as $key => $value )	
				$this -> $key = $value;*/
		}
		
	}
	
	function dossiers($type) {
	
		switch ($type) {	
		case "pcea" : 	
			return new Comptespcea($this->id);	
			break;
		default:	
			break;
		}	
	}
}

/*
$user = new user(16);

echo var_dump($user->dossiers("pcea"));
$iddoss = $user->dossiers("pcea")->listecomptes["cpte0"]["IdDossier"];
$dossiers = $user->dossiers('pcea');

echo "-------------DOSSIER :" . $iddoss."---------------------";
echo var_dump($user->dossiers('pcea')->dossier($iddoss));
//echo var_dump($user->dossiers("pcea")->dossier($iddoss)->amis);
$idami = 176;
echo "-------------AMI ".$idami." DANS LE DOSSIER :" . $iddoss."---------------------
";
echo var_dump($user->dossiers("pcea")->dossier($iddoss)->ami($idami));

echo "-------------AMI ".$idami." EN JSON :" . $iddoss."---------------------
";
echo json_encode($user->dossiers("pcea")->dossier($iddoss)->ami($idami),JSON_FORCE_OBJECT);

echo "-------------dossiers('pcea') EN JSON ---------------------
      ";
echo json_encode($user->dossiers("pcea"),JSON_FORCE_OBJECT);
*/
/*

$user->name
$user->id
$user->dossiers('CMD')->items as   ('1231321')->lgdossiers
$user->dossiers('CMD')->edit()
$user->dossiers('CMD')->new()

$user->rights->isAdmin
$user->rights->level

*/
?>