<?php

class Conf_cafeterias_model extends Model {
	const TABLE_NAME = 'conf_cafeterias';
	
	function __construct() {
		parent::Model ();
	
	}
	
	/**
	 * Funcion que devuelve los valores de la tabla 
	 *
	 * @param int $hasta
	 * @param int $desde
	 * @return array
	 */
	public function getData($province_id) {
		$this->load->model ( 'conf/conf_provinces_model' );
		$this->load->model ( 'conf/conf_hotelchains_model' );
		$this->db->select ( 'conf_cafeterias.cafeteria_id, 
							conf_cafeterias.cafeteria_name,
							conf_provinces.province_id, 
							conf_provinces.province_name, 
							conf_hotelchains.chain_id, 
							conf_hotelchains.chain_name,
							conf_cafeterias.cafeteria_deleted' );
		$this->db->from ( self::TABLE_NAME );
		$this->db->join ( Conf_provinces_model::TABLE_NAME, Conf_provinces_model::TABLE_NAME . '.province_id = ' . self::TABLE_NAME . '.province_id', 'inner' );
		$this->db->join ( Conf_hotelchains_model::TABLE_NAME, Conf_hotelchains_model::TABLE_NAME . '.chain_id = ' . self::TABLE_NAME . '.chain_id', 'inner' );
		if (! is_null ( $province_id ) && is_numeric ( $province_id )) {
			$this->db->where ( self::TABLE_NAME . '.province_id', $province_id );
		}
		$this->db->where ( 'cafeteria_deleted', 'no' );
		$this->db->order_by("conf_provinces.province_id", "asc");
		$result = $this->db->get ();
		$value=array();
		if ($result->result () != null) {
			foreach ( $result->result () as $row ) {
				$deleted = ucfirst($row->cafeteria_deleted);
				$value [] = array ('cafeteria_id' => $row->cafeteria_id, 
									'cafeteria_name' => $row->cafeteria_name, 
									'cafeteria_deleted' => $deleted,
									'province_id' => $row->province_id, 
									'province_name' => $row->province_name, 
									'chain_id' => $row->chain_id, 
									'chain_name' => $row->chain_name);
			}
		
		}
		return $value;
	} // de la funcion
	
	public function getDataGrid($to, $from, $name, $province) {
		$this->load->model ( 'conf/conf_provinces_model' );
		$this->load->model ( 'conf/conf_hotelchains_model' );
		$this->db->select ( 'conf_cafeterias.cafeteria_id, 
							conf_cafeterias.cafeteria_name,
							conf_cafeterias.province_id, 
							conf_provinces.province_name, 
							conf_hotelchains.chain_id, 
							conf_hotelchains.chain_name,
							conf_cafeterias.cafeteria_deleted' );
		$this->db->from ( self::TABLE_NAME );
		$this->db->join ( Conf_provinces_model::TABLE_NAME, Conf_provinces_model::TABLE_NAME . '.province_id = ' . self::TABLE_NAME . '.province_id', 'inner' );
		$this->db->join ( Conf_hotelchains_model::TABLE_NAME, Conf_hotelchains_model::TABLE_NAME . '.chain_id = ' . self::TABLE_NAME . '.chain_id', 'inner' );
		if (! empty( $province )) {
			$this->db->where ( self::TABLE_NAME. '.province_id', $province );
		}		
		if (! empty( $name )) {
			$this->db->like ( 'LOWER('.self::TABLE_NAME.'.cafeteria_name)' , strtolower($name) );
		}
		$this->db->order_by("conf_cafeterias.province_id", "asc"); 
		$this->db->limit ( $to, $from );
		$result = $this->db->get ();
		$value=array();
		if ($result->result () != null) {
			foreach ( $result->result () as $row ) {
				$deleted = ucfirst($row->cafeteria_deleted);
				$value [] = array ('cafeteria_id' => $row->cafeteria_id, 
									'cafeteria_name' => $row->cafeteria_name,
									'cafeteria_deleted' => $deleted,
									'province_id' => $row->province_id, 
									'province_name' => $row->province_name, 
									'chain_id' => $row->chain_id, 
									'chain_name' => $row->chain_name );
			}
		
		}
		return $value;
	} // de la funcion
	
	public function getDataByProvince($province_id) {
		$this->load->model ( 'conf/conf_provinces_model' );
		$this->load->model ( 'conf/conf_hotelchains_model' );
		$this->db->select ( 'conf_cafeterias.cafeteria_id, 
							conf_cafeterias.cafeteria_name, 
							conf_provinces.province_id, 
							conf_provinces.province_name, 
							conf_hotelchains.chain_id, 
							conf_hotelchains.chain_name' );
		$this->db->from ( self::TABLE_NAME );
		$this->db->join ( Conf_provinces_model::TABLE_NAME, Conf_provinces_model::TABLE_NAME . '.province_id = ' . self::TABLE_NAME . '.province_id', 'inner' );
		$this->db->join ( Conf_hotelchains_model::TABLE_NAME, Conf_hotelchains_model::TABLE_NAME . '.chain_id = ' . self::TABLE_NAME . '.chain_id', 'inner' );
		$this->db->where ( self::TABLE_NAME . '.province_id', $province_id );
		$this->db->where ( 'cafeteria_deleted', 'no' );
		$this->db->order_by("conf_provinces.province_id", "asc");
		$this->db->distinct();
		$result = $this->db->get ();
		$value=array();
		if ($result->result () != null) {
			foreach ( $result->result () as $row ) {
				$value [] = array ('cafeteria_id' => $row->cafeteria_id, 
									'cafeteria_name' => $row->cafeteria_name, 
									'province_id' => $row->province_id, 
									'province_name' => $row->province_name, 
									'chain_id' => $row->chain_id, 
									'chain_name' => $row->chain_name);
			}
		
		}
		return $value;
	} // de la funcion
	

	/**
	 * Funcion que devuelve la cantidad de registros en la tabla
	 *
	 */
	public function getCant($name, $province) {
		$this->load->model ( 'conf/conf_provinces_model' );
		$this->load->model ( 'conf/conf_hotelchains_model' );
		$this->load->model ( 'conf/conf_hotelchains_model' );
		$this->db->select ( 'conf_cafeterias.cafeteria_id, 
							conf_cafeterias.cafeteria_name, 
							conf_provinces.province_id, 
							conf_provinces.province_name, 
							conf_hotelchains.chain_id, 
							conf_hotelchains.chain_name,
							conf_cafeterias.cafeteria_deleted' );
		$this->db->from ( self::TABLE_NAME );
		$this->db->join ( Conf_provinces_model::TABLE_NAME, Conf_provinces_model::TABLE_NAME . '.province_id = ' . self::TABLE_NAME . '.province_id', 'inner' );
		$this->db->join ( Conf_hotelchains_model::TABLE_NAME, Conf_hotelchains_model::TABLE_NAME . '.chain_id = ' . self::TABLE_NAME . '.chain_id', 'inner' );
		if (! empty( $province )) {
			$this->db->where ( self::TABLE_NAME. '.province_id', $province );
		}		
		if (! empty( $name )) {
			$this->db->like ( 'LOWER('.self::TABLE_NAME.'.cafeteria_name)' , strtolower($name) );
		}
		return $this->db->count_all_results();
	}
	
	/**
	 * Esta es la funcion encargada de insertar los municipios
	 *
	 * @return boolean
	 */
	public function insert() {
		$cafeteria_id = $this->input->post ( 'cafeteria_id' );
		$cafeteria['cafeteria_name'] = $this->input->post ( 'cafeteria_name' );
		$cafeteria['chain_id'] = $this->input->post ( 'chain_id' );
		$cafeteria['province_id'] = $this->input->post ( 'province_id' );
		if (empty ( $cafeteria_id )) {
			$this->db->trans_begin ();
			$re = $this->db->insert ( self::TABLE_NAME, $cafeteria );
			$cafeteria_id = $this->db->insert_id ();
			$logs = new Logs ( );
			$myquery = $logs->sqlinsert ( self::TABLE_NAME, $cafeteria );
			$logs->write ( self::TABLE_NAME, 'INSERT', $myquery );
			
			if ($this->db->trans_status () === FALSE) {
				$this->db->trans_rollback ();
			} else {
				$this->db->trans_commit ();
			}
			if ($re == true) {
				return "true";
			} else
				return "false";			
		
		} else {
			$cafeteria['cafeteria_deleted'] = strtolower($this->input->post ( 'cafeteria_deleted' ));
			$this->db->where ( 'cafeteria_id', $cafeteria_id );
			$this->db->trans_begin ();
			$re = $this->db->update ( self::TABLE_NAME, $cafeteria );
			$logs = new Logs ( );
			
			$mywhere = 'where cafeteria_id = '.$cafeteria_id;
			$myquery = $logs->sqlupdate(self::TABLE_NAME, $cafeteria, $mywhere);
			
			$logs->write( self::TABLE_NAME, 'UPDATE', $myquery);
			
			if ($this->db->trans_status () === FALSE) {
				$this->db->trans_rollback ();
			} else {
				$this->db->trans_commit ();
			}
			
			if ($re == true) {
				return "true";
			} else
				return "false";			
		}
	
	}
	
	/**
	 * Funcion para eliminar un cafeteria por su nombre
	 *
	 * @param string $cafeteria_id
	 */
	public function delete($id) {
		$cafeteria['cafeteria_deleted'] = 'si';
		$this->db->where ( 'cafeteria_id', $id );
		$this->db->trans_begin ();
		$re = $this->db->update ( self::TABLE_NAME, $cafeteria );
		//$this->db->delete ( self::TABLE_NAME );
		$logs = new Logs ( );
		$mywhere = 'where cafeteria_id = ' . $id;
		$myquery = $logs->sqldelete ( self::TABLE_NAME, $mywhere );
		$logs->write ( self::TABLE_NAME, 'DELETE', $myquery );
		
		if ($this->db->trans_status () === FALSE) {
			$this->db->trans_rollback ();
		} else {
			$this->db->trans_commit ();
		}
		
	}
	
	public function getById($cafeteria_id) {
		$this->load->model ( 'conf/conf_hotelchains_model' );
		$this->load->model ( 'conf/conf_provinces_model' );
		$this->db->select ( 'conf_cafeterias.cafeteria_id, 
							conf_cafeterias.cafeteria_name, 
							conf_cafeterias.province_id, 
							conf_cafeterias.chain_id,
							conf_cafeterias.cafeteria_deleted' );
		$this->db->from ( self::TABLE_NAME );
		$this->db->where ( self::TABLE_NAME . '.cafeteria_id', $cafeteria_id );
		$result = $this->db->get ();
		$value = array();
		if ($result->result () != null) {
			foreach ( $result->result () as $row ) {
				$deleted = ucfirst($row->cafeteria_deleted);
				$value [] = array ('cafeteria_id' => $row->cafeteria_id, 
									'cafeteria_name' => $row->cafeteria_name, 
									'cafeteria_deleted' => $deleted,
									'province_id' => $row->province_id, 
									'chain_id' => $row->chain_id );
			}
		
		}
		return $value;
	
	}
	
	public function getNameById($cafeteria_id) {
		$this->db->select ( 'cafeteria_name' );
		$this->db->from ( self::TABLE_NAME );
		$this->db->where ( 'cafeteria_id', $cafeteria_id );
		$result = $this->db->get ();
		foreach ( $result->result () as $row ) {
			$cafeteria_name = $row->cafeteria_name;
		}
		return $cafeteria_name;
	}
	
	public function getProvinceIdById($cafeteria_id) {
		$this->db->select ( 'province_id' );
		$this->db->from ( self::TABLE_NAME );
		$this->db->where ( 'cafeteria_id', $cafeteria_id );
		$result = $this->db->get ();
		foreach ( $result->result () as $row ) {
			$province_id = $row->province_id;
		}
		return $province_id;
	}	

}
?>
