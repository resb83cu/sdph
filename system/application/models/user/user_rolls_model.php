<?php

class User_rolls_model extends Model {
	const TABLE_NAME = 'user_rolls';
	
	function __construct() {
		parent::__construct ();
	}
	
	/**
	 * Funcion que devuelve los valores de la tabla User_rolls 
	 *
	 * @param int $hasta
	 * @param int $desde
	 * @return array
	 */
	public function getData($to, $from) {
		$this->db->from ( self::TABLE_NAME );
		$this->db->limit ( $to, $from );
		$result = $this->db->get (); 
		return $result->result_array ();
	}
	

	/**
	 * funcion que devuelve la cantidad de registros en la tabla
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
		$roll_id = $this->input->post ( 'roll_id' );
		$rolls ['roll_name'] = $this->input->post ( 'roll_name' );
		$rolls ['roll_description'] = $this->input->post ( 'roll_description' );
		if (empty ( $roll_id )) {
		$this->db->trans_begin ();
			$re = $this->db->insert ( self::TABLE_NAME, $rolls );
			$logs = new Logs ( );
			$myquery = $logs->sqlinsert ( self::TABLE_NAME, $rolls );
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
			$this->db->where ( 'roll_id', $roll_id );
			$this->db->trans_begin ();
			$re = $this->db->update ( self::TABLE_NAME, $rolls );
			$logs = new Logs ( );
			
			$mywhere = 'where roll_id = '.$roll_id;
			$myquery = $logs->sqlupdate(self::TABLE_NAME, $rolls, $mywhere);
			
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
	 * @param string $roll_name
	 */
	public function delete($roll_id) {
		$this->db->where ( 'roll_id', $roll_id );
		$this->db->trans_begin ();
		$this->db->delete ( self::TABLE_NAME );
		$logs = new Logs ( );
		$mywhere = 'where roll_id = ' . $roll_id;
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
	 * @param string $roll_name
	 * @return rolls
	 */
	public function getByName($roll_name) {
		$result = $this->db->get_where ( self::TABLE_NAME, array ('roll_name' => $roll_name ) );
		return $result->result_array ();
	
	}
	
	public function getRolls() {
		$result = $this->db->get ( self::TABLE_NAME );
		return $result->result_array;
	} // de la funcion
	

	public function getById($roll_id) {
		$this->db->from ( self::TABLE_NAME );
		$this->db->where ( 'roll_id', $roll_id );
		$result = $this->db->get ();
		return $result->result_array ();
	
	} // de la funcion
	
	public function getDescriptionById($roll_id) {
		$this->db->select ( 'roll_description' );
		$this->db->from ( self::TABLE_NAME );
		$this->db->where ( 'roll_id', $roll_id );
		$result = $this->db->get ();
		foreach ( $result->result () as $row ) {
			$roll_description = $row->roll_description;
		}
		return $roll_description;
	
	}


}
?>
