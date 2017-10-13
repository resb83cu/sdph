<?php

if (!defined('BASEPATH'))
    exit ('No direct script access allowed');

class Centinela
{
    var $_person_id = 0;
    var $_person_idparent = 0;
    var $_user_name = "";
    var $_user_password = "";
    var $_roll_id = 0;
    var $_person_fullname = "";
    var $_center_id = 0;
    var $_province_id = 0;
    var $_session_ip = 0;
    var $_session_id = 0;
    var $_id = 0; //para identificador unico de session, pues la session_id auqnue sea unica es regenerada cada x tiempo
    var $_auth = FALSE;

    function Centinela($auto = TRUE)
    { //true por defecto cuando se crea y supone ya se esta logueadoen el sistema
        if ($auto) { //ya se supone esten creadas las variables de sesiones userdata en user_name y passw y las demas
            $CI = &get_instance();
            $message = "";
            if ($this->login(FALSE, $CI->session->userdata('user_name'), $CI->session->userdata('user_password'), $message) === TRUE) {
                $this->_auth = TRUE; //el tipo esta logueado correctamente
                $this->_person_id = $CI->session->userdata('person_id');
                $this->_person_idparent = $CI->session->userdata('person_idparent');
                $this->_user_name = $CI->session->userdata('user_name');
                $this->_user_password = $CI->session->userdata('user_password');
                $this->_person_fullname = $CI->session->userdata('person_fullname');
                $this->_roll_id = $CI->session->userdata('roll_id');
                $this->_center_id = $CI->session->userdata('center_id');
                $this->_province_id = $CI->session->userdata('province_id');
                $this->_session_ip = $CI->session->userdata('session_ip');
                $this->_session_id = $CI->session->userdata('session_id');
                $this->_id = $CI->session->userdata('id');

            }
        }
    }

    //--------------------------------------//
    #
    function dateadd($date, $dd = 0, $mm = 0, $yy = 0, $hh = 0, $mn = 0, $ss = 0)
    {
        $date_r = getdate(strtotime($date));
        $date_result = date("m/d/Y H:i:s", mktime(($date_r ["hours"] + $hh), ($date_r ["minutes"] + $mn), ($date_r ["seconds"] + $ss), ($date_r ["mon"] + $mm), ($date_r ["mday"] + $dd), ($date_r ["year"] + $yy)));
        return $date_result;
    }

    function today()
    {
        $fecha = getdate();
        $mes = $fecha ['mon'];
        strlen($mes) == 1 ? $mes = '0' . $mes : $mes = $mes;
        $dia = $fecha ['mday'];
        strlen($dia) == 1 ? $dia = '0' . $dia : $dia = $dia;
        $hora = $fecha ['hours'];
        strlen($hora) == 1 ? $hora = '0' . $hora : $hora = $hora;
        $minuto = $fecha ['minutes'];
        strlen($minuto) == 1 ? $minuto = '0' . $minuto : $minuto = $minuto;
        $segundo = $fecha ['seconds'];
        strlen($segundo) == 1 ? $segundo = '0' . $segundo : $segundo = $segundo;
        $datenow = $fecha ['year'] . '-' . $mes . '-' . $dia . ' ' . $hora . ':' . $minuto . ':' . $segundo;
        return $datenow;
    }

