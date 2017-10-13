<?php

/**
 * Created by PhpStorm.
 * User: ramiro
 * Date: 2/04/14
 * Time: 11:42
 */
class Sdph_Sagec_Service
{
    /**
     * @param integer $no_solicitud
     * @return bool
     */
    public function cancelarsolicitud ($no_solicitud, $user)
    {
        $CI = & get_instance ();
        $CI->load->model ('request/request_requests_model', 'conn', true);
        return $CI->conn->cancelSolicitudService($no_solicitud, $user);
    }

    /**
     * @param string $observaciones_solicitud_trabajador
     * @param string $solicitante
     * @param string $trabajador
     * @param string $fecha_entrada_hosp_solicitud_trabajador
     * @param string $fecha_salida_hosp_solicitud_trabajador
     * @param string $fecha_ida_pasaje_solicitud_trabajador
     * @param string $fecha_regreso_pasaje_solicitud_trabajador
     * @param string $origen_ubicacion
     * @param string $destino_ubicacion
     * @param string $regreso_ubicacion
     * @param string $transporte
     * @param string $uorganizativa_cargo_presupuesto
     * @param string $dieta_desde
     * @param string $dieta_hasta
     * @param string $centro_contable
     * @param string $consecutivo_modelo
     * @param string $persona_a_recibir_modelo
     * @param string $transporte_regreso
     * @return array array('result'=>true,'msg'=>'M_SOLICITUD_OK','no_solicitud'=>'500')
     */
    public function enviarsolicitud (
        $observaciones_solicitud_trabajador,
        $solicitante,
        $trabajador,
        $fecha_entrada_hosp_solicitud_trabajador,
        $fecha_salida_hosp_solicitud_trabajador,
        $fecha_ida_pasaje_solicitud_trabajador,
        $fecha_regreso_pasaje_solicitud_trabajador,
        $origen_ubicacion,
        $destino_ubicacion,
        $regreso_ubicacion,
        $transporte,
        $uorganizativa_cargo_presupuesto,
        $dieta_desde,
        $dieta_hasta,
        $centro_contable,
        $consecutivo_modelo,
        $persona_a_recibir_modelo,
        $transporte_regreso,
        $request_area)
    {
        $CI = & get_instance ();
        $CI->load->model ('request/request_requests_model', 'conn', true);
        return $CI->conn->insertService (
            $observaciones_solicitud_trabajador,
            $solicitante,
            $trabajador,
            $fecha_entrada_hosp_solicitud_trabajador,
            $fecha_salida_hosp_solicitud_trabajador,
            $fecha_ida_pasaje_solicitud_trabajador,
            $fecha_regreso_pasaje_solicitud_trabajador,
            $origen_ubicacion,
            $destino_ubicacion,
            $regreso_ubicacion,
            $transporte,
            $uorganizativa_cargo_presupuesto,
            $dieta_desde,
            $dieta_hasta,
            $centro_contable,
            $consecutivo_modelo,
            $persona_a_recibir_modelo,
            $transporte_regreso,
            $request_area);

//        $msg = array (
//            'E_SOLICITANTE_NO_EXISTE',
//            'E_SOLICITANTE_SIN_PERMISO',
//            'E_SDPH_VAL_DATOS'
//        );
//
    }

    /**
     * Obtener un listado.
     * @param string $entidad De que se va a obtener el listado. hotel, transporte, entidad
     * @return array Formato: array('1' => 'TEXTO', '2' => 'TEXTO2') Llave es el ID
     */
    public function get ($entidad)
    {
        $CI = & get_instance ();
        switch ($entidad) {
            case 'hotel':
            {
                $CI->load->model ('conf/conf_hotels_model', 'conn', true);
                return $CI->conn->getDataService (); //array('1' => 'Hotel 1', '2' => 'Hotel 2', '3' => 'Hotel 3');
            }
            case 'transporte':
            {
                $CI->load->model ('conf/conf_lodgingtransports_model', 'conn', true);
                return $CI->conn->getDataService ();
            }
            case 'entidad':
            { // Unidades organizativas

                $CI->load->model ('conf/conf_costcenters_model', 'conn', true);
                return $CI->conn->getDataService ();
            }
            case 'anticipo':
            { // Unidades organizativas

                $CI->load->model ('conf/conf_costcenters_model', 'conn', true);
                return $CI->conn->getDataSapService ();
            }
            case 'ubicacion':
            { // Provincias
                $CI->load->model ('conf/conf_provinces_model', 'conn', true);
                return $CI->conn->getDataService ();
            }
            default:
                { // Estados de la solicitud
                return array ('1' => 'Creada', '2' => 'Editada', '3' => 'Cancelada');
                }
        }

    }
}