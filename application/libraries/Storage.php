<?php

namespace Library;

use \Library\Helper;


class Storage  {

    private $base_folder = "uploads";
    private $CI;

    function __construct() {

        $this->CI =& get_instance();
    }

    public function getBaseUrl() {
        return $this->CI->config->item('base_url');
    }

    public function getSignatureUrl($name) {

        $url = $this->getPath('placeholders', 'blank.png');
        $path = $this->getPath('signature', $name);
        if (is_file($path))
            $url = $this->getUrl('signature', $name);

        return $url;
    }

    public function getPhotoUrl($name) {

        $url = $this->getAssetUrl('placeholders', 'Empty.png');
        $path = $this->getPath('photo', $name);
        if (is_file($path))
            $url = $this->getUrl('photo', $name);

        return $url;
    }

    public function getMapUrl($subfolder, $name) {
        $url = null;
        $folder = "prog/data/maps/{$subfolder}";
        $path = $this->getPath($folder, $name);
        if (is_file($path))
            $url = $this->getUrl($folder, $name);

        return $url;
    }

    public function getAssetUrl($folder, $file) {
        return $this->CI->config->item('base_url') . "/assets/{$folder}/{$file}";
    }


    public function getPath($folder, $file) {
        return WEB_PATH  . "/{$this->base_folder}/{$folder}/$file";
    }

    function getUrl($folder, $file) {
        return $this->getBaseUrl() . "/{$this->base_folder}/{$folder}/$file";
    }
}
