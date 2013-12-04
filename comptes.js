
/* JQuery comptes
*/
(function($){
	var defaultOptions = {
		
	};
	var methods = {
		init : function(options){
			options = jQuery.extend({}, defaultOptions, options);
			return this.each(function(){
					$(this)
						.empty();
						}
					); }

					
	,"getComptes" : function(lgdid,iddoss) {
		
		var mydata = {};
		if (iddoss) { mydata["iddossier"]=iddoss;}
		if (lgdid) {mydata["lgduser"]=lgdid;}
	//	if (myrights) {mydata["myrights"]=myrights;}
		
		$.ajax({url : "builder_comptes.php?debug=1"
			,data : mydata
			,cache : false
			,success : function(msg){
				$(".DataTable.async.dossiers.pcea table.pceaTable").comptes("showComptes",msg);						
						}
			,async : true 
			,type : "GET" 
			});
			}

					
	, "showComptes" : function(jsocptes) { // a priori this est la "table", jsocptes est la réponse de builder_comptes passée par getcomptes
			$this=$(this);
			$this.empty();
			$this.next().remove();
			$this.append($("<caption> </caption>"))
				.append($("<thead> </thead>")
					.append($("<tr> </tr>")
						.append($("<th class='admin'></th> <th>Petit compte</th><th>Solde</th><th>Membres / info</th>"))
						)
					)
				.append($("<tbody></tbody>")
					.attr("id","pceabodydossiers")
					);
					
			var cptesjso = $.parseJSON(jsocptes);
			
			
			var $mytbody =$("#pceabodydossiers");
			
			var hasAdmin = false; //drapeau detectant la présence d'un dossier avec droit d'administrateur.
			
			if (cptesjso.nbreCptes  > 0) {		
				for (var k=0; k<cptesjso.nbreCptes;k++)
					{ var numcpte = "cpte" + k;
					
					var jsodossier = cptesjso.listecomptes[numcpte];
					
					var commentdossier = (jsodossier.Comment) ? jsodossier.Comment : "";
					
					var stringamis = (jsodossier.NbAmis > 1) ? (jsodossier.NbAmis-1) : "Aucun(e)";
					stringamis += " ami(e)";
					stringamis += (jsodossier.NbAmis > 2) ? "s." : "."; 
					
					var stringcommuns;
					
					var strgsolde = "Mon solde : ";
					var couleursolde = "black";
					if (jsodossier.Solde == null || jsodossier.Solde == 0) {
						jsodossier.Solde = 0;
						strgsolde += " 0";
						}
					else {strgsolde += parseFloat(jsodossier.Solde).toFixed(2) + " euros";
						couleursolde = (jsodossier.Solde > 0) ?  "green" : "red";
						}
										
					//remplissage de tbody
					$mytbody.append($("<tr> </tr>")
						.attr({"id":"node-dossier-pcea-"+jsodossier.IdDossier,"name" : jsodossier.IdDossier})
						.addClass("dossier initialized parent collapsed")
						.attr("title",commentdossier)
						.data("myId",cptesjso.myId)
						.data("cptek",jsodossier));
					var $rowdossier = $mytbody.find("tr#node-dossier-pcea-"+jsodossier.IdDossier);	
					
					if ($rowdossier.data("cptek").NbCommuns==0) {stringcommuns = "En compte commun avec personne";}
					
					for (var i=0;i<jsodossier.NbCommuns;i++) {
						var commi="comm"+i;
						if (i==0) {
							stringcommuns = "En compte commun avec " + $rowdossier.data("cptek")[commi].NameComm;
							}
						else {if (i<jsodossier.NbCommuns-1) {stringcommuns += ", " + $rowdossier.data("cptek")[commi].NameComm;}
							else {stringcommuns += " et " + $rowdossier.data("cptek")[commi].NameComm;}
							}						
						}
					stringcommuns +=".";	
			
		
					var clicknormal = function() {var $trowdossier=$(this).parent(); //click sur un dossier 
					 		var thisiddossier=$trowdossier.data("cptek").IdDossier;
					 		if ($trowdossier.siblings(".amis").hasClass("dossierparent" + thisiddossier)|| $trowdossier.hasClass("developpe")) { //amis déjà affichés
					 			$trowdossier.removeClass("developpe")
					 				.siblings(".dossierparent" + thisiddossier + ", .creances.issu-de-dossier"+thisiddossier + ", .addtodossier" + thisiddossier).remove();
					 					}
						 	else { 	$trowdossier.addClass("developpe");
						 		 $trowdossier.comptes("getAmis");	
						  		return false;
								  }
						 	 };
					 
					 var clickinvite = function() { var $trowdossier=$(this).parent();
						$trowdossier.comptes("answerInvit")
						return false;					
									};
					 
					 var clickselfedit = function() {
					 	var $rdoss = $(this).parent().parent();
								 
						$rdoss.comptes("editSelf");		 
					
					 	return false;
					 }
					 
					 
					$rowdossier.append($("<td></td>")
							.addClass("infoinvit opeadmin")
								)
					.append($("<td></td>") //Le compte
						.addClass("tc")
						.attr("style","cursor: pointer;")
						.append("<span class='expandericon'></span>")									
						.append($("<span></span>")
						
							.addClass("imgEDVTypeDom")
							.css({"padding-left": "19px"})	
							.html(jsodossier.Name))
						.bind("click",clicknormal)
						)
					.append($("<td></td>") //Mon solde
						.addClass("sold")
						.append($("<span></span>")
							.html(strgsolde)
							.css("color",couleursolde))
						)
					.append($("<td></td>") // Info - Nbre d'amis
						.addClass("infostatut infoinvit")
						.append($("<span></span>")
							.addClass("nbreamis")
							.html(stringamis))
						.append($("<a href=''></a>")
							.addClass("amiscommuns")
							.html(stringcommuns)	
							.bind("click",clickselfedit)		
							)
						);	
					
	
				if (jsodossier.PCEAStatus=="INVIT")	{
					
					$rowdossier.find("td.infoinvit").append($("<a href=''> </a>")
									.addClass("linkinvit")
									.text("INVITATION.")
									.css("color","red")

									.click(function(e) {var $lrowdossier = 	$(this).parent().parent();			
										$lrowdossier.comptes("answerInvit")
										return false;					
										}
										)
									);										
					$rowdossier.find("td.tc").unbind("click",clicknormal)
								.bind("click",clickinvite)
								.css("color","red");
								};
				if (jsodossier.Rights < 7) {	switch (jsodossier.Rights) {
				
							case "1" : var strdroits=" Consultation uniquement."
								var rcolor = "red";
								break;
							default : var strdroits="Droits limités"
								var rcolor = "orange";
								break;
								}	 
											
						$rowdossier.find("td.tc").css("color",rcolor);
								$rowdossier.find("td.infostatut").append($("<span></span>")
											.html(strdroits)
											.css("color",rcolor));
												
						}						
				if (jsodossier.Rights == 15 && jsodossier.PCEAStatus == "OK") {
				
					hasAdmin = true;
					$rowdossier.addClass("isadmin");
					$rowdossier.find("td.opeadmin").append($("<a href=''>supprimer</a>")
										.addClass("delete edvicon edvimgDelete")
										.attr("title","Supprimer le compte")
										.css("text-align", "right")
										.hide()
	
										.click(function(e) {
											var $lrowdossier = 	$(this).parent().parent();			
											$lrowdossier.comptes("deleteCompte")
											return false;					
										})
									)
									.append($("<a href=''>modifier</a>")
										.addClass("edit edvicon edvimgEdit")
										.attr("title","Renommer le compte")
										.css("text-align","right")
										.hide()
										.click(function(e) {
											var $lrowdossier = 	$(this).parent().parent();			
											$lrowdossier.comptes("editCompte")
											return false;					
										})
									);
				}
		} // fin de la boucle sur les comptes enregistrés	
		
		
			if (hasAdmin) {
				$mytbody.siblings("thead")
					.find("th.admin").append(
						$("<a href=''></a>")
							.addClass("toggleadmin")
							.text("Admin")
							.click(function(){
								$this = $(this);
								$mytbody.find("tr td.opeadmin a").toggle();
								if($this.hasClass("ison"))
									$this.removeClass("ison"); 
								else
									$this.addClass("ison");
								return false;
							})
					);
			}
			if (cptesjso.cpteselected) { //lorsqu'on rafraichit depuis des opérations dans un compte				
				$that = $("#node-dossier-pcea-"+cptesjso.cpteselected);	
				$that.addClass("developpe");
				$that.comptes("getAmis"); 						
			} 							
		}	// fin du if (NbreComptes>0)
			
		else {   // Pas de compte enregistré
			
			$mytbody.append($("<tr> </tr>")
						.append($("<td></td>"))
						.append($("<td></td>")
								.append($("<span></span>")
								.html("Aucun compte en cours")
								)
							)
					);
			}
			$this.after(($("<a href=''> </a>")
							.text("Créer un nouveau compte entre amis")
							.addClass("linkadddossier imgEDVTypeAdd")
							.css("padding-left","19px")
							.click(function(e) {
								$this.comptes("addDossier",cptesjso.myId);
								return false;			
									}
								)
							)
						);	
			return $(this);	
								
		} // fin de showComptes
		
	,"answerInvit" : function() {
		
		var $rowdossier = $(this);
		
		var mydata={};		
		mydata["iddossier"] = $rowdossier.data("cptek").IdDossier;
		mydata["myid"] = $rowdossier.data("myId");
		mydata["myrights"] = $rowdossier.data("cptek").Rights;
		var namedossier = $rowdossier.data("cptek").Name;	
				
		switch (mydata["myrights"]) {
			
			case "1" : var strmyrights=" consultation uniquement.";
				var rcolour = "red";
				break;
			case "3" : var strmyrights=" vous ne pourrez enregistrer que des écritures de dettes.";
				var rcolour = "orange";
				break;
			case "7" : var strmyrights=" vous pourrez enregistrer tout type d'écritures.";
				var rcolour = "green";
				break;							
			case "15" : 
				var strmyrights=" tous.";
				var rcolour = "blue";
				break;
			default : var strmyrights=" droits non determinés !";
				var rcolour = "pink";
				break;
			} 
		$(".DataTable.async.dossiers.pcea").after($("<div> </div>")
							.attr({"id":"wrapperinvitok","title":"Invitation à rejoindre un compte"})
							.append($("<div></div>")
							.html("Acceptez vous l'invitation à rejoindre le compte "+namedossier+ " ?"))
							.append($("<div></div>")
								.css("color",rcolour)
								.html("Vos droits dans ce compte : "+strmyrights)));	
		$("div#wrapperinvitok").dialog({
					        height: 'auto',
					        width:'auto',
					        modal: true,
					        position: 'center',
					        overlay: {
					                backgroundColor: '#000',
					                opacity: 0.5
					        },
					        buttons: {
					               'Oui': function() {
					                	$.ajax({
					                        	url: "acceptinvit.php",
					                        	type: "GET",
									data: mydata,
					                     	  	error: function(){
					                                	alert("Il y a une erreur avec AJAX pour répondre à l'invit");
					                       		 	},
					                        	beforeSubmit:function(){},
					                        	success: function(answer) {
											jsoanswer=$.parseJSON(answer);
											var myid;
											if (jsoanswer["myid"]) {myid = jsoanswer["myid"]};
											var idd;
											if (jsoanswer["iddossier"]!=null) {idd = jsoanswer["iddossier"]};
											
											$(".DataTable.async.dossiers.pcea").comptes("getComptes",myid,idd);
											
											}
										});
					                	
								$(this).dialog('destroy').remove();
										
					               		}//fin de accepter invit
						
					              	,'Plus tard': function() {
				                        	$(this).dialog('destroy').remove();
					                	}
							,'Jamais': function() {
						                	$.ajax({
					                        	url: "declineinvit.php",
					                        	type: "GET",
									data: mydata,
					                     	  	 error: function(){
					                                	alert("Il y a une erreur avec AJAX pour répondre à l'invit");
					                       		 	},
					                        	beforeSend:function(){	var checkok = checkamiresign(mydata["myid"],mydata["iddossier"]);
												if (checkok) {
														if (!confirm("Etes vous sûrs ? Il faudra vous refaire inviter pour revoir ce compte...")) {throw new Error("Refus d'invitation annulé par l'utilisateur");}
													 	}
													else {alert("Désolé, vous ne pouvez refuser cette invitation car il y a déjà des lignes de créance enregistrées à votre nom");
														throw new Error("Refus d'invitation annulé par le serveur. Invité impliqué.");
														} 
					                        				
												},
					                        	success: function(answer) {
											jsoanswer=$.parseJSON(answer);
											var myid;
											if (jsoanswer["myid"]!=null) {
												myid = jsoanswer["myid"];		
												$(".DataTable.async.dossiers.pcea").comptes("getComptes",myid);
												}
											else {throw new Error("IdUser perdu en route");}
											}
										});
					                	
								$(this).dialog('destroy').remove();
																
					               		}//fin de decliner invit
					        	}//fin de buttons du popup
						});//fin de dialog	
		}//fin de answer invit
		
	,"deleteCompte" : function() {
		$thisrowcompte=$(this);
		var datatosend = {};
		datatosend["myid"] = $thisrowcompte.data("myId");
		datatosend["myrights"] = $thisrowcompte.data("cptek").Rights;
		datatosend["iddossier"] = $thisrowcompte.data("cptek").IdDossier;
		var namedossier = $thisrowcompte.data("cptek").Name;
		if (confirm("Etes vous sûr de vouloir détruire le compte " + namedossier + " ? Cette opération est définitive et vous perdrez toutes les données...")) 
		{
	
		$.ajax({url : "delete_compte.php?debug=1" 
			,data : datatosend
			,cache : false
			,success : function(msg){
					//jso = $.parseJSON(msg);
					$("div.DataTable.async.dossiers.pcea").comptes("getComptes",msg.myid);
					}
			,async : true 
			,type : "GET"
			,dataType : "json" 
			});
		}
	}
	,"editCompte" : function() {
	$trd = $(this); //row du dossier/compte
	var namedossier = $trd.data("cptek").Name;
	var comment = $trd.data("cptek").Comment;
	var myid = $trd.data("myId")
	var iddoss = $trd.data("cptek").IdDossier;
	var myrights = $trd.data("cptek").Rights;
		$(".DataTable.async.dossiers.pcea").after($("<div> </div>")
			.attr({"id":"wrapperformeditdossier","title":"Renommer le compte : "})
			.load("form_editdossierpcea.php", function() {
	
	
					$this=$(this);
					$this.find("input").each(function(){
							$thatinput = $(this);
							if ($thatinput.attr("id")=="namedossier") {$thatinput.val(namedossier)};
							if ($thatinput.attr("id")=="comment") {$thatinput.val(comment)};
							});
					$this.find("form").append("<input type='hidden' name='myid' value="+ myid +">")
							.append("<input type='hidden' name='iddossier' value="+ iddoss +">")
							.append("<input type='hidden' name='myrights' value="+ myrights +">");
					$this.dialog({
					height: 'auto',
					width:'auto',
					modal: true,
					position: 'center',
					overlay: {
				                backgroundColor: '#000',
				                opacity: 0.5
				        },
				        buttons: {
				               'Envoyer': function() {
				                	$.ajax({
				                        	url: "edit_dossierpcea.php",
				                        	type: "GET",
								data: $("#wrapperformeditdossier form").serialize(),
				                     	  	 error: function(){
				                                	alert("Il y a une erreur avec AJAX dans ");
				                       		 	},
				                        	beforeSubmit:function(){},
				                        	success: function(answer) {
										var status=answer.update;
										if (status=="ok") {
									
										var myid;
										if (answer["myid"]) {myid = answer["myid"]};
										 
									
										if (answer["iddossier"]) {
											var iddoss=answer["iddossier"];																
											$(".DataTable.async.dossiers.pcea").comptes("getComptes",myid,iddoss);
											}
										else {$(".DataTable.async.dossiers.pcea").comptes("getComptes",myid);}
										}
										}	
								,dataType : "json"	
									}
								);//fin de ajax
							$(this).dialog('destroy').remove();
				               		}//fin de envoyer
				              	,'Annuler': function() {
			                        $(this).dialog('destroy').remove();
				                	}
				        	}//fin de buttons du popup
							
					});//fin de dialog			
					
					})//fin du load
				);
	
			}			
	,"addDossier" : function(myid) {
		
		var data={};
		data["myid"]=myid;	
		callpopupform("adddossier",data,"Créer un nouveau compte");
			}
	,"getAmis" : function() {	
		var $trd = $(this);
		var datatosend = {};
		datatosend["lgduser"]=$trd.data("myId");
		datatosend["myRights"] = $trd.data("cptek").Rights;
		datatosend["idDossier"] = $trd.data("cptek").IdDossier;
		
		var iddoss = datatosend["idDossier"];
			
		$.ajax({url : "builder_amis.php?debug=1"
			,data : datatosend
			,cache : false
			,success : function(msg){				
				$(".DataTable.async>table>tr#node-dossier-pcea-"+iddoss).comptes("showAmis",msg);				
				}
			,async : true 
			,type : "GET" 
			});
		return $(this);
		} // fin de getAmis			
	,"showAmis" : function(jsoamis) {
			var amisjso = $.parseJSON(jsoamis);			
			var $trd = $("#node-dossier-pcea-"+amisjso.idDossier); //trow du dossier
			//ou	var $trd = $(this);
			
			var myrights = amisjso.myRights;
			
			var tableauamis = {};
			for (var k=0; k<amisjso.nbreAmis ; k++)		//établissement d'un jso de correspondances "id" : "Name" pour chacun des amis de ce compte
							{
							var numstr = "ami" + k;		
							var jsoami = amisjso.listeamis[numstr];
				
							var objjso = {};
							objjso[jsoami.IdContact]=jsoami.CName;
							tableauamis = $.extend(tableauamis, objjso);
							
							}
	/* bouton add ami*/						
			if (!$trd.siblings().hasClass("addtodossier"+amisjso.idDossier)) {	
				$trd.after($("<tr></tr>")
					.addClass("addtodossier"+amisjso.idDossier +" lastline")
						.append($("<td></td>")
							.addClass("annuleaddami"))
						.append($("<td></td>")
							.append($("<a href=''></a>")
								.addClass("linkaddami imgEDVTypeAdd")	
								.text("Ajouter un ami")							
								.attr("title","Inviter un nouvel ami")
								.click(function() {
									$that=$(this);
									if ($that.hasClass("disabled"))
										{
										return false;
										}
									else {
									$that.addClass("disabled")
										.parent().comptes("addAmi",amisjso.idDossier,amisjso.myId,myrights)
									return false;
									}
								}
								)
							)
						)
						.append("<td colspan=2> </td>")
					);
					}		
					
			for (var k=0; k<amisjso.nbreAmis ; k++){
				var numami = "ami" + k;	
					
				var jsoami = amisjso.listeamis[numami];
				
				var amistatus = jsoami["AmiStatus"];
				
				var iscommwithme=false;
				var strinfo="";
				
				if (!(jsoami.nbreCommun == 0 || jsoami.nbreCommun == "0")) {	//liste des amis en compte commun
					strinfo = "En compte commun avec ";
					for (var n=0; n<jsoami.nbreCommun;n++)
						{
						strinfo += (n==0) ? "" : (n==jsoami.nbreCommun-1) ? " et " : ", ";
						var idcptecommun = jsoami.isCommun["com"+n];
						if (idcptecommun==amisjso.myId) {
								iscommwithme=true;
								strinfo += "moi";
								}
							else {
								strinfo += tableauamis[idcptecommun];
							}
						}
						strinfo +=".";											
					}
				else iscommwithme = false;
				
				
				var strgsolde = "Solde : ";
				var couleursolde = "black";
				if (jsoami.Solde == null || jsoami.Solde == 0) {
					jsoami.Solde = 0;
					strgsolde += " 0";
				}
				else {
					if (jsoami.Solde>0) {strgsolde+="+";}
					strgsolde += parseFloat(jsoami.Solde).toFixed(2) + " euros";
					couleursolde = (jsoami.Solde > 0) ?  "green" : "red";
				}
						
						
				$trd.after($("<tr></tr>")					
						.addClass("amis dossierparent" + amisjso.idDossier)
						.attr("id", "dossier" + amisjso.idDossier +"-ami" + jsoami.IdContact)
						.attr("dossierparent" , amisjso.idDossier)
						//	  "idami" : jsoami.IdContact,
						//	  "idlgduser" : amisjso.myId,
						//	  "lgdrights" : amisjso.myRights,
						//	  "nameami" : tableauamis[jsoami.IdContact]})
						.data("jsoami",jsoami)
										
						.append($("<td class='opeadmin'></td>"))
						.append($("<td></td>")
								.addClass("amimain")
								.append($("<span></span>")
									.css({ "padding-left": "40px" })
									.attr("title", jsoami.CommentUser)
									.addClass("nameami pointer")
									
/* click sur le nom d'un ami" */	.click(function(){
										$(this).parents("tr:first").comptes("editAmi");			
									})
									.html(jsoami.CName)
								)
								.append($("<a></a>")
									.addClass("historique")
								//	.attr("style","cursor: pointer;")
									.attr("href","")
									
									.append($("<span></span>")
										.addClass("edvnode")
									)
									.append($("<span></span>")
										.text("Afficher l'historique")
	/* click sur "voir/cacher historique" */ .click(function(e) {
												var $this = $(this);
												$this.parent().toggleClass("expanded");
												var $thisrow = $this.parents('tr:first');	
												if ($thisrow.siblings(".creances.issu-de-dossier"+$thisrow.attr("dossierparent")).hasClass("issu-de-ami"+$thisrow.data("jsoami").IdContact)) 
													{ //effacement des creances
													$thisrow.removeClass("developpe");
													$thisrow.siblings(".creances.issu-de-dossier"+$thisrow.attr("dossierparent")+".issu-de-ami"+$thisrow.data("jsoami").IdContact).remove();
													$this.text("Afficher l'historique");
													}
											
												else {	//affichage des creances
												$thisrow.addClass("developpe");				
												$thisrow.comptes("getCreances"); 
												$this.text("Masquer l'historique");									
											}
											return false;
											}
										)
									)
								)
							)
					);																													
					var $rowami = $trd.siblings("#dossier" + amisjso.idDossier +"-ami" + jsoami.IdContact);
					
					var lmyrights = $trd.data("cptek").Rights;
					
				//	var lmyrights = $rowami.attr("lgdrights");		
					
					
					if (lmyrights >= 3) {
						$rowami.find("td.amimain").prepend(
							$("<a href=''></a>")
								.addClass("linkaddcreance imgEDVTypeAdd")
								.text("Ajouter une écriture")
								.attr("title","Ajouter une écriture entre vous et "+ jsoami.CName +".")
/*click sur ajout creance	*/					.click(function(e) {
										var $this=$(this);
										var $thisrow = $this.parents(".amis:first");	
										
										$this.hide();								
									 	$thisrow.comptes("addCreance");
									 											
									 	return false;
										})
//admin
							);
						if (lmyrights == 15) {
							$rowami.find("td.opeadmin").append(
								$("<a></a>")
									.addClass("delete")
									.addClass("edvicon edvimgTrash")
									.text("exclure")										
									.click(function() {														
										$(this).parents('tr:first').comptes("deleteAmi");																						
									})
							);																		
							if (!$rowami.parent().siblings("thead").find("tr th.admin a.toggleadmin").hasClass("ison")) {
								$rowami.find("td.opeadmin a.delete").hide();
							}
						}																	
					}
					$rowami.find("td.amimain").after(
						$("<td></td>")
							.addClass("sold")
							.append($("<span></span>")
								.css("color" , couleursolde)
								.html(strgsolde)
							)																			
							.after($("<td></td>")
								.addClass("infoami")
								.append($("<span></span>")
									.html(strinfo)
								)
							)
					);																
					if (amistatus && amistatus == "INVIT") {
						$rowami.find("td.infoami").append(
							$("<span></span>")
								.html("Doit répondre à l'invitation")
								.css("color","red")
						);
					}
				}//fin de la boucle sur les amis du compte
			}
			//fin de showAmis
			
	/* addAmi
	*/	
	,"addAmi" : function(iddoss, myid, myr) {				
			var ref = {};
			ref["iddossier"] = iddoss;
			ref["myid"] = myid;
			ref["myrights"] = myr;						
			$this=$(this); //td contenant le lien addami									
			$this.append($("<input name='mailami' value='>>saisir une adresse ici' onfocus='this.select();' size='30'/>"))
				.addClass("inputmailami")
				.append($("<a href=''> Valider </a>")
						.addClass("linksubmitami edvicon edvimgOk")
						.click(function(e) {
							$(this).comptes("submit","ami",ref);
							return false;
							}
							)
					)							
			$this.siblings("td.annuleaddami").append($("<a href=''></a>")
							.addClass("cancel edvicon edvimgDelete")
							.text("Annuler")
							.attr("title","Annuler le lancement d'invitation")							
							.click(function(e) {
								$that = $(this);
								$that.parent().siblings(".inputmailami").find("input, a.linksubmitami").remove();
								$that.parent().siblings(".inputmailami").find(".linkaddami").removeClass("disabled");
								$that.remove();
								return false;
								})
							
							.css("text-align","right")
							.css("display","block")
					);
			} // fin de addAmi
	
	,"editSelf" : function() {
		$thisrow = $(this); //row du dossier	
		var sdata = {};
		var ref = {};
		
		sdata["iddossier"] = $thisrow.data("cptek").IdDossier;
		sdata["myid"] = $thisrow.data("myId");
		sdata["idami"] = $thisrow.data("myId");
		sdata["myrights"]=$thisrow.data("cptek").Rights;	
	
		sdata["namedossier"] = $thisrow.data("cptek").Name;
		
		ref["namedossier"] = $thisrow.data("cptek").Name;
		ref["tableauidname"]={};
		ref["jsoami"]={};
		

		
		
		$.ajax({url : "get_selfinfo.php?debug=1"
				,data : sdata
				,cache : false
				,success : function(msg){
								
					ref["tableauidname"]=msg.tableauidname;
					ref["jsoami"]=msg.jsomyinfo;	
					ref["jsoami"]["isCommun"]={};
					
					ref["iddossier"] = $thisrow.data("cptek").IdDossier;
					ref["myid"] = $thisrow.data("myId");
					ref["idami"] = $thisrow.data("myId");
					ref["myrights"]=$thisrow.data("cptek").Rights;		
														
					ref["jsoami"]["nbreCommun"]=$thisrow.data("cptek").NbCommuns;
					
					if ($thisrow.data("cptek").NbCommuns>0) {					
						for (var k = 0;k<$thisrow.data("cptek").NbCommuns;k++) {
							ref["jsoami"]["isCommun"]["com"+k]=$thisrow.data("cptek")["comm"+k].IdComm;
						}
					}				
					callpopupform("edituser",ref,"Mes paramètres dans le compte "+ref["namedossier"]);												
					}
				,error: function(XMLHttpRequest, textStatus, errorThrown) {
				     alert("Erreur Ajax : "+ errorThrown);
				  }
				,async : true 
				,type : "GET" 
				,dataType : "json"
				});
	}
	
	
	
	
	,"editAmi" : function() {	
		$thisrow = $(this); //row de l'ami	
		var ref = {};
		ref["iddossier"] = $thisrow.attr("dossierparent");


		
			
		var iddoss = ref["iddossier"];
			
		var $trd = $thisrow.siblings("#node-dossier-pcea-"+iddoss);
					
		ref["myid"] = $trd.data("myId");
		ref["myrights"] = $trd.data("cptek").Rights;
		ref["idami"] = $thisrow.data("jsoami").IdContact;
		ref["namedossier"]=$trd.data("cptek").Name;

		
	//	ref["namedossier"] = $("#node-dossier-pcea-"+ref.iddossier+" td.tc span").text();
		
		ref["jsoami"]=$thisrow.data("jsoami");
		
		var tableauidname = {};
		
		$(".amis.dossierparent"+ref.iddossier).each(function(){
						var thisidami=$(this).data("jsoami").IdContact;
						var thisnameami=$(this).data("jsoami").CName;					
						tableauidname[thisidami]=thisnameami;						
						});	
		ref["tableauidname"]=tableauidname;
		var titre = "Deux ou trois choses que je sais de "+ref.jsoami.CName;		
		callpopupform("edituser",ref,titre)	
	}
	
	,"deleteAmi" : function() {
		$this = $(this); // doit être le rowami correspondant		
		datatosend = {};
		
		datatosend["iddossier"] = $this.attr("dossierparent");
		var iddoss = $this.attr("dossierparent");
		$trd = $this.siblings("#node-dossier-pcea-"+iddoss);
		
		//datatosend["myid"]=$this.attr("idlgduser")
		//	datatosend["myrights"] = $this.attr("lgdrights");
		//datatosend["idami"] = $this.attr("idami");
		//var nameami = $this.attr("nameami");
		datatosend["myid"] = $trd.data("myId");
		datatosend["myrights"] = $trd.data("cptek").Rights;
		datatosend["idami"] = $this.data("jsoami").IdContact;
		var nameami = $this.data("jsoami").CName;
		
		//var $rowdossier = $("#node-dossier-pcea-"+datatosend.iddossier);				
		if (confirm("Etes vous sûr de vouloir expulser "+ nameami+" de ce petit compte ? Toutes les opérations et données liées à son nom seront définitivement perdues."))
			{
			$.ajax({url : "delete_ami.php?debug=1", 
							data : datatosend
							,cache : false
							,success : function(msg){					
							
								$trd.comptes("getComptes",msg.myid,msg.iddossier);	//amis à afficher								
										}
							,async : true 
							,type : "GET" 
							,dataType : "json"
							});
						return $(this);
		}				
	}	
	,"getCreances" : function() { //lgdusr,iddoss,idami	
		$this = $(this); // doit être le rowami correspondant			
		var datatosend = {};
		datatosend["iddossier"] = $this.attr("dossierparent");
		var iddoss = datatosend["iddossier"];	
		$trd = $this.siblings("#node-dossier-pcea-"+iddoss);
		datatosend["NbCommuns"]=$trd.data("cptek").NbCommuns;
		if (datatosend["NbCommuns"]>0) {
			for (var k=0; k<datatosend["NbCommuns"];k++) {	
				datatosend["comm"+k]=$trd.data("cptek")["comm"+k];
			}
		}	
		datatosend["myid"]=$trd.data("myId");
		datatosend["idami"]= $this.data("jsoami").IdContact;	
	
		$.ajax({url : "builder_creances.php?debug=1" 
				,data : datatosend
				,cache : false
				,success : function(msg){					
						$this.comptes("showCreances",msg);								
							}
				,async : true 
				,type : "GET" 
				,datatype : "json"
						});
		return $(this);	
	} // fin de getCreances
	
	
	,"showCreances" : function(jsocreances) {
		var creancesjso = $.parseJSON(jsocreances);
		var $tra = $(this); //row de l'ami
		var jsoami = $tra.data("jsoami");
		var $trd = $tra.siblings("#node-dossier-pcea-" + creancesjso.idDossier);
		var cptek = $trd.data("cptek");
		//var $tra = $("#dossier"+creancesjso.idDossier+"-ami"+creancesjso.idAmi);

		if (cptek.NbCommuns > 0) {
	
		 for (var n = 0; n < cptek.NbCommuns; n++) {
			if (cptek["comm" + n].IdComm != jsoami.IdContact) {				 

			// afficher les creances entre " + cptek["comm"+n].NameComm + " et " + jsoami.CName );
			
				var creancesn = creancesjso.listecreancescommuns["comm" + n];
				var couleurcreance = "grey";
				if (creancesn.nbLignes > 0) {
			
				for( var k = creancesn.nbLignes - 1; k >= 0 ; k-- ){						
					var jsocreance = creancesn["line" + k];
					var strgnum = "line"+k;
					
					var date = jsocreance.CreationDate.substring(0, 10).split("-")
					, jour = date[2]
					, mois = date[1]
					, annee = date[0];
					
					var heuremin = jsocreance.CreationDate.substring(11, 16)
					, heure = heuremin.substring(0,2)
					, minut = heuremin.substring(3,5);
					
					
					var strgcreance = "Le " + jour + "/" + mois + "/" + annee + " à " + heure + "h" + minut + ", ";
					strgcreance += (jsocreance.Price < 0)
						? cptek["comm" + n].NameComm + " a reçu : " 
						: cptek["comm" + n].NameComm + " a payé : ";
					var strgcreanceabs = (jsocreance.Price < 0) 
						? ((-1) * parseFloat(jsocreance.Price)).toFixed(2) + " euros"
						: parseFloat(jsocreance.Price).toFixed(2) + " euros";
					var couleurcreance = (jsocreance.Price < 0) ? "LightCoral" : "LightGreen";
					
										
					var strgcomment = (jsocreance.Comment==null) ? "" : "<i>" + jsocreance.Comment + "</i>";
						
				$tra.after($("<tr></tr>")
						.addClass("creances issu-de-dossier"+creancesjso.idDossier+" issu-de-ami"+creancesjso.idAmi+" creancescomptecommun")
					//	.attr({"id": "dossier" + creancesjso.idDossier +"-ami" + creancesjso.idAmi + "-creance"+jsocreance.Line, "dossierparent" : creancesjso.idDossier, "idami" : creancesjso.IdAmi, "idlgduser" : creancesjso.myId})				
						.append($("<td></td>"))
						
						.append($("<td></td>")
								.append($("<span></span>")
									.css({"padding-left": "50px","color" : "grey"})										
									.html(strgcreance)
								)
								.append($("<span></span>")
										.addClass("sommecreance")
										.css({"color" : couleurcreance })										
										.html(strgcreanceabs)
									)
							)
						.append($("<td></td>"))
						.append($("<td></td>")
							.addClass("infoami")
							.html(strgcomment)
							)
						)					
					}				
					}				
				else { $tra.after($("<tr></tr>")
						.addClass("creances issu-de-dossier"+creancesjso.idDossier+" issu-de-ami"+creancesjso.idAmi + " creancescomptecommun")
						.append($("<td></td>"))	
						.append($("<td></td>")
							.append($("<span></span>")
								.css({"padding-left": "60px" ,"color" : couleurcreance })
								.html("Aucune opération enregistrée entre "+ cptek["comm"+n].NameComm +" et " + jsoami.CName)
								)
							)
						.append($("<td colspan=2 ></td>"))	
							)
					}		
	
	
	
				}
			
			}//fin de boucle sur les communs			
		}//fin du if Nbcommuns>0
		
		
						
		if (creancesjso.listecreances.nbLignes > 0) {				
			for (var k=creancesjso.listecreances.nbLignes - 1; k>=0 ; k--)
				{						
				var jsocreance = creancesjso.listecreances["line"+k];
				var strgnum = "line" + k;
			
				var date=jsocreance.CreationDate.substring(0,10).split("-");
				var jour = date[2]; var mois= date[1]; var annee = date[0];
				
				var heuremin = jsocreance.CreationDate.substring(11,16);
				var heure = heuremin.substring(0,2);
				var minut = heuremin.substring(3,5);
			
				var strgcreance = "Le " + jour + "/" + mois + "/" + annee + " à " + heure + "h" + minut + ", ";
				strgcreance += (jsocreance.Price < 0)
					? "j'ai reçu : " 
					: "j'ai payé : ";
				var strgcreanceabs = (jsocreance.Price < 0) 
					? ((-1) * parseFloat(jsocreance.Price)).toFixed(2) + " euros"
					: parseFloat(jsocreance.Price).toFixed(2) + " euros";
				var couleurcreance = (jsocreance.Price < 0) ? "red" : "green";					
			
				var strgcomment = (jsocreance.Comment==null) ? "" : "<i> " + jsocreance.Comment + "</i>";
					
				$tra.after($("<tr></tr>")
					.addClass("creances issu-de-dossier"+creancesjso.idDossier+" issu-de-ami"+creancesjso.idAmi)
				//	.attr({"id": "dossier" + creancesjso.idDossier +"-ami" + creancesjso.idAmi + "-creance"+jsocreance.Line, "dossierparent" : creancesjso.idDossier, "idami" : creancesjso.IdAmi, "idlgduser" : creancesjso.myId})				
					.append($("<td></td>"))
					
					.append($("<td></td>")
							.append($("<span></span>")
								.css({"padding-left": "50px"/*,"color" : couleurcreance */})										
								.html(strgcreance)
							)
							.append($("<span></span>")
								.addClass("sommecreance")
								.css({"color" : couleurcreance })										
								.html(strgcreanceabs)
							)
						)
					.append($("<td></td>"))
					.append($("<td></td>")
						.addClass("infoami")
						.html(strgcomment)
						)
					)					
				}				
		}				
		else { $tra.after($("<tr></tr>")
			.addClass("creances issu-de-dossier"+creancesjso.idDossier+" issu-de-ami"+creancesjso.idAmi)
			.append($("<td></td>"))	
			.append($("<td></td>")
				.append($("<span></span>")
					.css({"padding-left": "60px"/*,"color" : couleurcreance */})
					.html("Aucune opération enregistrée entre moi et " + jsoami.CName)
					)
				)
				)
		}
				
	} // fin de showCreances
	
	,"addCreance" : function() {			
			var $thisrow =$(this);			
			var ref = {};
			ref["iddossier"] = $thisrow.attr("dossierparent");
			var iddoss = ref["iddossier"];
			
			var $trd = $thisrow.siblings("#node-dossier-pcea-"+iddoss);
					
			ref["myid"] = $trd.data("myId");
			ref["myrights"] = $trd.data("cptek").Rights;
			ref["idami"] = $thisrow.data("jsoami").IdContact;
		
			var nameami = $thisrow.data("jsoami").CName;
			var namedossier = $trd.data("cptek").Name;
			
			$thisrow.after($("<div></div>)")
				.attr("title","Dans le compte " + namedossier) 
				.addClass("newcreanceform pourami"+ref["idami"])
				
					.load("form_addcreance.php",function() {
					$thisf = $(this);
					$thisf.find("h4.titre").append(nameami +" :");
					$thisf.find("div.selectdir option").each(function(){
									$thatopt=$(this);
									if ($thatopt.hasClass("credit")) {$thatopt.css({"color":"green"});} else {$thatopt.css({"color":"red"});};
									if ($thatopt.hasClass("nomavant")) {$thatopt.prepend(nameami+" ");} else {$thatopt.append(" "+nameami+ " :");};
									}
								);
					$thisf.find("h4.titre").after($("<input type='hidden' name='myid' value='"+ref.myid+"'>"))
							.after($("<input type='hidden' name='myrights' value='"+ref.myrights+"'>"))
							.after($("<input type='hidden' name='iddossier' value='"+ref.iddossier+"'>"))
							.after($("<input type='hidden' name='idami' value='"+ref.idami+"'>"))
							;
							
					if (ref.myrights<7) {

						$thisf.find("div.selectdir").find("legend").html("Vous ne pouvez enregistrer que des écritures de dette.").end()
									.find("option.credit, option.debit.nomavant").remove();
						}
						
					$thisf.dialog({
						height: 'auto',
						width:'auto',
						modal: true,
						position: 'center',
						overlay: {
						  backgroundColor: '#000',
						      opacity: 0.5
							  },
						buttons: {
						       "Enregistrer": function() {
						        	$.ajax({
						                	url: "add_creance.php",
						                	type: "GET",
									data: $("div.newcreanceform.pourami"+ref["idami"]+" form").serialize(),
						             	  	 error: function(){
						                        	alert("Il y a une erreur avec AJAX");
						               		 	},
						                	beforeSend:function(){
											var lprice = $thisf.find("div.somme input").val();
						                				if (lprice>0) {	}
						                				else {alert("La somme doit être un nombre positif. Formulez la créance autrement.");
						                					throw new Error("Ajout d'opération annulée par le serveur. Prix incorrect.");
													}
											},
						                	success: function(answer) {
											if (answer["iddossier"]) {
											var myid=ref.myid;
											var idd = answer["iddossier"];						
											$(".DataTable.async.dossiers.pcea").comptes("getComptes",myid,idd);
											}
											}
										,
									dataType : "json"
								});
								$(this).dialog('destroy').remove();
								
						       		}//fin de envoyer
						      	,"Annuler": function() {
							var myid=ref.myid;
							var idd=ref.iddossier;
						        $(this).dialog('destroy').remove();
							$(".DataTable.async.dossiers.pcea").comptes("getComptes",myid,idd);
						        	}
							}//fin de buttons du popup				
					
					}//fin param dialog
					
					)//fin du dialog
					
					
					}
					) // fin du load form
				);
			
			
		
																
			} // fin de addCreance	
	,"submit" : function(type,refer) {	//refer : objet jso contenant les references utiles (iddossier,idami,myrights..)
		switch (type)
		{case "creance" :	//inutile
	
		case "ami" :			
			var $inputmail = $(this).siblings("input");
			var datatosend = {};
			var name = $inputmail.attr("name");
			datatosend[name]=$inputmail.val();		
			if (datatosend[name] == ">>saisir une adresse ici" || datatosend[name] == "")
				{alert ("Il faut entrer une adresse mail");
				break;
				}
			else {
				$.extend(datatosend,refer);			
				$.ajax({url : "check_amitoadd.php?debug=1" 
					,data : datatosend
					,cache : false
					,success : function(msg){
							//jso = $.parseJSON(msg);
							
							
						if (msg["isknownuser"]=="no") {						
						
							if (msg["validmail"]=="yes") {							
							
							msg["namedossier"]=$("#node-dossier-pcea-"+msg.iddossier).data("cptek").Name;
							
							var k=0;
							var namesamis =[];
							var idsamis =[];
							$(".amis.dossierparent"+msg["iddossier"]).each(function(){
											var thisidami=$(this).data("jsoami").IdContact;
											var thisnameami=$(this).data("jsoami").CName;
										
											namesamis[k] = thisnameami;
											idsamis[k] = thisidami;
											k++;
											}
											);									
							msg["idsamis"]=idsamis;
							msg["namesamis"]=namesamis;
						
							$(".DataTable.async.dossiers.pcea").after($("<div> </div>")
											.attr({"id":"wrapperinvitform","title":"Envoi d'une invitation"})
											.load("form_mailinvit.php", function() {
											
											//mail message à revoir, inutilisé pour le moment
											var mailmessage = "Bonjour, <br/>"
												+" Vous êtes invité(e) par " + msg.myname + " à participer au petit compte entre amis " + msg.namedossier +"./n"
												 + " Rendez vous sur www.coopeshop.net pour valider l'invitation et participer au compte.";
												 
											$(this).find("div.entete").find("input#subject").val("Invitation dans Coopeshop").end()
														.find("input#mailto").val(msg.mailcontact).end()
														.find("input#mailfrom").val(msg.mymail).end()
														;
										//	$(this).find("div.message").find("textarea").html(mailmessage);
											
											$(this).dialog( {
												height: 'auto',
												width:'auto',
												modal: true,
												position: 'center',
										        	overlay: {
										              	  backgroundColor: '#000',
										          	      opacity: 0.5
										      			  },
											        buttons: {
											               "Envoyer l'invitation": function() {
											                	$.ajax({
											                        	url: "send_mailinvit.php",
											                        	type: "GET",
															data: $("#wrapperinvitform form").serialize(),
											                     	  	 error: function(){
											                                	alert("Il y a une erreur avec AJAX");
											                       		 	},
											                        	beforeSubmit:function(){},
											                        	success: function(answer) {
																	if (answer.Status == "1")
																		{alert("Mail envoyé");}
																	else {alert("Problème : mail non envoyé...");}
																	callpopupform("addami",msg,"Quelques informations à compléter sur " + msg["namecontact"] + ":");
																	}
																,
															dataType : "json"
														});
														$(this).dialog('destroy').remove();
								
											               		}//fin de envoyer
											              	,"Annuler l'envoi": function() {
										                        $(this).dialog('destroy').remove();
													callpopupform("addami",msg,"Quelques informations à compléter sur " + msg["namecontact"] + ":");
											                	}
											        	}//fin de buttons du popup
												}
												);//fin de dialog
											}
											)//fin du load
							
											);	
							
							}  //fin de validmail=yes)
							
							else 	alert("L'adresse mail saisie n'est pas une adresse mail valide...");	
																		
						}// fin de (isknownuser=no)
						
						else if (msg["isknownuser"]=="yes"){
							var isalreadyami=false;
							var k=0;
							var namesamis =[];
							var idsamis =[];
							$(".amis.dossierparent"+msg["iddossier"]).each(function(){
											var thisidami=$(this).data("jsoami").IdContact;
											var thisnameami=$(this).data("jsoami").CName;
										
											namesamis[k] = thisnameami;
											idsamis[k] = thisidami;
											k++;
											if (msg["idcontact"]==thisidami) {
												isalreadyami=true;
												alert("Cet adresse est celle de "+msg["namecontact"]+", il/elle est déjà participant(e) de ce compte.");}		
											}
													);
							
							if (msg["myid"]==msg["idcontact"]) {alert("Farceur! Vous avez saisi votre propre adresse mail !");isalreadyami=true;}									
							
							if (isalreadyami==false) 	//adresse valide
								{
							msg["idsamis"]=idsamis;
							msg["namesamis"]=namesamis;
																									
							callpopupform("addami",msg,"Quelques informations à compléter sur " + msg["namecontact"] + ":");
							
								}
							}	
						}
					,async : true 
					,type : "GET" 
					,dataType : "json"
					});
				}
			break;
		}
	}
	};	//fin de methods{}	
	//Plugin JQuery
	$.fn.comptes = function(method){
		if ( method && methods[method] ) {
			return methods[method].apply(this, Array.prototype.slice.call(arguments, 1));
		} else if ( typeof method === 'object' || ! method ) {
	      	return methods.init.apply(this, arguments);
		} else {
	      	$.error('$.comptes : Unknown method ' +  method);
		}
	};
	jQuery.extend($.fn.comptes, methods);
})(jQuery);

