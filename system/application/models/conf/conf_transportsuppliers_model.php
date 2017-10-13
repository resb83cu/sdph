<?php

class Conf_transportsuppliers_model extends Model {
	const TABLE_NAME = 'conf_transportsuppliers';
	
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
		$this->db->where ( 'supplier_deleted', 'no' );
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
				$deleted = ucfirst($row->supplier_deleted);
				$value [] = array ('supplier_id' => $row->supplier_id, 
									'supplier_name' => $row->supplier_name,
									'supplier_deleted' => $deleted );
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
	 * Esta es la funcion encargada de insertar los transportes
	 *
	 * @return boolean
	 */
	public function insert() {
		$supplier_id = $this->input->post ( 'supplier_id' );
		$suppliers ['supplier_name'] = $this->input->post ( 'supplier_name' );
		if (empty ( $supplier_id )) {
			$this->db->trans_begin ();
			$re = $this->db->insert ( self::TABLE_NAME, $suppliers );
		    $logs = new Logs ( );
			$myquery = $logs->sqlinsert ( self::TABLE_NAME, $suppliers );
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
			$suppliers ['supplier_deleted'] = strtolower($this->input->post ( 'supplier_deleted' ));
			$this->db->where ( 'supplier_id', $supplier_id );
			$this->db->trans_begin ();
			$re = $this->db->update ( self::TABLE_NAME, $suppliers );
		    $logs = new Logs ( );
			$mywhere = 'where supplier_id = '.$supplier_id;
			$myquery = $logs->sqlupdate(self::TABLE_NAME, $suppliers, $mywhere);
			
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
	 * @param string $requestsupplier_name
	 */
	public function delete($supplier_id) {
		$suppliers ['supplier_deleted'] = 'si';
		$this->db->where ( 'supplier_id', $supplier_id );
		$this->db->trans_begin ();
		$re = $this->db->update ( self::TABLE_NAME, $suppliers );
	    $logs = new Logs ( );
		$mywhere = 'where supplier_id = ' . $supplier_id;
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
	 * @param string $supplier_name
	 * @return suppliers
	 */
	public function getByName($supplier_name) {
		$result = $this->db->get_where ( self::TABLE_NAME, array ('supplier_name' => $supplier_name ) );
		return $result->result_array ();
	
	}
	
	public function get() {
		$result = $this->db->get ( self::TABLE_NAME );
		return $result->result_array;
	} // de la funcion
	

	public function getById($supplier_id) {
		$this->db->from ( self::TABLE_NAME );
		$this->db->where ( 'supplier_id', $supplier_id );
		$result = $this->db->get ();
		$value = array();
		if ($result->result () != null) {
			foreach ( $result->result () as $row ) {
				$deleted = ucfirst($row->supplier_deleted);
				$value [] = array ('supplier_id' => $row->supplier_id, 
									'supplier_name' => $row->supplier_name,
									'supplier_deleted' => $deleted );
			}
		
		}
		return $value;
	
	}
	
	public function getNameById($supplier_id) {
		$this->db->select ( 'supplier_name' );
		$this->db->from ( self::TABLE_NAME );
		$this->db->where ( 'supplier_id', $supplier_id );
		$result = $this->db->get ();
		foreach ( $result->result () as $row ) {
			$supplier_name = $row->supplier_name;
		}
		return $supplier_name;

	}


}
?>
