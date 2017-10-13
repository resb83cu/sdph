<?php

class Lodging_edit_model extends Model {
	const TABLE_NAME = 'lodging_edit';
	
	function __construct() {
		parent::__construct ();
	}

	
	/**
	 * funcion que devuelve la cantidad de registros en la tabla ejemplo
	 *
	 */
	public function getCant() {
		return $this->db->count_all ( self::TABLE_NAME );
	}
	
	/**
	 * Esta es la funcion encargada de insertar los transportes
	 *
	 * @return boolean
	 */
	public function insert() {
		$this->load->model ( 'request/request_lodgings_model' );
		$this->load->model ( 'request/request_requests_model' );
		
		$request_id = $this->input->post ( 'request_id' );
		//request_requests
		//$request_date = $this->input->post ( 'request_date' );
		$request_details = $this->input->post ( 'request_details' );
		$request_inversiontask = $this->input->post ( 'request_inversiontask');
		$person_idrequestedby = $this->input->post ( 'person_idrequestedby' );
		$person_idlicensedby = $this->input->post ( 'person_idlicensedby' );
		$person_idworker = $this->input->post ( 'person_idworker');
		$motive_id = $this->input->post ( 'motive_id');
		$center_id = $this->input->post ( 'center_id' );
		//request_lodging
		$lodging_entrancedate = $this->input->post ( 'lodging_entrancedate');
		$lodging_exitdate = $this->input->post ( 'lodging_exitdate');
		$province_idlodging = $this->input->post ( 'province_idlodging');
		$transport_idlodging = $this->input->post ( 'transport_idlodging');
		$transport_idreturnlodging = $this->input->post ( 'transport_idreturnlodging');
		$lodging_requestreinforceddiet = $this->input->post ( 'lodging_requestreinforceddiet' );
		$lodging_requestelongationdiet = $this->input->post ( 'lodging_requestelongationdiet' );
		//lodging_edit
		$lodging_reinforceddiet = $this->input->post ( 'lodging_reinforceddiet' );
		$lodging_elongationdiet = $this->input->post ( 'lodging_elongationdiet' );
		$lodging_noshow = $this->input->post ( 'lodging_noshow' );
		$lodging_prorogation = $this->input->post ( 'lodging_prorogation' );
		$hotel_id = $this->input->post ( 'hotel_id' );
		if (is_numeric($this->input->post ( 'linearity_id' )))
			$linearity_id = $this->input->post ( 'linearity_id' ); 
		else
			$linearity_id = null;
		if (is_numeric($this->input->post ( 'cafeteria_id' )))
			$cafeteria_id = $this->input->post ( 'cafeteria_id' ); 
		else
			$cafeteria_id = null;
			
		$person_ideditedby = $this->session->userdata('person_id');
		
		$lodging ['request_id'] = $request_id;
		$lodging ['lodging_reinforceddiet'] = $lodging_reinforceddiet;
		$lodging ['lodging_elongationdiet'] = $lodging_elongationdiet;
		$lodging ['lodging_noshow'] = $lodging_noshow;
		//$lodging ['lodging_prorogation'] = $lodging_prorogation;
		$lodging ['hotel_id'] = $hotel_id;
		$lodging ['linearity_id'] = $linearity_id;
		$lodging ['cafeteria_id'] = $cafeteria_id;
		$lodging ['person_ideditedby'] = $person_ideditedby;

		/*if (is_numeric($linearity_id))
			$lodging ['linearity_id'] = $linearity_id;
		else
			$lodging ['linearity_id'] = null;*/
		$flag = self::getCountById($request_id);
		if ($flag > 0) {
			if ($this->input->post ( 'lodging_prorogation' ) == 'on') {
				$date = new Dates();
				$prorogation_date = $this->input->post ('lodging_prorogationdate');
				$beginDate = date_parse($lodging_exitdate);
				$endDate = date_parse($prorogation_date);
				$diff = $date->dateDiff($beginDate, $endDate);
				if ($diff > 0) {//
					$l_lodging_entrancedate = $lodging_exitdate;
					$l_lodging_exitdate = $prorogation_date;
				} else {
					$lodging_prorogate = self::getProrogate($request_id);
					Request_lodgings_model::insert($request_id, $lodging_entrancedate, $prorogation_date,
													$transport_idlodging, $transport_idreturnlodging, $province_idlodging,
													$lodging_requestreinforceddiet, $lodging_requestelongationdiet, $lodging_prorogate);
					$l_lodging_entrancedate = $prorogation_date;
					$l_lodging_exitdate = $lodging_exitdate;
				}
				
				$hotel_idOld = self::getHotelByRequestId($request_id);
				if ($hotel_idOld != $hotel_id) {
					$lodging ['hotel_id'] = $hotel_idOld;
				}
				
				$cafeteria_idOld = self::getCafeteriaByRequestId($request_id);
				if ($cafeteria_idOld != $cafeteria_id) {
					$lodging ['cafeteria_id'] = $cafeteria_idOld;
				}
				//request_requests data
				$r_request_date = $date->now();
				$r_request_details = $request_details;
				$r_person_idrequestedby = $person_idrequestedby;
				$r_center_id = $center_id;
				$r_person_idlicensedby = $person_idlicensedby;
				$r_person_idworker = $person_idworker;
				$r_motive_id = $motive_id;
				$r_request_inversiontask = $request_inversiontask;
				//request_lodgings data
				$l_transport_idlodging = $transport_idlodging;
				$l_transport_idreturnlodging = $transport_idreturnlodging;
				$l_province_idlodging = $province_idlodging;
				$l_lodging_requestreinforceddiet = $lodging_reinforceddiet;
				$l_lodging_requestelongationdiet = $lodging_requestelongationdiet;
				$l_lodging_prorogate = 1;
				//lodging_edit data
				$l_lodging_reinforceddiet = $lodging_reinforceddiet;
				$l_lodging_elongationdiet = $lodging_elongationdiet;
				$l_lodging_noshow = $lodging_noshow;
				$l_lodging_prorogation = false;
				$l_hotel_id = $hotel_id;
				$l_linearity_id = $linearity_id;
				$l_cafeteria_id = $cafeteria_id;
				$l_person_editedby = $person_ideditedby;
				
				Request_requests_model::makeProrogation($r_request_date,
														$r_request_details,
														$r_person_idrequestedby,
														$r_center_id,
														$r_person_idlicensedby,
														$r_person_idworker,
														$r_motive_id,
														$r_request_inversiontask,
														$l_lodging_entrancedate, 
														$l_lodging_exitdate, 
														$l_transport_idlodging, 
														$l_transport_idreturnlodging, 
														$l_province_idlodging, 
														$l_lodging_requestreinforceddiet,
														$l_lodging_requestelongationdiet,
														$l_lodging_prorogate,
														$l_lodging_reinforceddiet, 
														$l_lodging_elongationdiet, 
														$l_lodging_noshow,
														$l_lodging_prorogation,
														$l_hotel_id, 
														$l_linearity_id,
														$l_cafeteria_id,
														$l_person_editedby);
				
			}
			$this->db->where ( 'request_id', $request_id );
			$this->db->trans_begin ();
			$re = $this->db->update ( self::TABLE_NAME, $lodging );
			$logs = new Logs ( );
			
			$mywhere = 'where request_id = '.$request_id;
			$myquery = $logs->sqlupdate(self::TABLE_NAME, $lodging, $mywhere);
			
			$logs->write( self::TABLE_NAME, 'UPDATE', $myquery);
			
			if ($this->db->trans_status () === FALSE) {
				$this->db->trans_rollback ();
			} else {
				$this->db->trans_commit ();
			}
			if ($re == true) {
				Request_lodgings_model::canceledState($request_id, 0);
				return "true";
			} else
				return "{success: false, errors: { reason: 'Vuelva a intentarlo.' }}";
		} else {
			$this->db->trans_begin ();
			$re = $this->db->insert ( self::TABLE_NAME, $lodging );
			$logs = new Logs ( );
			$myquery = $logs->sqlinsert ( self::TABLE_NAME, $lodging );
			$logs->write ( self::TABLE_NAME, 'INSERT', $myquery );
			
			if ($this->db->trans_status () === FALSE) {
				$this->db->trans_rollback ();
			} else {
				$this->db->trans_commit ();
			}			
			
			if ($re == true) {
				Request_lodgings_model::updateState( $request_id, 1 );
				return "true";
			} else
				return "{success: false, errors: { reason: 'Vuelva a intentarlo.' }}";
		}
	}

