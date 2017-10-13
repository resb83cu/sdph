<?php

class Conf_provinces_model extends Model {
	const TABLE_NAME = 'conf_provinces';
	
	function __construct() {
		parent::__construct ();
	}
	
	/**
	 * Funcion que devuelve los valores de la tabla Conf_provinces 
	 *
	 * @param int $hasta
	 * @param int $desde
	 * @return array
	 */
	public function getData() {
		$this->db->from ( self::TABLE_NAME );
		$this->db->where ( 'province_deleted', 'no' );
		$this->db->order_by("province_id", "asc"); 
		$result = $this->db->get ();
		$value=array();
		if ($result->result () != null) {
			foreach ( $result->result () as $row ) {
				$deleted = ucfirst($row->province_deleted);
				$value [] = array ('province_id' => $row->province_id, 
									'province_name' => $row->province_name,
									'province_deleted' => $deleted );
			}
		
		}
		return $value;
	} // de la funcion
	
	public function getDataGrid() {
		$this->db->from ( self::TABLE_NAME );
		$this->db->order_by("province_id", "asc"); 
		$result = $this->db->get ();
		$value=array();
		if ($result->result () != null) {
			foreach ( $result->result () as $row ) {
				$deleted = ucfirst($row->province_deleted);
				$value [] = array ('province_id' => $row->province_id, 
									'province_name' => $row->province_name,
									'province_deleted' => $deleted );
			}
		
		}
		return $value;
	} // de la funcion
	
	/**
	 * funcion que devuelve la cantidad de registros en la tabla ejemplo
	 *
	 */
	public function getCant() {
		return $this->db->count_all ( self::TABLE_NAME );
	}
	
	/**
	 * Esta es la funcion encargada de insertar las provincias
	 *
	 * @return boolean
	 */
	public function insertProvince() {
		$province_id = $this->input->post ( 'province_id' );
		$province ['province_name'] = $this->input->post ( 'province_name' );
		if (empty ( $province_id )) {
			$this->db->trans_begin ();
			$re = $this->db->insert ( self::TABLE_NAME, $province );
			$logs = new Logs ( );
			$myquery = $logs->sqlinsert ( self::TABLE_NAME, $province );
			$logs->write ( self::TABLE_NAME, 'INSERT', $myquery );
			
			if ($this->db->trans_status () === FALSE) {
				$this->db->trans_rollback ();
			} else {
				$this->db->trans_commit ();
			}			
			
			if ($re == true)
				return "true"; else
				return "false";
		
		} else {
			$province['province_deleted'] = strtolower($this->input->post ( 'province_deleted' ));
			$this->db->where ( 'province_id', $province_id );
			$this->db->trans_begin ();
			$re = $this->db->update ( self::TABLE_NAME, $province );
			$logs = new Logs ( );
			
			$mywhere = 'where province_id = '.$province_id;
			$myquery = $logs->sqlupdate(self::TABLE_NAME, $province, $mywhere);
			
			$logs->write( self::TABLE_NAME, 'UPDATE', $myquery);
			
			if ($this->db->trans_status () === FALSE) {
				$this->db->trans_rollback ();
			} else {
				$this->db->trans_commit ();
			}
			
			if ($re == true)
				return "true"; else
				return "false";
		}

	}
	
	/**
	 * Funcion para eliminar una provincia por su nombre
	 *
	 * @param string $province_name
	 */
	public function deleteProvince($province_id) {
		$provinces ['province_deleted'] = 'si';
		$this->db->where ( 'province_id', $province_id );
		$this->db->trans_begin ();
		$this->db->update ( self::TABLE_NAME, $provinces);
		$logs = new Logs ( );
		$mywhere = 'where province_id=' . $province_id;
		$myquery = $logs->sqldelete ( self::TABLE_NAME, $mywhere );
		$logs->write ( self::TABLE_NAME, 'DELETE', $myquery );
		
		if ($this->db->trans_status () === FALSE) {
			$this->db->trans_rollback ();
		} else {
			$this->db->trans_commit ();
		}
	}
	
	/**
	 * Esta funcion devuelve una Provincia dado el nombre de la misma
	 *
	 * @param string $province_name
	 * @return Provinces
	 */
	public function getProvinceByName($province_name) {
		$result = $this->db->get_where ( self::TABLE_NAME, array ('province_name' => $province_name ) );
		return $result->result_array ();
	
	}
	
	public function getNameById($province_id) {
		$this->db->select ( 'province_name' );
		$this->db->from ( self::TABLE_NAME );
		$this->db->where ( 'province_id', $province_id );
		$result = $this->db->get ();
		foreach ( $result->result () as $row ) {
			$name = $row->province_name;
		}
		return $name;
	
	}
	
	public function getProvinces() {
		$result = $this->db->get ( self::TABLE_NAME );
		return $result->result_array;
	} // de la funcion
	
	public function getById($province_id) {
		$this->db->from ( self::TABLE_NAME );
		$this->db->where ( 'province_id', $province_id );
		$result = $this->db->get ();
		$value=array();
		if ($result->result () != null) {
			foreach ( $result->result () as $row ) {
				$deleted = ucfirst($row->province_deleted);
				$value [] = array ('province_id' => $row->province_id, 
									'province_name' => $row->province_name,
									'province_deleted' => $deleted );
			}
		
		}
		return $value;
	
	} // de la funcion	


}
?>
