
<form name="comptetoadd" method="get" action="">
	<fieldset>
		
		<legend  class="info user"> 
		</legend>
	<div class=titre>
		<label>Nom du compte : </label>
		<input size="52" name="namedossier" type="text" />
	</div>
	
	<div class=comment>
		<label>Commentaire pour le compte : </label>
		<input size="54" name="commentdossier" type="text" />
	</div>
	
	<div class="selectdroits">
	<fieldset>
		<label>Vos droits pour ce dossier : </label>
		<select name="rights" size="1" >
		<option value="0">Compte invisible pour vous</option>
		<option value="1">Vous voyez le compte mais ne pouvez pas le modifier</option>
		<option value="3">Vous pouvez ajouter des écritures de dettes uniquement</option>
		<option value="7">Vous pouvez ajouter tout type d'écriture</option>
		<option value="15" selected="selected">Vous êtes administrateur du compte</option>
		</select>
		<br/>
		<label>Commentaire personnel : </label>
		<input size="54" name="commentuser" type="text" />
	</fieldset>
	</div>
	
	
	
	
	</fieldset>
</form>