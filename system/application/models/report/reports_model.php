<?php

class Reports_model extends Model
{

    function __construct()
    {
        parent::__construct();
    }

    public function getDataReportInternalTicket($to, $from, $datosForWheres, $isPdf = 'no')
    {

        $this->load->model('request/request_requests_model');
        $this->load->model('request/request_tickets_model');
        $this->load->model('ticket/ticket_editetecsa_model');
        $this->load->model('ticket/ticket_editeventual_model');
        $this->load->model('ticket/ticket_editviazul_model');
        $this->load->model('conf/conf_motives_model');
        $this->load->model('conf/conf_provinces_model');
        $this->load->model('conf/conf_costcenters_model');
        $this->load->model('person/person_persons_model');
        $this->load->model('conf/conf_ticketrequeststates_model');
        $this->load->model('conf/conf_ticketviazulstates_model'); //aunque no hace falta aun

        $this->db->select('request_requests.request_id,
							person_persons.person_id,
							request_tickets.ticket_date,
							request_tickets.province_idfrom,
							request_tickets.province_idto,
							request_requests.request_details,
							request_requests.motive_id,
							request_requests.center_id');
        $this->db->from('person_persons');
        $this->db->join(Request_requests_model::TABLE_NAME, Request_requests_model::TABLE_NAME . '.person_idworker = person_persons.person_id', 'inner');
        $this->db->join('request_tickets', 'request_tickets.request_id=' . Request_requests_model::TABLE_NAME . '.request_id', 'inner');
        $this->db->join('ticket_editetecsa', 'ticket_editetecsa.request_id = request_tickets.request_id and request_tickets.ticket_date = ticket_editetecsa.ticket_date', 'left');
        $this->db->join('ticket_editeventual', 'ticket_editeventual.request_id = request_tickets.request_id and request_tickets.ticket_date = ticket_editeventual.ticket_date', 'left');
        $this->db->join('ticket_editviazul', 'ticket_editviazul.request_id = request_tickets.request_id and request_tickets.ticket_date = ticket_editviazul.ticket_date', 'left');
        $this->db->join('ticket_editship', 'ticket_editship.request_id = request_tickets.request_id and request_tickets.ticket_date = ticket_editship.ticket_date', 'left');
        $this->db->join('ticket_editairplane', 'ticket_editairplane.request_id = request_tickets.request_id and request_tickets.ticket_date = ticket_editairplane.ticket_date', 'left');
        //los left anteriores son obligados para que si no hay inner en alguna de las 3 tablas deje el resto de los valores de la tabla que si tiene valores
        if (!empty ($datosForWheres ['province_idto']))
            $this->db->where('request_tickets.province_idto', $datosForWheres ['province_idto']);
        if (!empty ($datosForWheres ['province_idfrom']))
            $this->db->where('request_tickets.province_idfrom', $datosForWheres ['province_idfrom']);
        if (!empty ($datosForWheres ['motive_id']))
            $this->db->where('request_requests.motive_id', $datosForWheres ['motive_id']);
        if (!empty ($datosForWheres ['center_id']))
            $this->db->where('request_requests.center_id', $datosForWheres ['center_id']);
        if (!empty ($datosForWheres ['begindate'])) {
            if ($isPdf == 'no')
                $this->db->where('request_tickets.ticket_date >=', $datosForWheres ['begindate']);
            else
                $this->db->where('request_tickets.ticket_date >=', substr($datosForWheres ['begindate'], 0, 4) . '-' . substr($datosForWheres ['begindate'], 5, 2) . '-' . substr($datosForWheres ['begindate'], 8, 2));
        }
        if (!empty ($datosForWheres ['enddate'])) {
            if ($isPdf == 'no')
                $this->db->where('request_tickets.ticket_date <=', $datosForWheres ['enddate']);
            else
                $this->db->where('request_tickets.ticket_date <=', substr($datosForWheres ['enddate'], 0, 4) . '-' . substr($datosForWheres ['enddate'], 5, 2) . '-' . substr($datosForWheres ['enddate'], 8, 2));
        }
        if (!empty ($datosForWheres ['person_id'])) {
            $this->db->where('person_persons.person_id', $datosForWheres ['person_id']);
        } else if (!empty ($datosForWheres ['province_idworkers'])) {
            $this->db->where('person_persons.province_id', $datosForWheres ['province_idworkers']);
        }
        if (!empty ($datosForWheres ['person_identity']))
            $this->db->where('person_persons.person_identity', $datosForWheres ['person_identity']);

        $miclausula = '( ((ticket_editetecsa.state_id != 7) and (ticket_editetecsa.state_id != 8)) or ((ticket_editeventual.state_id != 7) and (ticket_editeventual.state_id != 8)) or (ticket_editviazul.viazulstate_id=1) or (ticket_editship.state_id=1) or (ticket_editairplane.state_id=1))';
        $this->db->where($miclausula); //se ve en tabla conf_ticketrequeststates <>7y de 8,  y la de ticketviazulstates=1
        $this->db->where('request_tickets.ticket_state', 1); //realmente no hace falta porque si los 3 left de los join no dan resultado al menos 1 entonces no sale como resultado la fila, pero por si acaso
        if (!empty ($datosForWheres ['transport_id']))
            $this->db->where('request_tickets.transport_id', $datosForWheres ['transport_id']);
        if (!empty ($datosForWheres ['transport_itinerary']))
            $this->db->where('request_tickets.transport_itinerary', $datosForWheres ['transport_itinerary']);
        $cant = $this->db->count_all_results(); //para coger la cantidad real de filas de la consulta filtrada sin from-limit
        //ya no cargo los model de nuevo
        $this->db->select('request_requests.request_id,person_persons.person_id,request_tickets.ticket_date,request_tickets.province_idfrom,request_tickets.province_idto,request_requests.request_details,request_requests.motive_id,request_requests.center_id');
        $this->db->from('person_persons');
        $this->db->join(Request_requests_model::TABLE_NAME, Request_requests_model::TABLE_NAME . '.person_idworker = person_persons.person_id', 'inner');
        $this->db->join('request_tickets', 'request_tickets.request_id=' . Request_requests_model::TABLE_NAME . '.request_id', 'inner');
        $this->db->join('ticket_editetecsa', 'ticket_editetecsa.request_id = request_tickets.request_id and request_tickets.ticket_date = ticket_editetecsa.ticket_date', 'left');
        $this->db->join('ticket_editeventual', 'ticket_editeventual.request_id = request_tickets.request_id and request_tickets.ticket_date = ticket_editeventual.ticket_date', 'left');
        $this->db->join('ticket_editviazul', 'ticket_editviazul.request_id = request_tickets.request_id and request_tickets.ticket_date = ticket_editviazul.ticket_date', 'left');
        $this->db->join('ticket_editship', 'ticket_editship.request_id = request_tickets.request_id and request_tickets.ticket_date = ticket_editship.ticket_date', 'left');
        $this->db->join('ticket_editairplane', 'ticket_editairplane.request_id = request_tickets.request_id and request_tickets.ticket_date = ticket_editairplane.ticket_date', 'left');

        if (!empty ($datosForWheres ['province_idto']))
            $this->db->where('request_tickets.province_idto', $datosForWheres ['province_idto']);

        if (!empty ($datosForWheres ['province_idfrom']))
            $this->db->where('request_tickets.province_idfrom', $datosForWheres ['province_idfrom']);

        if (!empty ($datosForWheres ['motive_id']))
            $this->db->where('request_requests.motive_id', $datosForWheres ['motive_id']);
        if (!empty ($datosForWheres ['center_id']))
            $this->db->where('request_requests.center_id', $datosForWheres ['center_id']);

        if (!empty ($datosForWheres ['begindate'])) {
            if ($isPdf == 'no') //viene el dateFormat de los componentes de fecha  como Y-m-d,pero sino viene standarsubmit y el componente tira d-m-Y
                $this->db->where('request_tickets.ticket_date >=', $datosForWheres ['begindate']);
            else
                $this->db->where('request_tickets.ticket_date >=', substr($datosForWheres ['begindate'], 0, 4) . '-' . substr($datosForWheres ['begindate'], 5, 2) . '-' . substr($datosForWheres ['begindate'], 8, 2));
        }

        if (!empty ($datosForWheres ['enddate'])) {
            if ($isPdf == 'no')
                $this->db->where('request_tickets.ticket_date <=', $datosForWheres ['enddate']);
            else
                $this->db->where('request_tickets.ticket_date <=', substr($datosForWheres ['enddate'], 0, 4) . '-' . substr($datosForWheres ['enddate'], 5, 2) . '-' . substr($datosForWheres ['enddate'], 8, 2));
        }

        if (!empty ($datosForWheres ['person_id'])) {
            $this->db->where('person_persons.person_id', $datosForWheres ['person_id']);
        } else if (!empty ($datosForWheres ['province_idworkers'])) {
            $this->db->where('person_persons.province_id', $datosForWheres ['province_idworkers']);
        }

        if (!empty ($datosForWheres ['person_identity']))
            $this->db->where('person_persons.person_identity', $datosForWheres ['person_identity']);

        $this->db->where('request_tickets.ticket_state', 1); //realmente no hace falta porque si los 3 left de los join no dan resultado al menos 1 entonces no sale como resultado la fila, pero por si acaso


        $miclausula = '( ((ticket_editetecsa.state_id != 7) and (ticket_editetecsa.state_id != 8)) or ((ticket_editeventual.state_id != 7) and (ticket_editeventual.state_id != 8)) or (ticket_editviazul.viazulstate_id=1) or (ticket_editship.state_id=1) or (ticket_editairplane.state_id=1))';
        $this->db->where($miclausula); //se ve en tabla conf_ticketrequeststates 7y8 no, la de viazul = 1 y etecsa y eventuales <>7 y de 8

        if (!empty ($datosForWheres ['transport_id']))
            $this->db->where('request_tickets.transport_id', $datosForWheres ['transport_id']);

        if (!empty ($datosForWheres ['transport_itinerary']))
            $this->db->where('request_tickets.transport_itinerary', $datosForWheres ['transport_itinerary']);
        $this->db->limit($to, $from);
        $this->db->orderby('ticket_date', 'desc');

        $result = $this->db->get();
        //
        $value = array(); //unset($value);
        if ($result->result() != null) {
            foreach ($result->result() as $row) {
                $provinceFrom = Conf_provinces_model::getNameById($row->province_idfrom);
                $provinceTo = Conf_provinces_model::getNameById($row->province_idto);
                $person = Person_persons_model::getNameById($row->person_id);
                $identity = Person_persons_model::getIdentityById($row->person_id);
                $motive = Conf_motives_model::getNameById($row->motive_id);
                $center = Conf_costcenters_model::getNameById($row->center_id);
                $estado = 'Viazul'; //por defecto esta en alguna de las 3 tablas si esta editado como es el caso de nuestro filtro anterior, asumimos por defecto en viazul
                $state_id = 0;
                $res = $this->db->query('select
												ticket_editetecsa.state_id 
											from ticket_editetecsa 
											where 
												ticket_editetecsa.request_id=' . $row->request_id . ' and ticket_editetecsa.ticket_date=\'' . $row->ticket_date . ' \'');
                if ($res->result() != null) {
                    foreach ($res->result() as $row2) { //esta en esta tabla
                        $state_id = $row2->state_id;
                    }
                    $estado = Conf_ticketrequeststates_model::getNameById($state_id);
                }
                //
                $res = $this->db->query('select
												ticket_editeventual.state_id 
											from ticket_editeventual 
											where 
												ticket_editeventual.request_id=' . $row->request_id . ' and ticket_editeventual.ticket_date=\'' . $row->ticket_date . ' \'');
                if ($res->result() != null) {
                    foreach ($res->result() as $row2) { //esta en esta tabla
                        $state_id = $row2->state_id;
                    }
                    $estado = Conf_ticketrequeststates_model::getNameById($state_id);
                }
                //BARCO
                $res = $this->db->query('select
												ticket_editship.state_id 
											from ticket_editship 
											where 
												ticket_editship.request_id=' . $row->request_id . ' and ticket_editship.ticket_date=\'' . $row->ticket_date . ' \'');
                if ($res->result() != null) {
                    foreach ($res->result() as $row2) { //esta en esta tabla
                        $estado = 'Barco';
                    }
                }

                //AVION
                $res = $this->db->query('select
												ticket_editairplane.state_id 
											from ticket_editairplane 
											where 
												ticket_editairplane.request_id=' . $row->request_id . ' and ticket_editairplane.ticket_date=\'' . $row->ticket_date . ' \'');
                if ($res->result() != null) {
                    foreach ($res->result() as $row2) { //esta en esta tabla
                        $estado = 'Avion';
                    }
                }

                $value [] = array('person' => $person,
                    'identity' => $identity,
                    'ticket_date' => $row->ticket_date,
                    'provinceFrom' => $provinceFrom,
                    'provinceTo' => $provinceTo,
                    'motive' => $motive,
                    'details' => $row->request_details,
                    'center' => $center,
                    'estado' => $estado);
            }
        } //end if result != null antes del foreach

        if ($isPdf == 'si') { //devuelve todos por exceso
            return $value;
        } else { //el cant es filtrado
            echo("{count : " . $cant . ", data : " . json_encode($value) . "}"); // este valor se le asigna al return del setDatagridconditional para pasarle
        }

    }

    //
    public function getDataReportInternalLodging($to, $from, $datosForWheres, $isPdf = 'no')
    {
        $this->load->model('request/request_requests_model');
        $this->load->model('request/request_lodgings_model');
        $this->load->model('lodging/lodging_edit_model');
        $this->load->model('conf/conf_provinces_model');
        $this->load->model('person/person_persons_model');
        $this->load->model('conf/conf_motives_model');
        $this->load->model('conf/conf_hotels_model');
        $this->load->model('conf/conf_costcenters_model');

        $this->db->select(' request_requests.request_id,
							person_persons.person_id,
							request_lodgings.lodging_entrancedate,
							request_lodgings.lodging_exitdate,
							request_lodgings.province_idlodging,
							request_requests.request_details,
							request_requests.motive_id,
							request_requests.center_id,
							lodging_edit.hotel_id');
        $this->db->from('person_persons');
        $this->db->join(Request_requests_model::TABLE_NAME, Request_requests_model::TABLE_NAME . '.person_idworker = person_persons.person_id', 'inner');
        $this->db->join('request_lodgings', 'request_lodgings.request_id=' . Request_requests_model::TABLE_NAME . '.request_id', 'inner');
        $this->db->join('lodging_edit', 'lodging_edit' . '.request_id = ' . 'request_lodgings' . '.request_id', 'inner');
        $this->db->join('conf_hotels', 'conf_hotels.hotel_id = ' . 'lodging_edit.hotel_id', 'inner');
        $this->db->where('lodging_edit.lodging_noshow !=', 'on'); //se ve en tabla lodgin_edit, o sea el hospedaje se llevo a cabo/*se edito pero no se uso el hospedaje, que es != a no editado, podria ser que estuviera request-lodging.lodging_state en 1 pero el on en true que dice que se edito pero no se uso, y esto si le hace falta al reporte*/
        $this->db->where('request_lodgings.lodging_state', 1/*editado*/); //realmente no hace falta porque al buscar quizas por error en lodging_edit si esta el noshow!=.on lo muestra igual, pero por si acaso
        $this->db->where('request_lodgings.lodging_canceled !=', 1/*cancelado*/);
        if (!empty ($datosForWheres ['province_idlodging']))
            $this->db->where('request_lodgings.province_idlodging', $datosForWheres ['province_idlodging']);
        if (!empty ($datosForWheres ['hotel_id']))
            $this->db->where('lodging_edit.hotel_id', $datosForWheres ['hotel_id']);
        if (!empty ($datosForWheres ['motive_id']))
            $this->db->where('request_requests.motive_id', $datosForWheres ['motive_id']);
        if (!empty ($datosForWheres ['center_id']))
            $this->db->where('request_requests.center_id', $datosForWheres ['center_id']);
        if (!empty ($datosForWheres ['begindate'])) {
            if ($isPdf == 'no') //viene el dateFormat de los componentes de fecha  como Y-m-d,pero sino viene standarsubmit y el componente tira d-m-Y
                $this->db->where('request_lodgings.lodging_entrancedate >=', $datosForWheres ['begindate']);
            else
                $this->db->where('request_lodgings.lodging_entrancedate >=', substr($datosForWheres ['begindate'], 0, 4) . '-' . substr($datosForWheres ['begindate'], 5, 2) . '-' . substr($datosForWheres ['begindate'], 8, 2));
        }
        if (!empty ($datosForWheres ['enddate'])) {
            if ($isPdf == 'no')
                $this->db->where('request_lodgings.lodging_entrancedate <=', $datosForWheres ['enddate']);
            else
                $this->db->where('request_lodgings.lodging_entrancedate <=', substr($datosForWheres ['enddate'], 0, 4) . '-' . substr($datosForWheres ['enddate'], 5, 2) . '-' . substr($datosForWheres ['enddate'], 8, 2));
        }
        if (!empty ($datosForWheres ['person_id'])) {
            $this->db->where('person_persons.person_id', $datosForWheres ['person_id']);
        } else if (!empty ($datosForWheres ['province_idworkers'])) {
            $this->db->where('person_persons.province_id', $datosForWheres ['province_idworkers']);
        }
        if (!empty ($datosForWheres ['person_identity']))
            $this->db->where('person_persons.person_identity', $datosForWheres ['person_identity']);

        if (!empty($datosForWheres ['reinforce'])) {
            if ($datosForWheres ['reinforce'] == 'Si')
                $this->db->where('lodging_edit.lodging_reinforceddiet', "on");
            else
                $this->db->where('lodging_edit.lodging_reinforceddiet !=', "on");
        }
        if (!empty ($datosForWheres ['chain_id']))
            $this->db->where('conf_hotels.chain_id', $datosForWheres ['chain_id']);
        //
        $cant = $this->db->count_all_results(); //para coger la cantidad real de filas de la consulta filtrada sin from-limit
        //ya no cargo los model de nuevo
        $this->db->select('request_requests.request_id,
							person_persons.person_id,
							request_lodgings.lodging_entrancedate,
							request_lodgings.lodging_exitdate,
							request_lodgings.province_idlodging,
							request_requests.request_details,
							request_requests.motive_id,
							request_requests.center_id,
							lodging_edit.hotel_id');
        $this->db->from('person_persons');
        $this->db->join(Request_requests_model::TABLE_NAME, Request_requests_model::TABLE_NAME . '.person_idworker = person_persons.person_id', 'inner');
        $this->db->join('request_lodgings', 'request_lodgings.request_id=' . Request_requests_model::TABLE_NAME . '.request_id', 'inner');
        $this->db->join('lodging_edit', 'lodging_edit' . '.request_id = ' . 'request_lodgings' . '.request_id', 'inner');
        $this->db->join('conf_hotels', 'conf_hotels.hotel_id = ' . 'lodging_edit.hotel_id', 'inner');
        $this->db->where('lodging_edit.lodging_noshow !=', 'on'); //se ve en tabla lodgin_edit, o sea el hospedaje se llevo a cabo/*se edito pero no se uso el hospedaje, que es != a no editado, podria ser que estuviera request-lodging.lodging_state en 1 pero el on en true que dice que se edito pero no se uso, y esto si le hace falta al reporte*/
        $this->db->where('request_lodgings.lodging_state', 1/*editado*/); //realmente no hace falta porque al buscar quizas por error en lodging_edit si esta el noshow!=.on lo muestra igual, pero por si acaso
        $this->db->where('request_lodgings.lodging_canceled !=', 1/*cancelado*/);
        if (!empty ($datosForWheres ['province_idlodging']))
            $this->db->where('request_lodgings.province_idlodging', $datosForWheres ['province_idlodging']);
        if (!empty ($datosForWheres ['hotel_id']))
            $this->db->where('lodging_edit.hotel_id', $datosForWheres ['hotel_id']);
        if (!empty ($datosForWheres ['motive_id']))
            $this->db->where('request_requests.motive_id', $datosForWheres ['motive_id']);
        if (!empty ($datosForWheres ['center_id']))
            $this->db->where('request_requests.center_id', $datosForWheres ['center_id']);
        if (!empty ($datosForWheres ['begindate'])) {
            if ($isPdf == 'no') //viene el dateFormat de los componentes de fecha  como Y-m-d,pero sino viene standarsubmit y el componente tira d-m-Y
                $this->db->where('request_lodgings.lodging_entrancedate >=', $datosForWheres ['begindate']);
            else
                $this->db->where('request_lodgings.lodging_entrancedate >=', substr($datosForWheres ['begindate'], 0, 4) . '-' . substr($datosForWheres ['begindate'], 5, 2) . '-' . substr($datosForWheres ['begindate'], 8, 2));
        }
        if (!empty ($datosForWheres ['enddate'])) {
            if ($isPdf == 'no')
                $this->db->where('request_lodgings.lodging_entrancedate <=', $datosForWheres ['enddate']);
            else
                $this->db->where('request_lodgings.lodging_entrancedate <=', substr($datosForWheres ['enddate'], 0, 4) . '-' . substr($datosForWheres ['enddate'], 5, 2) . '-' . substr($datosForWheres ['enddate'], 8, 2));
        }
        if (!empty ($datosForWheres ['person_id'])) {
            $this->db->where('person_persons.person_id', $datosForWheres ['person_id']);
        } else if (!empty ($datosForWheres ['province_idworkers'])) {
            $this->db->where('person_persons.province_id', $datosForWheres ['province_idworkers']);
        }
        if (!empty ($datosForWheres ['person_identity']))
            $this->db->where('person_persons.person_identity', $datosForWheres ['person_identity']);

        if (!empty($datosForWheres ['reinforce'])) {
            if ($datosForWheres ['reinforce'] == 'Si')
                $this->db->where('lodging_edit.lodging_reinforceddiet', "on");
            else
                $this->db->where('lodging_edit.lodging_reinforceddiet !=', "on");
        }
        if (!empty ($datosForWheres ['chain_id']))
            $this->db->where('conf_hotels.chain_id', $datosForWheres ['chain_id']);
        $this->db->limit($to, $from);
        $this->db->orderby('lodging_edit.hotel_id', 'desc');
        $result = $this->db->get();
        //
        $value = array(); //unset($value);
        if ($result->result() != null) {
            foreach ($result->result() as $row) {
                $provinceLodging = Conf_provinces_model::getNameById($row->province_idlodging);
                $person = Person_persons_model::getNameById($row->person_id);
                $identity = Person_persons_model::getIdentityById($row->person_id);
                $motive = Conf_motives_model::getNameById($row->motive_id);
                $center = Conf_costcenters_model::getNameById($row->center_id);
                $hotel = Conf_hotels_model::getNameById($row->hotel_id);
                $value [] = array('person' => $person,
                    'identity' => $identity,
                    'lodging_entrancedate' => $row->lodging_entrancedate,
                    'lodging_exitdate' => $row->lodging_exitdate,
                    'provinceLodging' => $provinceLodging,
                    'motive' => $motive,
                    'details' => $row->request_details,
                    'center' => $center,
                    'hotel' => $hotel);

            }
        } //end if result != null antes del foreach
        if ($isPdf == 'si') { //devuelve todos por exceso
            return $value;
        } else { //el cant es filtrado
            echo("{count : " . $cant . ", data : " . json_encode($value) . "}"); // este valor se le asigna al return del setDatagridconditional para pasarle
        }
    }

    //
    public function getDataReportTicket($to, $from, $datosForWheres, $isPdf = 'no')
    {
        $this->load->model('request/request_requests_model');
        $this->load->model('request/request_lodgings_model');
        $this->load->model('lodging/lodging_edit_model');
        $this->load->model('conf/conf_provinces_model');
        $this->load->model('person/person_persons_model');
        $this->load->model('conf/conf_motives_model');
        $this->load->model('conf/conf_hotels_model');
        $this->load->model('conf/conf_lodgingtransports_model');
        $this->load->model('conf/conf_ticketrequeststates_model');
        $this->load->model('conf/conf_ticketviazulstates_model');
        $this->load->model('request/request_tickets_model');
        $this->load->model('ticket/ticket_editetecsa_model');
        $this->load->model('ticket/ticket_editeventual_model');
        $this->load->model('ticket/ticket_editviazul_model');
        $this->db->select('request_requests.request_id,
							person_persons.person_id,
							request_tickets.ticket_date,
							request_tickets.province_idfrom,
							request_tickets.province_idto,
							request_requests.motive_id,
							request_tickets.transport_id,
							request_tickets.ticket_state,
							request_tickets.ticket_cancel'); //este ultimo campo para ver si esta editada ono, y sino lo esta evitar abajo en el for la busqueda en los ticketeditXXX
        $this->db->from('person_persons');
        $this->db->join(Request_requests_model::TABLE_NAME, Request_requests_model::TABLE_NAME . '.person_idworker = person_persons.person_id', 'inner');
        $this->db->join('request_tickets', 'request_tickets.request_id=' . Request_requests_model::TABLE_NAME . '.request_id', 'inner');

        if (!empty ($datosForWheres ['transport_id']))
            $this->db->where('request_tickets.transport_id', $datosForWheres ['transport_id']);

        if (!empty ($datosForWheres ['province_idto']))
            $this->db->where('request_tickets.province_idto', $datosForWheres ['province_idto']);

        if (!empty ($datosForWheres ['province_idfrom']))
            $this->db->where('request_tickets.province_idfrom', $datosForWheres ['province_idfrom']);

        if (!empty ($datosForWheres ['motive_id']))
            $this->db->where('request_requests.motive_id', $datosForWheres ['motive_id']);

        if (!empty ($datosForWheres ['begindate']))
            $this->db->where('request_tickets.ticket_date >=', $datosForWheres ['begindate']);

        if (!empty ($datosForWheres ['enddate']))
            $this->db->where('request_tickets.ticket_date <=', $datosForWheres ['enddate']);

        if (!empty ($datosForWheres ['person_id'])) {
            $this->db->where('person_persons.person_id', $datosForWheres ['person_id']);
        } else if (!empty ($datosForWheres ['province_idworkers'])) {
            $this->db->where('person_persons.province_id', $datosForWheres ['province_idworkers']);
        }
        if (!empty ($datosForWheres ['person_identity']))
            $this->db->where('person_persons.person_identity', $datosForWheres ['person_identity']);

        if (!empty ($datosForWheres ['transport_id']))
            $this->db->where('request_tickets.transport_id', $datosForWheres ['transport_id']);
        if (!empty ($datosForWheres ['transport_itinerary']))
            $this->db->where('request_tickets.transport_itinerary', $datosForWheres ['transport_itinerary']);
        //
        $cant = $this->db->count_all_results(); //para coger la cantidad real de filas de la consulta filtrada sin from-limit
        //ya no cargo los model de nuevo
        $this->db->select('request_requests.request_id,
							person_persons.person_id,
							request_tickets.ticket_date,
							request_tickets.province_idfrom,
							request_tickets.province_idto,
							request_requests.motive_id,
							request_tickets.transport_id,
							request_tickets.ticket_state,
							request_tickets.ticket_cancel'); //este ultimo campo para ver si esta editada ono, y sino lo esta evitar abajo en el for la busqueda en los ticketeditXXX
        $this->db->from('person_persons');
        $this->db->join('request_requests', 'request_requests.person_idworker = person_persons.person_id', 'inner');
        $this->db->join('request_tickets', 'request_tickets.request_id = request_requests.request_id', 'inner');
        if (!empty ($datosForWheres ['transport_id']))
            $this->db->where('request_tickets.transport_id', $datosForWheres ['transport_id']);

        if (!empty ($datosForWheres ['province_idto']))
            $this->db->where('request_tickets.province_idto', $datosForWheres ['province_idto']);

        if (!empty ($datosForWheres ['province_idfrom']))
            $this->db->where('request_tickets.province_idfrom', $datosForWheres ['province_idfrom']);

        if (!empty ($datosForWheres ['motive_id']))
            $this->db->where('request_requests.motive_id', $datosForWheres ['motive_id']);

        if (!empty ($datosForWheres ['begindate']))
            $this->db->where('request_tickets.ticket_date >=', $datosForWheres ['begindate']);

        if (!empty ($datosForWheres ['enddate']))
            $this->db->where('request_tickets.ticket_date <=', $datosForWheres ['enddate']);

        if (!empty ($datosForWheres ['person_id'])) {
            $this->db->where('person_persons.person_id', $datosForWheres ['person_id']);
        } else if (!empty ($datosForWheres ['province_idworkers'])) {
            $this->db->where('person_persons.province_id', $datosForWheres ['province_idworkers']);
        }
        if (!empty ($datosForWheres ['person_identity']))
            $this->db->where('person_persons.person_identity', $datosForWheres ['person_identity']);
        if (!empty ($datosForWheres ['transport_id']))
            $this->db->where('request_tickets.transport_id', $datosForWheres ['transport_id']);
        if (!empty ($datosForWheres ['transport_itinerary']))
            $this->db->where('request_tickets.transport_itinerary', $datosForWheres ['transport_itinerary']);

        $this->db->limit($to, $from);
        $this->db->orderby('ticket_date', 'desc');
        $result = $this->db->get();
        //
        $value = array(); //unset($value);
        if ($result->result() != null) {
            foreach ($result->result() as $row) {
                $motive = Conf_motives_model::getNameById($row->motive_id);
                $person = Person_persons_model::getNameById($row->person_id);
                $identity = Person_persons_model::getIdentityById($row->person_id);
                $transporte = Conf_lodgingtransports_model::getNameById($row->transport_id);
                $provinceFrom = Conf_provinces_model::getNameById($row->province_idfrom);
                $provinceTo = Conf_provinces_model::getNameById($row->province_idto);
                $request_id = $row->request_id; //ahora con este buscar en ticket_edit XXX o sea en las tablas de etecsa, viazul y eventuales, claro si el state esta en <>0 para buscar hotel , etc, sino esos datos van en ''''' vacios.
                $state = $row->ticket_state; //o es no editada y 1 editada, osea esta en los ticketeditXXX, auqnue de todas formas me meto en ellas
                //inicio de trasnporte=1 etecsa
                if ($row->transport_id == 1 || $row->transport_id == 4) { //ticket_editetecsa  y eventuales y hay que buscarlo en una de las 2 tablas
                    $hotel = '---'; //no tiene aun
                    $exithour = '---';
                    $arrivalhour = '---';
                    if ($row->ticket_cancel == 1) {
                        $tipo = 'Cancelada';
                    } else {
                        $tipo = '---';
                        if ($row->transport_id == 1)
                            $res = $this->db->query('select ticket_editetecsa.state_id from ticket_editetecsa where ticket_editetecsa.request_id=' . $row->request_id . ' and ticket_editetecsa.ticket_date=\'' . $row->ticket_date . ' \'');
                        if ($row->transport_id == 4)
                            $res = $this->db->query('select ticket_editeventual.state_id from ticket_editeventual where ticket_editeventual.request_id=' . $row->request_id . ' and ticket_editeventual.ticket_date=\'' . $row->ticket_date . ' \'');
                        $tipoEdit = 0; //estado por ejemplo propia,extra1,extra2...etc
                        if ($res->result() != null) { //existe y esta editada por supuesto en esta tabla
                            foreach ($res->result() as $row2) {
                                $tipoEdit = $row2->state_id;
                            }
                            if ($tipoEdit != 0)
                                $tipo = Conf_ticketrequeststates_model::getNameById($tipoEdit);
                        } //fin del if !=null
                    }
                    if ((empty ($datosForWheres ['transport_id'])) || (!empty ($datosForWheres ['transport_id'])) && (($datosForWheres ['transport_id'] == 1) || ($datosForWheres ['transport_id'] == 4))) { //existe 		y no esta filtrado para que no se devuelva el valor(fila)
                        $value [] = array('person' => $person,
                            'identity' => $identity,
                            'ticketdate' => $row->ticket_date,
                            'province_from' => $provinceFrom,
                            'province_to' => $provinceTo,
                            'transport' => $transporte,
                            'motive' => $motive,
                            'tipo' => $tipo,
                            'hotel' => $hotel,
                            'exithour' => $exithour,
                            'arrivalhour' => $arrivalhour);
                    } //fin del if de 3 condiciones
                } //fin $row->transport_id==1
                //inicio de trasnporte=2 viazul
                if ($row->transport_id == 2) { //ticket_viazul
                    $hotel = '---';
                    $exithour = '---';
                    $arrivalhour = '---';
                    if ($row->ticket_cancel == 1) {
                        $tipo = 'Cancelada';
                    } else {
                        $tipo = '---';
                        $res = $this->db->query('select
														viazulstate_id, 
														viazul_exithour, 
														viazul_arrivalhour 
													from ticket_editviazul 
													where 
														ticket_editviazul.request_id=' . $row->request_id . ' and ticket_editviazul.ticket_date=\'' . $row->ticket_date . ' \'');
                        $tipoEdit = 0; //estado por ejemplo propia,extra1,extra2...etc
                        if ($res->result() != null) { //existe y esta editada por supuesto en esta tabla
                            foreach ($res->result() as $row2) {
                                $tipoEdit = $row2->viazulstate_id;
                                $exithour = substr($row2->viazul_exithour, 0, 5);// abcd;
                                $arrivalhour = substr($row2->viazul_arrivalhour, 0, 5);
                            }
                            if ($tipoEdit != 0/*esta editada ya!!!!*/)
                                $tipo = Conf_ticketviazulstates_model::getNameById($tipoEdit);
                        } //fin del if !=null
                    }
                    if ((empty ($datosForWheres ['transport_id'])) || (!empty ($datosForWheres ['transport_id'])) && ($datosForWheres ['transport_id'] == 2)) { //existe 	y no esta filtrado para que no se devuelva el valor(fila)
                        $value [] = array('person' => $person,
                            'identity' => $identity,
                            'ticketdate' => $row->ticket_date,
                            'province_from' => $provinceFrom,
                            'province_to' => $provinceTo,
                            'transport' => $transporte,
                            'motive' => $motive,
                            'tipo' => $tipo,
                            'hotel' => $hotel,
                            'exithour' => $exithour,
                            'arrivalhour' => $arrivalhour);
                    } //fin del if de 3 condiciones
                } //fin $row->transport_id==2
                if ($row->transport_id == 3) { //avion
                    $hotel = '---';
                    $exithour = '---';
                    $arrivalhour = '---';
                    if ($row->ticket_cancel == 1) {
                        $tipo = 'Cancelada';
                    } else {
                        $tipo = '---';
                        $res = $this->db->query('select
														state_id, 
														airplane_exithour, 
														airplane_arrivalhour 
													from ticket_editairplane 
													where 
														ticket_editairplane.request_id=' . $row->request_id . ' and ticket_editairplane.ticket_date=\'' . $row->ticket_date . ' \'');
                        $tipoEdit = 0; //estado por ejemplo propia,extra1,extra2...etc
                        if ($res->result() != null) { //existe y esta editada por supuesto en esta tabla
                            foreach ($res->result() as $row2) {
                                $tipoEdit = $row2->state_id;
                                $exithour = substr($row2->airplane_exithour, 0, 5);// abcd;
                                $arrivalhour = substr($row2->airplane_arrivalhour, 0, 5);
                            }
                            if ($tipoEdit != 0/*esta editada ya!!!!*/)
                                $tipo = Conf_ticketviazulstates_model::getNameById($tipoEdit);
                        } //fin del if !=null
                    }
                    if ((empty ($datosForWheres ['transport_id'])) || (!empty ($datosForWheres ['transport_id'])) && ($datosForWheres ['transport_id'] == 3)) { //existe 	y no esta filtrado para que no se devuelva el valor(fila)
                        $value [] = array('person' => $person,
                            'identity' => $identity,
                            'ticketdate' => $row->ticket_date,
                            'province_from' => $provinceFrom,
                            'province_to' => $provinceTo,
                            'transport' => $transporte,
                            'motive' => $motive,
                            'tipo' => $tipo,
                            'hotel' => $hotel,
                            'exithour' => $exithour,
                            'arrivalhour' => $arrivalhour);
                    } //fin del if de 3 condiciones
                } //fin $row->transport_id==2
                if ($row->transport_id == 5) { //barco
                    $hotel = '---';
                    $exithour = '---';
                    $arrivalhour = '---';
                    if ($row->ticket_cancel == 1) {
                        $tipo = 'Cancelada';
                    } else {
                        $tipo = '---';
                        $res = $this->db->query('select
														state_id, 
														ship_exithour, 
														ship_arrivalhour 
													from ticket_editship 
													where 
														ticket_editship.request_id=' . $row->request_id . ' and ticket_editship.ticket_date=\'' . $row->ticket_date . ' \'');
                        $tipoEdit = 0; //estado por ejemplo propia,extra1,extra2...etc
                        if ($res->result() != null) { //existe y esta editada por supuesto en esta tabla
                            foreach ($res->result() as $row2) {
                                $tipoEdit = $row2->state_id;
                                $exithour = substr($row2->ship_exithour, 0, 5);// abcd;
                                $arrivalhour = substr($row2->ship_arrivalhour, 0, 5);
                            }
                            if ($tipoEdit != 0/*esta editada ya!!!!*/)
                                $tipo = Conf_ticketviazulstates_model::getNameById($tipoEdit);
                        } //fin del if !=null
                    }
                    if ((empty ($datosForWheres ['transport_id'])) || (!empty ($datosForWheres ['transport_id'])) && ($datosForWheres ['transport_id'] == 5)) { //existe 	y no esta filtrado para que no se devuelva el valor(fila)
                        $value [] = array('person' => $person,
                            'identity' => $identity,
                            'ticketdate' => $row->ticket_date,
                            'province_from' => $provinceFrom,
                            'province_to' => $provinceTo,
                            'transport' => $transporte,
                            'motive' => $motive,
                            'tipo' => $tipo,
                            'hotel' => $hotel,
                            'exithour' => $exithour,
                            'arrivalhour' => $arrivalhour);
                    } //fin del if de 3 condiciones
                } //fin $row->transport_id==2
            } //fin del foreach
        } //end if result != null antes del foreach
        if ($isPdf == 'si') { //devuelve todos por exceso
            return $value;
        } else { //el cant es filtrado
            echo("{count : " . $cant . ", data : " . json_encode($value) . "}"); // este valor se le asigna al return del setDatagridconditional para pasarle
        }
    }

    public function getDataReportLodging($to, $from, $datosForWheres, $isPdf = 'no')
    {
        $this->load->model('request/request_requests_model');
        $this->load->model('request/request_lodgings_model');
        $this->load->model('lodging/lodging_edit_model');
        $this->load->model('conf/conf_provinces_model');
        $this->load->model('person/person_persons_model');
        $this->load->model('conf/conf_motives_model');
        $this->load->model('conf/conf_hotels_model');
        $this->load->model('request/request_tickets_model');
        $this->load->model('ticket/ticket_editetecsa_model');
        $this->load->model('ticket/ticket_editeventual_model');
        $this->load->model('ticket/ticket_editviazul_model'); //por gusto los anteriores 4
        $this->db->select('request_requests.request_id,
							person_persons.person_id,
							request_lodgings.lodging_entrancedate,
							request_lodgings.lodging_exitdate,
							request_lodgings.province_idlodging,
							request_requests.motive_id,
							request_lodgings.lodging_state,
							request_lodgings.lodging_canceled,
							lodging_edit.lodging_noshow');
        $this->db->from('person_persons');
        $this->db->join(Request_requests_model::TABLE_NAME, Request_requests_model::TABLE_NAME . '.person_idworker = person_persons.person_id', 'inner');
        $this->db->join('request_lodgings', 'request_lodgings.request_id=' . Request_requests_model::TABLE_NAME . '.request_id', 'inner');
        $this->db->join('lodging_edit', 'lodging_edit' . '.request_id = ' . 'request_lodgings' . '.request_id', 'left'); //!!!!super obligado porque sino encuentra aqui editada la solicitud de hospedaje no muestra los que esten aun por editar y si aqui tienen que mostrasele a los clientes normales del sistema para que vean el estado
        //$this->db->where ( 'lodging_edit.lodging_noshow <> ', 'on' ); //se ve en tabla lodgin_edit, o sea el hospedaje se llevo a cabo, aqui se muestra todo, porque es para ver inclusive antes de editar
        if (!empty ($datosForWheres ['motive_id']))
            $this->db->where('request_requests.motive_id', $datosForWheres ['motive_id']);
        if (!empty ($datosForWheres ['province_idlodging']))
            $this->db->where('request_lodgings.province_idlodging', $datosForWheres ['province_idlodging']);
        if (!empty ($datosForWheres ['hotel_id']))
            $this->db->where('lodging_edit.hotel_id', $datosForWheres ['hotel_id']);
        if (!empty ($datosForWheres ['begindate']))
            $this->db->where('request_lodgings.lodging_entrancedate >=', $datosForWheres ['begindate']);
        if (!empty ($datosForWheres ['enddate']))
            $this->db->where('request_lodgings.lodging_entrancedate <=', $datosForWheres ['enddate']);
        if (!empty ($datosForWheres ['person_id'])) {
            $this->db->where('person_persons.person_id', $datosForWheres ['person_id']);
        } else if (!empty ($datosForWheres ['province_idworkers'])) {
            $this->db->where('person_persons.province_id', $datosForWheres ['province_idworkers']);
        }
        if (!empty ($datosForWheres ['person_identity']))
            $this->db->where('person_persons.person_identity', $datosForWheres ['person_identity']);
        //
        $cant = $this->db->count_all_results(); //para coger la cantidad real de filas de la consulta filtrada sin from-limit
        //ya no cargo los model de nuevo
        $this->db->select('request_requests.request_id ,
							person_persons.person_id,
							request_lodgings.lodging_entrancedate,
							request_lodgings.lodging_exitdate,
							request_lodgings.province_idlodging,
							request_requests.motive_id,
							request_lodgings.lodging_state,
							request_lodgings.lodging_canceled,
							lodging_edit.lodging_noshow');
        $this->db->from('person_persons');
        $this->db->join(Request_requests_model::TABLE_NAME, Request_requests_model::TABLE_NAME . '.person_idworker = person_persons.person_id', 'inner');
        $this->db->join('request_lodgings', 'request_lodgings.request_id=' . Request_requests_model::TABLE_NAME . '.request_id', 'inner');
        $this->db->join('lodging_edit', 'lodging_edit' . '.request_id = ' . 'request_lodgings' . '.request_id', 'left'); //!!!!super obligado porque sino encuentra aqui editada la solicitud de hospedaje no muestra los que esten aun por editar y si aqui tienen que mostrasele a los clientes normales del sistema para que vean el estado
        //$this->db->where ( 'lodging_edit.lodging_noshow <> ', 'on' ); //se ve en tabla lodgin_edit, o sea el hospedaje se llevo a cabo, aqui se muestra todo, porque es para ver inclusive antes de editar
        if (!empty ($datosForWheres ['motive_id']))
            $this->db->where('request_requests.motive_id', $datosForWheres ['motive_id']);
        if (!empty ($datosForWheres ['province_idlodging']))
            $this->db->where('request_lodgings.province_idlodging', $datosForWheres ['province_idlodging']);
        if (!empty ($datosForWheres ['hotel_id']))
            $this->db->where('lodging_edit.hotel_id', $datosForWheres ['hotel_id']);
        if (!empty ($datosForWheres ['begindate']))
            $this->db->where('request_lodgings.lodging_entrancedate >=', $datosForWheres ['begindate']);
        if (!empty ($datosForWheres ['enddate']))
            $this->db->where('request_lodgings.lodging_entrancedate <=', $datosForWheres ['enddate']);
        if (!empty ($datosForWheres ['person_id'])) {
            $this->db->where('person_persons.person_id', $datosForWheres ['person_id']);
        } else if (!empty ($datosForWheres ['province_idworkers'])) {
            $this->db->where('person_persons.province_id', $datosForWheres ['province_idworkers']);
        }
        if (!empty ($datosForWheres ['person_identity']))
            $this->db->where('person_persons.person_identity', $datosForWheres ['person_identity']);
        //
        $this->db->limit($to, $from);
        $this->db->orderby('person_id', 'desc');

        $result = $this->db->get();
        //
        $value = array(); //unset($value);
        if ($result->result() != null) {
            foreach ($result->result() as $row) {
                $person = Person_persons_model::getNameById($row->person_id);
                $identity = Person_persons_model::getIdentityById($row->person_id);
                $provinceLodging = Conf_provinces_model::getNameById($row->province_idlodging);
                $motive = Conf_motives_model::getNameById($row->motive_id);
                $hotel = '---';
                $hotel_id = 0;
                if ($row->lodging_state == 1) { //esta editada
                    $res = $this->db->query('select lodging_edit.hotel_id from lodging_edit where lodging_edit.request_id=' . $row->request_id);
                    if ($res->result() != null) { //existe y esta editada por supuesto en esta tabla
                        $hotel_id = 0;
                        foreach ($res->result() as $row2) {
                            $hotel_id = $row2->hotel_id;
                        }
                        $hotel = Conf_hotels_model::getNameById($row2->hotel_id); //solo si existe la solicitud editada con el hotel
                    }

                }
                if ($row->lodging_noshow == 'on') {
                    $hotel = 'No Show';
                }
                if ($row->lodging_canceled == 1) {
                    $hotel = 'Cancelada';
                }
                $flag = true;
                //verifico el filtro de hotel y province_idlodging
                if (!empty ($datosForWheres ['province_idlodging']))
                    if ($datosForWheres ['province_idlodging'] != $row->province_idlodging)
                        $flag = false;
                if (!empty ($datosForWheres ['hotel_id']))
                    if ($datosForWheres ['hotel_id'] != $hotel_id)
                        $flag = false;
                if ($flag == true)
                    $value [] = array('person' => $person,
                        'identity' => $identity,
                        'lodging_entrancedate' => $row->lodging_entrancedate,
                        'lodging_exitdate' => $row->lodging_exitdate,
                        'provinceLodging' => $provinceLodging,
                        'motive' => $motive,
                        'hotel' => $hotel);
            }
        } //end if result != null antes del foreach
        if ($isPdf == 'si') { //devuelve todos por exceso
            return $value;
        } else { //el cant es filtrado
            echo("{count : " . $cant . ", data : " . json_encode($value) . "}"); // este valor se le asigna al return del setDatagridconditional para pasarle
        }
    }

    //
    public function getDataReportSnack($date, $isPdf = 'no')
    {
        $this->load->model('request/request_requests_model');
        $this->load->model('request/request_tickets_model');
        $this->load->model('ticket/ticket_editetecsa_model');
        $this->load->model('ticket/ticket_editeventual_model');
        $this->load->model('conf/conf_motives_model');
        $this->load->model('conf/conf_provinces_model');
        $this->load->model('conf/conf_costcenters_model');
        $this->load->model('person/person_persons_model');
        $this->load->model('conf/conf_ticketrequeststates_model');

        $this->db->select('request_requests.request_id,
							person_persons.person_id,
							request_tickets.ticket_date,
							request_tickets.province_idfrom,
							request_tickets.province_idto,
							request_requests.motive_id,
							request_tickets.transport_id,
							request_tickets.ticket_state'); //este ultimo campo para ver si esta editada ono, y sino lo esta evitar abajo en el for la busqueda en los ticketeditXXX
        $this->db->from('person_persons');
        $this->db->join(Request_requests_model::TABLE_NAME, Request_requests_model::TABLE_NAME . '.person_idworker = person_persons.person_id', 'inner');
        $this->db->join('request_tickets', 'request_tickets.request_id=' . Request_requests_model::TABLE_NAME . '.request_id', 'inner');
        $this->db->where('request_tickets.ticket_date', $date);
        $this->db->where('request_tickets.transport_id', 1);
        $this->db->or_where('request_tickets.transport_id', 4);
        $this->db->where('request_tickets.province_idfrom', 14);
        $this->db->or_where('request_tickets.province_idfrom', 15);
        $this->db->or_where('request_tickets.province_idfrom', 16);
        $cant = $this->db->count_all_results(); //para coger la cantidad real de filas de la consulta filtrada sin from-limit

        $this->db->select('request_requests.request_id,
							person_persons.person_id,
							request_tickets.ticket_date,
							request_tickets.province_idfrom,
							request_tickets.province_idto,
							request_requests.request_details,
							request_requests.motive_id,
							request_requests.center_id');
        $this->db->from('person_persons');
        $this->db->join(Request_requests_model::TABLE_NAME, Request_requests_model::TABLE_NAME . '.person_idworker = person_persons.person_id', 'inner');
        $this->db->join('request_tickets', 'request_tickets.request_id=' . Request_requests_model::TABLE_NAME . '.request_id', 'inner');
        $this->db->join('ticket_editetecsa', 'ticket_editetecsa.request_id = request_tickets.request_id and request_tickets.ticket_date = ticket_editetecsa.ticket_date', 'left');
        $this->db->join('ticket_editeventual', 'ticket_editeventual.request_id = request_tickets.request_id and request_tickets.ticket_date = ticket_editeventual.ticket_date', 'left');
        $this->db->where('request_tickets.ticket_date', $date);
        $this->db->where('request_tickets.ticket_state', 1);
        $where_transport = '((request_tickets.transport_id = 1) or (request_tickets.transport_id = 4))';
        $this->db->where($where_transport);
        $where_provinces = '((request_tickets.transport_id = 1) or (request_tickets.transport_id = 4))';
        $this->db->where($where_provinces);
        $where_state = '(( request_tickets.province_idfrom = 14 ) or (request_tickets.province_idfrom = 15) or (request_tickets.province_idfrom = 16))';
        $this->db->where($where_state);
        $cant = $this->db->count_all_results();
        $this->db->select('request_requests.request_id,
							person_persons.person_id,
							request_tickets.ticket_date,
							request_tickets.province_idfrom,
							request_tickets.province_idto,
							request_requests.request_details,
							request_requests.motive_id,
							request_requests.center_id');
        $this->db->from('person_persons');
        $this->db->join(Request_requests_model::TABLE_NAME, Request_requests_model::TABLE_NAME . '.person_idworker = person_persons.person_id', 'inner');
        $this->db->join('request_tickets', 'request_tickets.request_id=' . Request_requests_model::TABLE_NAME . '.request_id', 'inner');
        $this->db->join('ticket_editetecsa', 'ticket_editetecsa.request_id = request_tickets.request_id and request_tickets.ticket_date = ticket_editetecsa.ticket_date', 'left');
        $this->db->join('ticket_editeventual', 'ticket_editeventual.request_id = request_tickets.request_id and request_tickets.ticket_date = ticket_editeventual.ticket_date', 'left');
        $this->db->where('request_tickets.ticket_date', $date);
        $this->db->where('request_tickets.ticket_state', 1);
        $where_transport = '((request_tickets.transport_id = 1) or (request_tickets.transport_id = 4))';
        $this->db->where($where_transport);
        $where_provinces = '((request_tickets.transport_id = 1) or (request_tickets.transport_id = 4))';
        $this->db->where($where_provinces);
        $where_state = '(( request_tickets.province_idfrom = 14 ) or (request_tickets.province_idfrom = 15) or (request_tickets.province_idfrom = 16))';
        $this->db->where($where_state);
        $this->db->orderby('ticket_date', 'desc');
        $result = $this->db->get();
        //
        $value = array(); //unset($value);
        if ($result->result() != null) {
            foreach ($result->result() as $row) {
                $provinceFrom = Conf_provinces_model::getNameById($row->province_idfrom);
                $provinceTo = Conf_provinces_model::getNameById($row->province_idto);
                $person = Person_persons_model::getNameById($row->person_id);
                $identity = Person_persons_model::getIdentityById($row->person_id);
                $state_id = 0;
                $estado = '';
                $res = $this->db->query('select
												ticket_editetecsa.state_id 
											from ticket_editetecsa 
											where 
												ticket_editetecsa.request_id=' . $row->request_id . ' and ticket_editetecsa.ticket_date=\'' . $row->ticket_date . ' \'');
                if ($res->result() != null) {
                    foreach ($res->result() as $row2) { //esta en esta tabla
                        $state_id = $row2->state_id;
                    }
                    $estado = Conf_ticketrequeststates_model::getNameById($state_id);
                }
                //
                $res = $this->db->query('select
												ticket_editeventual.state_id 
											from ticket_editeventual 
											where 
												ticket_editeventual.request_id=' . $row->request_id . ' and ticket_editeventual.ticket_date=\'' . $row->ticket_date . ' \'');
                if ($res->result() != null) {
                    foreach ($res->result() as $row2) { //esta en esta tabla
                        $state_id = $row2->state_id;
                    }
                    $estado = Conf_ticketrequeststates_model::getNameById($state_id);
                }

                $value [] = array('person' => $person,
                    'identity' => $identity,
                    'ticket_date' => $row->ticket_date,
                    'provinceFrom' => $provinceFrom,
                    'provinceTo' => $provinceTo,
                    'estado' => $estado);
            }
        }

        if ($isPdf == 'si') {
            return $value;
        } else {
            echo("{count : " . $cant . ", data : " . json_encode($value) . "}"); // este valor se le asigna al return del setDatagridconditional para pasarle
        }
    }

    public function getDataReportLodgingMenByDay($to, $from, $datosForWheres, $isPdf = 'no')
    {
        $this->load->model('request/request_requests_model');
        $this->load->model('request/request_lodgings_model');
        $this->load->model('lodging/lodging_edit_model');
        $this->load->model('conf/conf_provinces_model');
        $this->load->model('person/person_persons_model');
        $this->load->model('conf/conf_motives_model');
        $this->load->model('conf/conf_hotels_model');
        $this->load->model('conf/conf_costcenters_model');

        if (!empty ($datosForWheres ['province_idlodging']))
            $province_idlodging = "and (request_lodgings.province_idlodging = " . $datosForWheres ['province_idlodging'] . ") ";
        else
            $province_idlodging = "";
        if (!empty ($datosForWheres ['hotel_id']))
            $hotel_id = "and (lodging_edit.hotel_id = " . $datosForWheres ['hotel_id'] . ") ";
        else
            $hotel_id = "";
        if (!empty ($datosForWheres ['center_id']))
            $center_id = "and (request_requests.center_id = " . $datosForWheres ['center_id'] . ") ";
        else
            $center_id = "";
        if (!empty ($datosForWheres ['motive_id']))
            $motive_id = "and (request_requests.motive_id = " . $datosForWheres ['motive_id'] . ") ";
        else
            $motive_id = "";
        $sql = "select
					request_requests.request_id,
					request_requests.person_idworker,
					request_lodgings.lodging_entrancedate,
					request_lodgings.lodging_exitdate,
					request_lodgings.province_idlodging,
					request_requests.request_details,
					request_requests.motive_id,
					request_requests.center_id,
					lodging_edit.hotel_id
				from person_persons
				inner join request_requests on person_persons.person_id = request_requests.person_idworker
				inner join request_lodgings on request_lodgings.request_id = request_requests.request_id
				inner join lodging_edit on lodging_edit.request_id = request_lodgings.request_id
				where ( lodging_edit.lodging_noshow <> 'on') 
				and ( request_lodgings.lodging_canceled <> 1) 
				" . $province_idlodging . "
				" . $hotel_id . "
				" . $center_id . "
				" . $motive_id . "
				and (( request_lodgings.lodging_entrancedate < ? ) 
				and ( request_lodgings.lodging_exitdate > ? ) 
				or ( request_lodgings.lodging_entrancedate = ? ))";
        $result = $this->db->query($sql, array($datosForWheres ['begindate'], $datosForWheres ['begindate'], $datosForWheres ['begindate']));
        $cant = $this->db->count_all_results();

        $sql = "select
					request_requests.request_id,
					request_requests.person_idworker,
					request_lodgings.lodging_entrancedate,
					request_lodgings.lodging_exitdate,
					request_lodgings.province_idlodging,
					request_requests.request_details,
					request_requests.motive_id,
					request_requests.center_id,
					lodging_edit.hotel_id
				from person_persons
				inner join request_requests on person_persons.person_id = request_requests.person_idworker
				inner join request_lodgings on request_lodgings.request_id = request_requests.request_id
				inner join lodging_edit on lodging_edit.request_id = request_lodgings.request_id
				where ( lodging_edit.lodging_noshow <> 'on') 
				and ( request_lodgings.lodging_canceled <> 1) 
				" . $province_idlodging . "
				" . $hotel_id . "
				" . $center_id . "
				" . $motive_id . "
				and (( request_lodgings.lodging_entrancedate < ? ) 
				and ( request_lodgings.lodging_exitdate > ? ) 
				or ( request_lodgings.lodging_entrancedate = ? ))";
        $result = $this->db->query($sql, array($datosForWheres ['begindate'], $datosForWheres ['begindate'], $datosForWheres ['begindate']));
        $value = array();
        if ($result->result() != null) {
            foreach ($result->result() as $row) {
                $provinceLodging = Conf_provinces_model::getNameById($row->province_idlodging);
                $person = Person_persons_model::getNameById($row->person_idworker);
                $identity = Person_persons_model::getIdentityById($row->person_idworker);
                $motive = Conf_motives_model::getNameById($row->motive_id);
                $center = Conf_costcenters_model::getNameById($row->center_id);
                $hotel = Conf_hotels_model::getNameById($row->hotel_id);
                $value [] = array('person' => $person,
                    'identity' => $identity,
                    'lodging_entrancedate' => $row->lodging_entrancedate,
                    'lodging_exitdate' => $row->lodging_exitdate,
                    'provinceLodging' => $provinceLodging,
                    'motive' => $motive,
                    'details' => $row->request_details,
                    'center' => $center,
                    'hotel' => $hotel);
            }
        }
        if ($isPdf == 'si') {
            return $value;
        } else {
            echo("{count : " . $cant . ", data : " . json_encode($value) . "}");
        }
    }

}

?>
