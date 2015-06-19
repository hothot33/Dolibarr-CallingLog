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


require_once(DOL_DOCUMENT_ROOT."/core/lib/agenda.lib.php");
require_once(DOL_DOCUMENT_ROOT."/contact/class/contact.class.php");
require_once(DOL_DOCUMENT_ROOT."/user/class/user.class.php");
require_once(DOL_DOCUMENT_ROOT."/comm/action/class/cactioncomm.class.php");
require_once(DOL_DOCUMENT_ROOT."/comm/action/class/actioncomm.class.php");


class ActionCommCalling
	extends ActionComm{

	/**
		@var object db current doli
	*/
	protected $db;
	/**
		@var object actioncomm static
	*/
	static protected $actioncomm;

	/**
		@var boolean active / inactive trace in action
	*/
	protected $enable = false;

	function __construct($db){
		$this->db = $db;

		global $conf;

		if( $conf->global->CALLING_LOGS_IN_ACTION == 'yes'  )
			$this->enable = true;


			/*
				Load context action
			*/
			$cactioncomm = new CActionComm($this->db);
			$cactioncomm->fetch('AC_TEL');


			/*
				prepa action and base init
			*/
			self::$actioncomm = new ActionComm($this->db);
			self::$actioncomm->type_id = $cactioncomm->id;
			self::$actioncomm->code = $cactioncomm->code;
			self::$actioncomm->location = '';
	}


		/**
		@brief Add outgoing  calling in agenda
		@param  $object object result all data
		@param $user object
	*/
	function AddCallingOutgoing($object, $user){

			if(!$this->enable)
				return ;

			if($object->user->type =='societe'){
				$societe = new Societe($this->db);
				$societe->fetch($object->user->id);
				self::$actioncomm->societe = $societe;
			}
			elseif($object->user->type =='contact') {
				$contact = new Contact($this->db);
				$contact->fetch($object->user->id);
				self::$actioncomm->contact = $contact;
			}
			else
				return false;

			$object = $this->FormatItemEntryOutgoing($object);
			
			// Initialisation objet actioncomm
			self::$actioncomm->label = $object->label;
			self::$actioncomm->datep =$object->date;
			self::$actioncomm->datef =$object->date;


			self::$actioncomm->percentage = 20;
			self::$actioncomm->note = $object->note;
			self::$actioncomm->date =$object->date;
			self::$actioncomm->dateend =$object->date;


			self::$actioncomm->userdone = new StdClass();
			self::$actioncomm->userdone->id = (int)$user->id;
			self::$actioncomm->usertodo = new StdClass();
			self::$actioncomm->usertodo->id = (int)$user->id;


			return self::$actioncomm->add($user);
	}


	/**
		@brief Add incoming calling in agenda
		@param  $calling object calling class
		@param  $object object result all data
		@param $user object
	*/
	function AddCallingIncoming($calling,$object,$user){

			if(!$this->enable)
				return ;

			$object = $this->FormatItemEntryIncoming($object, $calling);

			// Initialisation objet actioncomm
			self::$actioncomm->label = $object->label;
			self::$actioncomm->datep =$object->date;
			self::$actioncomm->datef =$object->date;

			// if connect and reponse by local user
			if( $calling->mode == 3){
					self::$actioncomm->percentage = 100;
					self::$actioncomm->note = $object->note;
					self::$actioncomm->date =$object->date;
					self::$actioncomm->dateend =$object->date;
			}
			// no reponse
			else{
					self::$actioncomm->percentage = 0;
					self::$actioncomm->note = $object->note;
			}


			self::$actioncomm->userdone = new StdClass();
			self::$actioncomm->userdone->id = (int)$user->id;
			self::$actioncomm->usertodo = new StdClass();
			self::$actioncomm->usertodo->id = (int)$user->id;

			if($object->user->type =='societe'){
				$societe = new Societe($this->db);
				$societe->fetch($object->user->id);
				self::$actioncomm->societe = $societe;
			}
			elseif($object->user->type =='contact') {
				$contact = new Contact($this->db);
				$contact->fetch($object->user->id);
				self::$actioncomm->contact = $contact;
			}
			else
				return false;

			return self::$actioncomm->add($user);
	}


	/**
		@brief Format and display text
	*/
	private function FormatItemEntryOutgoing($object){
		global $langs;

			$object->label = $langs->trans("CallingTxtOutgoingCall");
			$object->date = dol_mktime( date('H'), date('i'), date('s'), date('m'), date('d'), date('Y'));
			$object->note = '';

			$object->note = $langs->trans("CallingTxtOutgoingCall");

			return $object;
	}

	/**
		@brief Format and display text
	*/
	private function FormatItemEntryIncoming($object, $calling){
		global $langs;

		// convert in minute
		if($calling->duration_calling > 60)
			$duration = round(($calling->duration_calling/60),2) .' min';
		else
			$duration = $calling->duration_calling . 's' ;

		$object->label = $langs->trans("CallingTxtIncomingCall");
		$object->date = dol_mktime( date('H'), date('i'), date('s'), date('m'), date('d'), date('Y'));
		$object->note = '';

// 				if(isset($data['etat']) && $data['etat'] =='CONNECT')
		if( $calling->mode >= 2)
			$object->note = $langs->trans("CallingTxtIncomingCall") . sprintf( $langs->trans("CallingTxtIncomingCallTime").'%s' , $duration );
		else
			$object->note = $langs->trans("CallingTxtIncomingCall") . $langs->trans("CallingTxtIncomingCallnotanswered");

		return $object;
	}
}

?>
