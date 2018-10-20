<?php

namespace Library;

use \Library\Security;

class DataObject {

    private $id_field = 'id'; // legacy backwards compatibility;
    private $fields;
    private $CI;
    private $db;
    private $Security;

    private $Model;

    function __construct($model)
    {
        if (empty($model))
            throw new \Exception("DataObject Error. Model not defined.");

        if (!is_object($model))
            throw new \Exception("DataObject Error. Model is not an object.");

        $this->Model = $model;
        $this->CI =& get_instance();
        $this->db = $this->CI->db;

        if ($this->db->table_exists($this->Model->table) == false)
            throw new \Exception("Table {$this->Model->table} does not exists.");


        $this->fields = $this->db->list_fields($this->Model->table);

        if (false == in_array('id', $this->fields) && in_array( $this->Model->table . '_id', $this->fields))
        {
            $this->id_field = $this->Model->table . '_id'; // legacy backwards compatibility;
        }

        $this->Security = new \Library\Security();

    }

    /**
     * @param array $updates  optional.  If provided, will use this as meta data instead of Model properties
     * @return bool
     */
    public function save($updates = array())
    {
        if (empty($updates))
            $updates = get_object_vars($this->Model);

        // check if Id is provided, then update
        $meta = array();
        foreach ($updates as $key => $value) {

            // primary key does not get updated
            if (in_array($key, array($this->id_field, 'user_id_added', 'user_id_modified', 'date_added', 'date_modified')))
                continue;

            if (in_array($key, $this->fields)) {
                $meta[$key] = $value;
                $this->db->set($key, $value);
            }
        }

        // audit fields
        $current_user_id = 1;
        /*
        $current_user_id = $this->Security->getUser()->admin_id; // old method
        if (empty($current_user_id))
            $current_user_id = $this->Security->getUser()->id;
        */

        if (in_array('date_modified', $this->fields) && empty($updates['date_modified']))
            $this->db->set('date_modified', 'NOW()');


        if (in_array('user_id_modified', $this->fields) && empty($updates['user_id_modified']))
            $this->db->set('user_id_modified', $current_user_id);

        $result = false;
        if (empty($this->Model->getId())) {

            if (in_array('date_added', $this->fields) && empty($updates['date_added']))
                $this->db->set('date_added',  'NOW()');

            if (in_array('user_id_added', $this->fields) && empty($updates['user_id_added']))
                $this->db->set('user_id_added', $current_user_id);

            if (in_array('ip', $this->fields) && empty($updates['user_id_added']))
                $this->db->set('ip', $_SERVER['REMOTE_ADDR']);

            if ($result = $this->db->insert($this->Model->table, $meta))
                $this->Model->setId($this->db->insert_id());
        }
        else {
            $this->db->where($this->id_field, $this->Model->getId());
            $result = $this->db->update($this->Model->table, $meta);
        }

        if (false == $result)
            throw new \Exception("Unable to update data.");

        return $this->Model;

    }
    
    public function getMeta() {
        return $this->get_object_public_vars($this->Model);
    }

    private function get_object_public_vars($object) {
        return get_object_vars($object);
    }
    
    /**
     * @return record Id primary key
     */
    public function getId()
    {
        $this->Model->getId();
    }

    public function delete()
    {
        $this->db->where($this->id_field, $this->getId());
        return $this->db->delete($this->Model->table);
    }

    public function enable()
    {

    }

    public function disable()
    {

    }

}