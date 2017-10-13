<?php

class Conf_municipalities_model extends Model {
	const TABLE_NAME = 'conf_municipalities';
	
	function __construct() {
		parent::Model ();
	}
	
	/**
	 * Funcion que devuelve los valores de la tabla Conf_municipalities 
	 *
	 * @param int $hasta
	 * @param int $desde
	 * @return array
	 */
	public function getDatos($hasta, $desde, $idProvince) {
		$this->db->select ( 'conf_municipalities.municipality_id, conf_municipalities.municipality_name, conf_municipalities.province_id, conf_provinces.province_name' );
		$this->db->from ( self::TABLE_NAME );
		$this->load->model ( 'conf/conf_provinces_model' );
		$this->db->join ( Conf_provinces_model::TABLE_NAME, Conf_provinces_model::TABLE_NAME . '.province_id = ' . self::TABLE_NAME . '.province_id' );
		if (! is_null ( $idProvince ) && is_numeric ( $idProvince )) {
			$this->db->where ( self::TABLE_NAME . '.province_id', $idProvince );
		}
		$this->db->limit ( $hasta, $desde );
		
		$result = $this->db->get (); // 
		return $result->result_array ();
	} // de la funcion
	

	/**
	 * Funcion que devuelve la cantidad de registros en la tabla conf_municipalities
	 *
	 */
	public function getCant() {
		return $this->db->count_all ( self::TABLE_NAME );
		//return $resul->result_array ();
	}
	
	/**
	 * Esta es la funcion encargada de insertar los municipios
	 *
	 * @return boolean
	 */
	public function insertMunicipality() {
		$municipality->municipality_name = $this->input->post ( 'municipality_name' );
		$municipality->province_id = $this->input->post ( 'province_id' );
		$this->db->trans_begin ();
		$re = $this->db->insert ( self::TABLE_NAME, $municipality );
		$logs = new Logs ( );
			$myquery = $logs->sqlinsert ( self::TABLE_NAME, $municipality );
			$logs->write ( self::TABLE_NAME, 'INSERT', $myquery );
			
			if ($this->db->trans_status () === FALSE) {
				$this->db->trans_rollback ();
			} else {
				$this->db->trans_commit ();
			}			
		if ($re == true)
			return "true"; else
			return "false";
	}
	
	/**
	 * Funcion para eliminar un municipio por su nombre
	 *
	 * @param string $municipality_name
	 */
	public function deleteMunicipality($municipality_id) {
		$this->db->where ( 'municipality_id', $municipality_id );
		$this->db->trans_begin ();
		$this->db->delete ( self::TABLE_NAME );
		$logs = new Logs ( );
		$mywhere = 'where municipality_id = ' . $municipality_id;
		$myquery = $logs->sqldelete ( self::TABLE_NAME, $mywhere );
		$logs->write ( self::TABLE_NAME, 'DELETE', $myquery );
		
		if ($this->db->trans_status () === FALSE) {
			$this->db->trans_rollback ();
		} else {
			$this->db->trans_commit ();
		}
	}
	
	/**
	 * Esta funcion devuelve un Municipio dado el nombre del mismo
	 *
	 * @param string $municipality_name
	 * @return municipalities
	 */
	public function getMunicipalityByName($municipality_name) {
		$result = $this->db->get_where ( self::TABLE_NAME, array ('municipality_name' => $municipality_name ) );
		return $result->result_array ();
	
	}

}
?>
