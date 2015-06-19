<?php
/* Copyright (C) 2005      Rodolphe Quiedeville <rodolphe@quiedeville.org>
 * Copyright (C) 2005-2008 Laurent Destailleur  <eldy@users.sourceforge.org>
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
 *	\defgroup   calling      Module calling
 *	\brief      Module pour gerer l'appel automatique
 */

/**
 *	\file       htdocs/includes/modules/modCalling.class.php
 *	\ingroup    calling
 *	\brief      Fichier de description et activation du module de click to Dial
 *	\author     Oscim <mail oscim@users.sourceforge.net>
 *	\version    $Id: modCalling.php,v 1.56 2012/06/10 15:28:01 oscim Exp $
 */

include_once(DOL_DOCUMENT_ROOT ."/core/modules/DolibarrModules.class.php");


/**
 \class      modCalling
 \brief      Classe de description et activation du module de Click to Dial
 */

class modcalling extends DolibarrModules
{

	/**
	 *   \brief      Constructeur. Definit les noms, constantes et boites
	 *   \param      DB      handler d'acces base
	 */
	function modCalling($DB)
	{
		$this->db = $DB ;
		$this->numero = 66 ;

		$this->family = "technic";
		// Module label (no space allowed), used if translation string 'ModuleXXXName' not found (where XXX is value of numeric property 'numero' of module)
		$this->name = preg_replace('/^mod/i','',get_class($this));
		$this->description = "Gestion des appels entrant et sortant";

		$this->version = 'dolibarr';		// 'development' or 'experimental' or 'dolibarr' or version

		$this->const_name = 'MAIN_MODULE_'.strtoupper($this->name);
		$this->special = 1;
		$this->picto='phoning';

		// Data directories to create when module is enabled
		$this->dirs = array("/calling");

		// Dependencies
		$this->depends = array('modoscimmods','modTiers', 'modClickToDial','modAgenda');
		$this->requiredby = array();

		$this->conflictwith = array();
		$this->langfiles = array("calling","users");

		// tabs
		$this->tabs = array(
				    'thirdparty:calling:@calling:/calling/tab/calling_societe.php?socid=__ID__',
				    'contact:calling:@calling:/calling/tab/calling_contact.php?id=__ID__',
				    );

		// Config pages
		$this->config_page_url = array(DOL_URL_ROOT."/calling/admin/index.php");

		// Constantes
		$this->const = array();


		$r=0;
		$this->const[$r][0] = "CALLING_ADDON";
		$this->const[$r][1] = "chaine";
		$this->const[$r][2] = "api_ovh";
		$this->const[$r][3] = "Nom de l'api utilisé";
		$this->const[$r][4] = 0;
		$this->const[$r][5] = 1; // supprime la constante à la désactivation du module

		$r++;
		$this->const[$r][0] = "CALLING_ALERT_ADDON";
		$this->const[$r][1] = "chaine";
		$this->const[$r][2] = "alert_simplejs";
		$this->const[$r][3] = "Nom de l'api utilisé pour les alertes d'appel entrant";
		$this->const[$r][4] = 0;
		$this->const[$r][5] = 1; // supprime la constante à la désactivation du module

		$r++;
		$this->const[$r][0] = "CALLING_ALERT_TYPE";
		$this->const[$r][1] = "integer";
		$this->const[$r][2] = "3";
		$this->const[$r][3] = "mode d'alerte";
		$this->const[$r][4] = 0;
		$this->const[$r][5] = 1; // supprime la constante à la désactivation du modules

		$r++;
		$this->const[$r][0] = "CALLING_CREATE_NOFOUND";
		$this->const[$r][1] = "chaine";
		$this->const[$r][2] = "contact";
		$this->const[$r][3] = "Creation de fiche de tiers";
		$this->const[$r][4] = 0;
		$this->const[$r][5] = 1; // supprime la constante à la désactivation du modules

		$r++;
		$this->const[$r][0] = "CALLING_CREATE_ANONYMOUS";
		$this->const[$r][1] = "chaine";
		$this->const[$r][2] = "yes";
		$this->const[$r][3] = "Creation fiche pour anonymous";
		$this->const[$r][4] = 0;
		$this->const[$r][5] = 1; // supprime la constante à la désactivation du modules

		$r++;
		$this->const[$r][0] = "CALLING_LOGS_IN_ACTION";
		$this->const[$r][1] = "chaine";
		$this->const[$r][2] = "yes";
		$this->const[$r][3] = "trace all call";
		$this->const[$r][4] = 0;
		$this->const[$r][5] = 1; // supprime la constante à la désactivation du modules

		$r++;
		$this->const[$r][0] = "CALLING_ALERT_TYPE_MODE";
		$this->const[$r][1] = "chaine";
		$this->const[$r][2] = "0";
		$this->const[$r][3] = "trace all call";
		$this->const[$r][4] = 0;
		$this->const[$r][5] = 1; // supprime la constante à la désactivation du modules

		$r++;
		$this->const[$r][0] = "CALLING_ALERT_TYPE_MODE_DISPLAY_BLOCK_USER";
		$this->const[$r][1] = "chaine";
		$this->const[$r][2] = "0";
		$this->const[$r][3] = "display block user for incoming call";
		$this->const[$r][4] = 0;
		$this->const[$r][5] = 1; // supprime la constante à la désactivation du modules

		$r++;
		$this->const[$r][0] = "CALLING_INCOMING_USER";
		$this->const[$r][1] = "integer";
		$this->const[$r][2] = "2";
		$this->const[$r][3] = "user for incoming call";
		$this->const[$r][4] = 0;
		$this->const[$r][5] = 1; // supprime la constante à la désactivation du modules

		// Boxes
		$this->boxes = array();

		// Permissions
		$this->rights = array();
		$this->rights_class = 'calling';


		// Main menu entries
		$this->menu = array();			// List of menus to add
		$r=0;
		$this->menu[$r]=array('fk_menu'=>0,
													'type'=>'top',
													'titre'=>'Telephonie',
													'mainmenu'=>'calling',
													'leftmenu'=>'1',		// Use 1 if you also want to add left menu entries using this descriptor.
													'url'=>'/comm/action/listactions.php?sortfield=a.datep&sortorder=desc&begin=&',
													'langs'=>'agenda',
													'position'=>100,
													'perms'=>'$user->rights->agenda->myactions->read',
													'enabled'=>'$conf->agenda->enabled',
													'target'=>'',
													'user'=>2);
		$r++;
	}

    /**
     *      \brief      Function called when module is enabled.
     *                  The init function add constants, boxes, permissions and menus (defined in constructor) into Dolibarr database.
     *                  It also creates data directories.
     *      \return     int             1 if OK, 0 if KO
     */
		function init()
		{
			global $conf;

			$sql = array();

		$this->load_tables();

			return $this->_init($sql);
		}

    /**
     *      \brief      Function called when module is disabled.
     *                  Remove from database constants, boxes and permissions from Dolibarr database.
     *                  Data directories are not deleted.
     *      \return     int             1 if OK, 0 if KO
     */
		function remove()
		{
			$sql = array();

			return $this->_remove($sql);
		}


    /**
     * 		Create tables, keys and data required by module
     * 		Files llx_table1.sql, llx_table1.key.sql llx_data.sql with create table, create keys
     * 		and create data commands must be stored in directory /mymodule/sql/
     * 		This function is called by this->init
     *
     * 		@return		int		<=0 if KO, >0 if OK
     */
    function load_tables() {
        return $this->_load_tables('/calling/sql/');
    }

}
?>
