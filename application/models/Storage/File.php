<?php

namespace Model\Storage;

class File extends \Model\AbstractModel {

    public $id;
    public $filename;
    public $fullpath;
    public $mime;
    public $size;

    public function setId($id)
    {
        $this->id = $id;
    }

    public function getId()
    {
        return $this->id;
    }

}