<?php

class Ticket_requestservices_model extends Model {
	const TABLE_NAME = 'ticket_requestservices';
	
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
	public function getData($to, $from, $dateStart, $dateEnd, $supplier) {
		$centinela = new Centinela();
		$roll_id = $centinela->get_roll_id();
		$this->load->model ( 'conf/conf_provinces_model' );
		$this->load->model ( 'conf/conf_transportsuppliers_model' );
		$this->db->select ( 'ticket_requestservices.service_id, 
							ticket_requestservices.service_capacity, 
							ticket_requestservices.service_date, 
							ticket_requestservices.service_hour, 
							ticket_requestservices.service_itinerary, 
							conf_transportsuppliers.supplier_name, 
							ticket_requestservices.province_idexit, 
							ticket_requestservices.province_idlunch, 
							ticket_requestservices.province_idarrival' );
		$this->db->from ( self::TABLE_NAME );
		$this->db->join ( Conf_transportsuppliers_model::TABLE_NAME, Conf_transportsuppliers_model::TABLE_NAME . '.supplier_id = ' . self::TABLE_NAME . '.supplier_id', 'inner' );
		if ( $roll_id < 5 ) {
			$this->db->where ( self::TABLE_NAME . '.province_idexit =', $centinela->get_province_id() );
			$this->db->or_where(self::TABLE_NAME . '.province_idlunch =', $centinela->get_province_id()); 
			$this->db->or_where(self::TABLE_NAME . '.province_idarrival =', $centinela->get_province_id()); 
		}
		if (! empty ( $dateStart ) && ! empty ( $dateEnd )) {
			$this->db->where ( 'service_date <=', $dateEnd );
			$this->db->where ( 'service_date >=', $dateStart );
		}
		if (! empty( $supplier )) {
			$this->db->where ( 'ticket_requestservices.supplier_id', $supplier );
		}
		$this->db->limit ( $to, $from );
		$result = $this->db->get ();
		$value = array();
		if ($result->result () != null) {
			foreach ( $result->result () as $row ) {
				$service_hour = substr ( $row->service_hour, 0, 5 );
				$province_nameexit = Conf_provinces_model::getNameById ( $row->province_idexit );
				$province_namelunch = !empty($row->province_idlunch) ? Conf_provinces_model::getNameById ( $row->province_idlunch) : '---';
				$province_namearrival = Conf_provinces_model::getNameById ( $row->province_idarrival );
				
				$value [] = array ('service_id' => $row->service_id, 
									'service_capacity' => $row->service_capacity, 
									'service_date' => $row->service_date, 
									'service_hour' => $service_hour, 
									'service_itinerary' => $row->service_itinerary, 
									'supplier_name' => $row->supplier_name, 
									'province_nameexit' => $province_nameexit, 
									'province_namelunch' => $province_namelunch, 
									'province_namearrival' => $province_namearrival );
			}
		
		}
		return $value;
	}
	
	public function getDataAccounting($dateStart, $dateEnd, $supplier) {
		$centinela = new Centinela();
		$roll_id = $centinela->get_roll_id();
		$this->load->model ( 'conf/conf_provinces_model' );
		$this->load->model ( 'conf/conf_transportsuppliers_model' );
		$this->db->select ( 'ticket_requestservices.service_id, 
							ticket_requestservices.service_capacity, 
							ticket_requestservices.service_date, 
							ticket_requestservices.service_hour, 
							ticket_requestservices.service_itinerary,
							ticket_requestservices.service_details,
							ticket_requestservices.service_amount,
							conf_transportsuppliers.supplier_name, 
							ticket_requestservices.province_idexit, 
							ticket_requestservices.province_idlunch, 
							ticket_requestservices.province_idarrival' );
		$this->db->from ( self::TABLE_NAME );
		$this->db->join ( Conf_transportsuppliers_model::TABLE_NAME, Conf_transportsuppliers_model::TABLE_NAME . '.supplier_id = ' . self::TABLE_NAME . '.supplier_id', 'inner' );
		/*if ( $roll_id < 5 ) {
			$this->db->where ( self::TABLE_NAME . '.province_idexit =', $centinela->get_province_id() );
			$this->db->or_where(self::TABLE_NAME . '.province_idlunch =', $centinela->get_province_id()); 
			$this->db->or_where(self::TABLE_NAME . '.province_idarrival =', $centinela->get_province_id()); 
		}*/
		if (! empty ( $dateStart ) && ! empty ( $dateEnd )) {
			$this->db->where ( 'service_date <=', $dateEnd );
			$this->db->where ( 'service_date >=', $dateStart );
		}
		if (! empty( $supplier )) {
			$this->db->where ( 'ticket_requestservices.supplier_id', $supplier );
		}
		$this->db->order_by( 'ticket_requestservices.service_id','asc' );
		$result = $this->db->get ();
		$value = array();
		$total = 0;
		if ($result->result () != null) {
			foreach ( $result->result () as $row ) {
				$service_hour = substr ( $row->service_hour, 0, 5 );
				$province_nameexit = Conf_provinces_model::getNameById ( $row->province_idexit );
				$province_namelunch = !empty($row->province_idlunch) ? Conf_provinces_model::getNameById ( $row->province_idlunch) : '---';
				$province_namearrival = Conf_provinces_model::getNameById ( $row->province_idarrival );
				
				$value [] = array ('service_id' => $row->service_id,
									'service_capacity' => $row->service_capacity,
									'service_date' => $row->service_date, 
									//'service_hour' => $service_hour, 
									//'service_itinerary' => $row->service_itinerary,
									'service_details' => $row->service_details,
									'service_amount' => $row->service_amount,
									'supplier_name' => $row->supplier_name, 
									'province_nameexit' => $province_nameexit, 
									'province_namelunch' => $province_namelunch, 
									'province_namearrival' => $province_namearrival );
				$total = $total + $row->service_amount;
			}
			$last = array();
			$last [] = array ('service_id' => '', 
								'service_capacity' => '', 
								'service_date' => '', 
								//'service_hour' => '', 
								//'service_itinerary' => '', 
								'service_details' => '',
								'service_amount' => $total, 
								'supplier_name' => '',
								'province_nameexit' => '', 
								'province_namelunch' => '', 
								'province_namearrival' => 'TOTAL' );
			$value = array_merge((array)$value, (array)$last);
		}
		return $value;
	}
	
	
	/**
	 * funcion que devuelve la cantidad de registros en la tabla ejemplo
	 *
	 */
	public function getCant($dateStart, $dateEnd, $supplier) {
		$centinela = new Centinela();
		$roll_id = $centinela->get_roll_id();
		$this->load->model ( 'conf/conf_provinces_model' );
		$this->load->model ( 'conf/conf_transportsuppliers_model' );
		$this->db->select ( 'ticket_requestservices.service_id, 
							ticket_requestservices.service_capacity, 
							ticket_requestservices.service_date, 
							ticket_requestservices.service_hour, 
							ticket_requestservices.service_itinerary, 
							conf_transportsuppliers.supplier_name, 
							ticket_requestservices.province_idexit, 
							ticket_requestservices.province_idlunch, 
							ticket_requestservices.province_idarrival' );
		$this->db->from ( self::TABLE_NAME );
		$this->db->join ( Conf_transportsuppliers_model::TABLE_NAME, Conf_transportsuppliers_model::TABLE_NAME . '.supplier_id = ' . self::TABLE_NAME . '.supplier_id', 'inner' );
		if ( $roll_id < 5 ) {
			$this->db->where ( self::TABLE_NAME . '.province_idexit =', $centinela->get_province_id() );
			$this->db->or_where(self::TABLE_NAME . '.province_idlunch =', $centinela->get_province_id()); 
			$this->db->or_where(self::TABLE_NAME . '.province_idarrival =', $centinela->get_province_id()); 
		}
		if (! empty ( $dateStart ) && ! empty ( $dateEnd )) {
			$this->db->where ( 'service_date <=', $dateEnd );
			$this->db->where ( 'service_date >=', $dateStart );
		}
		if (! empty( $supplier )) {
			$this->db->where ( 'ticket_requestservices.supplier_id', $supplier );
		}
		return $this->db->count_all_results();
	}
	
	/**
	 * Esta es la funcion encargada de insertar los transportes
	 *
	 * @return boolean
	 */
	public function insert() {
		$service_id = $this->input->post ( 'service_id' );
		$service = array ( );
		$services ['service_capacity'] = $this->input->post ( 'service_capacity' );
		$services ['service_date'] = $this->input->post ( 'service_date' );
		$services ['service_hour'] = $this->input->post ( 'service_hour' );
		$services ['service_itinerary'] = $this->input->post ( 'service_itinerary' );
		$services ['supplier_id'] = $this->input->post ( 'supplier_id' );
		$services ['province_idexit'] = $this->input->post ( 'province_idexit' );
		if ($this->input->post ( 'province_idlunch' ) != '') {
			$services ['province_idlunch'] = $this->input->post ( 'province_idlunch' );
		}else {
			$services ['province_idlunch'] = null;
		}
		
		$services ['province_idarrival'] = $this->input->post ( 'province_idarrival' );
		$services ['service_details'] = $this->input->post ( 'service_details' );
		$services ['place_exit'] = $this->input->post ( 'place_exit' );
		$services ['place_lunch'] = $this->input->post ( 'place_lunch' );
		$services ['place_arrival'] = $this->input->post ( 'place_arrival' );
		$services ['service_amount'] = $this->input->post ( 'service_amount' );
		$services ['service_costcenter'] = $this->input->post ( 'service_costcenter' );
		if (empty ( $service_id )) {
			$this->db->trans_begin ();
			$re = $this->db->insert ( self::TABLE_NAME, $services );
			$insertid = $this->db->insert_id ();
			$logs = new Logs ( );
			$myquery = $logs->sqlinsert ( self::TABLE_NAME, $services );
			$logs->write ( self::TABLE_NAME, 'INSERT', $myquery );
			
			if ($this->db->trans_status () === FALSE) {
				$this->db->trans_rollback ();
			} else {
				$this->db->trans_commit ();
			}
			if ($re == true) {
				$services ['service_id'] = $insertid; 
				return $services;
			
			} else
				return "false";
		
		} else {
			$this->db->where ( 'service_id', $service_id );
			$this->db->trans_begin ();
			$re = $this->db->update ( self::TABLE_NAME, $services );
			$logs = new Logs ( );
			
			$mywhere = 'where service_id = '.$service_id;
			$myquery = $logs->sqlupdate(self::TABLE_NAME, $services, $mywhere);
			
			$logs->write( self::TABLE_NAME, 'UPDATE', $myquery);
			
			if ($this->db->trans_status () === FALSE) {
				$this->db->trans_rollback ();
			} else {
				$this->db->trans_commit ();
			}
			if ($re == true){
				$services ['service_id'] = $service_id; 
				return $services;
			}
			else
				return "false";
		}
	}
	
	/**
	 * Funcion para eliminar una provincia por su nombre
	 *
	 * @param string $requestservice_name
	 */
	public function delete($service_id) {
		$this->db->where ( 'service_id', $service_id );
		$this->db->trans_begin ();
		$this->db->delete ( self::TABLE_NAME );
		$logs = new Logs ( );
		$mywhere = 'where service_id = ' . $service_id;
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
	 * @param string $service_name
	 * @return services
	 */
	public function getByName($service_name) {
		$result = $this->db->get_where ( self::TABLE_NAME, array ('service_name' => $service_name ) );
		return $result->result_array ();
	
	}
	
	public function get() {
		$result = $this->db->get ( self::TABLE_NAME );
		return $result->result_array;
	} // de la funcion
	

	public function getById($service_id) {
		$this->db->select ( 'ticket_requestservices.service_id, 
							ticket_requestservices.service_capacity, 
							ticket_requestservices.service_date, 
							ticket_requestservices.service_hour, 
							ticket_requestservices.service_itinerary, 
							ticket_requestservices.supplier_id, 
							ticket_requestservices.province_idexit, 
							ticket_requestservices.province_idlunch, 
							ticket_requestservices.province_idarrival,
							ticket_requestservices.service_details,
							ticket_requestservices.place_exit,
							ticket_requestservices.place_lunch,
							ticket_requestservices.place_arrival,
							ticket_requestservices.service_amount,
							ticket_requestservices.service_costcenter' );
		$this->db->from ( self::TABLE_NAME );
		$this->db->where ( 'service_id', $service_id );
		$result = $this->db->get ();
		if ($result->result () != null) {
			foreach ( $result->result () as $row ) {
				$service_hour = substr ( $row->service_hour, 0, 5 );
				$value [] = array ('service_id' => $row->service_id, 
									'service_capacity' => $row->service_capacity, 
									'service_date' => $row->service_date, 
									'service_hour' => $service_hour, 
									'service_itinerary' => $row->service_itinerary, 
									'supplier_id' => $row->supplier_id, 
									'province_idexit' => $row->province_idexit, 
									'province_idlunch' => $row->province_idlunch, 
									'province_idarrival' => $row->province_idarrival,
									'service_details' => $row->service_details,
									'place_exit' => $row->place_exit,
									'place_lunch' => $row->place_lunch,
									'place_arrival' => $row->place_arrival,
									'service_amount' => $row->service_amount,
									'service_costcenter' => $row->service_costcenter);
			}
		
		}
		
		return $value;
	}

}
?>
