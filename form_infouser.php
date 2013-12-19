

<form name="infoami" method="get" action="">
	<fieldset>
		
		<legend class="nameuser"> 
		</legend>
	<div class="infogene">
		<label for="nameami">Nom : </label>
		<input size="25" name="nameami" id="nameami"/>
		<label for="emailami">Email : </label>
		<input  size="25" name="emailami"id="emailami"/>
		<br/>	
	</div>
	<br/><br/>
	<fieldset>	
		<legend class="statutuser"> 
			</legend>
	<div class="statutindossier">
		<div class="selectdroits">
		<fieldset>
			<label>Droits : </label>
			<select name="droits" class="selectdroits" size="1" >
			<option value="0">Compte invisible pour cet utilisateur</option>
			<option value="1">Consultation seulement, pas d'écritures</option>
			<option value="3">Ecritures de dettes uniquement</option>
			<option value="7">Cet utilisateur peut ajouter tout type d'écriture</option>
			<option value="15" selected="selected">Cet utilisateur a les droits d'administrateur</option>
			</select>
		</fieldset>
		</div>
		
		
		<div class="checkamiscommun">
		<fieldset>
		<legend></legend>
		<span class="modedemploi"> Cliquez ci-dessous pour ajouter ou enlever des amis en compte communs </span>
		<fieldset class="listeamis"></fieldset>
		<span class="conclusion"> </span>
		</fieldset>
		</div>	
		<label for="comment">Commentaire : </label>
		<input  size="50" name="comment" id="comment"/>
	</div>	
	</fieldset>
	
	</fieldset>
</form>