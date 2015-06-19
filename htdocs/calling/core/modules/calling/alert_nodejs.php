<?php
/*  Copyright (C) 2012		 Oscim					       <aurelien@oscim.fr>
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 */

/**
 *	\file       includes/modules/calling/alert_nodejs.php
 *	\brief      File of class to manipulate calling by ovh api
 *	\author     Oscim <mail oscim@users.sourceforge.net>
 *	\version    $Id: alert_nodejs.php,v 1.56 2012/06/10 15:28:01 oscim Exp $
 */

include_once(DOL_DOCUMENT_ROOT ."/calling/core/modules/calling/calling_alert.php");

Class alert_nodejs
	extends calling_alert{


	/**
		@var level current mod
	*/
	public $version='dolibarr';		// 'development', 'experimental', 'dolibarr'
	/**
		@var string name api
	*/
	public $nom='nodejs';
	/**
		@var int version api
	*/
	public $api_vers='1.0';

	public $error='';


	/**     \brief      Return description of numbering module
		*      \return     string      Text with description
		*/
	function info(){
		global $langs,$db;
			return $langs->trans("CallingAlertNodejsDescription");
	}


	/**     \brief      Renvoi un exemple de numerotation
	 *      \return     string      Example
	 */
// 	function getDetail(){
// 		return "emission &amp; <s>reception</s>";
// 	}


	/**
		@brief      Url for external notifi in dolibarr
		@return     string
	 */
	function getUrlForNotification(){
		return $this->internalurlnotif.'?account=_ACCOUNT_&caller=_CALLER_&callee=_CALLEE_&type=_N_TYPE_&callref=_CALLREF_&version=_N_VERSION_';
	}


	/**     \brief      Test si les numeros deje en vigueur dans la base ne provoquent pas de
	 *                  de conflits qui empechera cette numerotation de fonctionner.
	 *      \return     boolean     false si conflit, true si ok
	 */
	function canBeActivated()
	{
		global $conf,$langs;

		return true;
	}





	/**
		@brief put in function DipslayTop  in main.inc.php
	*/
	function DipslayTop(){
		global $langs, $conf, $user;


				$var = "
					var socket = io.connect('".$conf->global->NODEJS_ADDON_URL_SERVER.":".$conf->global->NODEJS_ADDON_URL_PORT ."');

					var CALLING_ALERT_TYPE_MODE_DISPLAY_BLOCK_USER = ". $conf->global->CALLING_ALERT_TYPE_MODE_DISPLAY_BLOCK_USER." ;
					var CallingAlertTiersContact = '". $langs->trans("CallingAlertTiersContact")."' ;
					var CallingAlertTiersSociete = '". $langs->trans("CallingAlertTiersSociete")."' ;
					var CallingAlertFiche = '". $langs->trans("CallingAlertFiche")."' ;
					var CallingAlertEdit = '". $langs->trans("CallingAlertEdit")."' ;
					var CallingAlertTodo = '". $langs->trans("CallingAlertTodo")."' ;

					var CallingAlertTiersCollaborateur = '". $langs->trans("CallingAlertTiersCollaborateur")."' ;
					var CallingAlertUserFiche = '". $langs->trans("CallingAlertUserFiche")."' ;

						function notification(data)
						{
							var obj = JSON.parse(data);

							var box = $('#alertcalling');
							var button = $('#callingbutton');


							if(obj.foruser ==  ".$user->id.") {
									if(obj.message == 1 ) {

										var mes = '';

													if(CALLING_ALERT_TYPE_MODE_DISPLAY_BLOCK_USER) {
														var collaborateur = obj.collaborateur;
														if( collaborateur.id != ".$user->id." ) {
															mes +=' <h3>' + CallingAlertUserFiche + ' - ' + collaborateur.name + ' ' + collaborateur.lastname + '</h3> ';
															mes +='<ul> ';
															mes +='<li><a href=\"http://".$_SERVER['HTTP_HOST'].DOL_URL_ROOT."//user/fiche.php?id=' + collaborateur.id + '&amp;action=edit\">' + CallingAlertTiersCollaborateur + '</a></li> ';
															mes +='</ul> ';
														}
													}

											for (var i in obj.user) {
												var object = obj.user[i];

													if(object.type =='contact') {
														mes +=' <h3>' + CallingAlertTiersContact + ' - ' + object.name + '</h3> ';
														mes +='<ul> ';
														mes +='<li><a href=\"http://".$_SERVER['HTTP_HOST'].DOL_URL_ROOT."/contact/fiche.php?id=' + object.id + '\">' + CallingAlertFiche + '</a></li> ';
														mes +='<li><a href=\"http://".$_SERVER['HTTP_HOST'].DOL_URL_ROOT."/contact/fiche.php?id=' + object.id + '&amp;action=edit\">' + CallingAlertEdit + '</a></li> ';
														mes +='</ul> ';
													}
													if(object.type =='societe') {
														mes +=' <h3>' + CallingAlertTiersSociete + ' - ' + object.name + '</h3> ';
														mes +='<br /> ';
														mes +='<a href=\"http://".$_SERVER['HTTP_HOST'].DOL_URL_ROOT."/societe/soc.php?socid=' + object.id + '\">' + object.name + '</a> ';
														mes +='<ul> ';
														mes +='<li><a href=\"http://".$_SERVER['HTTP_HOST'].DOL_URL_ROOT."/societe/soc.php?id=' + object.id + '\">' + CallingAlertFiche + '</a></li> ';
														mes +='<li><a href=\"http://".$_SERVER['HTTP_HOST'].DOL_URL_ROOT."/societe/soc.php?id=' + object.id + '&amp;action=edit\">' + CallingAlertEdit + '</a></li> ';
														mes +='<li><a href=\"http://".$_SERVER['HTTP_HOST'].DOL_URL_ROOT."/societe/soc.php?id=' + object.id + '&amp;action=edit&amp;status=todo\">' + CallingAlertTodo + '</a></li> ';
														mes +='</ul> ';
													}
											}


											box.html(mes);
											box.addClass('alert');

											button.html('".$langs->trans("CallingButtonDisplay")."');
											button.addClass('incoming');

									} else {
											box.html('');
											box.hide();
											box.removeClass('alert');

											button.html('');
											button.removeClass('incoming');
									}
							}
						}

						$(function() {

							$('.login_block table.nobordernopadding tr').append(
								'<td width=\"14\" valign=\"top\"><div id=\"advancedjs_button\"> ' +
								'<a href=\"#\" id=\"callingbutton\" class=\"espace_menu_right\" title=\"user connect\"> </a>' +
								'<div id=\"alertcalling\" ></div>' +
								'</div></td>'
							);

							$('#callingbutton').toggle(
								function(){
									$('#alertcalling').show();
									$('#alertcalling').addClass('opened');
									return false;
								},
								function(){
									$('#alertcalling').hide();
									$('#alertcalling').removeClass('opened');
									return false;
								}
							); ";

				/*
				* Disconnect if logoff
				*/
				$var .="	$('.login').parent().click( function(){
									socket.emit('logoff', { userid : '".$user->id."'  });
									return true;
								});";


						$var .="
								socket.on('notification', function(x) { notification(x); });

								socket.emit('foruser', { userid : '".$user->id."' , calling_alert_type : '".$conf->global->CALLING_ALERT_TYPE."' });
						});
						";


				return $var;
	}

//
	/**
		@brief specific Activate
	*/
	function Activate($db, $conf){
				dolibarr_set_const($db, "MAIN_HTML_HEADER","<link rel=\"stylesheet\" type=\"text/css\" href=\"".DOL_URL_ROOT."/calling/lib/alert_advancedjs.css\" /><script type=\"text/javascript\" src=\"".$conf->global->NODEJS_ADDON_URL_SERVER.":".$conf->global->NODEJS_ADDON_URL_PORT."/socket.io/socket.io.js\"></script><script type=\"text/javascript\" src=\"".DOL_URL_ROOT."/calling/lib/lib.calling.js.php\"></script>",'chaine',0,'',$conf->entity);
	}

	/**
		@brief specific Unactivate
	*/
	function UnActivate($db, $conf){
				dolibarr_set_const($db, "MAIN_HTML_HEADER","",'chaine',0,'',$conf->entity);
	}
}

?>