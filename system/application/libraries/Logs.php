<?php
if (!defined('BASEPATH'))
    exit ('No direct script access allowed');

class Logs
{

    function Logs()
    {

    }

    function sqlinsert($tablename, $values)
    {
        $myquery = 'insert into ' . $tablename . ' values(';
        foreach ($values as $value) {
            $myquery .= $value . ',';
        }
        $myquery = substr($myquery, 0, strlen($myquery) - 1);
        $myquery .= ')';
        return $myquery;
    }

    function sqlupdate($tablename, $values, $mywhere = '')
    {
        $myquery = 'update ' . $tablename . ' values(';
        foreach ($values as $value) {
            $myquery .= $value . ',';
        }
        $myquery = substr($myquery, 0, strlen($myquery) - 1);
        $myquery .= ') ' . $mywhere;
        return $myquery;

    }

    function sqldelete($tablename, $mywhere)
    {
        $myquery = 'delete ' . $tablename . '  ' . $mywhere;
        return $myquery;
    }

    //
    function write($tablename, $operation, $query = '')
    {
        $CI = & get_instance();
        $dates = new Dates ();
        $date = $dates->now();
        $sql = 'insert into logs (user_id,session_id,date,tablename,operation,query) values(' . $CI->session->userdata('person_id') . ',' . $CI->session->userdata('id') . ',\'' . $date . '\',\'' . $tablename . '\',\' ' . $operation . '\' ,\'' . $query . '\' ) ';
        $CI->db->query($sql);

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
        $enddate = $fecha['year'] . '-' . $mes . '-' . $dia . ' ' . $hora . ':' . $minuto . ':' . $segundo;
        if ($CI->session->userdata('id') != FALSE) //verificando que no ha expirado aun la sesion,si expiro no se hace nada
            $CI->db->query('update user_sessions  set session_id=\'' . session_id() . '\'      , session_enddate=\'' . $enddate . '\'  where  id = ' . $CI->session->userdata('id') . '   ');
        //
    }

    function writeService($tablename, $operation, $query = '', $person_id)
    {
        $CI = & get_instance();
        $dates = new Dates ();
        $date = $dates->now();
        $agent = 'webservice';
        $ip = '192.168.8.115';
        $CI->db->query('insert into user_sessions (session_id,person_id,session_begindate,session_enddate,session_ip,session_user_agent)
					values(' . '\'' . 'sagecwebservicesession' . rand(1, 1000000) . '\',' . $person_id . ',\'' . $date . '\',\'' . $dates->DateAddManyDays($date, 1) . '\',\' ' . $ip . '\' ,\'' . $agent . '\' )') ;
        $session = $CI->db->insert_id();
        $sql = 'insert into logs (user_id,session_id,date,tablename,operation,query) values(' . $person_id . ',' . $session . ',\'' . $date . '\',\'' . $tablename . '\',\' ' . $operation . '\' ,\'' . $query . '\' ) ';
        $CI->db->query($sql);

    }

}

?>