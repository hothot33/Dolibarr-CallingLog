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
	@remarks Config Api Alert Mod
*/
if (GETPOST("action") == 'setalertmod' && $user->admin)
{



	// desactivate current
	$file = $conf->global->CALLING_ALERT_ADDON;
	require_once(DOL_DOCUMENT_ROOT ."/calling/core/modules/calling/".$file.".php");
	$current = new $file;
	$result=$current->Unactivate($db, $conf);

	$result = dolibarr_set_const($db, "CALLING_ALERT_ADDON",$_GET["value"],'chaine',0,'',$conf->entity);
	$file = GETPOST("value");
	require_once(DOL_DOCUMENT_ROOT ."/calling/core/modules/calling/".$file.".php");
	$current = new $file;
	// specifical activate command
	$result=$current->Activate($db, $conf);

  	if ($result >= 0)
  	{
				/*
					Clean cache tmp file js
				*/
			clearstatcache();
				$dir = DOL_DATA_ROOT.'/calling/';

				if (is_dir($dir))
				{
					$handle = opendir($dir);
					if (is_resource($handle))
					{
						$var=true;

						while (($file = readdir($handle))!==false)
						{
							if (substr($file, 0, 14) == 'tmp_calling_js' && substr($file, dol_strlen($file)-3, 3) == 'txt')
							{
								unlink($dir . $file);
							}
						}
						closedir($handle);
					}
				}


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
dol_fiche_head($head,'mod_alert',$langs->trans("Module66Name"),0,'calling@calling');



print $langs->trans("CallingDescModAlert")."<br>\n";


if ($mesg) print '<br>'.$mesg;

print '<br>';

$var=true;



print '<table class="noborder" width="100%">';
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

				if (substr($file, 0, 6) == 'alert_' && substr($file, dol_strlen($file)-3, 3) == 'php')
				{
					$file = substr($file, 0, dol_strlen($file)-4);

					require_once(DOL_DOCUMENT_ROOT ."/calling/core/modules/calling/".$file.".php");

					$module = new $file;

					if ($conf->global->CALLING_ALERT_ADDON == "$file")
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

// 						if ($module->UseSending()) print $langs->trans('CallingApiSendActive').'<br />';

// 						if ($module->UseReceving()) print $langs->trans('CallingApiRecevingActive');

						print '</td>'."\n";

						print '<td align="center">';
						if ($conf->global->CALLING_ALERT_ADDON == "$file")
						{
							print img_picto($langs->trans("Activated"),'on');
						}
						else
						{
							print '<a href="'.$_SERVER["PHP_SELF"].'?action=setalertmod&amp;value='.$file.'">';
							print img_picto($langs->trans("Disabled"),'off');
							print '</a>';
						}
						print '</td>';

						print '<td align="center">';
// 						print $html->textwithpicto('',$htmltooltip,1,0);
						print '</td>';

						print '</tr>';
					}
				}
			}
			closedir($handle);
		}
	}
}
print '</table>';

	if ($conf->global->CALLING_ALERT_ADDON == 'alert_nodejs'):
print '<br />';
	print '<table class="noborder" width="100%">';
	print '<tr class="liste_titre">';
	print '<td width="100">'.$langs->trans("Name").'</td>';
	print '<td>'.$langs->trans("Reglage").'</td>';
	print '<td>'.$langs->trans("Description").'</td>';
	print "</tr>\n";

	$var=!$var;
	print '<tr '.$bc[$var].'>';
	print '<td valign="top">'.$langs->trans("NodejsAddonUrlServer").'</td>';
	print '<td><input name="nodejs_addon_url_server" size="20" style="width:400px" value="'.$conf->global->NODEJS_ADDON_URL_SERVER.'" /></td>';
	print '<td>'.$langs->trans("NodejsAddonUrlServerDetail").'</td>';
	print '</tr>';

	$var=!$var;
	print '<tr '.$bc[$var].'>';
	print '<td valign="top">'.$langs->trans("NodejsAddonUrlPort").'</td>';
	print '<td><input name="nodejs_addon_url_port" size="6" value="'.$conf->global->NODEJS_ADDON_URL_PORT.'" /></td>';
	print '<td>'.$langs->trans("NodejsAddonUrlPortDetail").'</td>';
	print '</tr>';
print '</table>';
endif;


$db->close();

llxFooter('$Date: 2011/07/31 22:23:24 $ - $Revision: 1.24 $');
?>
