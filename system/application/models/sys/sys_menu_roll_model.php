<?php
class Sys_menu_roll_model extends Model {
	
	const TABLE_NAME = 'sys_menus_rel_roll';
	
	function __construct() {
		parent::Model ();
	}
	
	function getData() {
		$this->load->model ( 'sys/sys_menu_model' );
		$this->db->select ( 'sys_menus_rel_roll.roll_id, 
							sys_menus_rel_roll.menus_id, 
							sys_menus.menus_title,
							sys_menus.menus_parentid, 
							user_rolls.roll_name' );
		$this->db->from ( self::TABLE_NAME );
		$this->db->join ( 'sys_menus', 'sys_menus.menus_id = ' . self::TABLE_NAME . '.menus_id', 'inner' );
		$this->db->join ( 'user_rolls', 'user_rolls.roll_id = ' . self::TABLE_NAME . '.roll_id', 'inner' );
		$this->db->where ( 'sys_menus.menus_id !=', 'root' );
		$this->db->order_by ( "roll_id", "desc" );
		$result = $this->db->get ();
		if ($result->result () != null) {
			foreach ( $result->result () as $row ) {
				if ($row->menus_parentid == 'root') {
					$value [] = array ('roll_id' => $row->roll_id, 
										'menus_id' => $row->menus_id, 
										'menus_title' => $row->menus_title, 
										'roll_name' => $row->roll_name );
				} else {
					$menu = Sys_menu_model::getNameById ( $row->menus_parentid );
					$value [] = array ('roll_id' => $row->roll_id, 
										'menus_id' => $row->menus_id, 
										'menus_title' => $menu . ' / ' . $row->menus_title, 
										'roll_name' => $row->roll_name );
				}
			
			}
		
		} else {
			$value = array ();
		}
		return $value;
	}
	
	public function getCant() {
		return $this->db->count_all ( self::TABLE_NAME );
	}
	
	public function getById($center_id) {
		$this->db->from ( self::TABLE_NAME );
		$this->db->where ( 'center_id', $center_id );
		$result = $this->db->get ();
		return $result->result_array ();
	
	}
	
	public function getCountById($menus_id, $roll_id) {
		$this->db->select ( 'roll_id, menus_id' );
		$this->db->from ( self::TABLE_NAME );
		$this->db->where ( 'menus_id', $menus_id );
		$this->db->where ( 'roll_id', $roll_id );
		return $this->db->count_all_results ();
	}
	
	public function insert() {
		$menus_id = $this->input->post ( 'menus_id' );
		$roll_id = $this->input->post ( 'roll_id' );
		$menuroll ['menus_id'] = $menus_id;
		$menuroll ['roll_id'] = $roll_id;
		$count = self::getCountById ( $menus_id, $roll_id );
		if ($count > 0) {
			return "{success: false, errors: { reason: 'Ya el rol seleccionado tiene asignada esta opci&oacute;n de men&uacute;.' }}";
		} else {
			$re = $this->db->insert ( self::TABLE_NAME, $menuroll );
			if ($re == true)
				return "true";
			else
				return "false";
		}
	}

}
?>