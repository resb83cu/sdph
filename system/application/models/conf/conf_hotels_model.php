<?php

class Conf_hotels_model extends Model {
	const TABLE_NAME = 'conf_hotels';
	
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
		$this->load->model ( 'conf/conf_hotellinearities_model' );
		$this->db->select ( 'conf_hotels.hotel_id, 
							conf_hotels.hotel_name, 
							conf_hotels.hotel_price, 
							conf_provinces.province_id, 
							conf_provinces.province_name, 
							conf_hotelchains.chain_id, 
							conf_hotelchains.chain_name, 
							conf_hotellinearities.linearity_id, 
							conf_hotellinearities.linearity_name,
							conf_hotels.hotel_deleted' );
		$this->db->from ( self::TABLE_NAME );
		$this->db->join ( Conf_provinces_model::TABLE_NAME, Conf_provinces_model::TABLE_NAME . '.province_id = ' . self::TABLE_NAME . '.province_id', 'inner' );
		$this->db->join ( Conf_hotelchains_model::TABLE_NAME, Conf_hotelchains_model::TABLE_NAME . '.chain_id = ' . self::TABLE_NAME . '.chain_id', 'inner' );
		$this->db->join ( Conf_hotellinearities_model::TABLE_NAME, Conf_hotellinearities_model::TABLE_NAME . '.hotel_id = ' . self::TABLE_NAME . '.hotel_id', 'left' );
		if (! is_null ( $province_id ) && is_numeric ( $province_id )) {
			$this->db->where ( self::TABLE_NAME . '.province_id', $province_id );
		}
		$this->db->where ( 'hotel_deleted', 'no' );
		$this->db->order_by("conf_provinces.province_id", "asc");
		$result = $this->db->get ();
		$value=array();
		if ($result->result () != null) {
			foreach ( $result->result () as $row ) {
				if ($row->linearity_name != null) {
					$linearity = $row->linearity_name;
				} else {
					$linearity = '---';
				}
				$deleted = ucfirst($row->hotel_deleted);
				$value [] = array ('hotel_id' => $row->hotel_id, 
									'hotel_name' => $row->hotel_name, 
									'hotel_deleted' => $deleted, 
									'hotel_price' => $row->hotel_price, 
									'province_id' => $row->province_id, 
									'province_name' => $row->province_name, 
									'chain_id' => $row->chain_id, 
									'chain_name' => $row->chain_name, 
									'linearity_id' => $row->linearity_id, 
									'linearity_name' => $linearity );
			}
		
		}
		return $value;
	} // de la funcion
	
