<?php

class Conf_viazul_routes extends Controller {

    function __construct() {
        parent::Controller();
        $this->load->model('conf/conf_viazul_routes_model', "conn", true);
    }

    function index() {
        $centinela = new Centinela();
        $flag = $centinela->accessTo('conf/conf_viazul_routes');
        if ($flag) {
            $this->load->view('sys/header_view');
            $this->load->view('conf/conf_viazul_routes_view');
            $this->load->view('sys/footer_view');
        } else {
            $this->redirectError();
        }
    }

    function redirectError() {
        $this->load->view('sys/header_view');
        $this->load->view('error_message');
        $this->load->view('sys/footer_view');
    }

    /**
     * Busca los datos para llenar el grid y los devuelve en formato JSON
     *
     */
    public function setDataGrid() {
        $province_id = $this->input->post('province_id');
        $datos = $this->conn->getData($province_id);
        die("{data:" . json_encode($datos) . "}");
    }

    public function setData() {
        $to = (!isset($_POST ['limit'])) ? 50 : $_POST ['limit'];
        $from = (!isset($_POST ["start"])) ? 0 : $_POST ["start"];
        $origen = $this->input->post('from');
        $destino = $this->input->post('to');
        $data = $this->conn->getDataGrid($to, $from, $origen, $destino);
        $cant = $this->conn->getCant($to, $from, $origen, $destino);
        die("{count:" . $cant . ",data:" . json_encode($data) . "}");
    }

    public function getPriceByRoute() {
        $province_id = $this->input->post('province_id');
        $province_id2 = $this->input->post('province_id');
        $datos = $this->conn->getPriceByRoute(1,2);
        die("{data:" . json_encode($datos) . "}");
    }

    public function canInsert() {
        $datos = $this->conn->canInsert(1,2);
        die("{data:" . json_encode($datos) . "}");
    }

    /**
     * Funcion para Insertar hotels
     *
     */
    function insert() {
        $centinela = new Centinela();
        $flag = $centinela->accessTo('conf/conf_viazul_routes');
        if ($flag) {
            $result = $this->conn->insert();
            die($result);
        } else {
            $this->redirectError();
        }
    }

    /**
     * Elimina Ruta
     * recibe como parametro el id de la Ruta
     */
    function delete($id) {
        $centinela = new Centinela();
        $flag = $centinela->accessTo('conf/conf_viazul_routes');
        if ($flag) {
            $this->conn->delete($id);
        } else {
            $this->redirectError();
        }
    }

    function getById($id) {
        $data = $this->conn->getById($id);
        die("{data : " . json_encode($data) . "}");
    }

    function exportExcel() {
        $today = new Dates();
        $this->load->library('excel');
        $this->db->select('conf_viazul_routes.hotel_name, 
                           conf_viazul_routes.hotel_price, 
                           conf_provinces.province_name, 
                           conf_hotelchains.chain_name');
        $this->db->from('conf_viazul_routes');
        $this->db->join('conf_provinces', 'conf_provinces.province_id = conf_viazul_routes.province_id', 'inner');
        $this->db->join('conf_hotelchains', 'conf_hotelchains.chain_id = conf_viazul_routes.chain_id', 'inner');
        $this->db->where('conf_viazul_routes.hotel_deleted', 'no');
        $this->db->order_by("conf_provinces.province_id", "asc");
        $result = $this->db->get();
        $value = array();
        if ($result->result() != null) {
            foreach ($result->result() as $row) {
                $value [] = array(
                    'Provincia' => $row->province_name,
                    'Hotel' => $row->hotel_name,
                    'Precio' => $row->hotel_price,
                    'Cadena' => $row->chain_name
                );
            }
        }

        $filename = "Relacion_de_hoteles";
        /* header("Content-Disposition: attachment; filename=\"$filename\""); 
          header("Content-Type: application/vnd.ms-excel"); */
        header("Content-type: application/x-msdownload");
        header("Content-Disposition: attachment; filename=$filename.xls");

        $flag = false;
        foreach ($value as $row) {
            if (!$flag) { // display field/column names as first row 
                echo implode("\t", array_keys($row)) . "\r\n";
                $flag = true;
            }
            array_walk($row, array($this, 'cleanData'));
            echo implode("\t", array_values($row)) . "\r\n";
        }
        exit;
    }

    function cleanData(&$str) {
        $str = preg_replace("/\t/", "\\t", $str);
        $str = preg_replace("/\r?\n/", "\\n", $str);
        if (strstr($str, '"'))
            $str = '"' . str_replace('"', '""', $str) . '"';
    }

}