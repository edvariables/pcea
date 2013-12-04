<form name="amitoadd" method="get" action="">
<fieldset>
<legend class="titre">
</legend>

<div class="selectdroits">
<fieldset>
	<label>Ses Droits : </label>
	<select name="droits" size="1" >
	<option value="0">Compte invisible pour cet utilisateur</option>
	<option value="1">Cet utilisateur voit le compte mais ne peut pas le modifier</option>
	<option value="3">Cet utilisateur peut ajouter des écritures de dettes uniquement</option>
	<option value="7">Cet utilisateur peut ajouter tout type d'écriture</option>
	<option value="15" selected="selected">Cet utilisateur a les droits d'administrateur sur ce compte</option>
	</select>
</fieldset>
</div>


<div class="checkamiscommun">

<fieldset>
<fieldset class="amis">
<legend class="explication"></legend>


</fieldset>
<span class="conclusion"></span>
</fieldset>
</div>



<div class="textareacomment">
<fieldset>
<legend> Ajouter un commentaire : </legend>
<input name="comment"type="text" size="60" maxlength="255" />


</fieldset>
</div>

</fieldset>
</form>
