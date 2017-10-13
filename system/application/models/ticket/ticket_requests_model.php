<?php

class Ticket_requests_model extends Model {
	const TABLE_NAME = 'ticket_requests';
	
	function __construct() {
		parent::__construct ();
	}
	
	/**
	 * Funcion que devuelve los valores de la tabla ticket_request
	 *
	 * @param int $hasta
	 * @param int $desde
	 * @return array
	 
	 
	 La funcion getdata coge los datos de  ticket_request para mostrar por ejemplo en el grid, para ello se cargan los model de las tablas de campos foraneos, luego el select coge todo lo 
	 de ticket_request mas los campos descriptivos de las de las tablas; fijarse bien en los join; luego en el foreach es para devolver en el arreglo los campos que se muestran, ej curioso nombre+lastname+secondlastname que se concatena en una variable	 para devolver el nombre completo de person_xxx en vez de person_id.
	 
		 */
	public function getData($to, $from) {
	    $this->load->model ( 'person/person_persons_model' );
		$this->load->model ( 'person/person_workers_model' ); //no me hace falta pues esta tabla es como herencia de person_persons, solo si devuelvo algun campo propio de person_worker
		
		$this->load->model ( 'conf/conf_motives_model' );
		$this->load->model ( 'conf/conf_costcenters_model' );
		$this->load->model ( 'conf/conf_tickettransports_model' );
		$this->load->model ( 'conf/conf_provinces_model' );
		/*notar que en el select no uso TABLE_NAME para no tener que estar concatenando*/
		$this->db->select ( 'ticket_requests.request_id, 
							ticket_requests.request_date, 
							ticket_requests.request_exitdate, 
							ticket_requests.request_returndate, 
							ticket_requests.request_lodging, 
							ticket_requests.request_details,
							ticket_requests.person_idrequestedby, 
							conf_costcenters.center_name,
							conf_tickettransports.transport_name,
							conf_motives.motive_name,
							ticket_requests.transport_itinerary,
							ticket_requests.person_idworker,
							ticket_requests.province_idfrom,
							ticket_requests.province_idto,
							ticket_requests.person_idlicensedby,
							ticket_requests.ticket_state');
											/*anteriormente se cogieron loscampos tal y como estan en la tabla ticket_request pero mas abajo se vera en este mismo metodo como en el arreglo se devuelve lo que se mostrara, para ello con los id anteriormente seleccionado de tablas foraneas se consulta a sus tablas nomencladoras para devolver campos descriptivos en ves del id, ej nombres completos, etc
				con nombres de personas no se hizo directamente en el select porque hay mas de 1 person_xxx, igula con provinces hay 2 la from y la provinceidto, pero com los simples si se hizo ej transport y motives y centroscostos*/
		$this->db->from ( self::TABLE_NAME);
		$this->db->join ( Conf_tickettransports_model::TABLE_NAME, Conf_tickettransports_model::TABLE_NAME . '.transport_id = ' . self::TABLE_NAME . '.transport_id', 'inner' );
		$this->db->join ( Conf_motives_model::TABLE_NAME, Conf_motives_model::TABLE_NAME . '.motive_id = ' . self::TABLE_NAME . '.motive_id', 'inner' );
		$this->db->join ( Conf_costcenters_model::TABLE_NAME, Conf_costcenters_model::TABLE_NAME. '.center_id = ' . self::TABLE_NAME . '.center_id', 'inner' );

//los join solo para campos que no se repiten la llave extarnjera con otra tabla, sino da error
		
	$this->db->limit ( $to, $from );
		$result = $this->db->get();
		$value=array();//unset($value); //para refresacar el model, pero no, no se lo pongo porque me produce una linea en blanco
		if ($result->result () != null) {
			foreach ( $result->result() as $row ) {
			  //de estos 3 campos buscamos con el id pues el nombre+apellido1+apellido2	
				$temp=$row->person_idworker;
				$person_worker = Person_persons_model::getNameById($temp);//ojo este metodo es diferente al de getById porque solo devuelve el nombre concatenado completo
				//quite el anterior porque de todasf formas person_workers es una tabla quye si se viene a ver hereda de person_persons
				$temp=$row->person_idlicensedby;
				$person_licensedby=Person_persons_model::getNameById($temp);
				$temp=$row->person_idrequestedby;
				$person_requestedby=Person_persons_model::getNameById($temp);
				//unos campos los cojo directamente ed las tablas otras relacionadas y otros no porque con el id busco mas datos como en los siguientes casos  con , provinciafrom y provinciato,etc
				$pidf =$row->province_idfrom;
				$pidt= $row->province_idto;
				$provincefrom=Conf_provinces_model::getNameById($pidf);
				$provinceto=Conf_provinces_model::getNameById($pidt);
				$value [] = array ('request_id' => $row->request_id, 'request_date' => $row->request_date, 'request_exitdate' => $row->request_exitdate, 'request_returndate' => $row->request_returndate, 'request_lodging' => $row->request_lodging, 'request_details' => $row->request_details,'person_requestedby' =>  $person_requestedby, 'center_name' => $row->center_name, 'transport_name' =>  $row->transport_name,'transport_itinerary' =>  $row->transport_itinerary, 'person_worker' =>  $person_worker, 'provincefrom' =>  $provincefrom, 'provinceto' =>  $provinceto, 'person_licensedby' =>  $person_licensedby ,   'motive_name' =>  $row->motive_name, 'ticket_state' =>  $row->ticket_state);
			}
		}
		return $value;
	} 
	/**
	 * funcion que devuelve la cantidad de registros en la tabla ejemplo
	 *
	 */
	public function getCant() {
		return $this->db->count_all (self::TABLE_NAME);
	}
	
	/**
	 * Esta es la funcion encargada de insertar los tickets
	 *
	 * @return boolean
	 */
	 //ver bien nombres que vienen por los post
	 //INSERT A LA VEZ MODIFICA, ES UNA FUNCION PARA TANTO INSERTAR MODIFICAR EN DEPENDENCIA DE LA OPCION SELECCIONADA
	public function insert() {
	//los datos del post son los del nombre de los componentes del formulario ojo he.. se coge del name (no del id)
		$request_id = $this->input->post ( 'request_id' );
		//$request ['request_id'] = $this->input->post ( 'request_id' ); no se modifica
		//arrgelar de nuevo y ver lo de now que es lo que iria aqui
		//$request ['request_date'] = now(); //$this->input->post ( 'request_date' );
		$request ['request_date'] = $this->input->post ( 'request_exitdate' );
		$request ['request_exitdate'] = $this->input->post ( 'request_exitdate' );
		$request ['request_returndate'] = $this->input->post ( 'request_returndate' );
		//ver como rayos cojo el valor true o false del post de un checkbox
		if ($this->input->post ( 'request_lodging' ) != null)
			$request ['request_lodging'] = 'TRUE';
		else//$this->input->post ( 'request_lodging' );
			$request ['request_lodging'] = 'FALSE';
		$request ['request_details'] = $this->input->post ( 'request_details' );
		//ver como cojo este
		$request ['person_idrequestedby'] = 13;//no porque se coje del centinela $this->input->post ( 'person_idrequestedby' );
		$request ['center_id'] = $this->input->post ( 'center_id' );
		$request ['transport_id'] =$this->input->post ( 'transport_id' );
		$request ['transport_itinerary'] = $this->input->post ( 'transport_itinerary' );
        //ver bien
		if ($request ['transport_id'] !=1)
			$request ['transport_itinerary']='';
		$request ['person_idworker'] = $this->input->post ( 'person_idworker' );
		$request ['province_idfrom'] = $this->input->post ( 'province_idfrom' );
		$request ['province_idto'] = $this->input->post ( 'province_idto' );
		//ver como cojo este, es del centinela igual
		//$request ['person_idlicensedby'] = $this->input->post ( 'person_idlicensedby' );
		$request ['person_idlicensedby'] = 13;
		$request ['motive_id'] = $this->input->post ( 'motive_id' );
		//ver comopongo este
		$request ['ticket_state'] = 0;//no, este no viene porque s epone solo en dependencia de si se borra en el delete o modifica o adiciona aqui $this->input->post ( 'ticket_state' );
		if (empty ( $request_id )) {/*viene vacio porque es para insertar*/
			$this->db->trans_begin ();
			$re = $this->db->insert ( self::TABLE_NAME, $request);
			$logs = new Logs ( );
			$myquery = $logs->sqlinsert ( self::TABLE_NAME, $request );
			$logs->write ( self::TABLE_NAME, 'INSERT', $myquery );
			
			if ($this->db->trans_status () === FALSE) {
				$this->db->trans_rollback ();
			} else {
				$this->db->trans_commit ();
			}
			if ($re == true)
				return "true"; else
				return "false";
		} else {/*viene lleno el id porque es para modificar*/
			$this->db->where ( 'request_id', $request_id );
			$this->db->trans_begin ();
			$re = $this->db->update ( self::TABLE_NAME, $request );
			$logs = new Logs ( );
			
			$mywhere = 'where request_id = '.$request_id;
			$myquery = $logs->sqlupdate(self::TABLE_NAME, $request, $mywhere);
			
			$logs->write( self::TABLE_NAME, 'UPDATE', $myquery);
			
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
	 * Funcion para eliminar un ticket_request por su nombre
	 *
	 * @param string $request_id
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
	
	/**
	 * 
	 */
	/*   esto seria en tablas de nomencladores  
	public function getByName($service_name) {
		$result = $this->db->get_where ( self::TABLE_NAME, array ('service_name' => $service_name ) );
		return $result->result_array ();
	}
	*/
	public function get() {
		$result = $this->db->get ( self::TABLE_NAME );
		return $result->result_array;
	} // de la funcion

	public function getById($request_id) {
		$this->db->select ('ticket_requests.request_id, 
							ticket_requests.request_date, 
							ticket_requests.request_exitdate, 
							ticket_requests.request_returndate, 
							ticket_requests.request_lodging, 
							ticket_requests.request_details,
							ticket_requests.person_idrequestedby, 
							ticket_requests.center_id, 
							ticket_requests.transport_id,
							ticket_requests.transport_itinerary,
							ticket_requests.person_idworker,
							ticket_requests.province_idfrom,
							ticket_requests.province_idto,
							ticket_requests.person_idlicensedby,
							ticket_requests.motive_id,		
							ticket_requests.ticket_state');
		$this->db->from ( self::TABLE_NAME );
		$this->db->where ('request_id', $request_id );
		$result = $this->db->get();
		$value=array();//si pongo esto se me jode la cosa no se porque
		if ($result->result () != null) {
			foreach ( $result->result () as $row ) {
				
				$value [] = array ('request_id' => $row->request_id, 'request_date' => $row->request_date, 'request_exitdate' => $row->request_exitdate,'request_returndate' => $row->request_returndate, 'request_lodging' => $row->request_lodging, 'request_details' => $row->request_details, 'person_idrequestedby' =>  $row->person_idrequestedby, 'center_id' =>  $row->center_id,'transport_id' =>  $row->transport_id,'transport_itinerary' =>  $row->transport_itinerary,'person_idworker' =>  $row->person_idworker,'province_idfrom' => $row->province_idfrom,'province_idto' =>  $row->province_idto,'person_idlicensedby' =>  $row->person_idlicensedby,'motive_id' =>  $row->motive_id,'ticket_state' =>  $row->ticket_state);
			}
		
		}
		
		return $value;
	}
	//ahora para reporte, pude haberlo hecho en el mismo getData pero para separar  y mejor entendimeinto lo hago aparte
	public function getDataConditional($to, $from, $datosForWheres/*que es un arreglo*/ ) {
	    $this->load->model ( 'person/person_persons_model' );
		$this->load->model ( 'person/person_workers_model' ); //no me hace falta pues esta tabla es como herencia de person_persons, solo si devuelvo algun campo propio de person_worker
		$this->load->model ( 'conf/conf_motives_model' );
		$this->load->model ( 'conf/conf_costcenters_model' );
		$this->load->model ( 'conf/conf_tickettransports_model' );
		$this->load->model ( 'conf/conf_provinces_model' );
		/*notar que en el select no uso TABLE_NAME para no tener que estar concatenando*/
		$this->db->select ( 'ticket_requests.request_id, 
							ticket_requests.request_date, 
							ticket_requests.request_exitdate, 
							ticket_requests.request_returndate, 
							ticket_requests.request_lodging, 
							ticket_requests.request_details,
							ticket_requests.person_idrequestedby, 
							conf_costcenters.center_name,
							conf_tickettransports.transport_name,
							conf_motives.motive_name,
							ticket_requests.transport_itinerary,
							ticket_requests.person_idworker,
							ticket_requests.province_idfrom,
							ticket_requests.province_idto,
							ticket_requests.person_idlicensedby,
							ticket_requests.ticket_state');
				/*anteriormente se cogieron loscampos tal y como estan en la tabla ticket_request pero mas abajo se vera en este mismo metodo como en el arreglo se devuelve lo que se mostrara, para ello con los id anteriormente seleccionado de tablas foraneas se consulta a sus tablas nomencladoras para devolver campos descriptivos en ves del id, ej nombres completos, etc
				con nombres de personas no se hizo directamente en el select porque hay mas de 1 person_xxx, igula con provinces hay 2 la from y la provinceidto, pero com los simples si se hizo ej transport y motives y centroscostos*/
		$this->db->from ( self::TABLE_NAME);
		$this->db->join ( Conf_tickettransports_model::TABLE_NAME, Conf_tickettransports_model::TABLE_NAME . '.transport_id = ' . self::TABLE_NAME . '.transport_id', 'inner' );
		$this->db->join ( Conf_motives_model::TABLE_NAME, Conf_motives_model::TABLE_NAME . '.motive_id = ' . self::TABLE_NAME . '.motive_id', 'inner' );
		$this->db->join ( Conf_costcenters_model::TABLE_NAME, Conf_costcenters_model::TABLE_NAME. '.center_id = ' . self::TABLE_NAME . '.center_id', 'inner' );
		//los join solo para campos que no se repiten la llave extarnjera con otra tabla, sino da error
		//ahora los where 
		if (! empty($datosForWheres['motive_id']))
			$this->db->where (self::TABLE_NAME.'.motive_id ' ,$datosForWheres['motive_id']);
		if (! empty($datosForWheres['province_idfrom']))
			$this->db->where (self::TABLE_NAME.'.province_idfrom ' ,$datosForWheres['province_idfrom']);
		if (! empty($datosForWheres['province_idto']))
			$this->db->where (self::TABLE_NAME.'.province_idto ' ,$datosForWheres['province_idto']);
		if (! empty($datosForWheres['transport_id']))
			$this->db->where (self::TABLE_NAME.'.transport_id ' ,$datosForWheres['transport_id']);
		if (! empty($datosForWheres['transport_itinerary']))
			$this->db->where (self::TABLE_NAME.'.transport_itinerary ' ,$datosForWheres['transport_itinerary']);
		/*if (! empty($datosForWheres['request_exitdate']))
		$this->db->where (self::TABLE_NAME.'.request_exitdate ' ,$datosForWheres['request_exitdate']);
		if (! empty($datosForWheres['request_returndate']))
		$this->db->where (self::TABLE_NAME.'.request_returndate ' ,$datosForWheres['request_returndate']);
		*/
		$this->db->limit ( $to, $from );
		$result = $this->db->get();
		$value=array();//unset($value); //para refresacar el model, pero no, no se lo pongo porque me produce una linea en blanco
		if ($result->result () != null) {
			foreach ( $result->result() as $row ) {
			  //de estos 3 campos buscamos con el id pues el nombre+apellido1+apellido2	
				$temp=$row->person_idworker;
				$person_worker = Person_persons_model::getNameById($temp);//ojo este metodo es diferente al de getById porque solo devuelve el nombre concatenado completo
				//quite el anterior porque de todasf formas person_workers es una tabla quye si se viene a ver hereda de person_persons
				$temp=$row->person_idlicensedby;
				$person_licensedby=Person_persons_model::getNameById($temp);
				$temp=$row->person_idrequestedby;
				$person_requestedby=Person_persons_model::getNameById($temp);
				//unos campos los cojo directamente ed las tablas otras relacionadas y otros no porque con el id busco mas datos como en los siguientes casos  con , provinciafrom y provinciato,etc
				$pidf =$row->province_idfrom;
				$pidt= $row->province_idto;
				$provincefrom=Conf_provinces_model::getNameById($pidf);
				$provinceto=Conf_provinces_model::getNameById($pidt);
				$value [] = array ('request_id' => $row->request_id, 'request_date' => $row->request_date, 'request_exitdate' => $row->request_exitdate,	'request_returndate' => $row->request_returndate, 'request_lodging' => $row->request_lodging, 'request_details' => $row->request_details,'person_requestedby' =>  $person_requestedby, 'center_name' => $row->center_name, 'transport_name' =>  $row->transport_name,'transport_itinerary' =>  $row->transport_itinerary, 'person_worker' =>  $person_worker, 'provincefrom' =>  $provincefrom, 'provinceto' =>  $provinceto, 'person_licensedby' =>  $person_licensedby ,   'motive_name' =>  $row->motive_name, 'ticket_state' =>  $row->ticket_state);
			}
		}
		return $value;
	} 
}
?>