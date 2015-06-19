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
	@remarks Config Api Mod
*/
if (GETPOST("action") == 'setmod' && $user->admin)
{
	$result = dolibarr_set_const($db, "CALLING_ADDON",$_GET["value"],'chaine',0,'',$conf->entity);


	$file = GETPOST("value");
	require_once(DOL_DOCUMENT_ROOT ."/calling/core/modules/calling/".$file.".php");
	$current = new $file;

	// force define url for clicktocall
	if ($current->UseSending())
		$result = dolibarr_set_const($db, "CLICKTODIAL_URL",'http://'.$_SERVER['HTTP_HOST'].''.DOL_URL_ROOT.'/calling/calling.php?doli=1&login=__LOGIN__&password=__PASS__&caller=__PHONEFROM__&called=__PHONETO__','chaine',0,'',$conf->entity);


  	if ($result >= 0)
  	{
  		$mesg='<div class="ok">'.$langs->trans("RecordModifiedSuccessfully").'</div>';
  	}
  	else
  	{
		dol_print_error($db);
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
dol_fiche_head($head,'mod_provider',$langs->trans("Module66Name"),0,'calling@calling');



print $langs->trans("CallingDescModProvider")."<br>\n";


if ($mesg) print '<br>'.$mesg;

print '<br>';
print '<form method="post" action="calling.php">';
print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
print '<input type="hidden" name="action" value="setvalue">';

$var=true;



print '<table class="noborder" width="100%">';
print '<caption>'.$langs->trans("CallingProviderChoose").'</caption>';
print '<tr class="liste_titre">';
print '<td width="100">'.$langs->trans("Name").'</td>';
print '<td>'.$langs->trans("Description").'</td>';
print '<td>'.$langs->trans("Example").'</td>';
print '<td align="center" width="60">'.$langs->trans("Status").'</td>';
print '<td align="center" width="16">'.$langs->trans("Infos").'</td>';
print "</tr>\n";

clearstatcache();

foreach ($conf->file->dol_document_root as $dirroot)
{
	$dir = $dirroot . "/calling/core/modules/calling/";

	if (is_dir($dir))
	{
		$handle = opendir($dir);
		if (is_resource($handle))
		{
			$var=true;

			while (($file = readdir($handle))!==false)
			{

				if (substr($file, 0, 4) == 'api_' && substr($file, dol_strlen($file)-3, 3) == 'php')
				{
					$file = substr($file, 0, dol_strlen($file)-4);

					require_once(DOL_DOCUMENT_ROOT ."/calling/core/modules/calling/".$file.".php");

					$module = new $file;

					if ($conf->global->CALLING_ADDON == "$file")
						$current = $module;

					// Show modules according to features level
					if ($module->version == 'development'  && $conf->global->MAIN_FEATURES_LEVEL < 2) continue;
					if ($module->version == 'experimental' && $conf->global->MAIN_FEATURES_LEVEL < 1) continue;

					if ($module->isEnabled())
					{
						$var=!$var;
						print '<tr '.$bc[$var].'><td>'.$module->nom."</td><td>\n";
						print $module->info();
						print '</td>';

                        // Show example of numbering module
                        print '<td nowrap="nowrap">';

                        if ($module->UseSending()) print $langs->trans('CallingApiSendActive').'<br />';

                        if ($module->UseReceving()) print $langs->trans('CallingApiRecevingActive');

                        print '</td>'."\n";

						print '<td align="center">';
						if ($conf->global->CALLING_ADDON == "$file")
						{
							print img_picto($langs->trans("Activated"),'on');
						}
						else
						{
							print '<a href="'.$_SERVER["PHP_SELF"].'?action=setmod&amp;value='.$file.'">';
							print img_picto($langs->trans("Disabled"),'off');
							print '</a>';
						}
						print '</td>';

						print '<td align="center">';
						print $html->textwithpicto('',$htmltooltip,1,0);
						print '</td>';

						print '</tr>';
					}
				}
			}
			closedir($handle);
		}
	}
}

print '</table><br>';

print '</form>';

print '<form method="post" action="calling.php">';
print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
print '<input type="hidden" name="action" value="setconf">';


if(is_object($current)) {

	if ($conf->global->CALLING_ADDON == 'api_asterisk'):
print '<br />';
	print '<table class="noborder" width="100%">';
	print '<tr class="liste_titre">';
	print '<td width="100">'.$langs->trans("Name").'</td>';
	print '<td>'.$langs->trans("Reglage").'</td>';
	print '<td>'.$langs->trans("Description").'</td>';
	print "</tr>\n";

	$var=!$var;
	print '<tr '.$bc[$var].'>';
	print '<td valign="top">'.$langs->trans("AsteriskIp").'</td>';
	print '<td><input name="asterisk_host" size="20" style="width:400px" value="'.$conf->global->ASTERISK_HOST.'" /></td>';
	print '<td>'.$langs->trans("NodejsAddonUrlServerDetail").'</td>';
	print '</tr>';

	$var=!$var;
	print '<tr '.$bc[$var].'>';
	print '<td valign="top">'.$langs->trans("ASteriskType").'</td>';
	print '<td><input name="asterisk_type" size="6" value="'.$conf->global->ASTERISK_TYPE.'" /></td>';
	print '<td>'.$langs->trans("NodejsAddonUrlPortDetail").'</td>';
	print '</tr>';

	$var=!$var;
	print '<tr '.$bc[$var].'>';
	print '<td valign="top">'.$langs->trans("AsteriskIndicatif").'</td>';
	print '<td><input name="asterisk_indicatif" size="20" style="width:400px" value="'.$conf->global->ASTERISK_INDICATIF.'" /></td>';
	print '<td>'.$langs->trans("NodejsAddonUrlServerDetail").'</td>';
	print '</tr>';

	$var=!$var;
	print '<tr '.$bc[$var].'>';
	print '<td valign="top">'.$langs->trans("AsteriskPort").'</td>';
	print '<td><input name="asterisk_port" size="6" value="'.$conf->global->ASTERISK_PORT.'" /></td>';
	print '<td>'.$langs->trans("NodejsAddonUrlPortDetail").'</td>';
	print '</tr>';

print '</table>';
endif;


print '<br />';
	print '<table class="nobordernopadding" width="100%">';
	print '<tr class="liste_titre">';
	print '<td width="120">'.$langs->trans("Name").'</td>';
	print '<td>'.$langs->trans("Value").'</td>';
	print "</tr>\n";

	if ($current->UseSending()) {
		$var=!$var;
		print '<tr '.$bc[$var].'><td valign="top">';
		print $langs->trans("URLforsendingcall").'</td><td>';
		// print '<input size="92" type="text" name="url" value="'.$conf->global->CLICKTODIAL_URL.'"><br>';
		// print '<br>';
		// print $langs->trans("ClickToDialUrlDesc").'<br>';
		print $conf->global->CLICKTODIAL_URL.'';
		print '</td></tr>';
	}

	if ($current->UseReceving()) {
		$var=!$var;
		print '<tr '.$bc[$var].'><td valign="top">';
		print $langs->trans("URLfornotifiindoli").'</td><td>';
		// print '<input size="92" type="text" name="url" value="'.$conf->global->CLICKTODIAL_URL.'"><br>';
		// print '<br>';
		// print $langs->trans("ClickToDialUrlDesc").'<br>';
		print 'http://'.$_SERVER['HTTP_HOST'].''.DOL_URL_ROOT.'/'.$current->getUrlForNotification().'';
		print '</td></tr>';

		print '</table>';
	}
}



$db->close();

llxFooter('$Date: 2011/07/31 22:23:24 $ - $Revision: 1.24 $');
?>