    function daysLastSessionToNow($person_id)
    {
        $CI = &get_instance();
        $queryN = $CI->db->query("select
										session_begindate 
									from user_sessions 
									where person_id = " . $person_id . "  order by session_begindate desc limit 1");
        $rowN = $queryN->row();
        if (!empty ($rowN->session_begindate)) {
            $beginDate = date_parse($rowN->session_begindate);
            $beginYear = $beginDate ['year'];
            $beginMonth = $beginDate ['month'];
            $beginDay = $beginDate ['day'];
        } else {
            $beginDate = getdate();
            $beginYear = $beginDate ['year'];
            $beginMonth = $beginDate ['mon'];
            $beginDay = $beginDate ['mday'];
        }
        $endDate = getdate();
        $endYear = $endDate ['year'];
        $endMonth = $endDate ['mon'];
        $endDay = $endDate ['mday'];
        $begin = mktime(0, 0, 0, $beginMonth, $beginDay, $beginYear);
        $end = mktime(0, 0, 0, $endMonth, $endDay, $endYear);
        $dateDiff = $end - $begin;
        $fullDays = floor($dateDiff / (60 * 60 * 24));
        return $fullDays;
    }

    function verifyDeveloper($isFirst = FALSE, $username = "", $password = "", &$message)
    {

        if (empty ($password) || empty ($username)) {
            $message = "Usuario o password en blanco";
            return FALSE;
        }
        $fecha = getdate();
        $mes = $fecha ['mon'];
        strlen($mes) == 1 ? $mes = '0' . $mes : $mes = $mes;
        $dia = $fecha ['mday'];
        strlen($dia) == 1 ? $dia = '0' . $dia : $dia = $dia;
        $hora = $fecha ['hours'];
        strlen($hora) == 1 ? $hora = '0' . $hora : $hora = $hora;
        $minuto = $fecha ['minutes'];
        strlen($minuto) == 1 ? $minuto = '0' . $minuto : $minuto = $minuto;
        $segundo = $fecha ['seconds'];
        strlen($segundo) == 1 ? $segundo = '0' . $segundo : $segundo = $segundo;

        $datenow = $fecha ['year'] . '-' . $mes . '-' . $dia . ' ' . $hora . ':' . $minuto . ':' . $segundo;

        $CI = &get_instance();
        $CI->load->model('person/person_persons_model');
        $CI->load->model('person/person_workers_model');
        $CI->load->model('conf/conf_costcenters_model');
        $CI->load->model('conf/conf_provinces_model');

        /*$sql = "select
                 user_users.person_id,
                 user_users.user_name,
                 user_users.user_password,
                 user_users.roll_id,
                 user_users.center_id,
            user_users.locked,
                 person_persons.province_id,
            user_users.person_idparent
             from user_users
             inner join person_workers on user_users.person_id = person_workers.person_id
             inner join person_persons on person_workers.person_id = person_persons.person_id
            WHERE user_name=? AND user_password=? AND locked=?";*/
        $sql = "select
	     		user_users.person_id,
	     		user_users.user_name,
	     		user_users.user_password,
	     		user_users.roll_id,
	     		user_users.center_id,
			    user_users.locked,
	     		person_persons.province_id,
			    conf_costcenters.person_id as person_idparent
         	from user_users
         	inner join person_workers on user_users.person_id = person_workers.person_id
         	inner join person_persons on person_workers.person_id = person_persons.person_id
         	inner join conf_costcenters on user_users.center_id = conf_costcenters.center_id
        	WHERE user_name=? AND user_password=? AND locked=?";

        $query = $CI->db->query($sql, array($username, $password, '0'));
        if ($query->num_rows() == 1) { //ok el usuario
            $row = $query->row();
            $aBloquear = false;
            $queryN = $CI->db->query("select session_begindate from user_sessions where person_id=" . $row->person_id . "  order by session_begindate desc limit 1");
            $rowN = $queryN->row();
            if (!empty ($rowN->session_begindate)) {
                $beginDate = date_parse($rowN->session_begindate);
                $beginYear = $beginDate ['year'];
                $beginMonth = $beginDate ['month'];
                $beginDay = $beginDate ['day'];
            } else {
                $beginDate = getdate();
                $beginYear = $beginDate ['year'];
                $beginMonth = $beginDate ['mon'];
                $beginDay = $beginDate ['mday'];
            }
            $endDate = getdate();
            $endYear = $endDate ['year'];
            $endMonth = $endDate ['mon'];
            $endDay = $endDate ['mday'];
            $begin = mktime(0, 0, 0, $beginMonth, $beginDay, $beginYear);
            $end = mktime(0, 0, 0, $endMonth, $endDay, $endYear);
            $dateDiff = $end - $begin;
            $fullDays = floor($dateDiff / (60 * 60 * 24));
            if ($fullDays > 90)
                $aBloquear = true;
            if ($aBloquear == true) {
                $CI->db->query("update user_users set locked = 'on' where person_id = " . $row->person_id);
                $this->_auth = FALSE;
                $this->logout();
                $message = "La cuenta esta bloqueada";
                return FALSE;
            } else { //ok
                $query2 = $CI->db->query('select person_name,person_lastname,person_secondlastname  from person_persons where person_id=? ', array($row->person_id));
                $row2 = $query2->row();
                $person_fullname = $row2->person_name . ' ' . $row2->person_lastname . ' ' . $row2->person_secondlastname;
                $CI->session->set_userdata('person_fullname', $person_fullname);
                $this->_person_fullname = $person_fullname;
                $CI->session->set_userdata('person_id', $row->person_id);
                $this->_person_id = $row->person_id;
                $CI->session->set_userdata('person_idparent', $row->person_idparent);
                $this->_person_idparent = $row->person_idparent;
                $CI->session->set_userdata('user_name', $row->user_name);
                $this->_user_name = $row->user_name;
                $CI->session->set_userdata('user_password', $row->user_password);
                $this->_user_password = $row->user_password;
                $CI->session->set_userdata('roll_id', $row->roll_id);
                $this->_roll_id = $row->roll_id;
                $CI->session->set_userdata('center_id', $row->center_id);
                $this->_center_id = $row->center_id;
                $CI->session->set_userdata('province_id', $row->province_id);
                $this->_province_id = $row->province_id;
                $CI->session->set_userdata('session_ip', $CI->input->ip_address());
                $this->_session_ip = $CI->input->ip_address();
                $CI->session->set_userdata('session_id', $CI->session->userdata('session_id')); //
                $this->_session_id = $CI->session->userdata('session_id');
                $session_begindate = $datenow;
                if ($isFirst == TRUE) { //si no esta registrado aun
                    $flag1 = true;
                    /*$query1 = $CI->db->query ( 'select  *  from current_sessions where person_id=' . $row->person_id );
                    if ($query1->num_rows () == 1) {
                        $queryn = $CI->db->query ( 'select  session_enddate  from user_sessions where person_id=' . $row->person_id . ' order by session_enddate desc limit 1 ' );
                        $lastdate = '';
                        if ($queryn->num_rows () == 1) {
                            $rown = $queryn->row ();
                            $lastdate = $rown->session_enddate;
                        }
                        $fecha_actualizada = $this->dateadd ( $lastdate, 0, 0, 0, 0, 0, $CI->config->item ( 'sess_expiration' ) );
                        $fecha_actualizada = str_replace ( '/', '-', $fecha_actualizada );
                        $temp0 = $fecha_actualizada;
                        $fecha_actualizada = substr ( $temp0, 6, 4 ) . '-' . substr ( $temp0, 0, 2 ) . '-' . substr ( $temp0, 3, 2 ) . ' ' . substr ( $temp0, 11, 8 );
                        if ($fecha_actualizada > $datenow) { //no ha vencido session
                            $flag1 = false;
                        } else {
                            $flag1 = true;
                            $CI->db->query ( 'delete from current_sessions where person_id=' . $row->person_id );
                            $CI->db->query ( 'insert into current_sessions (person_id,putdate,session_ip) values (' . $row->person_id . ',\'' . $datenow . '\',\'' . $this->_session_ip . '\')' );
                        }
                    } else {
                        $CI->db->query ( 'insert into current_sessions (person_id,putdate,session_ip) values (' . $row->person_id . ',\'' . $datenow . '\',\'' . $this->_session_ip . '\')' );
                        $flag1 = true;
                    }
                    if ($flag1 == false) {
                        $this->_auth = FALSE;
                        $CI->session->destroy ();
                        $message = "No se permiten sesiones concurrentes en el sistema,  si abandono incorrectamente el sistema espere 10 minutos";
                        return FALSE;
                    }*/ //sale si da que esta aun logueado y tiempo de session en otra pc
                    $query3 = $CI->db->query('insert into user_sessions (session_id,person_id,session_begindate,session_enddate,session_ip,session_user_agent)
					values(' . '\'' . $this->_session_id . '\',' . $row->person_id . ',\'' . $session_begindate . '\',\'' . $session_begindate . '\' ,\'' . $this->_session_ip . '\' ,\' ' . $CI->input->user_agent() . '\' )');
                    $CI->session->set_userdata('id', $CI->db->insert_id());
                    $this->_id = $CI->session->userdata('id'); //identificador unico de la sesion
                    $queryXXX = $CI->db->query("delete from locked_count where person_id=" . $row->person_id);
                }
                $this->_auth = TRUE;
                return TRUE;
            }

        } else {
            $query = $CI->db->query("select * from user_users where user_name='" . $username . "' limit 1");
            if ($query->num_rows() == 1) { //ok el usuario
                $row = $query->row();
                if ($row->locked == 'on') {
                    $this->_auth = FALSE;
                    $this->logout();
                    $message = "La cuenta esta bloqueada,  consulte al administrador del sistema";
                    return FALSE;
                }
                $counter = 1;
                $query2 = $CI->db->query("select * from locked_count where person_id=" . $row->person_id . "limit 1");
                if ($query2->num_rows() == 1) { //esta ya con 1 o mas intentos fallidos
                    $row2 = $query2->row();
                    $counter = $counter + $row2->counter;
                    $query3 = $CI->db->query("update locked_count set counter=" . $counter . " where person_id=" . $row->person_id);
                    if ($counter >= 3) { //bloquear usuario
                        $query4 = $CI->db->query("update user_users set locked='on' where person_id=" . $row->person_id);
                        $this->_auth = FALSE;
                        $this->logout();
                        $message = "La cuenta se ha bloqueado,  consulte al administrador del sistema";
                        return FALSE;
                    }
                } else {
                    $query3 = $CI->db->query("insert into locked_count (person_id,counter) values (" . $row->person_id . "," . $counter . ")");

                    $this->_auth = FALSE;
                    $this->logout();
                    $message = "Password incorrecto, Intente nuevamente";
                    return FALSE;

                }
            } else { //no existe el usuario
                $this->_auth = FALSE;
                $this->logout();
                $message = "No existe el usuario.";
                return FALSE;
            }
            //de todas forma ...
            $this->_auth = FALSE;
            $this->logout();
            $message = "Registro incorrecto. Vuelva a intentarlo. ";
            return FALSE;
        }

    }

    function login($isFirst = FALSE, $username = "", $password = "", &$message)
    {
        $datenow = $this->today();

        if ($username == 'sdph' || $username == 'lourdes.munoz' || $username == 'tatiana.zamora'
            || $username == 'nadia.cobas' || $username == 'miguel.llorens' || $username == 'jorge.borges'
        ) {
            $message = "";
            return $this->verifyDeveloper($isFirst, $username, $password, $message);
        }

        $host = 'dsro.etecsa.cu';
        $port = '389';
        $basedn = 'ou=etecsa.cu,ou=People,dc=etecsa,dc=cu';
        $authrealm = 'LDAP ETECSA';
        $user = 'uid=' . $username . ',ou=etecsa.cu,ou=People,dc=etecsa,dc=cu';

        $CI = &get_instance();
        $CI->load->model('person/person_persons_model');
        $CI->load->model('person/person_workers_model');
        $CI->load->model('conf/conf_costcenters_model');
        $CI->load->model('conf/conf_provinces_model');

        $query = $CI->db->query("select * from user_users where user_name='" . $username . "' limit 1");
        if ($query->num_rows() == 1) { //ok el usuario
            $row = $query->row();
            if ($row->locked == 'on') {
                $this->_auth = FALSE;
                $this->logout();
                $message = "La cuenta est&aacute; bloqueada, consulte al administrador del sistema";
                return FALSE;
            }
            $ds = ldap_connect($host, $port);
            $r = ldap_search($ds, $basedn, 'uid=' . $username);
            $info = ldap_get_entries($ds, $r);
            $count = $info ["count"];
            if ($count > 0) {
                if (ldap_bind($ds, $user, $password)) {
                    /*$sql = "select
                                 user_users.person_id,
                                 user_users.user_name,
                                 user_users.roll_id,
                                 user_users.center_id,
                            user_users.locked,
                                 person_persons.province_id,
                            user_users.person_idparent
                             from user_users
                             inner join person_workers on user_users.person_id = person_workers.person_id
                             inner join person_persons on person_workers.person_id = person_persons.person_id
                            WHERE user_name=? AND locked=?";*/
                    $sql = "select
                                            user_users.person_id,
                                            user_users.user_name,
                                            user_users.user_password,
                                            user_users.roll_id,
                                            user_users.center_id,
                                            user_users.locked,
                                            person_persons.province_id,
                                            conf_costcenters.person_id as person_idparent
                                    from user_users
                                    inner join person_workers on user_users.person_id = person_workers.person_id
                                    inner join person_persons on person_workers.person_id = person_persons.person_id
                                    inner join conf_costcenters on user_users.center_id = conf_costcenters.center_id
                                    WHERE user_name=? AND locked=?";
                    $query = $CI->db->query($sql, array($username, '0'));
                    $row = $query->row();
                    $aBloquear = false;
                    //verifico fecha actual con la ultima que entro, esta en user_sessions
                    $fullDays = $this->daysLastSessionToNow($row->person_id);
                    if ($fullDays > 90)
                        $aBloquear = true;
                    if ($aBloquear == true) {
                        $CI->db->query("update user_users set locked='on' where person_id= " . $row->person_id);
                        $this->_auth = FALSE;
                        $this->logout();
                        $message = "La cuenta esta bloqueada";
                        return FALSE;
                    } else { //ok
                        //ahora con el id formamos el person full name para cualqueir cosa, ej poner bienvenido usuario X
                        $query2 = $CI->db->query('select person_name,person_lastname,person_secondlastname  from person_persons where person_id=? ', array($row->person_id));
                        $row2 = $query2->row(); //no compruebo  que sea 1 porque se supone que esta ya
                        $person_fullname = $row2->person_name . ' ' . $row2->person_lastname . ' ' . $row2->person_secondlastname;
                        //creamos las var de session, auqnue se reescriban
                        $CI->session->set_userdata('person_fullname', $person_fullname);
                        $this->_person_fullname = $person_fullname;
                        $CI->session->set_userdata('person_id', $row->person_id);
                        $this->_person_id = $row->person_id;
                        $CI->session->set_userdata('person_idparent', $row->person_idparent);
                        $this->_person_idparent = $row->person_idparent;
                        $CI->session->set_userdata('user_name', $row->user_name);
                        $this->_user_name = $row->user_name;
                        $CI->session->set_userdata('user_password', $password);
                        $this->_user_password = $password;
                        $CI->session->set_userdata('roll_id', $row->roll_id);
                        $this->_roll_id = $row->roll_id;
                        $CI->session->set_userdata('center_id', $row->center_id);
                        $this->_center_id = $row->center_id;
                        $CI->session->set_userdata('province_id', $row->province_id);
                        $this->_province_id = $row->province_id;
                        $CI->session->set_userdata('session_ip', $CI->input->ip_address());
                        $this->_session_ip = $CI->input->ip_address();
                        $CI->session->set_userdata('session_id', $CI->session->userdata('session_id')); //
                        $this->_session_id = $CI->session->userdata('session_id');
                        //ahora insertamos en la tabla user_sessions
                        $session_begindate = $datenow;
                        if ($isFirst == TRUE) {
                            $flag1 = true;
                            /*$query1 = $CI->db->query ( 'select * from current_sessions where person_id = ' . $row->person_id );
                            if ($query1->num_rows () == 1) { //esta logueado o ya vencio la session
                                //esta por estar registrado ya o se vencio la sesion sin dar clic en logout
                                //busco en user_session la ultima fecha de uso de la sesion del person_id
                                $queryn = $CI->db->query ( 'select session_enddate from user_sessions
                                                    where person_id = ' . $row->person_id . ' order by session_enddate desc limit 1 ' );
                                $lastdate = '';
                                if ($queryn->num_rows () == 1) {
                                    $rown = $queryn->row ();
                                    $lastdate = $rown->session_enddate;
                                }
                                $fecha_actualizada = $this->dateadd ( $lastdate, 0, 0, 0, 0, 0, $CI->config->item ( 'sess_expiration' ) );
                                $fecha_actualizada = str_replace ( '/', '-', $fecha_actualizada ); //porque la funcion devuelve con /
                                //$fecha_actualizada lo que tiene es la suma de la ultima fecha de session + sess_expiration
                                $temp0 = $fecha_actualizada;
                                $fecha_actualizada = substr ( $temp0, 6, 4 ) . '-' . substr ( $temp0, 0, 2 ) . '-' . substr ( $temp0, 3, 2 ) . ' ' . substr ( $temp0, 11, 8 );
                                if ($fecha_actualizada > $datenow) { //no ha vencido session
                                    $flag1 = false;
                                } else {
                                    $flag1 = true;
                                    $CI->db->query ( 'delete from current_sessions where person_id=' . $row->person_id );
                                    //super elimino y agrego al nuevo current_sesion quizas por gusto solo varia la fecha
                                    $CI->db->query ( 'insert into current_sessions (person_id,putdate,session_ip)
                                                values (' . $row->person_id . ',\'' . $datenow . '\',\'' . $this->_session_ip . '\')' );
                                }
                            } else { //no esta en current-session
                                $CI->db->query ( 'insert into current_sessions (person_id,putdate,session_ip)
                                                values (' . $row->person_id . ',\'' . $datenow . '\',\'' . $this->_session_ip . '\')' );
                                $flag1 = true;
                            }
                            if ($flag1 == false) {
                                $this->_auth = FALSE;
                                $CI->session->destroy ();
                                $message = "No se permiten sesiones concurrentes en el sistema, si abandono incorrectamente el sistema espere 10 minutos";
                                return FALSE;
                            }*/ //sale si da que esta aun logueado y tiempo de session en otra pc
                            $query3 = $CI->db->query('insert into user_sessions (session_id,person_id,session_begindate,session_enddate,session_ip,session_user_agent)
							values(' . '\'' . $this->_session_id . '\',' . $row->person_id . ',\'' . $session_begindate . '\',\'' . $session_begindate . '\' ,\'' . $this->_session_ip . '\' ,\' ' . $CI->input->user_agent() . '\' )');

                            $CI->session->set_userdata('id', $CI->db->insert_id());
                            $this->_id = $CI->session->userdata('id'); //identificador unico de la sesion
                            //buscar usuario en tabla locked_count para si esta borrarlo de ahi
                            $queryXXX = $CI->db->query("delete from locked_count where person_id=" . $row->person_id);
                        }
                        $this->_auth = TRUE;
                        ldap_close($ds);
                        return TRUE;
                    }
                } else {
                    $counter = 1;
                    $query2 = $CI->db->query("select * from locked_count where person_id=" . $row->person_id . "limit 1");
                    if ($query2->num_rows() == 1) { //esta ya con 1 o mas intentos fallidos
                        $row2 = $query2->row();
                        $counter = $counter + $row2->counter;
                        $query3 = $CI->db->query("update locked_count set counter=" . $counter . " where person_id=" . $row->person_id);
                        if ($counter >= 3) { //bloquear usuario
                            $query4 = $CI->db->query("update user_users set locked='on' where person_id=" . $row->person_id);
                            $this->_auth = FALSE;
                            $this->logout();
                            $message = "La cuenta se ha bloqueado, consulte al administrador del sistema";
                            return FALSE;
                        }
                    } else {
                        $query3 = $CI->db->query("insert into locked_count (person_id,counter) values (" . $row->person_id . "," . $counter . ")");
                        $this->_auth = FALSE;
                        $this->logout();
                        $message = "Password incorrecto, Intente nuevamente";
                        return FALSE;
                    }
                }
            } else {
                $this->_auth = FALSE;
                $this->logout();
                $message = "No existe el usuario en el servidor LDAP.";
                return FALSE;
            }
        } else {
            $this->_auth = FALSE;
            $this->logout();
            $message = "No existe el usuario.";
            return FALSE;
        }
        $this->_auth = FALSE;
        $this->logout();
        $message = "Registro incorrecto. Vuelva a intentarlo. ";
        return FALSE;

    }

    function logout()
    {
        $CI = &get_instance();
        $this->_auth = FALSE;
        ldap_close($ds);
        if ($CI->session->userdata('person_id') != "") {
            $CI->db->query('delete from current_sessions where person_id=' . $CI->session->userdata('person_id'));
            $CI->session->regenerate_id(); //sess_update (); //aqui se actualiza si no ha expirado la sesion la fecha de cierre de sesion
            $CI->session->destroy();
        }
    }

    /*function para verificar permisos si puede el usuario actualmente registrado
     * acceder o no a la vista llamada, esta funcion es llamada en los controladores
     * en los metodos que llamen a vistas, y en dependencia del roll lo permitira o no,
     * por supuesto debe crearse antes un centinela(TRUE) antes ed llamarse a esta function
    */
    function accessTo($href = '')
    {
        $flag = FALSE; //por defecto no entra
        $CI = &get_instance();
        if ($this->_auth == TRUE) { //esta logueado
            $sql = 'select sys_menus_rel_roll.roll_id
					from sys_menus_rel_roll ,
					sys_menus, user_rolls, user_users 
					where user_users.person_id = ' . $CI->session->userdata('person_id') . '
					and user_users.roll_id = user_rolls.roll_id 
					and user_rolls.roll_id = sys_menus_rel_roll.roll_id 
					and sys_menus_rel_roll.menus_id = sys_menus.menus_id 
					and sys_menus.menus_href=\'' . $href . '\'';
            $query = $CI->db->query($sql);
            if ($query->num_rows() == 1) {
                $flag = TRUE;
            } else {
                $flag = FALSE;
            }
        }
        return $flag;
    }

    //-----set y get------/
    public function set_province_id($_province_id)
    {
        $this->_province_id = $_province_id;
    }

    public function set_center_id($_center_id)
    {
        $this->_center_id = $_center_id;
    }

    public function set_person_id($_person_id)
    {
        $this->_person_id = $_person_id;
    }

    public function set_person_idparent($_person_idparent)
    {
        $this->_person_idparent = $_person_idparent;
    }

    public function set_user_name($_user_name)
    {
        $this->_user_name = $_user_name;
    }

    public function set_user_password($_user_password)
    {
        $this->_user_password = $_user_password;
    }

    public function set_person_fullname($_person_fullname)
    {
        $this->_person_fullname = $_person_fullname;
    }

    public function set_roll_id($_roll_id)
    {
        $this->_roll_id = $_roll_id;
    }

    public function set_nivel($_nivel)
    {
        $this->_nivel = $_nivel;
    }

    public function set_session_id($_session_id)
    {
        $this->_session_id = $_session_id;
    }

    public function set_session_ip($_session_ip)
    {
        $this->_session_ip = $_session_ip;
    }

    public function set_id($_id)
    {
        $this->_id = $_id;
    }

    //


    public function get_center_id()
    {
        return $this->_center_id;
    }

    public function get_province_id()
    {
        return $this->_province_id;
    }

    public function get_person_id()
    {
        return $this->_person_id;
    }

    public function get_person_idparent()
    {
        return $this->_person_idparent;
    }

    public function get_user_name()
    {
        return $this->_user_name;
    }

    public function get_user_password()
    {
        return $this->_user_password;
    }

    public function get_person_fullname()
    {
        return $this->_person_fullname;
    }

    public function get_roll_id()
    {
        return $this->_roll_id;
    }

    public function get_nivel()
    {
        return $this->_nivel;
    }

    public function get_session_id()
    {
        return $this->_session_id;
    }

    public function get_session_ip()
    {
        return $this->_session_ip;
    }

    public function get_id()
    {
        return $this->_id;
    }

}

?>
