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
 *	\file       includes/modules/calling/api_ovh.php
 *	\brief      File of class to manipulate calling by ovh api
 *	\author     Oscim <mail oscim@users.sourceforge.net>
 *	\version    $Id: api_ovh.php,v 1.56 2012/06/10 15:28:01 oscim Exp $
 */

include_once(DOL_DOCUMENT_ROOT ."/calling/core/modules/calling/calling_api.php");

Class api_ovh
	extends calling_api{


	/**
		@var level current mod
	*/
	public $version='dolibarr';		// 'development', 'experimental', 'dolibarr'
	/**
		@var string name api
	*/
	public $nom='ovh';
	/**
		@var int version api
	*/
	public $api_vers='1.2';

	public $error='';


	/**     \brief      Return description of numbering module
		*      \return     string      Text with description
		*/
	function info(){
		global $langs,$db;
			return $langs->trans("CallingApiOvhDescription");
	}


	/**     \brief      Renvoi un exemple de numerotation
	 *      \return     string      Example
	 */
// 	function getDetail(){
// 		return "emission &amp; <s>reception</s>";
// 	}


	/**
		@brief      Url for external notifi in dolibarr
		@return     string
	 */
	function getUrlForNotification(){
		return $this->internalurlnotif.'?account=_ACCOUNT_&caller=_CALLER_&callee=_CALLEE_&type=_N_TYPE_&callref=_CALLREF_&version=_N_VERSION_';
	}


	/**     \brief      Test si les numeros deje en vigueur dans la base ne provoquent pas de
	 *                  de conflits qui empechera cette numerotation de fonctionner.
	 *      \return     boolean     false si conflit, true si ok
	 */
	function canBeActivated()
	{
		global $conf,$langs;

		return true;
	}



	/**
		@brief Emission d'un appel
	*/
	function Process_Send($user){
		global $conf,$langs,$db;
		$telephone = $_GET["called"];
		$user = $_GET["login"];
		$password = $_GET["password"];
		$mytel = $_GET["caller"];
		$account_billing = $_GET["caller"];


		try {
		$soap = new SoapClient("https://www.ovh.com/soapi/soapi-re-1.56.wsdl");

				//telephonyClick2CallDo
				if( ($result = $soap->telephonyClick2CallDo($user, $password, $mytel ,$telephone, $account_billing) ) && $result != false){

					$telephone = $_GET["called"];
					$hash= trim($_GET['login']).':'.trim($_GET['password']);

					$array_res = calling_api::FormatTelNum($telephone);
					$telephone = $array_res['master'];

					$user_array = calling_api::SearchTelInDb($array_res['decline']);

					if(is_array($user_array))
						$obj = $user_array[0];


					if($result =='OK'){
						echo "telephonyClick2CallDo successfull Ok\n";
						flush();

						if(isset($obj->socid) && $obj->socid > 0) {
							$object->user->type = 'societe';
							$object->user->id =$obj->socid;
							$object->user->name = $obj->nom;
						}
						elseif(isset($obj->contact_id) && $obj->contact_id > 0) {
							$object->user->type = 'contact';
							$object->user->id = $obj->contact_id;
							$object->user->name = $obj->contact;
						}

						// trace in Action Agenda
						$actcom = new ActionCommCalling($db);
						$r= $actcom->AddCallingOutgoing($object,$user);

						// l appel passe donc je te renvoie vers Dolibarr
						echo "<script type='text/javascript'>history.back();</script>";
					}
				}

				// l appel passe donc je te renvoie vers Dolibarr
				echo "<script type='text/javascript'>history.back();</script>";

		}
		catch(SoapFault $fault) {
		echo $fault;
		}


	}

}





?>