function callpopupform(action,ref,titre) {	//action : définit url du formulaire popup, urlsubmit, ref : references à passer, titre : title de la pseudo fenêtre popup
	
	var myid;
	if (ref.myid) {myid=ref.myid};
	
	var iddossier; 
	if(ref["iddossier"]!=null){iddossier= ref["iddossier"];};
	
	var namedossier;
	if (ref.namedossier != null) {namedossier = ref.namedossier};
	
	var myrights;
	if (ref.myrights!=null) {myrights=ref.myrights};
	
	switch (action) {	
		case "adddossier" : var urlform = "form_adddossier.php";
				var urlsubmit = "add_dossier.php";
				break;
				
		case "edituser" : var urlform = "form_infouser.php";
				var urlsubmit = "edit_ami.php";
				if (ref.jsoami!=null) {
						nameami=ref.jsoami.CName;
						mailami = ref.jsoami.EMail;
						amistatus = ref.jsoami.AmiStatus;
						amirights = ref.jsoami.AmiRights;
						amicomment = ref.jsoami.CommentUser;
						idami = ref.jsoami.IdContact;
						nbrecommun=ref.jsoami.nbreCommun;
						iscommun = ref.jsoami.isCommun;				
						};
				if (ref.tableauidname!=null) {tableauidname = ref.tableauidname};
				break;
				
		case "addami" : var urlform = "form_addami.php";
				var urlsubmit = "add_ami.php";
				var idcontact;
				var namecontact;
					if (ref.idcontact) {idcontact = ref.idcontact;}
					if (ref.namecontact)	{namecontact=ref.namecontact;}
				var namesamis=[];
				var idsamis=[];
					if (ref["namesamis"]&&ref["idsamis"]) {namesamis=ref["namesamis"]; idsamis=ref["idsamis"]}; 
				break;
				
		default : break;
	}	
	$(".DataTable.async.dossiers.pcea").after($("<div> </div>")
					.attr({"id":"wrapperpopupform","title":titre})
					.load(urlform, function() {
						switch (action) 
							{
																		
				case "addami" : //urlform = form_editami
					
						$divcheck = $(this).find("div.checkamiscommun");
						$divcheckfieldset = $divcheck.find("fieldset.amis");
				//checkboxs amiscommun	
					var strcommuns = namecontact + " ne sera en compte commun avec personne.";
						
					$divcheck.find("span.conclusion").text(strcommuns);
					$divcheck.find("legend.explication").text("Cliquez sur les noms ci-dessous pour définir les amis en compte commun avec " +namecontact + ".");
					var totalpresents = namesamis.length +1;
					$divcheckfieldset.append($("<input type='hidden' name='totalpresents' value='"+ totalpresents+"' />"));
					
					namesamis[namesamis.length]="moi-même";
					idsamis[idsamis.length]=ref.myid;
			
					var newstringlist = function(from) {
						$that=from;
						var listestr = namecontact;
						var listecommuns = new Array();
						var k=0;
						$that.parent().find("span.iscommun").each(function() {
										listecommuns[k]=$(this).attr("name");
										k++
												}
												);
							if (listecommuns.length > 0)
							 	{	//liste des amis en compte commun
								listestr += " sera en compte commun avec "
								for (var n=0;n<listecommuns.length;n++)
										{ 
										listestr += (n==0) ? "" : (n==listecommuns.length-1) ? " et " : ", ";
										listestr += listecommuns[n];
										}
					
										listestr +=".";											
											}
							else {listestr += " ne sera en compte commun avec personne.";}
						return listestr;
					
					};
					for (var i=0;i<totalpresents;i++) 
						{						
						$divcheckfieldset.append($("<span></span>").addClass("isnotcommun")
											.text(namesamis[i])
											.attr("idami",idsamis[i])
											.attr("name",namesamis[i])
											.append($("<input type='hidden' name='ami"+ i +"' value='0' />"))
											.click(function(){
												$this=$(this);
												if ($this.hasClass("isnotcommun")) { 
													$this.addClass("iscommun")
														.removeClass("isnotcommun");
													$this.find("input").val($this.attr("idami"));
																
													$divcheck.find("span.conclusion").text(newstringlist($this));			
																
														}
												else { $this.removeClass("iscommun").addClass("isnotcommun");
													$this.find("input").val("0");
													
													$divcheck.find("span.conclusion").text(newstringlist($this));
														}
												})
												);
											
						};
							

				//label droits	
						$divcheck.siblings("div.selectdroits").find("label").text("Fixer les droits de " + namecontact + ":");	
					
				//titre	
						$divcheck.siblings("legend.titre").text("Dans le petit compte : "+namedossier);
				
				//transmettre iddossier idinvite et myrights
						$divcheck.parent().append($("<input type='hidden' name='idinvite' value='"+ idcontact +"' />"))
								.append($("<input type='hidden' name='iddossier' value='"+ iddossier +"' />"))
								.append($("<input type='hidden' name='myrights' value='"+ myrights +"' />"));
						break;	
						 // fin du cas (form_editami.php)
							
				case "adddossier" : // urlform = form_adddossier
					
						var date=new Date();					
						var stringdate = date.getDate() + "-" + (date.getMonth()+1) + "-" + date.getFullYear();
						
						var defaultname = "Nouveau compte du " + stringdate;
						
						$(this).find("legend.info.user").text(defaultname);
						$(this).find("div.titre input").val(defaultname);
						
						break;
						// fin du cas (form_editdossier.php)
						
				case "edituser" : //urlform = form_infouser
						$(this).find("legend.nameuser").text(nameami);
						
						$divinfo = $(this).find("div.infogene");
						
						$divinfo.find("input#emailami").prop("disabled",true)
									.val(mailami);
						$divinfo.find("input#nameami").prop("disabled",true)
									.val(nameami);
					
						if (iddossier != null) {
					
							
					
							var newstringlist = function(from,temps) {
								$that=from;
								var listestr;
								
								var newcommuns = new Array();
								var n=0;
								$that.parent().find("span.iscommun").each(function() {
												newcommuns[n]=$(this).attr("name");
												n++
														}
														);
														
								if (newcommuns.length > 0)
								 	{	//liste des amis en compte commun
									if (temps=="now") {
										listestr = (idami==myid) ? "Vous êtes" : nameami + " est";
										listestr += " actuellement en compte commun avec ";}
									if (temps=="futur") {
										listestr = (idami==myid) ? "Vous serez" : nameami + " sera";
										listestr += " en compte commun avec ";}
									
									for (var i=0;i<newcommuns.length;i++)
											{ 
											listestr += (i==0) ? "" : (i==newcommuns.length-1) ? " et " : ", ";
											listestr += newcommuns[i];
											}
						
											listestr +=".";											
												}
								else {	if (temps=="now") {
										listestr = (idami==myid) ? "Vous n'êtes" : nameami + " n'est";
										listestr += " actuellement en compte commun avec personne.";}
									if (temps=="futur") {
										listestr = (idami==myid) ? "Vous ne serez" : nameami + " ne sera";
										listestr += " en compte commun avec personne.";};
									
									}	
								return listestr;
							
							};
							
					
							$(this).find("legend.statutuser").text("Dans le petit compte '" + namedossier+"'");
							$divdossier = $(this).find("div.statutindossier");
							
							$divdossier.find("input#comment").val(amicomment);							
							
							$divdossier.find("div.selectdroits select option").each(function(){$thatopt=$(this);
												if ($thatopt.val() == amirights) {$thatopt.prop("selected",true);$thatopt.css("font-weight","Bold");
																}
												});
							
							
							$checks = $divdossier.find("div.checkamiscommun fieldset");
							var k = 0; //pour numéroter les input et compte les amis à la sortie
							for (var key in tableauidname) { 
								var sel = false;
								 var id = key; var name=tableauidname[key];
								 if (id==idami) {id=myid; name="moi-même";}
								 if (nbrecommun!=null)	{if (nbrecommun!=0) {for (var j=0;j<nbrecommun;j++) {
									 							if (id==iscommun["com"+j]) {sel=true;}
									 								}
														}
												}
								var classe = (sel) ? "iscommun isoldcommun" :"isnotcommun";	
								$checks.find("fieldset.listeamis").append($("<span></span>")
												.addClass(classe)
												.text(name)
												.attr("idami",id)
												.attr("name",name)
												.append($("<input type='hidden' name='ami"+ k +"' value='"+((sel) ? id : 0) +"' />"))
												.click(function(){
													$this=$(this);
													if ($this.hasClass("isnotcommun")) { 
														$this.addClass("iscommun")
															.removeClass("isnotcommun");
														$this.find("input").val($this.attr("idami"));
																	
														$checks.parent().find("span.conclusion").text(newstringlist($this,"futur"));			
																	
															}
													else { $this.removeClass("iscommun").addClass("isnotcommun");
														$this.find("input").val("0");
														
														$checks.parent().find("span.conclusion").text(newstringlist($this,"futur"));
															}
													}));
												
											k++;				
											}								
						var nombreamis = k;	
			
						var listenow = newstringlist($("div.checkamiscommun fieldset.listeamis span"),"now");	
							
							
						$divdossier.find("div.checkamiscommun").find("legend").text(listenow);
							
						if (myrights<15) {	
							$divdossier.find("select.selectdroits").attr("disabled",true);							
							$divdossier.find("div.checkamiscommun fieldset.listeamis").hide();	
							$divdossier.find("div.checkamiscommun span.modedemploi").hide();
							$divdossier.append('</br></br>')
								.append($('<span class="notabene"> </span>')
								.text("Vous n'avez pas les droits administrateurs pour modifier ces données")
								.css("font-weight","Bold")
									);
									}																	
								
					//transmettre iddossier idinvite, myrights et nombreamis
						$(this).find("div.infogene").append($("<input type='hidden' name='idami' value='"+ idami +"' />"))
									.append($("<input type='hidden' name='iddossier' value='"+ iddossier +"' />"))
									.append($("<input type='hidden' name='nombreamis' value='"+ nombreamis +"' />"))
									.append($("<input type='hidden' name='myid' value='"+ myid +"' />"))
									.append($("<input type='hidden' name='myrights' value='"+ myrights +"' />"));
						} // fin de if iddossier	
									
					else { $(this).find("div.statutindossier").remove();					
							}							
					break;}	
	
					$(this).dialog({
					        height: 'auto',
					        width:'auto',
					        modal: true,
					        position: 'center',
					        overlay: {
					                backgroundColor: '#000',
					                opacity: 0.5
					        },
					        buttons: {
					               'Envoyer': function() {
					                	$.ajax({
					                        	url: urlsubmit,
					                        	type: "GET",
									data: $("#wrapperpopupform form").serialize(),
					                     	  	 error: function(){
					                                	alert("Il y a une erreur avec AJAX");
					                       		 	},
					                        	beforeSubmit:function(){},
					                        	success: function(answer) {
											jsoanswer=$.parseJSON(answer);
											var myid;
											if (jsoanswer["myid"]) {myid = jsoanswer["myid"]};
											var idd; 
										
											if (jsoanswer["iddossier"]) {
												idd = jsoanswer["iddossier"];
											
								
												$(".DataTable.async.dossiers.pcea").comptes("getComptes",myid,idd);
												}
											else {$(".DataTable.async.dossiers.pcea").comptes("getComptes",myid);}
											}
										});
								$(this).dialog('destroy').remove();
							//	$(".DataTable.async.dossiers.pcea>table>tbody>tr.addtodossier"+iddossier+">td.inputmailami").find("a.linkaddami").removeClass("disabled").end()
							//							.find("input,a.linksubmitami,a.cancel").remove(); 
					               		}//fin de envoyer
					              	,'Annuler': function() {
				                        $(this).dialog('destroy').remove();
							$(".DataTable.async.dossiers.pcea>table>tbody>tr.addtodossier"+iddossier).find("td.inputmailami a.linkaddami").removeClass("disabled").end()
															.find("td.inputmailami input, td.inputmailami a.linksubmitami,td.annuleaddami a.cancel").remove(); 
					                	}
					        	}//fin de buttons du popup
						});//fin de dialog
					})//fin du load urlform
					);//fin de l'insertion du div#wrapperpopupform par jquery
	} //Fin de callpopupform

function checkamiresign(idami,iddoss) {
			var mydata = {};
			mydata.idami = idami;
			mydata.iddossier = iddoss;
			var answer;		
			try {
			$.ajax({url :  "check_amiresign.php"
				,data : mydata
				,cache : false
				,success : function(msg) {
						if (msg.resign == "ok") {
							answer = true;
							}
						else answer = false;			
						}
				,type : "GET"
				,async : false
				,dataType : "json"
				});
				}
			catch (e){ throw new Error("problème avec AJAX pour autoriser l'abandon de poste..."+ e );}
			return answer;
	} // Fin de checkamiresign
	
function print_r(theObj) {    
    var win_print_r = "";   
    for(var p in theObj){  
           var _type = typeof(theObj[p]);  
           if( (_type.indexOf("array") >= 0) || (_type.indexOf("object") >= 0) ){  
                  win_print_r += "<li>";  
                  win_print_r += "["+_type+"] =>"+p;  
                  win_print_r += "<ul>";  
                  win_print_r += print_r(theObj[p]);  
                  win_print_r += "</ul></li>";  
         } else {  
                 win_print_r += "<li>["+p+"] =>"+theObj[p]+"</li>";  
         }  
     }  
     return win_print_r;  
}