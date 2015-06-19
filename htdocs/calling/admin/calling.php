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
 *   \file       htdocs/admin/calling.php
 *   \ingroup    calling
 *   \brief      Page to setup module clicktodial
 *	\author     Oscim <mail oscim@users.sourceforge.net>
 *	\version    $Id: calling.php,v 1.56 2012/06/10 15:28:01 oscim Exp $
 */

$res = 0;
if (!$res && file_exists("../main.inc.php"))
    $res = @include("../main.inc.php");
if (!$res && file_exists("../../main.inc.php"))
    $res = @include("../../main.inc.php");
if (!$res && file_exists("../../../main.inc.php"))
    $res = @include("../../../main.inc.php");
if (!$res && file_exists("../../../../main.inc.php"))
    $res = @include("../../../../main.inc.php");
if (!$res && file_exists("../../../dolibarr/htdocs/main.inc.php"))
    $res = @include("../../../dolibarr/htdocs/main.inc.php");     // Used on dev env only
if (!$res && file_exists("../../../../dolibarr/htdocs/main.inc.php"))
    $res = @include("../../../../dolibarr/htdocs/main.inc.php");   // Used on dev env only
if (!$res && file_exists("../../../../../dolibarr/htdocs/main.inc.php"))
    $res = @include("../../../../../dolibarr/htdocs/main.inc.php");   // Used on dev env only
if (!$res)
    die("Include of main fails");
require_once(DOL_DOCUMENT_ROOT."/core/lib/admin.lib.php");
require_once "../lib/lib.calling.admin.php";

$langs->load("admin");
$langs->load("calling@calling");

if (!$user->admin)
  accessforbidden();




/**
	@remarks Config value conf
*/
if (GETPOST("action") == 'setconf')
{
	// TODO Verifier si module numerotation choisi peut etre active
	// par appel methode canBeActivated



	dolibarr_set_const($db, "CALLING_ALERT_TYPE",GETPOST("calling_alert_type"),'chaine',0,'',$conf->entity);

	dolibarr_set_const($db, "CALLING_CREATE_NOFOUND",GETPOST("calling_create_nofound"),'chaine',0,'',$conf->entity);

	dolibarr_set_const($db, "CALLING_CREATE_ANONYMOUS",GETPOST("calling_create_anonymous"),'chaine',0,'',$conf->entity);

	dolibarr_set_const($db, "CALLING_LOGS_IN_ACTION",GETPOST("calling_logs_in_action"),'chaine',0,'',$conf->entity);

	dolibarr_set_const($db, "CALLING_ALERT_TYPE_MODE",GETPOST("calling_alert_type_mode"),'chaine',0,'',$conf->entity);

	dolibarr_set_const($db, "CALLING_ALERT_TYPE_MODE_DISPLAY_BLOCK_USER",GETPOST("calling_alert_type_mode_display_block_user"),'chaine',0,'',$conf->entity);

	dolibarr_set_const($db, "CALLING_INCOMING_USER",GETPOST("calling_incoming_user"),'int',0,'',$conf->entity);

	if ($conf->global->CALLING_ALERT_ADDON == 'alert_nodejs'){
		dolibarr_set_const($db, "NODEJS_ADDON_URL_SERVER",GETPOST("nodejs_addon_url_server"),'chaine',0,'',$conf->entity);

		dolibarr_set_const($db, "NODEJS_ADDON_URL_PORT",GETPOST("nodejs_addon_url_port"),'chaine',0,'',$conf->entity);
	}


	if ($conf->global->CALLING_ADDON == 'api_asterisk'){
		dolibarr_set_const($db, "ASTERISK_HOST",GETPOST("asterisk_host"),'chaine',0,'',$conf->entity);

		dolibarr_set_const($db, "ASTERISK_TYPE",GETPOST("asterisk_type"),'chaine',0,'',$conf->entity);

		dolibarr_set_const($db, "ASTERISK_INDICATIF",GETPOST("asterisk_indicatif"),'chaine',0,'',$conf->entity);

		dolibarr_set_const($db, "ASTERISK_PORT",GETPOST("asterisk_port"),'chaine',0,'',$conf->entity);
	}

}



/*
 *
 *
 */

$wikihelp='EN:Module_ClickToDial_En|FR:Module_ClickToDial|ES:Módulo_ClickTodial_Es';


$html=new Form($db);
llxHeader('',$langs->trans("ClickToDialSetup"),$wikihelp);

$linkback='<a href="'.DOL_URL_ROOT.'/admin/modules.php">'.$langs->trans("BackToModuleList").'</a>';
print_fiche_titre($langs->trans("CallingSetup"),$linkback);

// Configuration header
$head = callingAdminPrepareHead();
dol_fiche_head($head,'settings',$langs->trans("Module66Name"),0,'calling@calling');



print $langs->trans("CallingDesc")."<br>\n";


if ($mesg) print '<br>'.$mesg;

print '<br>';
print '<form method="post" action="calling.php">';
print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
print '<input type="hidden" name="action" value="setvalue">';

$var=true;




