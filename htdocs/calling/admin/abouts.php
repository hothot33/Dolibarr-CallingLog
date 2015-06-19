<?php
/* Copyright (C) 2014		 Oscim       <oscim@users.sourceforge.net>
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 * or see http://www.gnu.org/
 */

/**
 * 	\file		admin/about.php
* 	\ingroup	calling
 * 	\brief		This file is about page
 */
// Dolibarr environment
$res = @include("../../main.inc.php"); // From htdocs directory
if (! $res) {
	$res = @include("../../../main.inc.php"); // From "custom" directory
}

// Libraries
require_once DOL_DOCUMENT_ROOT . "/core/lib/admin.lib.php";
require_once "../lib/lib.calling.admin.php";


// oscimmods Required
dol_include_once('/oscimmods/core/lib/PHP_Markdown_1.0.1o/markdown.php');


// Translations
$langs->load("admin");
$langs->load("calling@calling");

// Access control
if (! $user->admin) {
	accessforbidden();
}


/*
 * View
 */
llxHeader('', $langs->trans("Module66Name") );

// Subheader
$linkback = '<a href="' . DOL_URL_ROOT . '/admin/modules.php">'. $langs->trans("BackToModuleList") . '</a>';
print_fiche_titre($langs->trans("CallingSetup"), $linkback);

// Configuration header
$head = callingAdminPrepareHead();
dol_fiche_head($head,'about',$langs->trans("Module66Name"),0,'calling@calling');



$buffer = file_get_contents(dol_buildpath('/calling/README.md', 0));
echo Markdown($buffer);


$buffer = file_get_contents(dol_buildpath('/oscimmods/docs/LICENCE.md', 0));
echo Markdown($buffer);

llxFooter();

$db->close();