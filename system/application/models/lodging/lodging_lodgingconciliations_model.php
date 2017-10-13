<?php

class Lodging_lodgingconciliations_model extends Model {
	const TABLE_NAME = 'lodging_conciliations';
	
	function __construct() {
		parent::__construct ();
	}
	
	public function getData($dateStart, $dateEnd, $hotel, $province) {

		$this->load->model ( 'person/person_persons_model' );
		$this->load->model ( 'conf/conf_hotels_model' );
		$request_requests_table = 'request_requests';
		$request_lodgings_table = 'request_lodgings';
		$conf_costcenters_table = 'conf_costcenters';
	    $lodging_edit_table	= 'lodging_edit';
		$date = new Dates();
		$today = $date->now();
	    
	   	$dateStart = empty($dateStart) ? '1900-01-01' : $dateStart;
	   	$dateEnd = empty($dateEnd) ? '1900-01-01' : $dateEnd;
	   	$this->db->select ($lodging_edit_table.'.request_id, '.
							self::TABLE_NAME . '.bill_number, ' . 
	   						$request_requests_table . '.request_date, ' . 
							$request_lodgings_table . '.lodging_entrancedate, ' . 
							$request_lodgings_table . '.lodging_exitdate, ' . 
							$conf_costcenters_table . '.center_name, ' . 
							$lodging_edit_table . '.person_ideditedby, ' . 
							$request_requests_table . '.person_idworker, ' .
							$lodging_edit_table.'.hotel_id');
		$this->db->from ( $request_requests_table );
		$this->db->join ( $request_lodgings_table, $request_lodgings_table . '.request_id = ' . $request_requests_table . '.request_id', 'inner' );
		$this->db->join ( $lodging_edit_table, $lodging_edit_table . '.request_id = ' . $request_lodgings_table . '.request_id', 'inner' );
		$this->db->join ( self::TABLE_NAME, self::TABLE_NAME . '.request_id = ' . $lodging_edit_table . '.request_id', 'left' );
		$this->db->join ( $conf_costcenters_table, $conf_costcenters_table . '.center_id = ' . $request_requests_table . '.center_id', 'inner' );
		$this->db->where($lodging_edit_table.'.hotel_id =', $hotel);
		$this->db->where($request_lodgings_table.'.province_idlodging =', $province);
		$this->db->where( $request_lodgings_table.'.lodging_entrancedate >=', $dateStart);
		$this->db->where( $request_lodgings_table.'.lodging_entrancedate <=', $dateEnd);
		$this->db->where ( $request_lodgings_table . '.lodging_exitdate <', $today );
		
		$result = $this->db->get ();
		$cant = 0;
		if ($result->result () != null) {
			foreach ( $result->result () as $row ) {
				$hotel = Conf_hotels_model::getNameById ( $row->hotel_id );
				$person_worker = Person_persons_model::getNameById ( $row->person_idworker );
				$person_nameeditedby = Person_persons_model::getNameById ( $row->person_ideditedby );
				//$person_identity = Person_persons_model::getIdentityById($row->person_idworker);
				$value [] = array ('request_id' => $row->request_id, 
									'bill_number' => $row->bill_number,
									'request_date' => $row->request_date, 
									'lodging_entrancedate' => $row->lodging_entrancedate, 
									'lodging_exitdate' => $row->lodging_exitdate, 
									'center_name' => $row->center_name, 
									'person_worker' => $person_worker, 
									'person_nameeditedby' => $person_nameeditedby,
									'hotel_name' => $hotel );
				$cant ++;
			}
		}		
		else {
			$value = array ( );
		}
		echo ("{count : " . $cant . ", data : " . json_encode ( $value ) . "}");
	}
	
