<?php

class Conf_viazul_routes_model extends Model
{
    const TABLE_NAME = 'conf_viazul_routes';

    function __construct()
    {
        parent::Model();

    }

    /**
     * Funcion que devuelve los valores de la tabla
     *
     * @param int $hasta
     * @param int $desde
     * @return array
     */
    public function getData($province_id)
    {
        $this->db->select('conf_viazul_routes.*,
							conf_provinces.province_name');
        $this->db->from(self::TABLE_NAME);
        $this->db->join('conf_provinces', 'conf_provinces.province_id = ' . self::TABLE_NAME . '.province_id', 'inner');
        if (!is_null($province_id) && is_numeric($province_id)) {
            $this->db->where(self::TABLE_NAME . '.province_id', $province_id);
        }
        $this->db->where('viazul_place_deleted', 'no');
        $this->db->order_by("conf_provinces.province_id", "asc");
        $result = $this->db->get();
        $value = array();
        if ($result->result() != null) {
            foreach ($result->result() as $row) {
//				$deleted = ucfirst($row->viazul_place_deleted);
                $value [] = array('viazul_place_id' => $row->viazul_place_id,
                    'viazul_place_name' => $row->viazul_place_name,
                    'viazul_place_deleted' => ucfirst($row->viazul_place_deleted),
                    'province_id' => $row->province_id,
                    'province_name' => $row->province_name);
            }

        }
        return $value;
    } // de la funcion

    public function getDataGrid($to, $from, $place_from, $place_to)
    {
        $this->db->select('conf_viazul_routes.*,
							po.viazul_place_name as origen,
							prov_o.province_name as provincia_o,
							pd.viazul_place_name as destino,
							prov_d.province_name as provincia_d');
        $this->db->from(self::TABLE_NAME);
        $this->db->join('conf_viazul_places as po', 'po.viazul_place_id = ' . self::TABLE_NAME . '.viazul_place_id_form', 'inner');
        $this->db->join('conf_provinces as prov_o', 'prov_o.province_id = ' . 'po.province_id', 'inner');
        $this->db->join('conf_viazul_places as pd', 'pd.viazul_place_id = ' . self::TABLE_NAME . '.viazul_place_id_to', 'inner');
        $this->db->join('conf_provinces as prov_d', 'prov_d.province_id = ' . 'pd.province_id', 'inner');
        if (!empty($place_from)) {
            $this->db->where(self::TABLE_NAME . '.viazul_place_id_form', $place_from);
        }
        if (!empty($place_to)) {
            $this->db->where(self::TABLE_NAME . '.viazul_place_id_to', $place_to);
        }
        $this->db->order_by("conf_viazul_routes.viazul_place_id_form", "asc");
        $this->db->order_by("conf_viazul_routes.viazul_place_id_to", "asc");
        $this->db->limit($to, $from);
        $result = $this->db->get();
        $value = array();
        if ($result->result() != null) {
            foreach ($result->result() as $row) {
                $value [] = array('viazul_route_id' => $row->viazul_route_id,
                    'viazul_place_id_form' => $row->viazul_place_id_form,
                    'viazul_place_id_to' => $row->viazul_place_id_to,
                    'viazul_route_price' => number_format($row->viazul_route_price, 2, '.', ''),
                    'origen' => $row->provincia_o . ' / ' . $row->origen,
                    'province_name' => $row->provincia_o,
                    'destino' => $row->provincia_d . ' / ' . $row->destino,
                    'viazul_route_deleted' => ucfirst($row->viazul_route_deleted));
            }

        }
        return $value;
    } // de la funcion

    /**
     * Funcion que devuelve la cantidad de registros en la tabla conf_municipalities
     *
     */
    public function getCant($to, $from, $place_from, $place_to)
    {
        $this->db->select('conf_viazul_routes.*,
							po.viazul_place_name as origen,
							pd.viazul_place_name as destino');
        $this->db->from(self::TABLE_NAME);
        $this->db->join('conf_viazul_places as po', 'po.viazul_place_id = ' . self::TABLE_NAME . '.viazul_place_id_form', 'inner');
        $this->db->join('conf_viazul_places as pd', 'pd.viazul_place_id = ' . self::TABLE_NAME . '.viazul_place_id_to', 'inner');
        if (!empty($place_from)) {
            $this->db->where(self::TABLE_NAME . '.viazul_place_id_form', $place_from);
        }
        if (!empty($place_to)) {
            $this->db->where(self::TABLE_NAME . '.viazul_place_id_to', $place_to);
        }
        $this->db->limit($to, $from);
        return $this->db->count_all_results();
    }

    /**
     * Esta es la funcion encargada de insertar
     *
     * @return boolean
     */
    public function insert()
    {
        $viazul_route_id = $this->input->post('viazul_route_id');
        $viazul_route['viazul_place_id_form'] = $this->input->post('viazul_place_id_form');
        $viazul_route['viazul_place_id_to'] = $this->input->post('viazul_place_id_to');
        $viazul_route['viazul_route_price'] = $this->input->post('viazul_route_price');
        if (empty ($viazul_route_id)) {
            $flag = $this->canInsert($viazul_route['viazul_place_id_form'], $viazul_route['viazul_place_id_to']);

            if (intval($flag) === 0) {
                $this->db->trans_begin();
                $re = $this->db->insert(self::TABLE_NAME, $viazul_route);
                $viazul_route_id = $this->db->insert_id();
                $logs = new Logs ();
                $myquery = $logs->sqlinsert(self::TABLE_NAME, $viazul_route);
                $logs->write(self::TABLE_NAME, 'INSERT', $myquery);

                if ($this->db->trans_status() === FALSE) {
                    $this->db->trans_rollback();
                } else {
                    $this->db->trans_commit();
                }
                if ($re == true) {
                    return "{success : " . $re . "}";
                } else
                    return "{success: false}";
            } else {
                return "{success: false, errors: { reason: 'La ruta que esta tratando de introducir ya existe.' }}";
            }

        } else {
            $viazul_route['viazul_route_deleted'] = strtolower($this->input->post('viazul_route_deleted'));
            $this->db->where('viazul_route_id', $viazul_route_id);
            $this->db->trans_begin();
            $re = $this->db->update(self::TABLE_NAME, $viazul_route);
            $logs = new Logs ();

            $mywhere = 'where viazul_route_id = ' . $viazul_route_id;
            $myquery = $logs->sqlupdate(self::TABLE_NAME, $viazul_route, $mywhere);

            $logs->write(self::TABLE_NAME, 'UPDATE', $myquery);

            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
            } else {
                $this->db->trans_commit();
            }

            if ($re == true) {
                return "true";
            } else
                return "false";
        }

    }

    public function canInsert($place_from, $place_to)
    {
        $this->db->select('conf_viazul_routes.*');
        $this->db->from(self::TABLE_NAME);
        $this->db->where(self::TABLE_NAME . '.viazul_place_id_form', $place_from);
        $this->db->where(self::TABLE_NAME . '.viazul_place_id_to', $place_to);
        return $this->db->count_all_results();
    } // de la funcion

    /**
     * Funcion para eliminar un hotel por su nombre
     *
     * @param string $id
     */

    public function delete($id)
    {
        $viazul_route['viazul_route_deleted'] = 'si';
        $this->db->where('viazul_route_id', $id);
        $this->db->trans_begin();
        $re = $this->db->update(self::TABLE_NAME, $viazul_route);
        $logs = new Logs ();
        $mywhere = 'where viazul_route_id = ' . $id;
        $myquery = $logs->sqldelete(self::TABLE_NAME, $mywhere);
        $logs->write(self::TABLE_NAME, 'DELETE', $myquery);

        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
        } else {
            $this->db->trans_commit();
        }

    }

    public function getById($viazul_route_id)
    {
        $this->db->select('conf_viazul_routes.*,
							po.viazul_place_name,
							prov_o.province_name as provincia_o,
							pd.viazul_place_name as destino,
							prov_d.province_name as provincia_d');
        $this->db->from(self::TABLE_NAME);
        $this->db->join('conf_viazul_places as po', 'po.viazul_place_id = ' . self::TABLE_NAME . '.viazul_place_id_form', 'inner');
        $this->db->join('conf_provinces as prov_o', 'prov_o.province_id = ' . 'po.province_id', 'inner');
        $this->db->join('conf_viazul_places as pd', 'pd.viazul_place_id = ' . self::TABLE_NAME . '.viazul_place_id_to', 'inner');
        $this->db->join('conf_provinces as prov_d', 'prov_d.province_id = ' . 'pd.province_id', 'inner');
        $this->db->where(self::TABLE_NAME . '.viazul_route_id', $viazul_route_id);
        $result = $this->db->get();
        $value = array();
        if ($result->result() != null) {
            foreach ($result->result() as $row) {
                $value [] = array(
                    'viazul_route_id' => $row->viazul_route_id,
                    'viazul_place_id_form' => $row->viazul_place_id_form,
                    'viazul_place_id_to' => $row->viazul_place_id_to,
                    'viazul_route_price' => number_format($row->viazul_route_price, 2, '.', ''),
                    'viazul_place_name' => $row->provincia_o . ' / ' . $row->viazul_place_name,
                    'destino' => $row->provincia_d . ' / ' . $row->destino,
                    'viazul_route_deleted' => ucfirst($row->viazul_route_deleted)
                );
            }

        }
        return $value;

    }

    public function getPriceByRoute($from, $to)
    {
        $this->db->select('viazul_route_price');
        $this->db->from(self::TABLE_NAME);
        $this->db->where('viazul_place_id_form', $from);
        $this->db->where('viazul_place_id_to', $to);
        $result = $this->db->get()->row();
        return number_format($result->viazul_route_price, 2, '.', '');
    }


}
