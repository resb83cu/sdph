<?php

class Conf_ticketrequeststates_model extends Model {
	const TABLE_NAME = 'conf_ticketrequeststates';
	
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
	public function getData() {
		$this->db->from ( self::TABLE_NAME );
		$this->db->where ( 'state_deleted', 'no' );
		$result = $this->db->get (); // 
		return $result->result_array ();
	} // de la funcion
	

	public function getDataGrid($to, $from) {
		$this->db->from ( self::TABLE_NAME );
		$this->db->limit ( $to, $from );
		$result = $this->db->get ();
		$value = array();
		if ($result->result () != null) {
			foreach ( $result->result () as $row ) {
				$deleted = ucfirst($row->state_deleted);
				$value [] = array ('state_id' => $row->state_id, 
									'state_name' => $row->state_name,
									'state_deleted' => $deleted );
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
	 * Esta es la funcion encargada de insertar los transportes
	 *
	 * @return boolean
	 */
	public function insert() {
		$state_id = $this->input->post ( 'state_id' );
		$states ['state_name'] = $this->input->post ( 'state_name' );
		if (empty ( $state_id )) {
			$this->db->trans_begin ();
			$re = $this->db->insert ( self::TABLE_NAME, $states );
			$logs = new Logs ( );
			$myquery = $logs->sqlinsert ( self::TABLE_NAME, $states );
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
			$states ['state_deleted'] = strtolower($this->input->post ( 'state_deleted' ));
			$this->db->where ( 'state_id', $state_id );
			$this->db->trans_begin ();
			$re = $this->db->update ( self::TABLE_NAME, $states );
			$logs = new Logs ( );
			
			$mywhere = 'where state_id = '.$state_id;
			$myquery = $logs->sqlupdate(self::TABLE_NAME, $states, $mywhere);
			
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
	 * @param string $requeststate_name
	 */
	public function delete($state_id) {
		$states ['state_deleted'] = 'si';
		$this->db->where ( 'state_id', $state_id );
		$this->db->trans_begin ();
		$this->db->update ( self::TABLE_NAME, $states );
		$logs = new Logs ( );
		$mywhere = 'where state_id = ' . $state_id;
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
	 * @param string $state_name
	 * @return states
	 */
	public function getByName($state_name) {
		$result = $this->db->get_where ( self::TABLE_NAME, array ('state_name' => $state_name ) );
		return $result->result_array ();
	
	}
	
	public function get() {
		$result = $this->db->get ( self::TABLE_NAME );
		return $result->result_array;
	} // de la funcion
	

	public function getById($state_id) {
		$this->db->from ( self::TABLE_NAME );
		$this->db->where ( 'state_id', $state_id );
		$result = $this->db->get ();
		$value = array();
		if ($result->result () != null) {
			foreach ( $result->result () as $row ) {
				$deleted = ucfirst($row->state_deleted);
				$value [] = array ('state_id' => $row->state_id, 
									'state_name' => $row->state_name,
									'state_deleted' => $deleted );
			}
		
		}
		return $value;
	
	} // de la funcion
	
	public function getNameById($state_id) {
		$this->db->select ( 'state_name' );
		$this->db->from ( self::TABLE_NAME );
		$this->db->where ( 'state_id', $state_id );
		$result = $this->db->get ();
		foreach ( $result->result () as $row ) {
			$name = $row->state_name;
		}
		return $name;
	}


}
?>
