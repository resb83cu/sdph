<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Dates {

    function Dates() {
        
    }

    public function now() {
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

        $date = $fecha ['year'] . '-' . $mes . '-' . $dia . ' ' . $hora . ':' . $minuto . ':' . $segundo;
        return $date;
    }

    public function nowShort() {
        $fecha = getdate();
        $mes = $fecha ['mon'];
        strlen($mes) == 1 ? $mes = '0' . $mes : $mes = $mes;
        $dia = $fecha ['mday'];
        strlen($dia) == 1 ? $dia = '0' . $dia : $dia = $dia;

        $date = $fecha ['year'] . '-' . $mes . '-' . $dia;
        return $date;
    }

    /*
     *
     * Metodo para contar la diferencia en dias entre 2 fechas
     * los parametros deben pasarse en en forma de arreglos o
     * sea, que se le haya aplicado un date_parse()
     * */

    public function dateDiffNow($begin, $end) {
        $beginYear = $begin ['year'];
        $beginMonth = $begin ['mon'];
        $beginDay = $begin ['mday'];
        $endYear = $end ['year'];
        $endMonth = $end ['month'];
        $endDay = $end ['day'];
        $beginDate = mktime(0, 0, 0, $beginMonth, $beginDay, $beginYear);
        $endDate = mktime(0, 0, 0, $endMonth, $endDay, $endYear);
        $dateDiff = $endDate - $beginDate;
        $diff = floor($dateDiff / (60 * 60 * 24));
        return $diff;
    }

    public function dateDiff($begin, $end) {
        $beginYear = $begin ['year'];
        $beginMonth = $begin ['month'];
        $beginDay = $begin ['day'];
        $endYear = $end ['year'];
        $endMonth = $end ['month'];
        $endDay = $end ['day'];
        $beginDate = mktime(0, 0, 0, $beginMonth, $beginDay, $beginYear);
        $endDate = mktime(0, 0, 0, $endMonth, $endDay, $endYear);
        $dateDiff = $endDate - $beginDate;
        $diff = floor($dateDiff / (60 * 60 * 24));
        return $diff;
    }

    public function diasEntreFechas($begin, $end) {
        $f1 = new DateTime($begin);
        $f2 = new DateTime($end);
        $interval = $f1->diff($f2);
        return $interval->format('%a');
    }

    /*
     * Este método devuelve el dia de la semana segun la fecha
     * que se le pase, el Domingo = 0, Sabado = 6
     * a la fecha debe aplicarse date_parse
     * 
     * */

    public function week_day($date) {
        //$dias = array('Domingo', 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado');
        $dates = date_parse($date);
        $year = $dates ['year'];
        $month = $dates ['month'];
        $day = $dates ['day'];
        return date("w", mktime(0, 0, 0, $month, $day, $year));
    }

    public function tomorrow($date) {
        $dates = date_parse($date);
        $year = $dates ['year'];
        $month = $dates ['month'];
        $day = $dates ['day'];
        //return mktime(0, 0, 0, date("m") , date("d")+1, date("Y"));
        $mydate = mktime(0, 0, 0, $month, $day + 1, $year);
        return strftime('%Y-%m-%d', $mydate);
    }

    function DateAdd($date) {
        $date_time_array = date_parse($date);
        $hours = 0;
        $minutes = 0;
        $seconds = 0;
        $month = $date_time_array ['month'];
        $day = $date_time_array ['day'];
        $year = $date_time_array ['year'];
        $day += 1;
        $timestamp = mktime($hours, $minutes, $seconds, $month, $day, $year);
        return strftime('%Y-%m-%d', $timestamp);
    }

    function DateAddManyDays($date, $noDays) {
        $date_time_array = date_parse($date);
        $hours = 0;
        $minutes = 0;
        $seconds = 0;
        $month = $date_time_array ['month'];
        $day = $date_time_array ['day'];
        $year = $date_time_array ['year'];
        $day += $noDays;
        $timestamp = mktime($hours, $minutes, $seconds, $month, $day, $year);
        return strftime('%Y-%m-%d', $timestamp);
    }

}
