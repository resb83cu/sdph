<?php

class Conf_positions_model extends Model {
	const TABLE_NAME = 'conf_positions';
	
	function __construct() {
		parent::__construct ();
	}
	
	/**
	 * Funcion que devuelve los valores de la tabla Conf_positions 
	 *
	 * @param int $hasta
	 * @param int $desde
	 * @return array
	 */
	public function getData () {
		$this->db->from ( self::TABLE_NAME );
		$this->db->where ( 'position_deleted', 'no' );
		$this->db->order_by ('position_name', 'asc');
		$result = $this->db->get ();
		return $result->result_array ();
	} // de la funcion
	
	public function getDataGrid ($to, $from, $name) {
		$this->db->select ( 'position_id, 
							position_name,
							position_deleted' );
		$this->db->from ( self::TABLE_NAME );
		if (! empty( $name )) {
			$this->db->like ( self::TABLE_NAME. '.position_name', strtolower($name) );
			$this->db->or_like ( self::TABLE_NAME. '.position_name', ucfirst($name) );
		}
		$this->db->order_by("position_name", "asc");		
		$this->db->limit ( $to, $from);
		$result = $this->db->get ();
		$value = array();
		if ($result->result () != "") {
			foreach ( $result->result () as $row ) {
				$deleted = ucfirst($row->position_deleted);
				$value [] = array ('position_id' => $row->position_id, 
									'position_name' => $row->position_name,
									'position_deleted' => $deleted);
			}
		
		}
		return $value;
	} // de la funcion	
	

	/**
	 * funcion que devuelve la cantidad de registros en la tabla ejemplo
	 *
	 */
	public function getCant($to, $from, $name) {
		$this->db->select ( 'position_id, 
							position_name,
							position_deleted' );
		$this->db->from ( self::TABLE_NAME );
		if (! empty( $name )) {
			$this->db->like ( self::TABLE_NAME. '.position_name', strtolower($name) );
			$this->db->or_like ( self::TABLE_NAME. '.position_name', ucfirst($name) );
		}		
		$this->db->limit ( $to, $from);
		return $this->db->count_all_results();
	}
	
	/**
	 * Esta es la funcion encargada de insertar
	 *
	 * @return boolean
	 */
	public function insert() {
		$position_id = $this->input->post ( 'position_id' );
		$positions ['position_name'] = $this->input->post ( 'position_name' );
		$exist = $this->getByName($this->input->post ( 'position_name' ));

		if (empty ( $position_id )) {
			if ($exist > 0) {
				return "{success: false, errors: { reason: 'Ya existe una plaza con ese nombre.' }}";
			}
			$this->db->trans_begin ();
			$re = $this->db->insert ( self::TABLE_NAME, $positions );
			$logs = new Logs ( );
			$myquery = $logs->sqlinsert ( self::TABLE_NAME, $positions );
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
			$positions ['position_deleted'] = strtolower($this->input->post ( 'position_deleted' ));
			$this->db->where ( 'position_id', $position_id );
			$this->db->trans_begin ();
			$re = $this->db->update ( self::TABLE_NAME, $positions );
			$logs = new Logs ( );
			
			$mywhere = 'where position_id = '.$position_id;
			$myquery = $logs->sqlupdate(self::TABLE_NAME, $positions, $mywhere);
			
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
	 * @param string $position_name
	 */
	public function delete($position_id) {
		$positions ['position_deleted'] = 'si';
		$this->db->where ( 'position_id', $position_id );
		$this->db->trans_begin ();
		$this->db->update ( self::TABLE_NAME, $positions );
		$logs = new Logs ( );
		$mywhere = 'where position_id = ' . $position_id;
		$myquery = $logs->sqldelete ( self::TABLE_NAME, $mywhere );
		$logs->write ( self::TABLE_NAME, 'DELETE', $myquery );
		
		if ($this->db->trans_status () === FALSE) {
			$this->db->trans_rollback ();
		} else {
			$this->db->trans_commit ();
		}
		
	}
	
	/**
	 *
	 * @param string $position_name
	 * @return positions
	 */
	public function getByName($position_name) {
		$this->db->from ( self::TABLE_NAME );
		$this->db->where ( 'position_name', $position_name );
		return $this->db->count_all_results ();
	
	}
	
	public function get() {
		$result = $this->db->get ( self::TABLE_NAME );
		return $result->result_array;
	} // de la funcion
	

	public function getById($position_id) {
		$this->db->select ( 'position_id, 
							position_name,
							position_deleted' );
		$this->db->from ( self::TABLE_NAME );
		$this->db->where ( 'position_id', $position_id );
		$result = $this->db->get ();
		$value = array();
		if ($result->result () != "") {
			foreach ( $result->result () as $row ) {
				$deleted = ucfirst($row->position_deleted);
				$value [] = array ('position_id' => $row->position_id, 
									'position_name' => $row->position_name,
									'position_deleted' => $deleted);
			}
		
		}
		return $value;
	
	} // de la funcion
	
	public function getNameById($position_id) {
		$this->db->select ( 'position_name' );
		$this->db->from ( self::TABLE_NAME );
		$this->db->where ( 'position_id', $position_id );
		$result = $this->db->get ();
		foreach ( $result->result () as $row ) {
			$position_name = $row->position_name;
		}
		return $position_name;
	
	}


}
?>
