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
 *	\file       includes/modules/calling/lib.calling.php
 *	\brief      lib functions for calling module
 *	\author     Oscim <mail oscim@users.sourceforge.net>
 *	\version    $Id: alert_simplejs.php,v 1.56 2012/06/10 15:28:01 oscim Exp $
 */

function callingAdminPrepareHead() {
	global $langs, $conf;

	$langs->load("calling@calling");

	$h = 0;
	$head = array();
	
	
	$head[$h][0] = dol_buildpath("/calling/admin/index.php", 1);
	$head[$h][1] = $langs->trans("Note");
	$head[$h][2] = 'info';
	$h++;
	
	$head[$h][0] = dol_buildpath("/calling/admin/calling.php", 1);
	$head[$h][1] = $langs->trans("Settings");
	$head[$h][2] = 'settings';
	$h++;
	
	$head[$h][0] = dol_buildpath("/calling/admin/mod_provider.php", 1);
	$head[$h][1] = $langs->trans("CallingProvider");
	$head[$h][2] = 'mod_provider';
	$h++;
	
	$head[$h][0] = dol_buildpath("/calling/admin/mod_alert.php", 1);
	$head[$h][1] = $langs->trans("CallingAlert");
	$head[$h][2] = 'mod_alert';
	$h++;
	
	$head[$h][0] = dol_buildpath("/calling/admin/editor.php", 1);
	$head[$h][1] = $langs->trans("Editor");
	$head[$h][2] = 'editor';
	$h++;
	
	$head[$h][0] = dol_buildpath("/calling/admin/abouts.php", 1);
	$head[$h][1] = $langs->trans("About");
	$head[$h][2] = 'about';
	$h++;

// 	complete_head_from_modules($conf, $langs, $object, $head, $h, 'calling');

// 	complete_head_from_modules($conf, $langs, $object, $head, $h, 'calling', 'remove');

	return $head;
}


?>