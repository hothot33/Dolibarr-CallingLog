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
 *	\file       htdocs/includes/modules/calling/calling_api.php
 *	\brief      File of class to manage calling_api
 *	\author     Oscim <mail oscim@users.sourceforge.net>
 *	\version    $Id: calling_api.php,v 1.56 2012/06/10 15:28:01 oscim Exp $
 */

include_once(DOL_DOCUMENT_ROOT .'/calling/lib/lib.calling.php');

Class calling_api {

	public $internalurlnotif = 'calling/calling.php';


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
	public function UseSending(){
		return method_exists($this, 'Process_Send');
	}

	/**
		@brief check if module content Process_Receive method
		@return boolean
	*/
	public function UseReceving(){
		/*
			If login and password
		*/
		if(isset($_GET['login']) && isset($_GET['password'])  )
			return false;
			
		return method_exists($this, 'Process_Receive');
	}



	/**
		Static method lib for ajust alert, display and message
	*/

	/**
		@brief Create fiche in dolibarr
		@param $telephone
	*/
	static public function FicheNoFound($telephone) {
			global $conf, $db, $user;

			if($conf->global->CALLING_CREATE_ANONYMOUS =='yes' && in_array(strtoupper($telephone), array('anonymous','***','')))
				$process_creat = true;
			elseif(!in_array(strtoupper($telephone), array('anonymous','***','')))
				$process_creat = true;
			else
				$process_creat = false;


			if($process_creat){
				$obj = new StdClass();

				if( $conf->global->CALLING_CREATE_NOFOUND == 'tiers') {
					$id = CreateTiersMin($telephone);

					$obj->socid = $id;
					$obj->nom = $telephone;
				}
				elseif( $conf->global->CALLING_CREATE_NOFOUND == 'contact'){
					$id = CreateContactMin($telephone);

					$obj->contact_id = $id;
					$obj->contact = $telephone;
				}

				return $obj;
			}

			return false;
	}

	/**
		@brief method for adjust comportement incomming call for other local user
			Adjust display message or block
		@param $array_list_num array, list for all format num local user tel
		@return false OR object detail for display message
	*/
	static public function AlertTypeMode($array_list_num=array()) {
			global $conf, $db, $user;

			$user_array = calling_api::GetInternalTel($array_list_num);
// var_dump($user_array);
			// echap if numero tel interne entreprise
			if($conf->global->CALLING_ALERT_TYPE_MODE ==0 && count($user_array) > 0)
				return false;
			elseif($conf->global->CALLING_ALERT_TYPE_MODE ==1 && count($user_array) > 0){

					//uniquement poste appelé notifié via popup
// 					if( /*$conf->global->CALLING_ALERT_TYPE == 1
// 							// if current user tel != numero appelé
// 							&& */($user_array[0]->id == $user->id) )
// 						return false;

					// GET info collaborateur appelé
// 					if( $conf->global->CALLING_ALERT_TYPE_MODE_DISPLAY_BLOCK_USER == 1)
							$collaborateur = new User($db);
							$collaborateur->fetch($user_array[0]->id);

							$tmp = new StdClass();
							$tmp->id  = $collaborateur->id;
							$tmp->name  = $collaborateur->name;
							$tmp->lastname  = $collaborateur->lastname;

				return $tmp;
			}
		return false;
	}








	/**
		Static method Lib
	*/

	/**
		@brief Search In db for all internal num owner local user
		@param $array list all format tel
		@param $etat boolean , true for init call
		@return array or false (boolean)
	*/
	static public function GetExternalTel($array_res, $etat = false){

			$telephone = $array_res['master'];
			$soc_array = self::SearchTelInDb($array_res['decline']);
			$string = '';

			if(is_array($soc_array))
				$obj = $soc_array[0];

			/**
					@remarks Le tel est introuvable en base
					Creation du contact en prospect avec comme non le tel
			*/
			if( (!isset($obj) || !is_object($obj)) ){
				if($etat){
					$string .= '';
					if(  ($tmp = self::FicheNoFound($telephone) ) && $tmp != false )
						$soc_array[] = $tmp;
				}
			}

			if(is_array($soc_array)){
				$array = array();
				foreach($soc_array as $row ) {
					$tmp = new stdClass();
					if(isset($row->socid) && $row->socid > 0) {
						$tmp->type = 'societe';
						$tmp->id =$row->socid;
						$tmp->name = $row->nom;
					}
					elseif(isset($row->contact_id) && $row->contact_id > 0) {
						$tmp->type = 'contact';
						$tmp->id = $row->contact_id;
						$tmp->name = $row->contact;
					}
					$array[] = $tmp;
				}
			}

			return $array;
	}

	/**
		@brief Search In db for all internal num owner local user
		@param $array list all format tel
		@return array or false (boolean)
	*/
	static public function GetInternalTel($array){
		global $db;

			if(!is_array($array) || count($array) <=0)
				return false;

			$list = '';
			foreach($array as $row)
				$list.=",'".$row."' ";
			$list= substr($list, 1);

			/**
					@remarks search in societe and addresse  table
			*/
			$sql = "SELECT s.rowid as id, s.lastname, s.firstname ,  ";
			$sql.= " s.office_phone ";
			$sql.= " FROM ".MAIN_DB_PREFIX."user as s ";
			$sql.= " LEFT JOIN  ".MAIN_DB_PREFIX."user_clicktodial as ctd ON(ctd.fk_user = s.rowid) ";
			$sql.= " WHERE 1 ";
			$sql .= " AND (
							(s.office_phone IS NOT NULL AND  s.office_phone IN (".$list." ) )
							OR
							(s.user_mobile IS NOT NULL AND s.user_mobile IN (".$list." ) )
							OR
							(ctd.poste IS NOT NULL AND ctd.poste IN (".$list." ) )
			)";

			$result = $db->query($sql);
			$i = 0;
			$num = $db->num_rows($result);

			if($num>0){
				while ($i < $num){
					$user_array[] = $db->fetch_object($result);
					$i++;
				}
			}

		if(count($user_array)>0)
			return $user_array;
		else
			return false;
	}

	/**
		@brief
		@return boolean
	*/
