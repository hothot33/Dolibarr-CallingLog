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



class calling
// 	extends
	{

	/**
		@var int 1: init, 2: connect, 3: end
	*/
	public $mode;
	/**
		@var string Num tel ext appelant
	*/
	public $call_to;
	/**
		@var string Num tel calling , is internal local num
	*/
	public $call_from;
	/**
		@var int  local user id based on call_from num callling
	*/
	public $call_to_user_id;
	/**
		@var int value in second for delay connect
	*/
	public $duration_connect;
	/**
		@var int  value in seconde for duraction calling
	*/
	public $duration_calling;
	/**
		@var object db current doli
	*/
	protected $db;

	/**
		@fn
	*/
	function __construct($db){
		$this->db = $db;
	}

	/**
		@fn create()
	*/
	function create($user){

		$this->db->begin();

		$sql = "INSERT INTO ".MAIN_DB_PREFIX."calling (";
		$sql.= "mode";
		$sql.= ", fk_user_id";
		$sql.= ", call_to";
		$sql.= ", call_from";
		$sql.= ", call_to_user_id";
		$sql.= ", data ";
		$sql.= ", time_init ";
		$sql.= ") VALUES (";
		$sql.= " 1 ";
		$sql.= ", ".$user->id;
		$sql.= ", '".$this->call_to."'";
		$sql.= ", '".$this->call_from."'";
		$sql.= ", '".$this->call_to_user_id."' ";
		$sql.= ", '".(is_array($this->data) ?  json_encode($this->data) : $this->data )."' ";
		$sql.= ", NOW() ";
		$sql.= ")";
// echo $sql;
		$result = $this->db->query($sql);
		if ( $result )
		{
			$id = $this->db->last_insert_id(MAIN_DB_PREFIX."calling");

			if ($id > 0)
			{
				$this->db->commit();
				return $id;
			}
			else
			{
				$this->db->rollback();
				return -1;
			}
		}
		else
		{
			$this->error=$this->db->lasterror();
				return -2;
		}
	}

	/**
		@fn update()
	*/
	function update($user){


	
		$this->db->begin();



		$sql = "UPDATE ".MAIN_DB_PREFIX."calling SET ";
		$sql.= "mode = '".$this->mode."' ";
// 		$sql.= ", fk_user_id = '".$this->user_id."'";
// 		$sql.= ", call_to = '".$this->call_to."'";
// 		$sql.= ", call_from = '".$this->call_from."'";

		if($this->mode == 2)
			$sql.= ", time_connect ='".date('Y-m-d H:i:s')."' ";
		elseif($this->mode == 3)
			$sql.= ", time_release = NOW() ";

		if(is_object($this->data))
			$sql.= ", data = '". json_encode($this->data) ."' ";
		
		$sql.= " WHERE rowid = ".$this->id;

		$result = $this->db->query($sql);
		if ( $result )
		{
			$this->db->commit();
			return $this->id;
		}
		else
		{
		
			$this->error=$this->db->lasterror();
			$this->db->rollback();
			return -1;
		}
	}

	/**
		@fn fetch()
		@param $id integer
	*/
	function fetch($id, $user){

			if($id<=0)
				return false;

			$sql = "SELECT rowid,
									mode ,
									call_to,
									call_from,
									call_to_user_id,
									data,
									TIMESTAMPDIFF(SECOND, time_init, time_connect ) as  duration_connect,
									TIMESTAMPDIFF(SECOND,time_connect, time_release ) as  duration_calling
									FROM ".MAIN_DB_PREFIX."calling  ";
			$sql .=" WHERE rowid = '".$id."' ";


			$result = $this->db->query($sql);

			if($result !=false) {
				$num =  $this->db->num_rows($result);
				if($num) {
					$objp =  $this->db->fetch_object($result);

					$this->id = $objp->rowid;
					$this->mode = $objp->mode;
					$this->call_to = $objp->call_to;
					$this->call_from = $objp->call_from;
					$this->call_to_user_id = $objp->call_to_user_id;
					$this->data =  json_decode($objp->data);
// 					$this->data = ($objp->data);

					$this->duration_connect = (($objp->duration_connect> 0 ) ? $objp->duration_connect : 0);
					$this->duration_calling =  (($objp->duration_calling> 0 ) ? $objp->duration_calling : 0);
					return 1;
				}

				return 0;
			}

			return false;
	}

	/**
		@fn search(opt)
	*/
	function search($opt){
			$array_res = array();
			$sql = "SELECT rowid FROM ".MAIN_DB_PREFIX."calling  WHERE 1 ";

			if(isset($opt['mode']) )
				$sql.= " AND mode = '".$opt['mode']."' ";
			elseif(isset($opt['in_mode']) )
				$sql.= " AND mode IN (1,2) ";
			if(isset($opt['call_to']) )
				$sql.= " AND call_to = '".$opt['call_to']."' ";
			if(isset($opt['call_from']) )
				$sql.= " AND call_from = '".$opt['call_from']."' ";
			if(isset($opt['to_user']) )
				$sql.= " AND ( call_to_user_id = '".$opt['to_user']."' OR call_to_user_id = 0 ) ";


			$sql.= " LIMIT 1 ";

			$result = $this->db->query($sql);

			if($result !=false) {
				$num =  $this->db->num_rows($result);
				if($num) {
						$objp = $this->db->fetch_object($result);
						return $objp->rowid;
				}
			}

			return false;
	}

	/**
		@fn delete()
	*/
	function delete($user){
			if($this->id<=0)
				return false;

			$sql = "DELETE FROM  ".MAIN_DB_PREFIX."calling  WHERE rowid = ".$this->id;

			return  $this->db->query($sql);
	}

	/**
		@fn maintenance($user)
	*/
	function maintenance($user){

			// delete all row  mode 1 and 3 (init and relase) > 5 min
			$sql = "DELETE FROM  ".MAIN_DB_PREFIX."calling  WHERE DATE_ADD(time_init , INTERVAL 1 MINUTE) < NOW()  AND mode IN (1, 3);    ";

			return  $this->db->query($sql);
	}

}

?>
