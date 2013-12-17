

<form class="mailinvit" name="mailinvit" method="get" action="">
	<div class="entete">
		<label for="subject">Sujet : </label>
			<input size="60" name="subject" id="subject"/>
		<br/>
		<label for="mailto">Vers : </label>
			<input size="24" name="mailto" id="mailto"/>
		<label for="mailfrom" class="mailfrom">De : </label>
			<input size="24" name="mailfrom" id="mailfrom"/>
	</div>
	<div class=message>
		<label for="message">Message : </label><br/>
		<textarea name="message" rows="10" cols="60" id="message"><?php/*
	Bonjour, <br/>
Vous êtes invité(e) à participer à un petit compte entre amis. <br/>
Rendez vous sur www.coopeshop.net, rubrique "Petits comptes entre amis" pour valider l'invitation et participer au compte. <br/>
Notez votre mot de passe : 
		*/?></textarea>
	</div>
</form>
