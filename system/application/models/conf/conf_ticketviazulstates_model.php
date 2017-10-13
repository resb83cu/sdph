<?php

class Conf_ticketviazulstates_model extends Model {
	const TABLE_NAME = 'conf_ticketviazulstates';
	
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
		$this->db->where ( 'viazulstate_deleted', 'no' );
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
				$deleted = ucfirst($row->viazulstate_deleted);
				$value [] = array ('viazulstate_id' => $row->viazulstate_id, 
									'viazulstate_name' => $row->viazulstate_name,
									'viazulstate_deleted' => $deleted );
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
		$viazulstate_id = $this->input->post ( 'viazulstate_id' );
		$viazulstates ['viazulstate_name'] = $this->input->post ( 'viazulstate_name' );
		if (empty ( $viazulstate_id )) {
			$this->db->trans_begin ();
			$re = $this->db->insert ( self::TABLE_NAME, $viazulstates );
			$logs = new Logs ( );
			$myquery = $logs->sqlinsert ( self::TABLE_NAME, $viazulstates );
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
			$viazulstates ['viazulstate_deleted'] = strtolower($this->input->post ( 'viazulstate_deleted' ));
			$this->db->where ( 'viazulstate_id', $viazulstate_id );
			$this->db->trans_begin ();
			$re = $this->db->update ( self::TABLE_NAME, $viazulstates );
			$logs = new Logs ( );
			
			$mywhere = 'where viazulstate_id = '.$viazulstate_id;
			$myquery = $logs->sqlupdate(self::TABLE_NAME, $viazulstates, $mywhere);
			
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
	 * @param string $viazulstate_name
	 */
	public function delete($viazulstate_id) {
		$viazulstates ['viazulstate_deleted'] = 'si';
		$this->db->where ( 'viazulstate_id', $viazulstate_id );
		$this->db->trans_begin ();
		$this->db->update ( self::TABLE_NAME, $viazulstates);
		$logs = new Logs ( );
		$mywhere = 'where viazulstate_id = ' . $viazulstate_id;
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
	 * @param string $viazulstate_name
	 * @return viazulstates
	 */
	public function getByName($viazulstate_name) {
		$result = $this->db->get_where ( self::TABLE_NAME, array ('viazulstate_name' => $viazulstate_name ) );
		return $result->result_array ();
	
	}
	
	public function get() {
		$result = $this->db->get ( self::TABLE_NAME );
		return $result->result_array;
	} // de la funcion
	

	public function getById($viazulstate_id) {
		$this->db->from ( self::TABLE_NAME );
		$this->db->where ( 'viazulstate_id', $viazulstate_id );
		$result = $this->db->get ();
		$value = array();
		if ($result->result () != null) {
			foreach ( $result->result () as $row ) {
				$deleted = ucfirst($row->viazulstate_deleted);
				$value [] = array ('viazulstate_id' => $row->viazulstate_id, 
									'viazulstate_name' => $row->viazulstate_name,
									'viazulstate_deleted' => $deleted );
			}
		
		}
		return $value;
	
	}// de la funcion
	
	public function getNameById($viazulstate_id) {
		$this->db->select ( 'viazulstate_name' );
		$this->db->from ( self::TABLE_NAME );
		$this->db->where ( 'viazulstate_id', $viazulstate_id );
		$result = $this->db->get ();
		$name = '';
		foreach ( $result->result () as $row ) {
			$name = $row->viazulstate_name;
		}
		return $name;
	}

}
?>
