<?php

class Conf_hotellinearities_model extends Model {
	const TABLE_NAME = 'conf_hotellinearities';
	
	function __construct() {
		parent::Model ();
	}
	
	/**
	 * Funcion que devuelve los valores de la tabla Conf_hotellinearities 
	 *
	 * @param int $hasta
	 * @param int $desde
	 * @return array
	 */
	public function getData($hotel_id) {
		$this->load->model ( 'conf/conf_hotels_model' );
		$this->load->model ( 'conf/conf_provinces_model' );
		$this->db->select ( 'conf_hotellinearities.linearity_id, 
							conf_hotellinearities.linearity_name,
							conf_hotellinearities.linearity_deleted, 
							conf_hotels.hotel_name, 
							conf_hotels.hotel_id,
							conf_provinces.province_id,
							conf_provinces.province_name' );
		$this->db->from ( self::TABLE_NAME );
		$this->db->join ( Conf_hotels_model::TABLE_NAME, Conf_hotels_model::TABLE_NAME . '.hotel_id = ' . self::TABLE_NAME . '.hotel_id', 'inner' );
		$this->db->join ( Conf_provinces_model::TABLE_NAME, Conf_provinces_model::TABLE_NAME . '.province_id = ' . Conf_hotels_model::TABLE_NAME . '.province_id', 'inner' );
		if (! empty( $hotel_id )) {
			$this->db->where ( self::TABLE_NAME . '.hotel_id', $hotel_id );
		}		
		$this->db->where ( Conf_hotels_model::TABLE_NAME . '.hotel_deleted', 'no' );
		$this->db->where ( 'linearity_deleted', 'no' );
		$result = $this->db->get ();
		$value = array();
		if ($result->result () != "") {
			foreach ( $result->result () as $row ) {
				$deleted = ucfirst($row->linearity_deleted);
				$value [] = array ('linearity_id' => $row->linearity_id, 
									'linearity_name' => $row->linearity_name,
									'linearity_deleted' => $deleted,
									'hotel_id' => $row->hotel_id, 
									'hotel_name' => $row->hotel_name,
									'province_id' => $row->province_id, 
									'province_name' => $row->province_name);
			}
		
		}
		return $value;
	} // de la funcion
	
	public function getDataGrid($to, $from) {
		$this->load->model ( 'conf/conf_hotels_model' );
		$this->load->model ( 'conf/conf_provinces_model' );
		$this->db->select ( 'conf_hotellinearities.linearity_id, 
							conf_hotellinearities.linearity_name,
							conf_hotellinearities.linearity_deleted, 
							conf_hotels.hotel_name, 
							conf_hotels.hotel_id,
							conf_provinces.province_id,
							conf_provinces.province_name' );
		$this->db->from ( self::TABLE_NAME );
		$this->db->join ( Conf_hotels_model::TABLE_NAME, Conf_hotels_model::TABLE_NAME . '.hotel_id = ' . self::TABLE_NAME . '.hotel_id', 'inner' );
		$this->db->join ( Conf_provinces_model::TABLE_NAME, Conf_provinces_model::TABLE_NAME . '.province_id = ' . Conf_hotels_model::TABLE_NAME . '.province_id', 'inner' );
		$this->db->where ( Conf_hotels_model::TABLE_NAME . '.hotel_deleted', 'no' );
		$this->db->limit ( $to, $from);
		$result = $this->db->get ();
		$value = array();
		if ($result->result () != "") {
			foreach ( $result->result () as $row ) {
				$deleted = ucfirst($row->linearity_deleted);
				$value [] = array ('linearity_id' => $row->linearity_id,
									'linearity_name' => $row->linearity_name,
									'linearity_deleted' => $deleted,
									'hotel_id' => $row->hotel_id,
									'hotel_name' => $row->hotel_name,
									'province_id' => $row->province_id,
									'province_name' => $row->province_name);
			}
		
		}
		return $value;
	} // de la funcion
	

	/**
	 * Funcion que devuelve la cantidad de registros en la tabla conf_municipalities
	 *
	 */
	public function getCant() {
		$this->load->model ( 'conf/conf_hotels_model' );
		$this->load->model ( 'conf/conf_provinces_model' );
		$this->db->select ( 'conf_hotellinearities.linearity_id, 
							conf_hotellinearities.linearity_name,
							conf_hotellinearities.linearity_deleted, 
							conf_hotels.hotel_name, 
							conf_hotels.hotel_id,
							conf_provinces.province_id,
							conf_provinces.province_name' );
		$this->db->from ( self::TABLE_NAME );
		$this->db->join ( Conf_hotels_model::TABLE_NAME, Conf_hotels_model::TABLE_NAME . '.hotel_id = ' . self::TABLE_NAME . '.hotel_id', 'inner' );
		$this->db->join ( Conf_provinces_model::TABLE_NAME, Conf_provinces_model::TABLE_NAME . '.province_id = ' . Conf_hotels_model::TABLE_NAME . '.province_id', 'inner' );
		$this->db->where ( Conf_hotels_model::TABLE_NAME . '.hotel_deleted', 'no' );
		return $this->db->count_all_results();
	}
	
	/**
	 * Esta es la funcion encargada de insertar los municipios
	 *
	 * @return boolean
	 */
	public function insert() {
		$linearity_id = $this->input->post ( 'linearity_id' );
		$linearity['linearity_name'] = $this->input->post ( 'linearity_name' );//$linearity_name;
		$linearity['hotel_id'] = $this->input->post ( 'hotel_id' );//$hotel_id;
		if (!empty($linearity_id )) {
			$this->db->where ( 'linearity_id', $linearity_id );
			$this->db->trans_begin ();
			$re = $this->db->update ( self::TABLE_NAME, $linearity );
			$logs = new Logs ( );
			
			$mywhere = 'where linearity_id = '.$linearity_id;
			$myquery = $logs->sqlupdate(self::TABLE_NAME, $linearity, $mywhere);
			
			$logs->write( self::TABLE_NAME, 'UPDATE', $myquery);
			
			if ($this->db->trans_status () === FALSE) {
				$this->db->trans_rollback ();
			} else {
				$this->db->trans_commit ();
			}
			
			if ($re == true)
				return "true"; else
				return "false";
		
		} else {
			$linearity ['linearity_deleted'] = strtolower($this->input->post ( 'linearity_deleted' ));
			$this->db->trans_begin ();
			$re = $this->db->insert ( self::TABLE_NAME, $linearity );
			$logs = new Logs ( );
			$myquery = $logs->sqlinsert ( self::TABLE_NAME, $linearity );
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
	}
	
	/*public function update($linearity_id, $linearity_name, $hotel_id) {
		$linearity->linearity_name = $linearity_name;
		$linearity->hotel_id = $hotel_id;
		$this->db->where ( 'linearity_id', $linearity_id );
		$re = $this->db->update ( self::TABLE_NAME, $linearity );
		if ($re == true)
			return "true"; else
			return "false";
	}*/
	/**
	 * Funcion para eliminar un hotel por su nombre
	 *
	 * @param string $hotel_id
	 */
	public function delete($id) {
		$linearity ['linearity_deleted'] = 'si';
		$this->db->where ( 'linearity_id', $id );
		$this->db->trans_begin ();
		$re = $this->db->update ( self::TABLE_NAME, $linearity );
		$logs = new Logs ( );
		$mywhere = 'where linearity_id = ' . $id;
		$myquery = $logs->sqldelete ( self::TABLE_NAME, $mywhere );
		$logs->write ( self::TABLE_NAME, 'DELETE', $myquery );
		
		if ($this->db->trans_status () === FALSE) {
			$this->db->trans_rollback ();
		} else {
			$this->db->trans_commit ();
		}
		
	}
	
	public function getCountById($linearity_id) {
		$this->db->select ( 'linearity_id' );
		$this->db->from ( self::TABLE_NAME );
		$this->db->where ( 'linearity_id', $linearity_id );
		return $this->db->count_all_results (); //$this->db->get ();
	

	}
	
	public function getById($linearity_id) {
		$this->load->model ( 'conf/conf_hotels_model' );
		$this->load->model ( 'conf/conf_provinces_model' );
		$this->db->select ( 'conf_hotellinearities.linearity_id, 
							conf_hotellinearities.linearity_name,
							conf_hotellinearities.linearity_deleted,
							conf_hotels.hotel_id,
							conf_hotels.province_id' );
		$this->db->from ( self::TABLE_NAME );
		$this->db->join ( Conf_hotels_model::TABLE_NAME, Conf_hotels_model::TABLE_NAME . '.hotel_id = ' . self::TABLE_NAME . '.hotel_id', 'inner' );
		$this->db->where ( self::TABLE_NAME . '.linearity_id', $linearity_id );
		$result = $this->db->get ();
		$value = array();
		if ($result->result () != "") {
			foreach ( $result->result () as $row ) {
				$deleted = ucfirst($row->linearity_deleted);
				$value [] = array ('linearity_id' => $row->linearity_id, 
									'linearity_name' => $row->linearity_name,
									'linearity_deleted' => $deleted,
									'hotel_id' => $row->hotel_id,
									'province_id' => $row->province_id);
			}
		
		}
		return $value;	
	}
	
	public function getLinearitiesById($hotel_id) {
		$this->db->select ( 'linearity_name' );
		$this->db->from ( self::TABLE_NAME );
		$this->db->where ( 'hotel_id', $hotel_id );
		$result = $this->db->get ();
		$value = array();
		if ($result->result () != "") {
			foreach ( $result->result () as $row ) {
				$value [] = array ('linearity_name' => $row->linearity_name);
			}
		
		}
		return $value;
	}

}
?>
