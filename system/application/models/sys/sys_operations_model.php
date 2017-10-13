<?php

class Sys_operations_model extends Parent_model {
	
	const TABLE_NAME = 'sys_operations';
	function __construct() {
		parent::__construct ( self::TABLE_NAME );
	}
	
	/**
	 * Funcion que devuelve los valores de la tabla Conf_provinces 
	 *
	 * @param int $hasta
	 * @param int $desde
	 * @return array
	 */
	public function getDatos($to, $from) {
		$result = $this->getData ( $from, $to );
		return $result;
	} // de la funcion
	

	/**
	 * funcion que devuelve la cantidad de registros en la tabla ejemplo
	 *
	 */
	public function getCant() {
		return $this->getCount ();
	}
	
	/**
	 * Esta es la funcion encargada de insertar las provincias
	 *
	 * @return boolean
	 */
	public function insertOperation($operations) {
		/*
		$data ['operations_name'] = $this->input->post ( 'operations_name' );
		$data ['operations_description'] = $this->input->post ( 'operations_description' );
		$data ['operations_name'] = $this->input->post ( 'operations_name' );
		$data ['operations_description'] = $this->input->post ( 'operations_description' );
		$data ['operations_name'] = $this->input->post ( 'operations_name' );
		$data ['operations_description'] = $this->input->post ( 'operations_description' );
		*/
		$re = $this->insert ( $operations );
		if ($re == true)
			return "true"; else
			return "false";
	}
	
	/**
	 * Funcion para eliminar una provincia por su nombre
	 *
	 * @param string $provinces_name
	 */
	public function deleteOperation($operations_id) {
		$this->db->where ( 'operations_id', $operations_id );
		$this->db->delete ( self::TABLE_NAME );
	}
	
	/**
	 * Esta funcion devuelve una Provincia dado el nombre de la misma
	 *
	 * @param string $provinces_name
	 * @return Provinces
	 */
	public function getOperationByName($operations_name) {
		$result = $this->db->get_where ( self::TABLE_NAME, array ('operations_name' => $operations_name ) );
		return $result->result_array ();
	
	}
	
	public function getOperations() {
		$result = $this->db->get ( self::TABLE_NAME );
		return $result->result_array;
	} // de la funcion


}
?>
