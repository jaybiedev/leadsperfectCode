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
    protected $bindings = array();

    private $query; // db query resource

    public function __construct()
    {
        $this->CI =& get_instance();
        $this->db = $this->CI->db;
    }

    /**
     * escaping example:
     * $query = 'SELECT * FROM subscribers_tbl WHERE user_name='.$this->db->escape($email); 
     * 
     * bindings example:
     * 
     * $this->db->get_where('subscribers_tbl',array('status' => 'active','email' => 'info@arjun.net.in'));
     *
     * $sql = "SELECT * FROM subscribers_tbl WHERE status = ? AND email= ?"; 
     * $this->db->query($sql, array('active', 'info@arjun.net.in'));
     * 
     * handling likes
     * $search = '20% raise';
     * $sql = "SELECT id FROM table WHERE column LIKE '%" .
     * $this->db->escape_like_str($search)."%' ESCAPE '!'";
     */
    private function getQuery() {

        if ($this->offset)
            $this->sql .= "\n OFFSET {$this->offset}";

        if ($this->limit)
            $this->sql .= "\n LIMIT {$this->limit}";

        $this->query = $this->db->query($this->sql, $this->bindings);
        
        if (!$this->query) {
            throw new Exception($this->db->error());
        }
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

    public function getNext() {
        if (empty($this->query))
            $this->query = $this->getQuery();
         
        $row = $this->query->unbuffered_row();
        // $this->query->data_seek(1); // skip next
        if (!$row)
            return false;
        
        return $row;
    }
    
    public function getArray($key=null) {

        if (empty($this->query))
            $this->query = $this->getQuery();

        $records = array();
        foreach ($this->query->result_array() as $row)
        {
            if (empty($this->model)) {
                if (!empty($key)) {
                    $records[$row[$key]] = $row;
                }
                else {
                    $records[] = $row;                    
                }
            }
            else {
                $classname = "Model\\"  . $this->model;
                if (!empty($key))
                    $records[$row[$key]] = new $classname($row);
                else
                    $records[] = new $classname($row);
            }
        }

        return $records;
    }

}