<?php

/**
 * Created by PhpStorm.
 * User: ramiro
 * Date: 2/04/14
 * Time: 12:17
 */
class Sagec_Sdph_Cliente
{
//put your code here
    private $_sagec_cliente;

    public function __construct ()
    {
        $CI = & get_instance ();
        $this->_sagec_cliente = new SoapClient(null, array ('location' => $CI->config->item ('ws'),
            'uri' => 'urn:webservices', "style" => SOAP_RPC, "use" => SOAP_ENCODED));
    }

    /**
     * @param $no_solicitud
     * @param $id_solicitud_estado_sdph (2 => editado, 3=> cancelado)
     * @param $hotel
     * @param $id_trasporte_estado
     * @return mixed
     */
    public function cambiar_estado_solicitud ($no_solicitud, $id_solicitud_estado_sdph, $hotel, $id_trasporte_estado)
    {
        $parametros = array (new SoapParam($no_solicitud, "no_solicitud"),
            new SoapParam($id_solicitud_estado_sdph, "id_solicitud_estado_sdph"),
            new SoapParam($hotel, "hotel"),
            new SoapParam($id_trasporte_estado, "id_trasporte_estado"));
        return $this->_sagec_cliente->__call ("cambiar_estado_solicitud", $parametros, array ("uri" => "urn:webservices", "soapaction" => "urn:webservices#cambiar_estado_solicitud"));
    }

}