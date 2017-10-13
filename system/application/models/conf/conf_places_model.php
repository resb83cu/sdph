<?php

class Conf_places_model extends Model {
	const TABLE_NAME = 'conf_places';
	/**
	 * Campos de la tabla que se llenan al construir el objeto
	 *
	 * @var array
	 */
	public $fields = array ( );
	
	function __construct() {
		parent::Model ();
		/**
		 * Obteniendo los campos de la tabla
		 */
		$fields = $this->db->field_data ( self::TABLE_NAME );
		foreach ( $fields as $field ) {
			$this->fields [] = $field->name;
		}
	}
	
	/**
	 * Funcion que devuelve los valores de la tabla con las tablas relacionadas
	 *
	 * @param integer $from :hasta
	 * @param integer $to :desde
	 * @return array
	 */
	public function getData($to, $from) {
		$result = $this->db->query ( "SELECT
										p.province_id,
										p.province_name,
										m.municipality_id,
										m.municipality_name,
										pl.place_id,
										pl.place_name
									FROM conf_places pl
									INNER JOIN conf_municipalities m ON pl.municipality_id = m.municipality_id
									INNER JOIN conf_provinces p ON m.province_id = p.province_id" );
		
		$this->db->limit ( $from, $to );
		return $result->result_array ();
	} // de la funcion
	

	public function getDataByProvincesId($to, $from, $province_id) {
		$result = $this->db->query ( "SELECT
										p.province_id,
										p.province_name,
										m.municipality_id,
										m.municipality_name,
										pl.place_id,
										pl.place_name
									FROM conf_places pl
									INNER JOIN conf_municipalities m ON pl.municipality_id = m.municipality_id
									INNER JOIN conf_provinces p ON m.province_id = p.province_id
									WHERE p.province_id =" . $province_id . "" );
		
		$this->db->limit ( $from, $to );
		return $result->result_array ();
	} // de la funcion
	/**
	 * Funcion que devuelve la cantidad de registros en la tabla
	 *
	 */
	public function getCount() {
		return $this->db->count_all ( self::TABLE_NAME );
	}
	
	/**
	 * Esta es la funcion encargada de insertar los registros
	 *
	 * @return boolean
	 */
	public function insert($place_name, $municipality_id) {
		$place->place_name = $place_name;
		$place->municipality_id = $municipality_id;
		$this->db->trans_begin ();
		return $this->db->insert ( self::TABLE_NAME, $place );
		$logs = new Logs ( );
			$myquery = $logs->sqlinsert ( self::TABLE_NAME, $place );
			$logs->write ( self::TABLE_NAME, 'INSERT', $myquery );
			
			if ($this->db->trans_status () === FALSE) {
				$this->db->trans_rollback ();
			} else {
				$this->db->trans_commit ();
			}			
	}
	
	/**
	 * Funcion para eliminar un registro por su id
	 *
	 * @param integer $id
	 */
	public function delete($place_id) {
		$this->db->where ( 'place_id', $place_id );
		$this->db->trans_begin ();
		$this->db->delete ( self::TABLE_NAME );
		$logs = new Logs ( );
		$mywhere = 'where place_id = ' . $place_id;
		$myquery = $logs->sqldelete ( self::TABLE_NAME, $mywhere );
		$logs->write ( self::TABLE_NAME, 'DELETE', $myquery );
		
		if ($this->db->trans_status () === FALSE) {
			$this->db->trans_rollback ();
		} else {
			$this->db->trans_commit ();
		}
		
	}
	
	/**
	 * Funcion para modificar un registro
	 *
	 * @param $id :valor de la llave del registro a modificar
	 * @param  :valores de los campos a modificar
	 */
	public function update($places_id, $places_name, $municipalities_id) { /* Cambiar*/
		//Poner aqui el campo igual que en la bd
		/* Cambiar */
		$whereArgNum = 1; //este es la cantidad de argumentos(parametros) que se usaran en el where del update a partir del primero
		/** Fin Cambiar **/
		$Arr = get_defined_vars (); //Obtener un arreglo clave valor de los parametros
		$Arr = $this->_checkFieldValueArr ( $Arr ); //Chequear que el arreglo es correcto
		

		$ArgsNames = array_keys ( $Arr );
		$whereArr = array ( );
		for($i = 0; $i < $whereArgNum; $i ++) {
			$whereArr [$ArgsNames [$i]] = $Arr [$ArgsNames [$i]];
		}
		
		if (count ( $whereArr ) <= 0) {
			$whereArr = null;
		}
		
		return $this->db->update ( self::TABLE_NAME, $Arr, $whereArr );
	}
	
	/* Esta funcion devuelve un registro dado un arreglo de campo valor
	 *
	 * @param array $fieldValue :arreglo de campo valor
	 * @return array
	 */
	public function get($fieldValueArr = null) {
		$w = $this->_checkFieldValueArr ( $fieldValueArr );
		$result = $this->db->get_where ( self::TABLE_NAME, $w );
		return $result->result_array ();
	}
	
	/**
	 * Chequea si el arreglo de clave valor que se le pasa por parametro es valido
	 * 
	 * @param array $fieldValue :arreglo de campo valor
	 * @return array :arreglo de clave valor chequeado
	 */
	private function _checkFieldValueArr($fieldValueArr) {
		if (! is_null ( $fieldValueArr ) && is_array ( $fieldValueArr ) && count ( $fieldValueArr ) > 0) {
			$w = array ( );
			/**
			 * Chequeamos si el arreglo contiene todas las claves
			 */
			foreach ( $fieldValueArr as $fieldKey => $fieldValue ) {
				if (in_array ( $fieldKey, $this->fields )) {
					$w [$fieldKey] = $fieldValue;
				}
			}
		
		} else {
			$w = null;
		}
		return $w;
	}

}
?>
