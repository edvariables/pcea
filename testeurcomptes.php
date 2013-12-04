<?php

	include_once("cls_comptes.php");
	include_once("cls_amis.php");
	
	$myid = 16;
	
	$liste = new Comptes($myid);
	echo $liste->serialize('json');
	
	?>
	<table>
	<?php
	foreach ($liste->listecomptes as $row) { 
		$sold = (!isset($row["Solde"])) ? 0 : $row["Solde"];
		?>
		
		<tr class="dossier" name="<?php echo $row["IdDossier"] ?>" id="node-dossier-pcea-<?php echo $row["IdDossier"]?>" >		
			
			<td class="tc"><span class="imgEDVTypeDom"><?php echo $row["Name"]?></span>
			<td class="sold <?php if($sold < 0) echo("negative")?>"><small>mon&nbsp;solde&nbsp;: </small><?php echo number_format($sold,2,","," ")?>&nbsp;Ø
			<td><?php $namis = ($row["NbAmis"] == 1) ? "aucun(e)" : $row["NbAmis"]-1; echo $namis?> ami(e)<?php if($row["NbAmis"]>2) echo("s")?>
			<?php if(!is_null($row["Comment"]) && $row["Comment"]!="") {?>
				<pre><?php echo $row["Comment"]?></pre>
			<?php }?>
		</tr>
	

<?php	 }?>
	</table>
<?php 	foreach ($liste->listecomptes as $row) {
	?> <pre> <?php
	$compte = new Amis($myid, $row["IdDossier"]);
	?> </pre> <?php

}	