	public function insertProrogation($request_id, $lodging_reinforceddiet, $lodging_elongationdiet, $lodging_noshow, $lodging_prorogation, $hotel_id, $linearity_id, $cafeteria_id, $person_editedby) {
		$this->load->model ( 'request/request_lodgings_model' );
		$this->load->model ( 'request/request_requests_model' );

		$lodging ['request_id'] = $request_id;
		$lodging ['lodging_reinforceddiet'] = $lodging_reinforceddiet;
		$lodging ['lodging_elongationdiet'] = $lodging_elongationdiet;
		$lodging ['lodging_noshow'] = $lodging_noshow;
		$lodging ['lodging_prorogation'] = $lodging_prorogation;
		$lodging ['hotel_id'] = $hotel_id;
		$lodging ['linearity_id'] = $linearity_id;
		$lodging ['cafeteria_id'] = $cafeteria_id; 
		$lodging ['person_ideditedby'] = $person_editedby;
		$this->db->trans_begin ();
		$re = $this->db->insert ( self::TABLE_NAME, $lodging );
		$logs = new Logs ( );
			$myquery = $logs->sqlinsert ( self::TABLE_NAME, $lodging );
			$logs->write ( self::TABLE_NAME, 'INSERT', $myquery );
			
			if ($this->db->trans_status () === FALSE) {
				$this->db->trans_rollback ();
			} else {
				$this->db->trans_commit ();
			}			
			
		if ($re == true) {
			Request_lodgings_model::updateState( $request_id, 1 );
			return "true";
		} else
			return "false";
	}
	
