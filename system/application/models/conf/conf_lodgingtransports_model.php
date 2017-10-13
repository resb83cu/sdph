<?php

class Conf_lodgingtransports_model extends Model {
	const TABLE_NAME = 'conf_lodgingtransports';
	
	function __construct() {
		parent::__construct ();
	}
	
	/**
	 * Funcion que devuelve los valores de la tabla 
	 *
	 * @param int $hasta
	 * @param int $desde
	 * @return array
	 */
	public function getData($to, $from) {
		$this->db->from ( self::TABLE_NAME );
		$this->db->limit ( $to, $from );
		$result = $this->db->get (); // 
		return $result->result_array ();
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
		$transport_id = $this->input->post ( 'transport_id' );
		$transports ['transport_name'] = $this->input->post ( 'transport_name' );
		if (empty ( $transport_id )) {
			$this->db->trans_begin ();
			$re = $this->db->insert ( self::TABLE_NAME, $transports );
			$logs = new Logs ( );
			$myquery = $logs->sqlinsert ( self::TABLE_NAME, $transports );
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
			$this->db->where ( 'transport_id', $transport_id );
			$this->db->trans_begin ();
			$re = $this->db->update ( self::TABLE_NAME, $transports );
			$logs = new Logs ( );
			
			$mywhere = 'where transport_id = '.$transport_id;
			$myquery = $logs->sqlupdate(self::TABLE_NAME, $transports, $mywhere);
			
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
	 * @param string $transport_name
	 */
	public function delete($transport_id) {
		$this->db->where ( 'transport_id', $transport_id );
		$this->db->trans_begin ();
		$this->db->delete ( self::TABLE_NAME );
		$logs = new Logs ( );
		$mywhere = 'where transport_id = ' . $transport_id;
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
	 * @param string $transport_name
	 * @return transports
	 */
	public function getByName($transport_name) {
		$result = $this->db->get_where ( self::TABLE_NAME, array ('transport_name' => $transport_name ) );
		return $result->result_array ();
	
	}
	
	public function get() {
		$result = $this->db->get ( self::TABLE_NAME );
		return $result->result_array;
	} // de la funcion
	

	public function getById($transport_id) {
		$this->db->from ( self::TABLE_NAME );
		$this->db->where ( 'transport_id', $transport_id );
		$result = $this->db->get ();
		return $result->result_array ();
	
	} // de la funcion

	public function getNameById($transport_id) {
		$this->db->select ( 'transport_name' );
		$this->db->from ( self::TABLE_NAME );
		$this->db->where ( 'transport_id', $transport_id );
		$result = $this->db->get ();
		foreach ( $result->result () as $row ) {
			$transport_name = $row->transport_name;
		}
		return $transport_name;
	
	}


}
?>