print '<br />';
	print '<table class="noborder" width="100%">';
	print '<tr class="liste_titre">';
	print '<td width="100">'.$langs->trans("Name").'</td>';
	print '<td>'.$langs->trans("Reglage").'</td>';
	print '<td>'.$langs->trans("Description").'</td>';
	print "</tr>\n";

	$var=!$var;
	print '<tr '.$bc[$var].'>';
	print '<td valign="top">'.$langs->trans("CallingAlertTypePopup").'</td>';
	print '<td>'.
						'<select name="calling_alert_type">'.
							'<option value="0">'.$langs->trans("CallingAlertType0").'</option>'.
							'<option value="1" '.(($conf->global->CALLING_ALERT_TYPE == 1)? 'selected="selected"' : '' ).'>'.$langs->trans("CallingAlertType1").'</option>'.
							'<option value="2" '.(($conf->global->CALLING_ALERT_TYPE == 2)? 'selected="selected"' : '' ).'>'.$langs->trans("CallingAlertType2").'</option>'.
							'<option value="3" '.(($conf->global->CALLING_ALERT_TYPE == 3)? 'selected="selected"' : '' ).'>'.$langs->trans("CallingAlertType3").'</option>'.
						'</select>'.
				'</td>';
	print '<td>'.$langs->trans("CallingAlertTypeDetail").'</td>';
	print '</tr>';

	$var=!$var;
	print '<tr '.$bc[$var].'>';
	print '<td valign="top">'.$langs->trans("CallingAlertTypeMode").'</td>';
	print '<td>'.
						'<select name="calling_alert_type_mode">'.
							'<option value="0" >'.$langs->trans("CallingAlertTypeModeNo").'</option>'.
							'<option value="1" '.(($conf->global->CALLING_ALERT_TYPE_MODE == 1)? 'selected="selected"' : '' ).'>'.$langs->trans("CallingAlertTypeModeYesButNoUser").'</option>'.
// 							'<option value="2" '.(($conf->global->CALLING_ALERT_TYPE_MODE == 2)? 'selected="selected"' : '' ).'>'.$langs->trans("CallingAlertTypeModeYesBuJustTrace").'</option>'.
						'</select>'.
				'</td>';
	print '<td>'.$langs->trans("CallingAlertTypeModeDetail").'</td>';
	print '</tr>';

	if($conf->global->CALLING_ALERT_TYPE_MODE == 1) {

			$var=!$var;
			print '<tr '.$bc[$var].'>';
			print '<td valign="top">'.$langs->trans("CallingAlertTypeModeDisplayBlockUser").'</td>';
			print '<td>'.
								'<select name="calling_alert_type_mode_display_block_user">'.
									'<option value="0" >'.$langs->trans("CallingAlertTypeModeDisplayBlockUserNo").'</option>'.
									'<option value="1" '.(($conf->global->CALLING_ALERT_TYPE_MODE_DISPLAY_BLOCK_USER == 1)? 'selected="selected"' : '' ).'>'.$langs->trans("CallingAlertTypeModeDisplayBlockUserYes").'</option>'.
								'</select>'.
						'</td>';
			print '<td>'.$langs->trans("CallingAlertTypeModeDisplayBlockUserDetail").'</td>';
			print '</tr>';

	}

	$var=!$var;
	print '<tr '.$bc[$var].'>';
	print '<td valign="top">'.$langs->trans("CallingAlertCreateTiersInconnu").'</td>';
	print '<td>'.
						'<select name="calling_create_nofound">'.
							'<option value="no">'.$langs->trans("CallingAlertCreateNo").'</option>'.
							'<option value="tiers" '.(($conf->global->CALLING_CREATE_NOFOUND == "tiers")? 'selected="selected"' : '' ).'>'.$langs->trans("CallingAlertCreateTiers").'</option>'.
							'<option value="contact" '.(($conf->global->CALLING_CREATE_NOFOUND == "contact")? 'selected="selected"' : '' ).'>'.$langs->trans("CallingAlertCreateContact").'</option>'.
						'</select>'.
				'</td>';
	print '<td>'.$langs->trans("CallingAlertCreateTiersInconnuDetail").'</td>';
	print '</tr>';


	$var=!$var;
	print '<tr '.$bc[$var].'>';
	print '<td valign="top">'.$langs->trans("CallingAlertCreateAnonymous").'</td>';
	print '<td>'.
						'<select name="calling_create_anonymous">'.
							'<option value="no">'.$langs->trans("CallingAlertCreateNo").'</option>'.
							'<option value="yes" '.(($conf->global->CALLING_CREATE_ANONYMOUS == "yes")? 'selected="selected"' : '' ).'>'.$langs->trans("CallingAlertCreateYes").'</option>'.
						'</select>'.
				'</td>';
	print '<td>'.$langs->trans("CallingAlertCreateAnonymousDetail").'</td>';
	print '</tr>';

	$var=!$var;
	print '<tr '.$bc[$var].'>';
	print '<td valign="top">'.$langs->trans("CallingLogInAction").'</td>';
	print '<td>'.
						'<select name="calling_logs_in_action">'.
							'<option value="no">'.$langs->trans("CallingLogInActionNo").'</option>'.
							'<option value="yes" '.(($conf->global->CALLING_LOGS_IN_ACTION == "yes")? 'selected="selected"' : '' ).'>'.$langs->trans("CallingLogInActionYes").'</option>'.
						'</select>'.
				'</td>';
	print '<td>'.$langs->trans("CallingLogInActionDetail").'</td>';
	print '</tr>';



	$var=!$var;
	print '<tr '.$bc[$var].'>';
	print '<td valign="top">'.$langs->trans("CallingUserIncoming").'</td>';
	print '<td>';
	$html->select_users( (GETPOST("calling_incoming_user")?GETPOST("calling_incoming_user"):$conf->global->CALLING_INCOMING_USER),'calling_incoming_user',1);
	print '</td>';
	print '<td>'.$langs->trans("CallingUserIncomingDetail").'</td>';
	print '</tr>';


	print '</table>';



// print '<form action="'.$_SERVER["PHP_SELF"].'" method="post">';
print '<tr '.$bc[$var].'><td colspan="2" align="right">';
print '<input type="submit" class="button" value="'.$langs->trans("Modify").'">';
print "</td></tr>\n";
print '</form>';


$db->close();

llxFooter('$Date: 2011/07/31 22:23:24 $ - $Revision: 1.24 $');
?>