	public function insertMulti($request_id, $hotel_id) {
		$this->load->model ( 'request/request_lodgings_model' );
		$lodging ['request_id'] = $request_id;
		$lodging ['hotel_id'] = $hotel_id;
		/*if ($this->input->post ( 'linearity_id' ) != null) //el empty no funciona 
			$lodging ['linearity_id'] = $this->input->post ( 'linearity_id' ); else
			$lodging ['linearity_id'] = null;*/
		$lodging ['person_ideditedby'] = $this->session->userdata('person_id');
		$flag = self::getCountById ( $request_id );
		if ($flag > 0) {
			$this->db->where ( 'request_id', $request_id );
			$this->db->trans_begin ();
			$re = $this->db->update ( self::TABLE_NAME, $lodging );
			$logs = new Logs ( );
			
			$mywhere = 'where request_id = '.$request_id;
			$myquery = $logs->sqlupdate(self::TABLE_NAME, $lodging, $mywhere);
			
			$logs->write( self::TABLE_NAME, 'UPDATE', $myquery);
			
			if ($this->db->trans_status () === FALSE) {
				$this->db->trans_rollback ();
			} else {
				$this->db->trans_commit ();
			}
			if ($re == true) {
				Request_lodgings_model::canceledState($request_id, 0);
				return "true";
			} else
				return "false";
		
		} else {
		    $this->db->trans_begin ();
			$re = $this->db->insert ( self::TABLE_NAME, $lodging );
			$logs = new Logs ( );
			$myquery = $logs->sqlinsert ( self::TABLE_NAME, $lodging );
			$logs->write ( self::TABLE_NAME, 'INSERT', $myquery );
			
			if ($this->db->trans_status () === FALSE) {
				$this->db->trans_rollback ();
			} else {
				$this->db->trans_commit ();
			}			
			if ($re == true) {
				Request_lodgings_model::updateState( $request_id, 1 );
				return "true";
			} else
				return "false";
		}
	}
	