	public function getDataGrid($to, $from, $name, $province) {
		$this->load->model ( 'conf/conf_provinces_model' );
		$this->load->model ( 'conf/conf_hotelchains_model' );
		$this->load->model ( 'conf/conf_hotellinearities_model' );
		$this->db->select ( 'conf_hotels.hotel_id, 
							conf_hotels.hotel_name, 
							conf_hotels.hotel_price, 
							conf_hotels.province_id, 
							conf_provinces.province_name, 
							conf_hotelchains.chain_id, 
							conf_hotelchains.chain_name, 
							conf_hotellinearities.linearity_id, 
							conf_hotellinearities.linearity_name,
							conf_hotels.hotel_deleted' );
		$this->db->from ( self::TABLE_NAME );
		$this->db->join ( Conf_provinces_model::TABLE_NAME, Conf_provinces_model::TABLE_NAME . '.province_id = ' . self::TABLE_NAME . '.province_id', 'inner' );
		$this->db->join ( Conf_hotelchains_model::TABLE_NAME, Conf_hotelchains_model::TABLE_NAME . '.chain_id = ' . self::TABLE_NAME . '.chain_id', 'inner' );
		$this->db->join ( Conf_hotellinearities_model::TABLE_NAME, Conf_hotellinearities_model::TABLE_NAME . '.hotel_id = ' . self::TABLE_NAME . '.hotel_id', 'left' );
		if (! empty( $province )) {
			$this->db->where ( self::TABLE_NAME. '.province_id', $province );
		}		
		if (! empty( $name )) {
			$this->db->like ( self::TABLE_NAME. '.hotel_name', strtolower($name) );
			$this->db->or_like ( self::TABLE_NAME. '.hotel_name', strtoupper($name) );
			$this->db->or_like ( self::TABLE_NAME. '.hotel_name', ucfirst($name) );
		}
		$this->db->order_by("conf_hotels.province_id", "asc"); 
		$this->db->limit ( $to, $from );
		$result = $this->db->get ();
		$value=array();
		if ($result->result () != null) {
			foreach ( $result->result () as $row ) {
				$linearities = Conf_hotellinearities_model::getLinearitiesById($row->hotel_id);
				$count = count($linearities);
				if ($count > 0) {
					$temp = $linearities[0]['linearity_name'];
					for ($i = 1; $i < $count; $i++){
						$temp = $temp.' , '.$linearities[$i]['linearity_name'];
					}
					$linearity = $temp;
				} else {
					$linearity = '---';
				}
				$deleted = ucfirst($row->hotel_deleted);
				$value [] = array ('hotel_id' => $row->hotel_id, 
									'hotel_name' => $row->hotel_name,
									'hotel_deleted' => $deleted, 
									'hotel_price' => $row->hotel_price, 
									'province_id' => $row->province_id, 
									'province_name' => $row->province_name, 
									'chain_id' => $row->chain_id, 
									'chain_name' => $row->chain_name, 
									'linearity_id' => $row->linearity_id, 
									'linearity_name' => $linearity );
			}
		
		}
		return $value;
	} // de la funcion
	
	public function getDataByProvince($province_id) {
		$this->load->model ( 'conf/conf_provinces_model' );
		$this->load->model ( 'conf/conf_hotelchains_model' );
		$this->load->model ( 'conf/conf_hotellinearities_model' );
		$this->db->select ( 'conf_hotels.hotel_id, 
							conf_hotels.hotel_name, 
							conf_hotels.hotel_price, 
							conf_provinces.province_id, 
							conf_provinces.province_name, 
							conf_hotelchains.chain_id, 
							conf_hotelchains.chain_name' );
		$this->db->from ( self::TABLE_NAME );
		$this->db->join ( Conf_provinces_model::TABLE_NAME, Conf_provinces_model::TABLE_NAME . '.province_id = ' . self::TABLE_NAME . '.province_id', 'inner' );
		$this->db->join ( Conf_hotelchains_model::TABLE_NAME, Conf_hotelchains_model::TABLE_NAME . '.chain_id = ' . self::TABLE_NAME . '.chain_id', 'inner' );
		$this->db->where ( self::TABLE_NAME . '.province_id', $province_id );
		$this->db->where ( 'hotel_deleted', 'no' );
		$this->db->order_by("conf_provinces.province_id", "asc");
		$this->db->distinct();
		$result = $this->db->get ();
		$value=array();
		if ($result->result () != null) {
			foreach ( $result->result () as $row ) {
				$value [] = array ('hotel_id' => $row->hotel_id, 
									'hotel_name' => $row->hotel_name, 
									'hotel_price' => $row->hotel_price, 
									'province_id' => $row->province_id, 
									'province_name' => $row->province_name, 
									'chain_id' => $row->chain_id, 
									'chain_name' => $row->chain_name);
			}
		
		}
		return $value;
	} // de la funcion
	

	/**
	 * Funcion que devuelve la cantidad de registros en la tabla conf_municipalities
	 *
	 */
	public function getCant($to, $from, $name, $province) {
		$this->load->model ( 'conf/conf_provinces_model' );
		$this->load->model ( 'conf/conf_hotelchains_model' );
		$this->load->model ( 'conf/conf_hotellinearities_model' );
		$this->db->select ( 'conf_hotels.hotel_id, 
							conf_hotels.hotel_name, 
							conf_hotels.hotel_price, 
							conf_provinces.province_id, 
							conf_provinces.province_name, 
							conf_hotelchains.chain_id, 
							conf_hotelchains.chain_name, 
							conf_hotellinearities.linearity_id, 
							conf_hotellinearities.linearity_name,
							conf_hotels.hotel_deleted' );
		$this->db->from ( self::TABLE_NAME );
		$this->db->join ( Conf_provinces_model::TABLE_NAME, Conf_provinces_model::TABLE_NAME . '.province_id = ' . self::TABLE_NAME . '.province_id', 'inner' );
		$this->db->join ( Conf_hotelchains_model::TABLE_NAME, Conf_hotelchains_model::TABLE_NAME . '.chain_id = ' . self::TABLE_NAME . '.chain_id', 'inner' );
		$this->db->join ( Conf_hotellinearities_model::TABLE_NAME, Conf_hotellinearities_model::TABLE_NAME . '.hotel_id = ' . self::TABLE_NAME . '.hotel_id', 'left' );
		if (! empty( $province )) {
			$this->db->where ( self::TABLE_NAME. '.province_id', $province );
		}		
		if (! empty( $name )) {
			$this->db->like ( strtolower(self::TABLE_NAME. '.hotel_name'), strtolower($name) );
			$this->db->or_like ( self::TABLE_NAME. '.hotel_name', ucfirst($name) );
		} 
		$this->db->limit ( $to, $from );
		return $this->db->count_all_results();
	}
	
	/**
	 * Esta es la funcion encargada de insertar los municipios
	 *
	 * @return boolean
	 */
	public function insert() {
		$hotel_id = $this->input->post ( 'hotel_id' );
		$hotel['hotel_name'] = $this->input->post ( 'hotel_name' );
		$hotel['hotel_price'] = $this->input->post ( 'hotel_price' );
		$hotel['chain_id'] = $this->input->post ( 'chain_id' );
		$hotel['province_id'] = $this->input->post ( 'province_id' );
		if (empty ( $hotel_id )) {
			$this->db->trans_begin ();
			$re = $this->db->insert ( self::TABLE_NAME, $hotel );
			$hotel_id = $this->db->insert_id ();
			$logs = new Logs ( );
			$myquery = $logs->sqlinsert ( self::TABLE_NAME, $hotel );
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
			$hotel['hotel_deleted'] = strtolower($this->input->post ( 'hotel_deleted' ));
			$this->db->where ( 'hotel_id', $hotel_id );
			$this->db->trans_begin ();
			$re = $this->db->update ( self::TABLE_NAME, $hotel );
			$logs = new Logs ( );
			
			$mywhere = 'where hotel_id = '.$hotel_id;
			$myquery = $logs->sqlupdate(self::TABLE_NAME, $hotel, $mywhere);
			
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
	 * Funcion para eliminar un hotel por su nombre
	 *
	 * @param string $hotel_id
	 */
	public function delete($id) {
		$hotel['hotel_deleted'] = 'si';
		$this->db->where ( 'hotel_id', $id );
		$this->db->trans_begin ();
		$re = $this->db->update ( self::TABLE_NAME, $hotel );
		//$this->db->delete ( self::TABLE_NAME );
		$logs = new Logs ( );
		$mywhere = 'where hotel_id = ' . $id;
		$myquery = $logs->sqldelete ( self::TABLE_NAME, $mywhere );
		$logs->write ( self::TABLE_NAME, 'DELETE', $myquery );
		
		if ($this->db->trans_status () === FALSE) {
			$this->db->trans_rollback ();
		} else {
			$this->db->trans_commit ();
		}
		
	}
	
	public function getById($hotel_id) {
		$this->load->model ( 'conf/conf_hotelchains_model' );
		$this->load->model ( 'conf/conf_provinces_model' );
		$this->db->select ( 'conf_hotels.hotel_id, 
							conf_hotels.hotel_name, 
							conf_hotels.hotel_price, 
							conf_provinces.province_id, 
							conf_hotelchains.chain_id,
							conf_hotels.hotel_deleted' );
		$this->db->from ( self::TABLE_NAME );
		$this->db->join ( Conf_provinces_model::TABLE_NAME, Conf_provinces_model::TABLE_NAME . '.province_id = ' . self::TABLE_NAME . '.province_id', 'inner' );
		$this->db->join ( Conf_hotelchains_model::TABLE_NAME, Conf_hotelchains_model::TABLE_NAME . '.chain_id = ' . self::TABLE_NAME . '.chain_id', 'inner' );
		$this->db->where ( self::TABLE_NAME . '.hotel_id', $hotel_id );
		$result = $this->db->get ();
		$value = array();
		if ($result->result () != null) {
			foreach ( $result->result () as $row ) {
				$deleted = ucfirst($row->hotel_deleted);
				$value [] = array ('hotel_id' => $row->hotel_id, 
									'hotel_name' => $row->hotel_name, 
									'hotel_deleted' => $deleted, 
									'hotel_price' => $row->hotel_price,
									'province_id' => $row->province_id, 
									'chain_id' => $row->chain_id );
			}
		
		}
		return $value;
	
	}
	
	public function getNameById($hotel_id) {
		$this->db->select ( 'hotel_name' );
		$this->db->from ( self::TABLE_NAME );
		$this->db->where ( 'hotel_id', $hotel_id );
		$result = $this->db->get ();
		foreach ( $result->result () as $row ) {
			$hotel_name = $row->hotel_name;
		}
		return $hotel_name;
	}
	
	
	public function getPriceById($hotel_id) {
		$this->db->select ( 'hotel_price' );
		$this->db->from ( self::TABLE_NAME );
		$this->db->where ( 'hotel_id', $hotel_id );
		$result = $this->db->get ();
		foreach ( $result->result () as $row ) {
			$hotel_price = $row->hotel_price;
		}
		return $hotel_price;
	}
	
	public function getProvinceIdById($hotel_id) {
		$this->db->select ( 'province_id' );
		$this->db->from ( self::TABLE_NAME );
		$this->db->where ( 'hotel_id', $hotel_id );
		$result = $this->db->get ();
		foreach ( $result->result () as $row ) {
			$province_id = $row->province_id;
		}
		return $province_id;
	}	

}
?>
