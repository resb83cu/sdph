<?php

class User_sessions_model extends Model {
	const TABLE_NAME = 'user_users';
	
	function __construct() {
		parent::__construct ();
	}
	
	function login($username = "", $password = "") {
		
		$sql = "SELECT * FROM users WHERE username=? AND password=?";
		$CI = & get_instance ();
		$query = $CI->db->query ( $sql, array ($username, $password ) );
		if ($query->num_rows () == 1) {
			$row = $query->row ();
			
			$CI->session->set_userdata ( 'person_id', $row->person_id );
			$this->_person_id = $row->person_id;
			$CI->session->set_userdata ( 'user_name', $user_name );
			$this->_user_name = $row->user_name;
			$CI->session->set_userdata ( 'user_password', $user_password );
			$this->_user_password = $row->user_password;
			$CI->session->set_userdata ( 'person_fullname', $person_fullname );
			$this->_person_fullname = $row->person_fullname;
			$CI->session->set_userdata ( 'roll_id', $row->roll_id );
			$this->_roll_id = $row->roll_id;
			$CI->session->set_userdata ( 'center_id', $row->center_id );
			$this->_center_id = $row->center_id;
			$CI->session->set_userdata ( 'province_id', $row->province_id );
			$this->_province_id = $row->province_id;
			$CI->session->set_userdata ( 'session_ip', $row->session_ip );
			//$this->_session_ip = $row->session_ip;
			//$this->_auth = TRUE;
			
			return TRUE;
		} 
		return FALSE;
		
		$this->load->model ( 'conf/conf_provinces_model' );
		$this->load->model ( 'user/user_rolls_model' );
		$this->load->model ( 'person/person_persons_model' );
		$person_persons_table = 'person_persons';
		$person_workers_table = 'person_workers';
		$user_sessions_table = 'user_sessions';
		$this->db->select ( 'user_users.person_id,
							user_users.user_name,
							user_users.user_password,
							user_users.roll_id,
							person_persons.province_id,
							person_workers.center_id,' );
		$this->db->from ( self::TABLE_NAME );
		$this->db->join ( $person_workers_table, $person_workers_table . '.person_id = ' . self::TABLE_NAME . '.person_id', 'inner' );
		$this->db->join ( $person_persons_table, $person_persons_table . '.person_id = ' . $person_workers_table . '.person_id', 'inner' );
		$this->db->limit ( $to, $from );
		//$this->db->where ( $person_persons_table.'.person_deleted', 'no' );
		$result = $this->db->get ();
		$value = array();
		if ($result->result () != null) {
			foreach ( $result->result () as $row ) {
				$person_fullname = Person_persons_model::getNameById($row->person_id);
				//$roll_description = User_rolls_model::getDescriptionById($row->roll_id);
				//$province_name = Conf_provinces_model::getNameById($row->province_id);
				$value [] = array ('person_id' => $row->person_id, 
								'user_name' => $row->user_name, 
								'user_password' => $row->user_password, 
								'person_fullname' => $person_fullname, 
								'roll_id' => $row->roll_id,
								'province_id' =>  $row->$province_id,
								'center_id' => $row->$center_id
								//'roll_description' => $roll_description, 
								//'province_name' =>  $province_name
								);
			}
		}
		return $value;
		
		
	}
}
?>
