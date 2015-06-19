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
 *	\file       htdocs/calling_alert.php
 *	\brief      File of class to manage widget boxes
 *	\author     Oscim <mail oscim@users.sourceforge.net>
 *	\version    $Id: calling_alert.php,v 1.56 2012/06/10 15:28:01 oscim Exp $
 */


	require_once(DOL_DOCUMENT_ROOT . "/calling/class/calling.class.php");

Class calling_alert
	{

	public $internalurlnotif = 'calling.php';


	/**
		@brief Constructor
		@return none
	*/
	public function __construct(){
		global $db, $conf, $user;
			// init support class
			$calling = new calling($db);

			$calling->maintenance($user);
	}

	/**
		@brief check if module is enabled
		@return boolean
	*/
	public function isEnabled(){
		return $this->canBeActivated();
	}

	/**
		@brief check if module content Process_Send method
		@return boolean
	*/
	public function top_menu(){
		global $langs;
			return $this->DipslayTop();
	}



	/**
		@brief return detail data stocked
		@return array/object data
	*/
	public function LoadData($user){

		$res = array();

		global $db, $conf;

			// init support class
			$calling = new calling($db);

			$opt = array('in_mode'=>true);
			if( $conf->global->CALLING_ALERT_TYPE ==1)
			$opt['to_user'] = $user->id;

			$id = $calling->search( $opt );

			if($id > 0) {
					$calling->fetch($id, $user);

					return $calling->data;

			}

	}


	/**
		@fn check_right
		@brief
		@param $caller string numero appele
		@param $user object user
		@return boolean true ok / false no ok
	*/
	public function check_right($user){
		global $conf;

		// all user notified
		if($conf->global->CALLING_ALERT_TYPE == 3)
			return true;

		if( $conf->clicktodial->enabled ){
			if (empty($user->clicktodial_loaded)) $user->fetch_clicktodial();

			// uniquement les utilisateur avec le click to dial configuré
			if($user->clicktodial_poste =='')
				return false;
			else
				return true;
		}

		return false;
	}

}



?>