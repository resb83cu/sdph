<?php
class Sys_menu_model extends Model {
	
	const TABLE_NAME = 'sys_menus';
	
	function __construct() {
		parent::Model ();
	}
	
	function getMenu($roll_id) {
		
		if ($roll_id > 1) {
			$query = $this->db->query ( "select 
											sm.menus_id, 
											sm.menus_parentid, 
											sm.menus_name as text, 
											sm.menus_title as fieldLabel,
											sm.menus_image as cheked,
											sm.menus_href as href 
										from sys_menus sm
										inner join sys_menus_rel_roll mr on sm.menus_id = mr.menus_id
										where mr.roll_id = " . $roll_id ."
										order by menus_order" );
		} else {
			$query = $this->db->query ( "select 
											sm.menus_id, 
											sm.menus_parentid, 
											sm.menus_name as text, 
											sm.menus_title as fieldLabel,
											sm.menus_image as cheked,
											sm.menus_href as href 
										from sys_menus sm
										inner join sys_menus_rel_roll mr on sm.menus_id = mr.menus_id
										where mr.roll_id = " . 1 ."
										order by menus_order" );
			
		}
		
		$menuArr = $query->result_array ();
		if (is_array ( $menuArr ) && count ( $menuArr ) > 0) {
			$burl = base_url () . $this->config->item ( 'index_page' ) . "/";
			foreach ( $menuArr as $key => $menu ) {
				if ($menuArr [$key] ['href'] != '') {
					$menuArr [$key] ['href'] = $burl . $menu ['href'];
				}
			}
		
		}
		
		return $menuArr;
	}
	
	public function getData() {
		$this->db->select ( 'sys_menus.menus_id, 
							sys_menus.menus_title,
							sys_menus.menus_parentid' );
		$this->db->from ( self::TABLE_NAME );
		$this->db->where ( 'sys_menus.menus_id !=', 'root' );
		$this->db->order_by ( "menus_title", "desc" );
		$result = $this->db->get ();
		if ($result->result () != null) {
			foreach ( $result->result () as $row ) {
				if ($row->menus_parentid == 'root') {
					$value [] = array ('menus_id' => $row->menus_id, 
										'menus_title' => $row->menus_title);
				} else {
					$menu = Sys_menu_model::getNameById ( $row->menus_parentid );
					$value [] = array ('menus_id' => $row->menus_id, 
										'menus_title' => $menu . ' / ' . $row->menus_title);
				}
			
			}
		
		} else {
			$value = array ();
		}
		return $value;
		
		$result = $this->db->get (self::TABLE_NAME);
		return $result->result_array ();
	}
	
	public function getNameById($menu_id) {
		$this->db->select ( 'menus_title' );
		$this->db->from ( self::TABLE_NAME );
		$this->db->where ( 'menus_id', $menu_id );
		$result = $this->db->get ();
		foreach ( $result->result () as $row ) {
			$menus_title = $row->menus_title;
		}
		return $menus_title;
	}

}
?>