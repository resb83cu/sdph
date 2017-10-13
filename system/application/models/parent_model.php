<?php

class Parent_model extends Model {
	/**
	 * Nombre de la tabla
	 *
	 * @var string
	 */
	private $tableName;
	
	/**
	 * Enter description here...
	 *
	 * @var unknown_type
	 */
	private $countReg = 30;
	
	function __construct($tableName = null) {
		parent::Model ();
		$this->tableName = $tableName;
	}
	/**
	 * @return string
	 */
	public function getTableName() {
		return $this->tableName;
	}
	
	/**
	 * @param string $tableName
	 */
	public function setTableName($tableName) {
		$this->tableName = $tableName;
	}
	
	/**
	 * Funcion que devuelve los valores de la tabla 
	 *
	 * @param int $hasta
	 * @param int $desde
	 * @return array
	 */
	public function getData($from, $to) {
		$this->db->from ( $this->tableName );
		$this->db->limit ( $to, $from );
		$result = $this->db->get ();
		return $result->result_array ();
	} // de la funcion
	

	/**
	 * Enter description here...
	 *
	 * @param table $table
	 * @return int
	 */
	public function getCount() {
		return $this->db->count_all ( $this->tableName );
		//return $resul->result_array ();
	}
	
	/**
	 * Esta es la funcion encargada de insertar
	 *
	 * @param array $data arreglo de pareja campo=>valor
	 * @return boolean
	 */
	public function insert($data) {
		$re=false;
		if (is_array ( $data ) && count ( $data ) > 0) {
		$this->db->trans_begin ();
		$re =  $this->db->insert ( $this->tableName, $data );
		$logs = new Logs ( );
			$myquery = $logs->sqlinsert ( self::TABLE_NAME, $data );
			$logs->write ( self::TABLE_NAME, 'INSERT', $myquery );
			
			if ($this->db->trans_status () === FALSE) {
				$this->db->trans_rollback ();
			} else {
				$this->db->trans_commit ();
			}			
		}
		return  $re;
	}
	
	/**
	 * Funcion para eliminar por su nombre
	 *
	 * @param array $data arreglo de pareja campo=>valor
	 * @param string $municipalities_name
	 */
	public function delete($data) {
		if (is_array ( $data ) && count ( $data ) > 0) {
		$this->db->trans_begin ();
			$result = $this->db->delete ( $this->tableName, $data );
	    $logs = new Logs ( );
		
		
		
		
		//$mywhere = 'where motive_id = ' . $motive_id;
		$myquery = $logs->sqldelete ( self::TABLE_NAME, $mywhere );
		$logs->write ( self::TABLE_NAME, 'DELETE', $myquery );
		
		if ($this->db->trans_status () === FALSE) {
			$this->db->trans_rollback ();
		} else {
			$this->db->trans_commit ();
		}
			return $result;
		}
		return false;
	}
	
	public function update($data) {
	 $re=false;
		if (is_array ( $data ) && count ( $data ) > 0) {
		$this->db->trans_begin ();
			$re= $this->db->update ( $this->tableName, $data );
			$logs = new Logs ( );
			
			
			
			
			//$mywhere = 'where motive_id = '.$motive_id;
			$myquery = $logs->sqlupdate(self::TABLE_NAME, $data, $mywhere);
			
			$logs->write( self::TABLE_NAME, 'UPDATE', $myquery);
			
			if ($this->db->trans_status () === FALSE) {
				$this->db->trans_rollback ();
			} else {
				$this->db->trans_commit ();
			}
		}
		return $re;
	}
}
?>