<?php

class Conf_hotelchains_model extends Model {
	const TABLE_NAME = 'conf_hotelchains';
	
	function __construct() {
		parent::__construct ();
	}
	
	/**
	 * Funcion que devuelve los valores de la tabla Conf_chains 
	 *
	 * @param int $hasta
	 * @param int $desde
	 * @return array
	 */
	public function getData() {
		$this->db->from ( self::TABLE_NAME );
		$this->db->where ( 'chain_deleted', 'no' );
		$result = $this->db->get ();
		$value = array();
		if ($result->result () != null) {
			foreach ( $result->result () as $row ) {
				$deleted = ucfirst($row->chain_deleted);
				$value [] = array ('chain_id' => $row->chain_id, 
									'chain_name' => $row->chain_name,
									'chain_deleted' => $deleted );
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
				$deleted = ucfirst($row->chain_deleted);
				$value [] = array ('chain_id' => $row->chain_id, 
									'chain_name' => $row->chain_name,
									'chain_deleted' => $deleted );
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
	 * Esta es la funcion encargada de insertar las chains
	 *
	 * @return boolean
	 */
	public function insert() {
		$chain_id = $this->input->post ( 'chain_id' );
		$chains ['chain_name'] = $this->input->post ( 'chain_name' );
		if (empty ( $chain_id )) {
			$this->db->trans_begin ();
			$re = $this->db->insert ( self::TABLE_NAME, $chains );
			$logs = new Logs ( );
			$myquery = $logs->sqlinsert ( self::TABLE_NAME, $chains );
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
			$chains ['chain_deleted'] = strtolower($this->input->post ( 'chain_deleted' ));
			$this->db->where ( 'chain_id', $chain_id );
			$this->db->trans_begin ();
			$re = $this->db->update ( self::TABLE_NAME, $chains );
			$logs = new Logs ( );
			
			$mywhere = 'where chain_id = '.$chain_id;
			$myquery = $logs->sqlupdate(self::TABLE_NAME, $chains, $mywhere);
			
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
	 * Funcion para eliminar una cadena por su nombre
	 *
	 * @param string $chain_name
	 */
	public function delete($chain_id) {
		$chains ['chain_deleted'] = 'si';
		$this->db->where ( 'chain_id', $chain_id );
		$this->db->trans_begin ();
		$re = $this->db->update ( self::TABLE_NAME, $chains );
		$logs = new Logs ( );
		$mywhere = 'where chain_id = ' . $chain_id;
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
	 * @param string $chain_name
	 * @return Cadenas
	 */
	public function getByName($chain_name) {
		$result = $this->db->get_where ( self::TABLE_NAME, array ('chain_name' => $chain_name ) );
		return $result->result_array ();
	
	}
	
	public function get() {
		$result = $this->db->get ( self::TABLE_NAME );
		return $result->result_array;
	} // de la funcion
	

	public function getById($chain_id) {
		$this->db->from ( self::TABLE_NAME );
		$this->db->where ( 'chain_id', $chain_id );
		$result = $this->db->get ();
		$value=array();
		if ($result->result () != null) {
			foreach ( $result->result () as $row ) {
				$deleted = ucfirst($row->chain_deleted);
				$value [] = array ('chain_id' => $row->chain_id, 
									'chain_name' => $row->chain_name,
									'chain_deleted' => $deleted );
			}
		
		}
		return $value;
	
	}
	
	public function getNameById($chain_id) {
		$this->db->select ( 'chain_name' );
		$this->db->from ( self::TABLE_NAME );
		$this->db->where ( 'hotel_id', $chain_id );
		$result = $this->db->get ();
		foreach ( $result->result () as $row ) {
			$chain_name = $row->chain_name;
		}
		return $chain_name;
	}
	

}
?>