// 	static public function CheckInternalTel($entrant, $array){
// 		global $db;
//
// 			foreach($array)
// 			$detail = self::FormatTelNum($telephone);
// 	}

	/**
		@brief extract and rewritre num, for different format
		@return array
	*/
	static public function FormatTelNum($telephone){

			if(strlen($telephone)>10){
					$lg = strlen($telephone);
					$tel = '0'.substr($telephone, ($lg - 9) );
					$prefix= substr($telephone,0, ($lg - 9) );
			}
			else{
					$tel = $telephone;
					$prefix= '';
			}

			// FORMAT IN 01-43-91-45-24
			$tel2 = substr($tel,0,2).'-'. substr($tel,2,2).'-'.substr($tel,4,2).'-'.substr($tel,6,2).'-'.substr($tel,8,2);
			// FORMAT IN 01 43 91 45 24
			$tel3 = substr($tel,0,2).' '. substr($tel,2,2).' '.substr($tel,4,2).' '.substr($tel,6,2).' '.substr($tel,8,2);

		return array(
			'prefix'=>$prefix,
			'master'=>$telephone,
			'decline'=>array(
					$telephone,
					$tel,
					$tel2,
					$tel3
				)
			);
	}




	/**
		@brief search number tel in all table db  (contact , address, tiers )
		@param $array all list format numero for search in db
		@return array or false
	*/
	private static function SearchTelInDb($array=array()){
		global $db;

			$user_array = array();
			$list = '';

			if(!is_array($array) || count($array) <=0)
				return false;

			foreach($array as $row)
				$list.=",'".$row."' ";
			$list= substr($list, 1);
			/**
					@remarks search in societe and addresse  table
			*/
			$sql = "SELECT s.rowid as socid, s.nom,  ";
			$sql.= " s.phone ";
			$sql.= " FROM ".MAIN_DB_PREFIX."societe as s ";
			$sql.= " LEFT JOIN ".MAIN_DB_PREFIX."societe_address as p ON s.rowid = p.fk_soc";
			$sql.= " WHERE 1 ";
			$sql .= " AND (
							(s.phone IS NOT NULL AND  s.phone IN (".$list." ) )
							OR
							(p.phone IS NOT NULL AND p.phone IN (".$list.") )

			)";

			$result = $db->query($sql);
			$i = 0;
			$num = $db->num_rows($result);
	// 			$string =' Appel entrant '.$tel2.' <br /> ';

			if($num>0){
				while ($i < $num){
					$user_array[] = $db->fetch_object($result);
					$i++;
				}
			}



			/**
					@remarks search in contact table
			*/
			$sql = "SELECT CONCAT (p.lastname,' ',p.firstname) as contact, p.rowid as contact_id ";

			$sql.= " FROM ".MAIN_DB_PREFIX."socpeople as p ";
			// $sql.= "LEFT JOIN  ".MAIN_DB_PREFIX."societe as s ON s.rowid = p.fk_soc ";
			$sql.= " WHERE 1 ";
			$sql .= " AND (
					(p.phone IS NOT NULL AND p.phone IN (".$list.")  )
					OR
					(p.phone_perso IS NOT NULL AND p.phone_perso IN (".$list.")  )
					OR
					(p.phone_mobile IS NOT NULL AND p.phone_mobile IN (".$list.") )

			)";


			$result = $db->query($sql);
			$i = 0;
			$num = $db->num_rows($result);
			$string ='';

			if($num>0){
				while ($i < $num){
					$user_array[] = $db->fetch_object($result);
					$i++;
				}
			}


		if(count($user_array)>0)
			return $user_array;
		else
			return false;
	}


}



?>