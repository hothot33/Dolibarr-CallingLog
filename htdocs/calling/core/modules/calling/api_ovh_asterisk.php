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
 *	\file       includes/modules/calling/api_asterisk.php
 *	\brief      File of class to manage widget boxes
 *	\author     Oscim <mail oscim@users.sourceforge.net>
 *	\version    $Id: api_asterisk.php,v 1.56 2012/06/10 15:28:01 oscim Exp $
 */

include_once(DOL_DOCUMENT_ROOT ."/calling/core/modules/calling/api_ovh.php");

Class api_ovh_asterisk
	extends api_ovh{


	/**
		@var level current mod
	*/
	public $version='dolibarr';		// 'development', 'experimental', 'dolibarr'
	/**
		@var string name api
	*/
	public $nom='ovh_asterisk';
	/**
		@var int version api
	*/
	public $api_vers='1.2';

	public $error='';



	/**
		@brief      Return description of numbering module
		@return     string      Text with description
		*/
	function info(){
		global $langs;
			return $langs->trans("CallingApiOvhAsteriskDescription");
	}


	/**
		@brief      Mode de fonctionnement
		@return     string
	 */
// 	function getDetail(){
// 		return "emission &amp; reception";
// 	}

	/**
		@brief      Url for external notifi in dolibarr
		@return     string
	 */
	function getUrlForNotification(){
		return $this->internalurlnotif.'?account=_ACCOUNT_&caller=_CALLER_&callee=_CALLEE_&type=_N_TYPE_&callref=_CALLREF_&version=_N_VERSION_';
	}



	/**
		@brief      Test activation possible du module
		@return     boolean
	 */
	function canBeActivated(){
		global $conf,$langs;

		return true;
	}


	/**
		@brief Reception des appels et add action in dolibarr
		@param $user object user current
		@param $time
		@return -99 for exe process clicktocall or boolean
	*/
	function Process_Receive($user, $time){
		global $conf,$langs,$db;

		/*
			If all GET is not exist, retrun for execute process clicktocall
		*/
		if(!isset($_GET['account']) || !isset($_GET['caller']) || !isset($_GET['callee']) || !isset($_GET['type'])  )
			return -99;

			// init support class
			$calling = new calling($db);

			/**
				@var $etat string is step calling
					SETUP calling : 1
					CONNECT calling : 2 uniquement si reponse
					RELEASE calling : 3
			*/
			$etat = $_GET['type'];
			$callto = $_GET["callee"]; // To
			$telephone = $_GET["caller"]; // From
			$string = '';
		  $object = new StdClass();





			/// Init open line
			if($etat =='SETUP'){
			}

			$array = array(
					'time'=>microtime(),
					'etat'=>$etat,
					'callto'=>$callto,
			);

			// format Tel To
			// clean tel and retrun array all format
			$array_res = calling_api::FormatTelNum($callto);
			$callto = $array_res['master'];

			/*
				CALLING_ALERT_TYPE_MODE
				Appel interne
				Collaborateur ou numero protable associé . Echappement cf config
			*/
			if(  ($local_user = calling_api::AlertTypeMode($array_res['decline']) ) && $local_user == false )
				return ;
			else{
				if(is_object($local_user))
// 					$array['collaborateur'] =$local_user->id;
					$array['collaborateur'] =$local_user;
			}


			// Format Call From
			$array_res = calling_api::FormatTelNum($telephone);
			$telephone = $array_res['master'];
			// search in db for tel in table soc, contact, tiers
			$array['user'] = calling_api::GetExternalTel($array_res, (($etat =='SETUP')? true: false ) );
			$object->user = $array['user'][0];


		if($etat =='SETUP'){
				$calling->call_to = $callto;
				$calling->call_to_user_id = (($local_user == false) ? 0 :  $local_user->id ) ;
				$calling->call_from = $telephone;
				$calling->data = $array;
				$r = $calling->create($user);
					return true ;
		}
		/**
				@remarks Stock in session ligne reponse
		*/
		elseif($etat =='CONNECT'){

				$id = $calling->search(array('call_to'=>$callto, 'call_from'=>$telephone, 'mode'=>1) );

				if($id > 0) {
					if( $calling->fetch($id, $user) ){
						$calling->mode = 2;
						$calling->update($user);
					}
					else
						return false;
				}
				return true;
		}
		/**
				@remarks If fin d'appel suppression du fichier
		*/
		elseif($etat =='RELEASE'){


				// Close Calling after connected
				if( ($id = $calling->search(array('callee'=>$callto, 'caller'=>$telephone, 'mode'=>2) )) && $id > 0) {
					$calling->fetch($id, $user);
					$calling->mode = 3;
					$calling->update($user);

					// reload
					$calling->fetch($id, $user);
				}
				// close calling but no reponse
				elseif( ($id = $calling->search(array('callee'=>$callto, 'caller'=>$telephone, 'mode'=>1) )) && $id > 0){
					$calling->fetch($id, $user);
					$calling->mode = 2;
				}
				else
					return false;

				// trace in Action Agenda
				$actcom = new ActionCommCalling($db);
				$r= $actcom->AddCallingIncoming($calling,$object,$user);

				// delete entry
				$calling->delete($user);
			}


		return true;
	}


}

?>