	public function getDataAccounting($dateStart='', $dateEnd='', $hotel='', $province='', $center='', $motive='',$show=false, $isPDF='no') {
		$this->load->model ( 'person/person_persons_model' );
		$this->load->model ( 'conf/conf_hotels_model' );
		$this->load->model ( 'conf/conf_provinces_model' );
		$request_requests_table = 'request_requests';
		$request_lodgings_table = 'request_lodgings';
		$conf_costcenters_table = 'conf_costcenters';
		$conf_hotels_table = 'conf_hotels';
		$lodging_edit_table = 'lodging_edit';
		$request_ids = self::getIds ();
		$request_ids = empty ( $request_ids ) ? 0 : $request_ids;
		$dateStart = empty ( $dateStart ) ? '1900-01-01' : $dateStart;
		$dateEnd = empty ( $dateEnd ) ? '1900-01-01' : $dateEnd;
		$this->db->select ( $request_requests_table . '.request_id, ' . 
							$request_requests_table . '.request_date, ' . 
							$request_requests_table . '.person_idlicensedby, ' .
							$request_requests_table . '.request_inversiontask, ' .
							$request_lodgings_table . '.lodging_entrancedate, ' .
							$request_requests_table . '.request_details,' . 
							$request_lodgings_table . '.lodging_exitdate, ' . 
							$conf_costcenters_table . '.center_name, ' . 
							$request_requests_table . '.person_idworker, ' . 
							$lodging_edit_table . '.hotel_id,' .
							$request_lodgings_table . '.province_idlodging,' .
							self::TABLE_NAME. '.bill_number' );
		$this->db->from ( $request_requests_table );
		$this->db->join ( $conf_costcenters_table, $conf_costcenters_table . '.center_id = ' . $request_requests_table . '.center_id', 'inner' );
		$this->db->join ( $request_lodgings_table, $request_lodgings_table . '.request_id = ' . $request_requests_table . '.request_id', 'inner' );
		$this->db->join ( $lodging_edit_table, $lodging_edit_table . '.request_id = ' . $request_lodgings_table . '.request_id', 'left' );
		$this->db->join ( $conf_hotels_table, $conf_hotels_table . '.hotel_id = ' . $lodging_edit_table . '.hotel_id', 'inner' );
		$this->db->join ( self::TABLE_NAME, self::TABLE_NAME . '.request_id = ' . $lodging_edit_table . '.request_id', 'left' );
		//$this->db->where ( $request_lodgings_table . '.province_idlodging =', $province );
		$this->db->where ( $request_lodgings_table . '.lodging_entrancedate >=', $dateStart );
		$this->db->where ( $request_lodgings_table . '.lodging_entrancedate <=', $dateEnd );
		$this->db->where ( $request_lodgings_table . '.lodging_canceled ', 0 );
		$this->db->where ( $lodging_edit_table . '.lodging_noshow !=', 'on' );
		if (! empty ( $province )) {
			$this->db->where ( $request_lodgings_table . '.province_idlodging =', $province );
		}
		if (! empty ( $hotel )) {
			$this->db->where ( $lodging_edit_table . '.hotel_id', $hotel );
		}
		if (! empty ( $center )) {
			$this->db->where ( $request_requests_table . '.center_id', $center );
		}
		if (! empty ( $motive )) {
			$this->db->where ( $request_requests_table . '.motive_id', $motive );
		}
		$this->db->distinct();
		$this->db->order_by ( 'lodging_entrancedate', 'asc' );
		$result = $this->db->get ();
		$cant = 0;
		$total_diet = 0;
		$total_lodging = 0;
		if ($result->result () != null) {
			$date = new Dates();
			$value = array();
			foreach ( $result->result () as $row ) {
				$hotel = Conf_hotels_model::getNameById ( $row->hotel_id );
				$person_worker = Person_persons_model::getNameById ( $row->person_idworker );
				$person_identity = Person_persons_model::getIdentityById ( $row->person_idworker );
				$person_licensedby = Person_persons_model::getNameById ( $row->person_idlicensedby );
				$province_lodging = Conf_provinces_model::getNameById ( $row->province_idlodging );
				$beginDate = date_parse($row->lodging_entrancedate);
				$endDate = date_parse($row->lodging_exitdate);
				$hotel_price = Conf_hotels_model::getPriceById ( $row->hotel_id );
				$dateDiff = $date->dateDiff($beginDate, $endDate);
				$lodging = $dateDiff * $hotel_price;
				$diet = self::dietCalculation($row->request_id);
				$value [] = array ('request_id' => $row->request_id, 
									'request_date' => $row->request_date,
									'person_licensedby' => $person_licensedby,
									'request_inversiontask' => $row->request_inversiontask,
									'lodging_entrancedate' => $row->lodging_entrancedate, 
									'lodging_exitdate' => $row->lodging_exitdate, 
									'center_name' => $row->center_name, 
									'person_worker' => $person_worker, 
									'person_identity' => $person_identity, 
									'request_details' => strtolower($row->request_details),
									'province_lodging' => $province_lodging,
									'hotel_name' => $hotel,
									'diet' => $diet,
									'lodging' => $lodging );
				$total_diet = $total_diet + $diet;
				$total_lodging = $total_lodging + $lodging;
				$cant ++;
			}
		} else {
			$value = array ( );
		}
		$last = array();
		$last [] = array ('request_id' => '', 
						'request_date' => '', 
						'person_licensedby' => '',
						'request_inversiontask' => '',
						'lodging_entrancedate' => '', 
						'lodging_exitdate' => '', 
						'center_name' => '', 
						'person_worker' => '', 
						'request_details' => '',
						'province_lodging' => '',
						'person_identity' => 'TOTAL', 
						'hotel_name' => '',
						'diet' => $total_diet ,
						'lodging' => $total_lodging );
		$value = array_merge((array)$value, (array)$last);
		if ($isPDF == 'si') { //devuelve todos por exceso
			return $value;
		} else { //el cant es filtrado
			 echo ("{count : " . $cant . ", data : " . json_encode ( $value ) . "}"); // este valor se le asigna al return del setDatagridconditional para pasarle
		}
		
		 
	
	}

