<?php

namespace Library\Repository;

abstract class RepositoryAbstract  {

    public $model;
    public $sql;
    public $offset = 0;
    public $limit = null;
    public $total_rows = 0;

    protected $CI;
    protected $db;

    private $query; // db query resource

    public function __construct()
    {
        $this->CI =& get_instance();
        $this->db = $this->CI->db;
    }

    private function getQuery() {

        if ($this->offset)
            $this->sql .= "\n OFFSET {$this->offset}";

        if ($this->limit)
            $this->sql .= "\n LIMIT {$this->limit}";

        $this->query = $this->db->query($this->sql);
        return $this->query;
    }

    public function getOne() {
        if (empty($this->query))
            $this->query = $this->getQuery();

        $record = $this->query->result_array();

        if (empty($record))
            $first_record = array();
        else
            $first_record =  $record[0];

        $classname =  "Model\\" . $this->model;
        $model = new $classname($first_record);

        return $model;

    }

    public function getCount() {
        $query = $this->db->query($this->sql);
        $this->total_rows = $query->num_rows();
        return $this->total_rows;
    }

    public function getArray() {

        if (empty($this->query))
            $this->query = $this->getQuery();

        $records = array();
        foreach ($this->query->result_array() as $row)
        {
            if (empty($this->mdoel)) {
                $records[] = $row;
            }
            else {
                $classname = "Model\{$this->model}";
                $records[] = new $classname($row);
            }
        }

        return $records;
    }

}