	/**
	 * Funcion para eliminar
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
	
	public function getHotelByRequestId($request_id) {
		$this->db->select('hotel_id');
		$this->db->from(self::TABLE_NAME);
		$this->db->where('request_id', $request_id);
		$result = $this->db->get ( );
		$hotel_id = 0;
		foreach ( $result->result () as $row ) {
			$hotel_id = $row->hotel_id;
		}
		return $hotel_id;
	}
	
	public function getCafeteriaByRequestId($request_id) {
		$this->db->select('cafeteria_id');
		$this->db->from(self::TABLE_NAME);
		$this->db->where('request_id', $request_id);
		$result = $this->db->get ( );
		$cafeteria_id = 0;
		foreach ( $result->result () as $row ) {
			$cafeteria_id = $row->cafeteria_id;
		}
		return $cafeteria_id;
	}
	
	public function getProrogate($request_id) {
		$this->db->select('lodging_prorogate');
		$this->db->from('request_lodgings');
		$this->db->where('request_id', $request_id);
		$result = $this->db->get ( );
		$prorogate = 0;
		foreach ( $result->result () as $row ) {
			$prorogate = $row->lodging_prorogate;
		}
		return $prorogate;
	}

	public function voucher($request_id) {
		$this->load->model ( 'request/request_lodgings_model' );
		$this->load->model ( 'person/person_persons_model' );
		$this->load->model ( 'conf/conf_provinces_model' );
		$this->load->model ( 'conf/conf_cafeterias_model' );
		$request_requests_table = 'request_requests';
		$request_lodgings_table = 'request_lodgings';
		$lodging_edit_table = 'lodging_edit';
		$conf_hotelchains_table = 'conf_hotelchains';
		$conf_hotels_table = 'conf_hotels';

		$query = 'request_requests.person_idworker,
					request_requests.request_details,
					request_requests.request_inversiontask,
					request_lodgings.lodging_entrancedate,
					request_lodgings.lodging_exitdate,
					request_lodgings.lodging_prorogate,
					lodging_edit.lodging_reinforceddiet,
					lodging_edit.lodging_elongationdiet,
					lodging_edit.cafeteria_id,
					conf_hotels.hotel_name,
					conf_hotelchains.chain_name';
		$this->db->select ( $query );
		$this->db->from ( $request_requests_table );
		$this->db->join ( $request_lodgings_table, $request_lodgings_table. '.request_id = ' . $request_requests_table . '.request_id', 'inner' );
		$this->db->join ( $lodging_edit_table, $lodging_edit_table. '.request_id = ' . $request_lodgings_table . '.request_id', 'inner' );
		$this->db->join ( $conf_hotels_table, $conf_hotels_table. '.hotel_id = ' . $lodging_edit_table . '.hotel_id', 'inner' );
		$this->db->join ( $conf_hotelchains_table, $conf_hotelchains_table. '.chain_id = ' . $conf_hotels_table. '.chain_id', 'inner' );
		$this->db->where ( $request_requests_table . '.request_id', $request_id );
		$result = $this->db->get ();
		if ($result->result () != null) {
			$dates = new Dates();
			foreach ( $result->result () as $row ) {
				$person_identity = Person_persons_model::getIdentityById( $row->person_idworker );
				$person_name = Person_persons_model::getNameById ( $row->person_idworker );
				$province = Person_persons_model::getProvinceById( $row->person_idworker );
				$cafeteria = empty($row->cafeteria_id) ? $row->cafeteria_id : Conf_cafeterias_model::getNameById ( $row->cafeteria_id );;
				//$province = Conf_provinces_model::getNameById($person_province);
				$reinfoced = $row->lodging_reinforceddiet;
				$elongation = $row->lodging_elongationdiet;
				$prorogate = $row->lodging_prorogate;
				$beginDate = $row->lodging_entrancedate;
				$endDate = $row->lodging_exitdate;
				//$countDays = $dates->dateDiff($beginDate, $endDate);
				$tempDate = $beginDate;
				$total = 0;
				while ($tempDate <= $endDate) {
					if ($tempDate == $beginDate) {
						if (($prorogate == 1 && ($dates->week_day($tempDate) == 0 || $dates->week_day($tempDate) == 6)) || ($prorogate == 1 && $reinfoced == 'on') || ($prorogate == 1 && $tempDate == '2014-05-01')) {
							/*if ($elongation == 'on') {
								$money = 7.00;
							}else {
								$money = 17.00;
							}*/
							$money = 17.00;
						} elseif ($prorogate == 1 && ($dates->week_day($tempDate) != 0 || $dates->week_day($tempDate) != 6)) {
							/*if ($elongation == 'on') {
								$money = 0.00;
							}else {
								$money = 10.00;
							}*/
							$money = 10.00;
						} else {
							$money = 13.00;
						}
					}
					
					if ($tempDate != $beginDate && $tempDate != $endDate) {
						if ($dates->week_day($tempDate) == 0 || $dates->week_day($tempDate) == 6 || $tempDate == '2014-05-01') {
							$money = 22.00;
						} else {
							$money = 15.00;
						}
					}
					
					if ($reinfoced == 'on' && $tempDate != $beginDate && $tempDate != $endDate) {
						$money = 22.00;
					}
					
					if ($tempDate == $endDate) {
						if ($elongation == 'on') {
							$money = 15.00;
						} else {
							$money = 5.00;
						}
					}
										
					$total = $total + $money;
					$value [] = array ('person_name' => $person_name, 
										'person_identity' => $person_identity, 
										'province' => $province,
										'hotel' => $row->hotel_name, 
										'chain' => $row->chain_name,
										'cafeteria' => $cafeteria,
										'date' => $tempDate,
										'money' => $money,
					                    'details' => $row->request_details,
										'task' => $row->request_inversiontask,
										'total' => 0);
					
					$temp = $dates->DateAdd($tempDate);
					$tempDate = $temp;
					
				}           
			
			}
		
		} else {
			$value = array ( );
		}
		$last [] = array ('person_name' => '', 
						'person_identity' => '', 
						'province' => '',
						'hotel' => '', 
						'chain' => '',
						'cafeteria' => '',
						'date' => '',
						'money' => '',
		               	'details' => $row->request_details,
						'task' => $row->request_inversiontask,
						'total' => $total );
		$value = array_merge((array)$value, (array)$last);
		return $value;
		
	}

	public function voucherSoftball($request_id)
    {
        $this->load->model('request/request_lodgings_model');
        $this->load->model('person/person_persons_model');
        $this->load->model('conf/conf_provinces_model');
        $this->load->model('conf/conf_cafeterias_model');
        $request_requests_table = 'request_requests';
        $request_lodgings_table = 'request_lodgings';
        $lodging_edit_table = 'lodging_edit';
        $conf_hotelchains_table = 'conf_hotelchains';
        $conf_hotels_table = 'conf_hotels';

        $query = 'request_requests.person_idworker,
					request_requests.request_details,
					request_requests.request_inversiontask,
					request_lodgings.lodging_entrancedate,
					request_lodgings.lodging_exitdate,
					request_lodgings.lodging_prorogate,
					lodging_edit.lodging_reinforceddiet,
					lodging_edit.lodging_elongationdiet,
					lodging_edit.cafeteria_id,
					conf_hotels.hotel_name,
					conf_hotelchains.chain_name';
        $this->db->select($query);
        $this->db->from($request_requests_table);
        $this->db->join($request_lodgings_table, $request_lodgings_table . '.request_id = ' . $request_requests_table . '.request_id', 'inner');
        $this->db->join($lodging_edit_table, $lodging_edit_table . '.request_id = ' . $request_lodgings_table . '.request_id', 'inner');
        $this->db->join($conf_hotels_table, $conf_hotels_table . '.hotel_id = ' . $lodging_edit_table . '.hotel_id', 'inner');
        $this->db->join($conf_hotelchains_table, $conf_hotelchains_table . '.chain_id = ' . $conf_hotels_table . '.chain_id', 'inner');
        $this->db->where($request_requests_table . '.request_id', $request_id);
        $result = $this->db->get();
        if ($result->result() != null) {
            $dates = new Dates();
            foreach ($result->result() as $row) {
                $person_identity = Person_persons_model::getIdentityById($row->person_idworker);
                $person_name = Person_persons_model::getNameById($row->person_idworker);
                $province = Person_persons_model::getProvinceById($row->person_idworker);
                $cafeteria = empty($row->cafeteria_id) ? $row->cafeteria_id : Conf_cafeterias_model::getNameById($row->cafeteria_id);
                $reinfoced = $row->lodging_reinforceddiet;
                $prorogate = $row->lodging_prorogate;
                $beginDate = $row->lodging_entrancedate;
                $endDate = $row->lodging_exitdate;
                $tempDate = $beginDate;
                $total = 0;
                while ($tempDate <= $endDate) {
                    if ($tempDate == $beginDate) {
                        if (($prorogate == 1 && ($dates->week_day($tempDate) == 0 || $dates->week_day($tempDate) == 6)) || ($prorogate == 1 && $reinfoced == 'on') || ($prorogate == 1 && $tempDate == '2011-10-10')) {
                            $money = 4.00;
                        } elseif ($prorogate == 1 && ($dates->week_day($tempDate) != 0 || $dates->week_day($tempDate) != 6)) {
                            $money = 4.00;
                        } else {
                            $money = 9.00;
                        }
                    }

                    if ($tempDate != $beginDate && $tempDate != $endDate) {
                        $money = 9.00;
                    }

                    if ($tempDate == $endDate) {
                        $money = 2.50;
                    }

                    $total = $total + $money;
                    $value [] = array('person_name' => $person_name,
                        'person_identity' => $person_identity,
                        'province' => $province,
                        'hotel' => $row->hotel_name,
                        'chain' => $row->chain_name,
                        'cafeteria' => $cafeteria,
                        'date' => $tempDate,
                        'money' => $money,
                        'details' => $row->request_details,
                        'task' => $row->request_inversiontask,
                        'total' => 0);

                    $temp = $dates->DateAdd($tempDate);
                    $tempDate = $temp;
                }
            }
        } else {
            $value = array();
        }
        $last [] = array('person_name' => '',
            'person_identity' => '',
            'province' => '',
            'hotel' => '',
            'chain' => '',
            'cafeteria' => '',
            'date' => '',
            'money' => '',
            'details' => $row->request_details,
            'task' => $row->request_inversiontask,
            'total' => $total);
        $value = array_merge((array)$value, (array)$last);
        return $value;
    }
	
	public function updateVoucher($request_id) {
		$lodging ['lodging_voucher'] = 'si';
		$this->db->where ( 'request_id', $request_id );
		$this->db->trans_begin ();
		$re = $this->db->update ( self::TABLE_NAME, $lodging );
		$logs = new Logs ( );
		
		$mywhere = 'where request_id = '.$request_id;
		$myquery = $logs->sqlupdate(self::TABLE_NAME, $lodging, $mywhere);
		
		$logs->write( self::TABLE_NAME, 'UPDATE', $myquery);
		
		if ($this->db->trans_status () === FALSE) {
			$this->db->trans_rollback ();
		} else {
			$this->db->trans_commit ();
		}
		if ($re == true)
			return "true"; 
		else
			return "false";
		
	}
	
	
	public function getById($request_id) {
		$this->load->model ( 'request/request_lodgings_model' );
		$this->load->model ( 'person/person_persons_model' );
		$request_requests_table = 'request_requests';
		$request_lodgings_table = 'request_lodgings';
		$person_persons_table = 'person_persons';
		$conf_provinces_table = 'conf_provinces';
		$conf_hotels_table = 'conf_hotels';
		$conf_hotellinearities_table = 'conf_hotellinearities';
		$conf_cafeterias_table = 'conf_cafeterias';
		$count = self::getCountById ( $request_id );
		$query = 'request_lodgings.request_id, 
					request_requests.request_date, 
					request_requests.request_details, 
					request_lodgings.lodging_entrancedate, 
					request_lodgings.lodging_exitdate,
					request_requests.person_idrequestedby, 
					request_requests.center_id,
					request_requests.request_inversiontask,
					request_lodgings.transport_idlodging,
					request_lodgings.transport_idreturnlodging,
					request_requests.motive_id,
					request_lodgings.province_idlodging,
					request_lodgings.lodging_requestreinforceddiet,
					request_lodgings.lodging_requestelongationdiet,
					request_requests.person_idworker,
					request_requests.person_idlicensedby,
					request_lodgings.lodging_state,
					person_persons.province_id';
		if ($count > 0) {
			$this->load->model ( 'conf/conf_hotels_model' );
			$this->load->model ( 'conf/conf_hotellinearities_model' );
			$edit = ',lodging_edit.lodging_reinforceddiet,
							lodging_edit.lodging_elongationdiet,
							lodging_edit.lodging_noshow,
							lodging_edit.lodging_prorogation,
							lodging_edit.hotel_id,
							lodging_edit.linearity_id,
							lodging_edit.cafeteria_id,
							lodging_edit.person_ideditedby';
			$query = $query . $edit;
		}
		$this->db->select ( $query );
		$this->db->from ( $request_requests_table );
		$this->db->join ( $request_lodgings_table, $request_lodgings_table. '.request_id = ' . $request_requests_table . '.request_id', 'inner' );
		$this->db->join ( $person_persons_table, $person_persons_table. '.person_id = ' . $request_requests_table . '.person_idworker', 'inner' );
		$this->db->join ( $conf_provinces_table, $conf_provinces_table. '.province_id = ' . $person_persons_table. '.province_id', 'inner' );
		if ($count > 0) {
			$this->db->join ( self::TABLE_NAME, self::TABLE_NAME . '.request_id = ' . $request_lodgings_table . '.request_id', 'inner' );
			$this->db->join ( $conf_hotels_table, $conf_hotels_table . '.hotel_id = ' . self::TABLE_NAME . '.hotel_id', 'inner' );
			$this->db->join ( $conf_hotellinearities_table, $conf_hotellinearities_table . '.hotel_id = ' . $conf_hotels_table . '.hotel_id', 'left' );
			//$this->db->join ( $conf_cafeterias_table, $conf_cafeterias_table . '.cafeteria_id = ' . self::TABLE_NAME . '.cafeteria_id', 'left' );
		}
		$this->db->where ( $request_lodgings_table . '.request_id', $request_id );
		$result = $this->db->get ();
		if ($result->result () != null) {
			foreach ( $result->result () as $row ) {
				//$person_namerequestedby = Person_persons_model::getNameById ( $row->person_idrequestedby );
				//$person_nameworker = Person_persons_model::getNameById ( $row->person_idworker );
				if ($count > 0) {
					$value [] = array ('request_id' => $row->request_id, 
										'request_date' => $row->request_date, 
										'request_details' => $row->request_details, 
										'lodging_entrancedate' => $row->lodging_entrancedate, 
										'lodging_exitdate' => $row->lodging_exitdate, 
										'person_idrequestedby' => $row->person_idrequestedby, 
										'center_id' => $row->center_id, 
										'request_inversiontask' => $row->request_inversiontask,
										'transport_idlodging' => $row->transport_idlodging, 
										'motive_id' => $row->motive_id, 
										'province_idlodging' => $row->province_idlodging,
										'transport_idreturnlodging' => $row->transport_idreturnlodging,
										'province_id' => $row->province_id, 
										'person_idworker' => $row->person_idworker, 
										'person_idlicensedby' => $row->person_idlicensedby, 
										'lodging_requestreinforceddiet' => $row->lodging_requestreinforceddiet,
										'lodging_requestelongationdiet' => $row->lodging_requestelongationdiet,
										'lodging_state' => $row->lodging_state, 
										'lodging_reinforceddiet' => $row->lodging_reinforceddiet, 
										'lodging_elongationdiet' => $row->lodging_elongationdiet, 
										'lodging_noshow' => $row->lodging_noshow, 
										'lodging_prorogation' => $row->lodging_prorogation, 
										'hotel_id' => $row->hotel_id, 
										'linearity_id' => $row->linearity_id, 
										'cafeteria_id' => $row->cafeteria_id,
										'person_ideditedby' => $row->person_ideditedby );
				} else {
					$value [] = array ('request_id' => $row->request_id, 
										'request_date' => $row->request_date, 
										'request_details' => $row->request_details, 
										'lodging_entrancedate' => $row->lodging_entrancedate, 
										'lodging_exitdate' => $row->lodging_exitdate, 
										'person_idrequestedby' => $row->person_idrequestedby, 
										'center_id' => $row->center_id, 
										'request_inversiontask' => $row->request_inversiontask,
										'transport_idlodging' => $row->transport_idlodging, 
										'transport_idreturnlodging' => $row->transport_idreturnlodging,
										'lodging_requestelongationdiet' => $row->lodging_requestelongationdiet,
										'motive_id' => $row->motive_id, 
										'province_idlodging' => $row->province_idlodging, 
										'province_id' => $row->province_id, 
										'person_idworker' => $row->person_idworker, 
										'person_idlicensedby' => $row->person_idlicensedby, 
										'lodging_requestreinforceddiet' => $row->lodging_requestreinforceddiet,
										'lodging_state' => $row->lodging_state );
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

	public function hotel_letter($id)
	{
		$date = new Dates();
		$this->db->set('letter_date', $date->now());
		
		$valuesLog['letter_id'] = $id;
		$valuesLog['letter_date'] = $date->now();
		
		
		$this->db->trans_begin ();
		$re = $this->db->insert ( 'lodging_hotelletters');
		if ($re == "true") {
			$letter_id = $this->db->insert_id ();
			$arrayId = explode("-", $id);
			foreach ($arrayId as $value) {
				$res = self::updateLetter($value, $letter_id);
			}
			$result = $res;
			$logs = new Logs ( );
			$myquery = $logs->sqlinsert ( self::TABLE_NAME, $valuesLog );
			$logs->write ( self::TABLE_NAME, 'INSERT', $myquery );
			
			if ($this->db->trans_status () === FALSE) {
				$this->db->trans_rollback ();
			} else {
				$this->db->trans_commit ();
			}
			if ($res == "true") {
				return self::getByLetter($letter_id);		
			}
			
		} else {
			$result = "false";
		}

		return $result;
	}
	
	public function getByLetter($letter_id) {
		$this->load->model ( 'conf/conf_hotels_model' );
		$this->load->model ( 'conf/conf_cafeterias_model' );
		$this->load->model ( 'person/person_persons_model' );
		$request_lodgings_table = 'request_lodgings';
		$request_requests_table = 'request_requests';
		$lodging_hotelletters_table = 'lodging_hotelletters';
		$query = 'request_requests.person_idworker, 
					request_lodgings.lodging_entrancedate, 
					request_lodgings.lodging_exitdate,
					lodging_edit.hotel_id,
					lodging_edit.cafeteria_id,
					lodging_hotelletters.letter_id';
		$this->db->select ( $query );
		$this->db->from ( $request_requests_table );
		$this->db->join ( $request_lodgings_table, $request_lodgings_table. '.request_id = ' . $request_requests_table . '.request_id', 'inner' );
		$this->db->join ( self::TABLE_NAME, self::TABLE_NAME . '.request_id = ' . $request_lodgings_table . '.request_id', 'inner' );
		$this->db->join ( $lodging_hotelletters_table, $lodging_hotelletters_table . '.letter_id = ' . self::TABLE_NAME . '.letter_id', 'inner' );
		$this->db->where ( self::TABLE_NAME . '.letter_id', $letter_id );
		$result = $this->db->get ();
		if ($result->result () != null) {
			foreach ( $result->result () as $row ) {
				$hotel = Conf_hotels_model::getNameById($row->hotel_id );
				$person_name = Person_persons_model::getNameById ( $row->person_idworker );
				$person_identity = Person_persons_model::getIdentityById( $row->person_idworker );
				$person_province = Person_persons_model::getProvinceById( $row->person_idworker );
				$cafeteria = empty($row->cafeteria_id) ? $row->cafeteria_id : Conf_cafeterias_model::getNameById($row->cafeteria_id);
				$value [] = array ('hotel' => $hotel,
									'cafeteria' => $cafeteria,
									'lodging_entrancedate' => $row->lodging_entrancedate, 
									'lodging_exitdate' => $row->lodging_exitdate, 
									'person_name' => $person_name, 
									'person_identity' => $person_identity, 
									'person_province' => $person_province, 
									'letter_id' => $row->letter_id );

			}
		
		} else {
			$value = array ( );
		}
		return $value;
	
	}
	
	public function updateLetter($request_id, $letter_id) {
		$data = array ('letter_id' => $letter_id );
		$this->db->where('request_id', $request_id);
		$this->db->trans_begin ();
		$re = $this->db->update ( self::TABLE_NAME, $data );
		$logs = new Logs ( );
			
			$mywhere = 'where request_id = '.$request_id;
			$myquery = $logs->sqlupdate(self::TABLE_NAME, $data, $mywhere);
			
			$logs->write( self::TABLE_NAME, 'UPDATE', $myquery);
			
			if ($this->db->trans_status () === FALSE) {
				$this->db->trans_rollback ();
			} else {
				$this->db->trans_commit ();
			}
		if ($re == true)
			return "true"; 
		else
			return "false";
	}
	
	public function getCountByLetterId($letter_id) {
		$this->load->model ( 'conf/conf_hotels_model' );
		$this->load->model ( 'person/person_persons_model' );
		$request_lodgings_table = 'request_lodgings';
		$request_requests_table = 'request_requests';
		$lodging_hotelletters_table = 'lodging_hotelletters';
		$query = 'request_requests.person_idworker, 
					request_lodgings.lodging_entrancedate, 
					request_lodgings.lodging_exitdate,
					lodging_edit.hotel_id,
					lodging_hotelletters.letter_id';
		$this->db->select ( $query );
		$this->db->from ( $request_requests_table );
		$this->db->join ( $request_lodgings_table, $request_lodgings_table. '.request_id = ' . $request_requests_table . '.request_id', 'inner' );
		$this->db->join ( self::TABLE_NAME, self::TABLE_NAME . '.request_id = ' . $request_lodgings_table . '.request_id', 'inner' );
		$this->db->join ( $lodging_hotelletters_table, $lodging_hotelletters_table . '.letter_id = ' . self::TABLE_NAME . '.letter_id', 'inner' );
		$this->db->where ( self::TABLE_NAME . '.letter_id', $letter_id );
		return $this->db->count_all_results ();
	}
	
}
?>
