<?php

class Conf_viazul_places_model extends Model
{
    const TABLE_NAME = 'conf_viazul_places';

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
        $this->db->select('conf_viazul_places.*,
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

    public function getAllData()
    {
        $this->db->select('conf_viazul_places.*,
							conf_provinces.province_name');
        $this->db->from(self::TABLE_NAME);
        $this->db->join('conf_provinces', 'conf_provinces.province_id = ' . self::TABLE_NAME . '.province_id', 'inner');
        $this->db->where('viazul_place_deleted', 'no');
        $this->db->order_by("conf_viazul_places.province_id", "asc");
        $this->db->order_by("conf_viazul_places.viazul_place_name", "asc");
        $result = $this->db->get();
        $value = array();
        if ($result->result() != null) {
            foreach ($result->result() as $row) {
                $value [] = array('viazul_place_id' => $row->viazul_place_id,
                    'viazul_place_name' => $row->province_name . ' / ' . $row->viazul_place_name);
            }

        }
        return $value;
    } // de la funcion

    public function getDataGrid($to, $from, $name, $province)
    {
        $this->db->select('conf_viazul_places.*,
							conf_provinces.province_name');
        $this->db->from(self::TABLE_NAME);
        $this->db->join('conf_provinces', 'conf_provinces.province_id = ' . self::TABLE_NAME . '.province_id', 'inner');
        if (!empty($province)) {
            $this->db->where(self::TABLE_NAME . '.province_id', $province);
        }
        if (!empty($name)) {
            $this->db->like(self::TABLE_NAME . '.viazul_place_name', strtolower($name));
            $this->db->or_like(self::TABLE_NAME . '.viazul_place_name', strtoupper($name));
            $this->db->or_like(self::TABLE_NAME . '.viazul_place_name', ucfirst($name));
        }
        $this->db->order_by("conf_viazul_places.province_id", "asc");
        $this->db->limit($to, $from);
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

    /**
     * Funcion que devuelve la cantidad de registros en la tabla conf_municipalities
     *
     */
    public function getCant($to, $from, $name, $province)
    {
        $this->db->select('conf_viazul_places.viazul_place_id,
							conf_viazul_places.viazul_place_name,
							conf_viazul_places.viazul_place_price,
							conf_viazul_places.province_id,
							conf_provinces.province_name,
							conf_viazul_places.viazul_place_deleted');
        $this->db->from(self::TABLE_NAME);
        $this->db->join('conf_provinces', 'conf_provinces.province_id = ' . self::TABLE_NAME . '.province_id', 'inner');
        if (!empty($province)) {
            $this->db->where(self::TABLE_NAME . '.province_id', $province);
        }
        if (!empty($name)) {
            $this->db->like(self::TABLE_NAME . '.viazul_place_name', strtolower($name));
            $this->db->or_like(self::TABLE_NAME . '.viazul_place_name', strtoupper($name));
            $this->db->or_like(self::TABLE_NAME . '.viazul_place_name', ucfirst($name));
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
        $viazul_place_id = $this->input->post('viazul_place_id');
        $viazul_place['viazul_place_name'] = $this->input->post('viazul_place_name');
        $viazul_place['province_id'] = $this->input->post('province_id');
        if (empty ($viazul_place_id)) {
            $this->db->trans_begin();
            $re = $this->db->insert(self::TABLE_NAME, $viazul_place);
            $viazul_place_id = $this->db->insert_id();
            $logs = new Logs ();
            $myquery = $logs->sqlinsert(self::TABLE_NAME, $viazul_place);
            $logs->write(self::TABLE_NAME, 'INSERT', $myquery);

            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
            } else {
                $this->db->trans_commit();
            }
            if ($re == true) {
                return "true";
            } else
                return "false";

        } else {
            $viazul_place['viazul_place_deleted'] = strtolower($this->input->post('viazul_place_deleted'));
            $this->db->where('viazul_place_id', $viazul_place_id);
            $this->db->trans_begin();
            $re = $this->db->update(self::TABLE_NAME, $viazul_place);
            $logs = new Logs ();

            $mywhere = 'where viazul_place_id = ' . $viazul_place_id;
            $myquery = $logs->sqlupdate(self::TABLE_NAME, $viazul_place, $mywhere);

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

    /**
     * Funcion para eliminar un hotel por su nombre
     *
     * @param string $id
     */

    public function delete($id)
    {
        $viazul_place['viazul_place_deleted'] = 'si';
        $this->db->where('viazul_place_id', $id);
        $this->db->trans_begin();
        $re = $this->db->update(self::TABLE_NAME, $viazul_place);
        $logs = new Logs ();
        $mywhere = 'where viazul_place_id = ' . $id;
        $myquery = $logs->sqldelete(self::TABLE_NAME, $mywhere);
        $logs->write(self::TABLE_NAME, 'DELETE', $myquery);

        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
        } else {
            $this->db->trans_commit();
        }

    }

    public function getById($viazul_place_id)
    {
        $this->db->select('conf_viazul_places.*');
        $this->db->from(self::TABLE_NAME);
        $this->db->where(self::TABLE_NAME . '.viazul_place_id', $viazul_place_id);
        $result = $this->db->get();
        $value = array();
        if ($result->result() != null) {
            foreach ($result->result() as $row) {
//				$deleted = ucfirst($row->viazul_place_deleted);
                $value [] = array('viazul_place_id' => $row->viazul_place_id,
                    'viazul_place_name' => $row->viazul_place_name,
                    'viazul_place_deleted' => ucfirst($row->viazul_place_deleted),
                    'province_id' => $row->province_id);
            }

        }
        return $value;

    }

    public function getNameById($viazul_place_id)
    {
        $this->db->select('viazul_place_name');
        $this->db->from(self::TABLE_NAME);
        $this->db->where('viazul_place_id', $viazul_place_id);
        $result = $this->db->get();
        foreach ($result->result() as $row) {
            $viazul_place_name = $row->viazul_place_name;
        }
        return $viazul_place_name;
    }


    public function getProvinceIdById($viazul_place_id)
    {
        $this->db->select('province_id');
        $this->db->from(self::TABLE_NAME);
        $this->db->where('viazul_place_id', $viazul_place_id);
        $result = $this->db->get();
        foreach ($result->result() as $row) {
            $province_id = $row->province_id;
        }
        return $province_id;
    }

}
