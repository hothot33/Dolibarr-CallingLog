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



/**
	@fn CreateTiersMin()
	@brief Create Soc incoming call
*/
function CreateTiersMin($telephone){
	global $db, $user;

	require_once(DOL_DOCUMENT_ROOT."/core/lib/company.lib.php");
	require_once(DOL_DOCUMENT_ROOT."/core/lib/images.lib.php");
	require_once(DOL_DOCUMENT_ROOT."/core/lib/files.lib.php");
	require_once(DOL_DOCUMENT_ROOT."/core/class/html.formadmin.class.php");
	require_once(DOL_DOCUMENT_ROOT."/core/class/html.formcompany.class.php");
	require_once(DOL_DOCUMENT_ROOT."/core/class/html.formfile.class.php");
	require_once(DOL_DOCUMENT_ROOT."/core/class/extrafields.class.php");
	require_once(DOL_DOCUMENT_ROOT."/contact/class/contact.class.php");

	$object = new Societe($db);

	$db->begin();

	$object->name    = $telephone;
	$object->tel   	 = $telephone;

	$id =  $object->create($user);

	$db->commit();

	if ($id <= 0)
		return false;

	return $id;
}


/**
	@fn CreateContactMin()
	@brief Create contact incoming call
*/
function CreateContactMin($telephone){
	global $db, $user;
	require_once(DOL_DOCUMENT_ROOT."/comm/action/class/actioncomm.class.php");
	require_once(DOL_DOCUMENT_ROOT."/contact/class/contact.class.php");
	require_once(DOL_DOCUMENT_ROOT."/core/lib/contact.lib.php");
	require_once(DOL_DOCUMENT_ROOT."/core/lib/company.lib.php");
	require_once(DOL_DOCUMENT_ROOT."/core/class/html.formcompany.class.php");

	$object = new Contact($db);

	$db->begin();

	$object->name         = $telephone;
	$object->firstname    = '';
	$object->civilite_id  = 'MR';
// 				$object->poste        = $_POST["poste"];
// 				$object->address      = $_POST["address"];
// 				$object->cp           = $_POST["cp"];
// 				$object->ville        = $_POST["ville"];
// 				$object->fk_pays      = $_POST["pays_id"];
// 				$object->fk_departement = $_POST["departement_id"];
// 				$object->email        = $_POST["email"];
	$object->phone_pro    = $telephone;
// 				$object->phone_perso  = $_POST["phone_perso"];
// 				$object->phone_mobile = $_POST["phone_mobile"];
// 				$object->fax          = $_POST["fax"];
// 				$object->jabberid     = $_POST["jabberid"];
// 				$object->priv         = $_POST["priv"];
// 				$object->note         = $_POST["note"];

	$id =  $object->create($user);

	$db->commit();

	if ($id <= 0)
		return false;

	return $id;
}



?>