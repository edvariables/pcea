<form name="creancetoadd" method="get" action="">
<h4 class="titre"> Nouvelle opération avec </h4>
<div class="selectdir">
	<fieldset>
	<legend>Sens de l'opération (débit ou crédit) : </legend>
		<select name="sens" size="1" >
		<option class="nomapres credit" value="1">J'ai payé pour </option>
		<option class="nomavant credit" value="1">a reçu de ma part : </option>
		
		<option class="nomavant debit" value="-1">a payé pour moi : </option>
		<option class="nomapres debit" value="-1">J'ai reçu de</option>	
		</select>
	</fieldset>
</div>


<div class="somme">
	<fieldset class="somme">
		<legend> Montant de l'opération (nombre positif): </legend>
		<input name="price" size="6"/>
	</fieldset>
</div>

<div class="comment">
<fieldset>
	<legend> Intitulé : </legend>
	<input name="comment" type="text" size="40"/>
</fieldset>

</form>