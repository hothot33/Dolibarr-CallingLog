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
 *	\file       includes/modules/calling/alert_norun.php
 *	\brief
 *	\author     Oscim <mail oscim@users.sourceforge.net>
 *	\version    $Id: alert_norun.php,v 1.56 2012/06/10 15:28:01 oscim Exp $
 */

include_once(DOL_DOCUMENT_ROOT ."/calling/core/modules/calling/calling_alert.php");

Class alert_norun
	extends calling_alert{


	/**
		@var level current mod
	*/
	public $version='dolibarr';		// 'development', 'experimental', 'dolibarr'
	/**
		@var string name api
	*/
	public $nom='norun';
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
			return $langs->trans("CallingAlertNorunDescription");
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
				return "";
	}

	/**
		@brief specific Activate
	*/
	function Activate($db, $conf){
			return true;
	}


	/**
		@brief specific Unactivate
	*/
	function UnActivate($db, $conf){
			return true;
	}

}

?>