<?php

class Conf_motives_model extends Model {
	const TABLE_NAME = 'conf_motives';
	
	function __construct() {
		parent::__construct ();
	}
	
	/**
	 * Funcion que devuelve los valores de la tabla Conf_motives 
	 *
	 * @param int $hasta
	 * @param int $desde
	 * @return array
	 */
	public function getData($to, $from) {
		$this->db->from ( self::TABLE_NAME );
		$this->db->where ( 'motive_deleted', 'no' );
		$this->db->limit ( $to, $from );
		$result = $this->db->get ();
		$value=array();
		if ($result->result () != null) {
			foreach ( $result->result () as $row ) {
				$deleted = ucfirst($row->motive_deleted);
				$value [] = array ('motive_id' => $row->motive_id, 
									'motive_name' => $row->motive_name,
									'motive_deleted' => $deleted );
			}
		
		}
		return $value;
	} // de la funcion
	
	public function getDataGrid($to, $from) {
		$this->db->from ( self::TABLE_NAME );
		$this->db->limit ( $to, $from );
		$result = $this->db->get ();
		$value = array();
		if ($result->result () != null) {
			foreach ( $result->result () as $row ) {
				$deleted = ucfirst($row->motive_deleted);
				$value [] = array ('motive_id' => $row->motive_id, 
									'motive_name' => $row->motive_name,
									'motive_deleted' => $deleted );
			}
		
		}
		return $value;
	}
	

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
	public function insert() {
		$motive_id = $this->input->post ( 'motive_id' );
		$motives ['motive_name'] = $this->input->post ( 'motive_name' );
		if (empty ( $motive_id )) {
			$this->db->trans_begin ();
			$re = $this->db->insert ( self::TABLE_NAME, $motives );
			$logs = new Logs ( );
			$myquery = $logs->sqlinsert ( self::TABLE_NAME, $motives );
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
			$motives['motive_deleted'] = strtolower($this->input->post ( 'motive_deleted' ));
			$this->db->where ( 'motive_id', $motive_id );
			$this->db->trans_begin ();
			$re = $this->db->update ( self::TABLE_NAME, $motives );
			$logs = new Logs ( );
			
			$mywhere = 'where motive_id = '.$motive_id;
			$myquery = $logs->sqlupdate(self::TABLE_NAME, $motives, $mywhere);
			
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
	 * @param string $motive_name
	 */
	public function delete($motive_id) {
		$motives['motive_deleted'] = 'si';
		$this->db->where ( 'motive_id', $motive_id );
		$this->db->trans_begin ();
		$re = $this->db->update ( self::TABLE_NAME, $motives );
		$logs = new Logs ( );
		$mywhere = 'where motive_id = ' . $motive_id;
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
	 * @param string $motive_name
	 * @return motives
	 */
	public function getByName($motive_name) {
		$result = $this->db->get_where ( self::TABLE_NAME, array ('motive_name' => $motive_name ) );
		return $result->result_array ();
	
	}
	
	public function getMotives() {
		$result = $this->db->get ( self::TABLE_NAME );
		return $result->result_array;
	} // de la funcion
	

	public function getById($motive_id) {
		$this->db->from ( self::TABLE_NAME );
		$this->db->where ( 'motive_id', $motive_id );
		$result = $this->db->get ();
		$value=array();
		if ($result->result () != null) {
			foreach ( $result->result () as $row ) {
				$deleted = ucfirst($row->motive_deleted);
				$value [] = array ('motive_id' => $row->motive_id, 
									'motive_name' => $row->motive_name,
									'motive_deleted' => $deleted );
			}
		
		}
		return $value;
	
	} // de la funcion
	
	public function getNameById($motive_id) {
		$this->db->select ( 'motive_name' );
		$this->db->from ( self::TABLE_NAME );
		$this->db->where ( 'motive_id', $motive_id );
		$result = $this->db->get ();
		foreach ( $result->result () as $row ) {
			$motive_name = $row->motive_name;
		}
		return $motive_name;
	
	}


}
?>