	public function dietCalculation($request_id) {
		$this->load->model ( 'request/request_lodgings_model' );
		$this->load->model ( 'person/person_persons_model' );
		$this->load->model ( 'conf/conf_provinces_model' );
		$request_requests_table = 'request_requests';
		$request_lodgings_table = 'request_lodgings';
		$lodging_edit_table = 'lodging_edit';
		$query = 'request_lodgings.lodging_entrancedate,
					request_lodgings.lodging_exitdate,
					request_lodgings.lodging_prorogate,
					lodging_edit.lodging_reinforceddiet,
					lodging_edit.lodging_elongationdiet';
		$this->db->select ( $query );
		$this->db->from ( $request_requests_table );
		$this->db->join ( $request_lodgings_table, $request_lodgings_table. '.request_id = ' . $request_requests_table . '.request_id', 'inner' );
		$this->db->join ( $lodging_edit_table, $lodging_edit_table. '.request_id = ' . $request_lodgings_table . '.request_id', 'inner' );
		$this->db->where ( $request_requests_table . '.request_id', $request_id );
		$result = $this->db->get ();
		if ($result->result () != null) {
			$dates = new Dates();
			foreach ( $result->result () as $row ) {
				$reinfoced = $row->lodging_reinforceddiet;
				$elongation = $row->lodging_elongationdiet;
				$prorogate = $row->lodging_prorogate;
				$beginDate = $row->lodging_entrancedate;
				$endDate = $row->lodging_exitdate;
				$tempDate = $beginDate;
				$total = 0;
				while ($tempDate <= $endDate) {
					if ($tempDate == $beginDate) {
						if ($prorogate == 1 && ($dates->week_day($tempDate) == 0 || $dates->week_day($tempDate) == 6)) {
							$money = 17.00;
						} elseif ($prorogate == 1 && ($dates->week_day($tempDate) != 0 || $dates->week_day($tempDate) != 6)) {
							$money = 10.00;
						} else {
							$money = 13.00;
						}
					}
					
					if ($tempDate != $beginDate && $tempDate != $endDate) {
						if ($dates->week_day($tempDate) == 0 || $dates->week_day($tempDate) == 6) {
							$money = 22.00;
						} else {
							$money = 15.00;
						}
					}
					
					if ($tempDate == $endDate) {
						if ($elongation == 'on') {
							$money = 15.00;
						} else {
							$money = 5.00;
						}
					}
					
					if ($reinfoced == 'on') {
						$money = 22.00;
					}
					$total = $total + $money;
					$temp = $dates->DateAdd($tempDate);
					$tempDate = $temp;
					
				}           
			
			}
		
		} else {
			$total = 0;
		}

		return $total;
		
	}
	
