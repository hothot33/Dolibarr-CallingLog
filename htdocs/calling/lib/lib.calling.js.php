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
 *	\file       includes/modules/calling/alert_simplejs.php
 *	\brief      File of class to manipulate calling by ovh api
 *	\author     Oscim <mail oscim@users.sourceforge.net>
 *	\version    $Id: alert_simplejs.php,v 1.56 2012/06/10 15:28:01 oscim Exp $
 */
define('NOREQUIREMENU', false);
define('NOREQUIREHTML', false);
define('NOREQUIREAJAX', false);
// define('NOREQUIREMENU', false);

require("../../main.inc.php");

if(!isset($user) || $user->id <=0)
	exit;

if(file_exists(DOL_DATA_ROOT.'/calling/tmp_calling_js.'.$user->id.'.txt')){
	include(DOL_DATA_ROOT.'/calling/tmp_calling_js.'.$user->id.'.txt');
	exit;
}

	// element langue
	$langs->load("calling@calling");

if ($conf->global->CALLING_ALERT_ADDON !='' ){
	header("Content-type: application/x-javascript");

	$file = $conf->global->CALLING_ALERT_ADDON;
	if(include_once(DOL_DOCUMENT_ROOT ."/calling/core/modules/calling/".$file.".php")){
		$module = new $file($db);

		$tmp = $module->top_menu();


		$fp = fopen(DOL_DATA_ROOT.'/calling/tmp_calling_js.'.$user->id.'.txt', 'w');
		fwrite($fp, $tmp );
		fclose($fp);

		echo $tmp;
	}
}
?>