<?php
/* Copyright (C) 2003      Rodolphe Quiedeville <rodolphe@quiedeville.org>
 * Copyright (C) 2012		 Oscim					       <oscim@users.sourceforge.net>
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
 *	\file       htdocs/calling.php
 *	\brief      File for execute calling process
 *	\author     Oscim <mail oscim@users.sourceforge.net>
 *	\version    $Id: calling.php,v 1.56 2012/06/10 15:28:01 oscim Exp $
 *  \note
			Si $_GET['doli'], l'utilisateur doit être connecté, sans quoi la page renverra vers login
			La valeur de doli n'a aucune importance. Dans le cas

			L'appel ne comptant que $_GET est strictement array('doli'), alors seul le popup d'alert sera affiché
			(Il est necessaire que l'api utilisé exploite la notifications des appels entrant)
 */


	// internal call script
	if( isset($_GET['doli']) )
		$require="main.inc.php";
	// externe call script
	else
		$require="master.inc.php";

	$res=0;
	if (! $res && file_exists("../".$require)) $res=@include("../".$require);			// for root directory
	if (! $res && file_exists("../../".$require)) $res=@include("../../".$require);		// for level1 directory ("custom" directory)
	if (! $res && file_exists("../../../".$require)) $res=@include("../../../".$require);	// for level2 directory
	if (! $res) die("Include of main fails");

	require_once(DOL_DOCUMENT_ROOT . "/calling/class/calling.class.php");

	// for trace in agenda/action exts
	if( $conf->global->CALLING_LOGS_IN_ACTION == 'yes')
		require_once(DOL_DOCUMENT_ROOT . "/calling/class/calling.actioncomm.class.php");

	// element langue
	$langs->load("calling@calling");
	
	// define init script time
	$time = microtime();
	

	/**
		@remarks Section for inform user in event sip
			this is called and return all current event
	*/
	if ($conf->global->CALLING_ALERT_ADDON !='' && isset($_GET['doli']) && count($_GET) == 1 ){


		// no notifications in popup page
		if ($conf->global->CALLING_ALERT_TYPE ==0)
			return;

		$file = $conf->global->CALLING_ALERT_ADDON;

		if(!include_once(DOL_DOCUMENT_ROOT ."/calling/core/modules/calling/".$file.".php"))
			exit;

		$module = new $file($db);


		if($module->check_right($user) ){
			$res = $module->LoadData($user);

			include( DOL_DOCUMENT_ROOT ."/calling/tpl/alert.tpl.php" );
			exit;
		}
	}







	/**
		@remarks Section for call or receive Sip process
	*/
	elseif ($conf->global->CALLING_ADDON !='' && count($_GET)>0){

		/*
			Load SubModule Specific Link for Provider and provider api
		*/
		$file = $conf->global->CALLING_ADDON;
		if(!include_once(DOL_DOCUMENT_ROOT ."/calling/core/modules/calling/".$file.".php"))
			exit;
		$module = new $file($db);

		/*
			Incomming Call
			Test if module containt method Process_Receive()
			For intercept and execute action
				Trace call in action/agenda
				Display Info in all page dolibarr
		*/
		$res = -99;

		if ($module->UseReceving() !=false){
			// Changement des droit de l'utilisateur courant
			//
			$user->fetch($conf->global->CALLING_INCOMING_USER);
			$user->getrights('');

			$res = $module->Process_Receive($user, $time);
		}


		/*
			Outgoing Call
			ClickToDial Dialer
			Trace call in action/agenda
		*/
		if ( $res ===  -99  && $module->UseSending()){
			$module->Process_Send($user);
		}

	}

?>