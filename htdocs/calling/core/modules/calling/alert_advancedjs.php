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
 *	\file       includes/modules/calling/alert_advancedjs.php
 *	\brief      File of class to manipulate calling by ovh api
 *	\author     Oscim <mail oscim@users.sourceforge.net>
 *	\version    $Id: alert_advancedjs.php,v 1.56 2012/06/10 15:28:01 oscim Exp $
 */

include_once(DOL_DOCUMENT_ROOT ."/calling/core/modules/calling/calling_alert.php");

Class alert_advancedjs
	extends calling_alert{


	/**
		@var level current mod
	*/
	public $version='dolibarr';		// 'development', 'experimental', 'dolibarr'
	/**
		@var string name api
	*/
	public $nom='advancedjs';
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
			return $langs->trans("CallingAlertAdvancedjsDescription");
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
		global $langs;


				return "
						function file(fichier){
							if(window.XMLHttpRequest) // FIREFOX
									xhr_object = new XMLHttpRequest();
							else if(window.ActiveXObject) // IE
									xhr_object = new ActiveXObject(\"Microsoft.XMLHTTP\");
							else
									return(false);
							xhr_object.open(\"GET\", fichier, false);
							xhr_object.send(null);
							if(xhr_object.readyState == 4)
									return(xhr_object.responseText);
							else return(false);
						}

						function InCall(){
								texte = file('".DOL_URL_ROOT."/calling/calling.php?doli=1');

								var box = $('#alertcalling');
								var button = $('#callingbutton');

								if(texte.length > 0 ){
									box.html(texte);
									box.addClass('alert');

									button.html('".$langs->trans("CallingButtonDisplay")."');
									button.addClass('incoming');


									setTimeout(InCall, 5000);
								}
								else{
									box.html('');
									box.hide();
									box.removeClass('alert');

									button.html('');
									button.removeClass('incoming');
									setTimeout(InCall, 3000);
								}
						}

						$(function() {

							$('.login_block ').append(
								'<div id=\"advancedjs_button\" class=\"login_block_other\"> ' +
								'<a href=\"#\" id=\"callingbutton\" class=\"espace_menu_right\" title=\"user connect\"> </a>' +
								'<div id=\"alertcalling\" ></div>' +
								'</div>'
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
							);

							InCall();
						});
				";
	}


	/**
		@brief specific Activate
	*/
	function Activate($db, $conf){
				dolibarr_set_const($db, "MAIN_HTML_HEADER","<link rel=\"stylesheet\" type=\"text/css\" href=\"".DOL_URL_ROOT."/calling/lib/".__CLASS__.".css\" /><script type=\"text/javascript\" src=\"".DOL_URL_ROOT."/calling/lib/lib.calling.js.php\"></script>",'chaine',0,'',$conf->entity);
	}

	/**
		@brief specific Unactivate
	*/
	function UnActivate($db, $conf){
				dolibarr_set_const($db, "MAIN_HTML_HEADER","",'chaine',0,'',$conf->entity);
	}
}

?>