	/**
	 * funcion que devuelve la cantidad de registros en la tabla
	 *
	 */
	public function getCant() {
		return $this->db->count_all ( self::TABLE_NAME );
	}
	
	/**
	 * Esta es la funcion encargada de insertar
	 *
	 * @return boolean
	 */
	public function insert($request_id, $bill_number) {
		//$request_id = $this->input->post('request_id');
		$conciliation ['request_id'] = $request_id;
		$conciliation ['bill_number'] = $bill_number; //$this->input->post('bill_number');
		/*
		 * ver despues la validacion que no haya request_id repetidos en la tabla
		 * 
		*/
		$flag = self::getCountById ( $request_id );
		if ($flag > 0) {
			$this->db->where ( 'request_id', $request_id );
			$this->db->trans_begin ();
			$re = $this->db->update ( self::TABLE_NAME, $conciliation );
			$logs = new Logs ( );
			
			$mywhere = 'where request_id = '.$request_id;
			$myquery = $logs->sqlupdate(self::TABLE_NAME, $conciliation, $mywhere);
			
			$logs->write( self::TABLE_NAME, 'UPDATE', $myquery);
			
			if ($this->db->trans_status () === FALSE) {
				$this->db->trans_rollback ();
			} else {
				$this->db->trans_commit ();
			}
			if ($re == true){
				return "true"; 
			}
			else
				return "false";
		} else {
		    $this->db->trans_begin ();
			$re = $this->db->insert ( self::TABLE_NAME, $conciliation );
			$logs = new Logs ( );
			$myquery = $logs->sqlinsert ( self::TABLE_NAME, $conciliation );
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
	
	/**
	 * Funcion para eliminar una provincia por su nombre
	 *
	 * @param string $requestservice_name
	 */
	public function delete($request_id) {
		$this->db->where ( 'request_id', $request_id );
		$this->db->trans_begin ();
		$this->db->delete ( self::TABLE_NAME );
		$logs = new Logs ( );
		$mywhere = 'where request_id = ' . $request_id;
		$myquery = $logs->sqldelete ( self::TABLE_NAME, $mywhere );
		$logs->write ( self::TABLE_NAME, 'DELETE', $myquery );
		
		if ($this->db->trans_status () === FALSE) {
			$this->db->trans_rollback ();
		} else {
			$this->db->trans_commit ();
		}
	}
	
	public function getIds() {
		$this->db->select ( 'request_id' );
		$this->db->from ( self::TABLE_NAME );
		$result = $this->db->get ();
		$value = array ( );
		$cant = 0;
		if ($result->result () != null) {
			foreach ( $result->result () as $row ) {
				$value [$cant] = $row->request_id;
				$cant ++;
			}
		}
		return $value;
	}
	
	public function getById($request_id) {
		$this->load->model ( 'conf/conf_provinces_model' );
		$this->load->model ( 'person/person_persons_model' );
		$this->load->model ( 'ticket/ticket_requests_model' );
		$this->load->model ( 'conf/conf_costcenters_model' );
		$this->load->model ( 'conf/conf_motives_model' );
		$this->load->model ( 'conf/conf_tickettransports_model' );
		$this->load->model ( 'conf/conf_ticketviazulstates_model' );
		$count = self::getCountById ( $request_id );
		$query = 'ticket_requests.request_id, 
							ticket_requests.request_date, 
							ticket_requests.request_exitdate, 
							ticket_requests.request_returndate,
							ticket_requests.person_idrequestedby, 
							conf_costcenters.center_name,
							conf_tickettransports.transport_name,
							ticket_requests.person_idworker,
							ticket_requests.province_idfrom,
							ticket_requests.province_idto,
							conf_motives.motive_name';
		if ($count > 0) {
			$editviazul = ',ticket_editviazul.viazul_voucher,
	        				ticket_editviazul.viazul_exithour,
							ticket_editviazul.viazul_arrivalhour,
							ticket_editviazul.viazul_price,
							ticket_editviazul.viazulstate_id';
			$query = $query . $editviazul;
		}
		$this->db->select ( $query );
		$this->db->from ( Ticket_requests_model::TABLE_NAME );
		$this->db->join ( Conf_costcenters_model::TABLE_NAME, Conf_costcenters_model::TABLE_NAME . '.center_id = ' . Ticket_requests_model::TABLE_NAME . '.center_id', 'inner' );
		$this->db->join ( Conf_tickettransports_model::TABLE_NAME, Conf_tickettransports_model::TABLE_NAME . '.transport_id = ' . Ticket_requests_model::TABLE_NAME . '.transport_id', 'inner' );
		$this->db->join ( Conf_motives_model::TABLE_NAME, Conf_motives_model::TABLE_NAME . '.motive_id = ' . Ticket_requests_model::TABLE_NAME . '.motive_id', 'inner' );
		if ($count > 0) {
			$this->db->join ( self::TABLE_NAME, self::TABLE_NAME . '.request_id = ' . Ticket_requests_model::TABLE_NAME . '.request_id', 'inner' );
			$this->db->join ( Conf_ticketviazulstates_model::TABLE_NAME, Conf_ticketviazulstates_model::TABLE_NAME . '.viazulstate_id = ' . self::TABLE_NAME . '.viazulstate_id', 'inner' );
		}
		$this->db->where ( Ticket_requests_model::TABLE_NAME . '.request_id', $request_id );
		$result = $this->db->get ();
		if ($result->result () != null) {
			foreach ( $result->result () as $row ) {
				$person_namerequestedby = Person_persons_model::getNameById ( $row->person_idrequestedby );
				$person_nameworker = Person_persons_model::getNameById ( $row->person_idworker );
				if ($count > 0) {
					if (is_null ( $row->viazul_exithour )) {
						$viazul_exithour = $row->viazul_exithour;
					} else {
						$viazul_exithour = substr ( $row->viazul_exithour, 0, 5 );
					}
					if (is_null ( $row->viazul_arrivalhour )) {
						$viazul_arrivalhour = $row->viazul_arrivalhour;
					} else {
						$viazul_arrivalhour = substr ( $row->viazul_arrivalhour, 0, 5 );
					}
					
					$value [] = array ('request_id' => $row->request_id, 'request_date' => $row->request_date, 'request_exitdate' => $row->request_exitdate, 'request_returndate' => $row->request_returndate, 'person_namerequestedby' => $person_namerequestedby, 'center_name' => $row->center_name, 'transport_name' => $row->transport_name, 'person_nameworker' => $person_nameworker, 'province_idfrom' => $row->province_idfrom, 'province_idto' => $row->province_idto, 'motive_name' => $row->motive_name, 'viazul_voucher' => $row->viazul_voucher, 'viazul_exithour' => $viazul_exithour, 'viazul_arrivalhour' => $viazul_arrivalhour, 'viazul_price' => $row->viazul_price, 'viazulstate_id' => $row->viazulstate_id );
				} else {
					$value [] = array ('request_id' => $row->request_id, 'request_date' => $row->request_date, 'request_exitdate' => $row->request_exitdate, 'request_returndate' => $row->request_returndate, 'person_namerequestedby' => $person_namerequestedby, 'center_name' => $row->center_name, 'transport_name' => $row->transport_name, 'person_nameworker' => $person_nameworker, 'province_idfrom' => $row->province_idfrom, 'province_idto' => $row->province_idto, 'motive_name' => $row->motive_name );
				}
			
			}
		
		} else {
			$value = array ( );
		}
		return $value;
	
	}
	
	public function getCountById($request_id) {
		$this->db->select ( 'request_id' );
		$this->db->from ( self::TABLE_NAME );
		$this->db->where ( 'request_id', $request_id );
		return $this->db->count_all_results ();
	}

}
?>
