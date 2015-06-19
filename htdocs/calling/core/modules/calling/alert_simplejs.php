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
 *	\file       includes/modules/calling/alert_simplejs.php
 *	\brief      File of class to manipulate calling by ovh api
 *	\author     Oscim <mail oscim@users.sourceforge.net>
 *	\version    $Id: alert_simplejs.php,v 1.56 2012/06/10 15:28:01 oscim Exp $
 */

include_once(DOL_DOCUMENT_ROOT ."/calling/core/modules/calling/calling_alert.php");

Class alert_simplejs
	extends calling_alert{


	/**
		@var level current mod
	*/
	public $version='dolibarr';		// 'development', 'experimental', 'dolibarr'
	/**
		@var string name api
	*/
	public $nom='simplejs';
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
			return $langs->trans("CallingAlertSimplejsDescription");
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

// 		if( $conf->global->CLICKTODIAL_URL !=''){

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

								if(texte.length > 0 ){


									$('#alertcalling').remove();
									$('<div id=\"alertcalling\" ></div>').html(texte).dialog();

									setTimeout(InCall, 5000);
								}
								else{
									$('#alertcalling').remove();
									setTimeout(InCall, 3000);
								}
						}

						$(function() {
							InCall();
						});
				";
	}


	/**
		@brief specific Activate
	*/
	function Activate($db, $conf){
				dolibarr_set_const($db, "MAIN_HTML_HEADER","<script type=\"text/javascript\" src=\"".DOL_URL_ROOT."/calling/lib/lib.calling.js.php\"></script>",'chaine',0,'',$conf->entity);
	}

	/**
		@brief specific Unactivate
	*/
	function UnActivate($db, $conf){
				dolibarr_set_const($db, "MAIN_HTML_HEADER","",'chaine',0,'',$conf->entity);
	}
}

?>