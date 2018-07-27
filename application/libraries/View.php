<?php

namespace Library;

use \Library\Helper;


class View {

    protected $stylesheets = array();
    protected $javascripts = array();

    protected  $global_stylesheets =  array(
        'main.css',
        'nav.css',
        'ie10-viewport-bug-workaround.css',
    );

    protected $global_javascripts = array(
        'jquery.min.js',
        'angular.min.js',
        'tools.js'
    );

    private $CI;

    public $page_title;


    function __construct() {

        $this->CI =& get_instance();

        $this->Helper = new \Library\Helper();
    }

    public function setPageTitle($title) {
        $this->page_title = $title;
    }

    public function getPageTitle() {
        return $this->page_title;
    }

    public function render($view, $data=array(), $return_as_string=false) {

        if (empty($data['menu'])) {
            $data['menu'] = Logic\Menu::getMenu($this->CI->router->fetch_class());
        }

        $data['Helper'] = $this->Helper;
        $data['View'] = $this;

        $data['page_id'] = basename($view, ".tpl");

        $data['header_data'] = array(
            'title'=>$view,
            'stylesheets'=>$this->get_stylesheets(),
        );

        $data['footer_data'] =  array (
            'javascripts'=>$this->getJavascripts(),
        );

        $is_smarty = strpos($view, '.tpl');

        if ($return_as_string && $is_smarty) {
            return $this->CI->Smarty->fetch($view, $data);
        }
        if ($return_as_string && !$is_smarty) {
            return $this->load->view($view, $data, $return_as_string);
        }
        else {
            $content = null;

            if ($is_smarty) {
                $content .= $this->CI->Smarty->fetch($view, $data);
            }
            elseif (file_exists(APPPATH . 'views/' . $view)) {
                $content .= $this->CI->load->view($view, $data, true);
            }
            elseif (file_exists(APPPATH . 'views/templates/' . $view)) {
                $view = '/templates/'. $view;
                $content .= $this->CI->load->view($view, $data, true);
            }
            else {
                $content = "View {$view} not found, ";
            }
            
            @ob_clean();
            echo $content;
            exit;
        }

    }

    public function renderJson($data, $success, $message) {
        $render = array('data'=>$data, 'success'=>$success, 'message'=>$message);
        echo json_encode($render);
        exit();
    }


    // View Stuff
    protected function set_stylesheet($file) {
        $this->stylesheets[sha1($file)] = $file;
    }

    protected function set_javascripts($file) {
        $this->javascripts[sha1($file)] = $file;
    }

    protected function get_stylesheets($include_global=true) {

        $stylesheets = $this->stylesheets;

        if ($include_global) {
            foreach ($this->global_stylesheets as $file) {
                $stylesheets[sha1($file)] = $file;
            }
        }

        $asset_path = realpath(APPPATH . '../assets/css');
        foreach (glob($asset_path . '/*.css') AS $file) {
            $stylesheets[sha1($file)] = basename($file);
        }

        return $stylesheets;
    }

    protected function getJavascripts($include_global=true) {
        $javascripts =  $this->javascripts;

        $asset_path = realpath(APPPATH . '../assets/js');

        if ($include_global) {
            foreach ($this->global_javascripts as $file) {
                $javascripts[sha1($file)] = $file;
            }
        }
        foreach (glob($asset_path . '/*.js') AS $file) {
            $javascripts[sha1($file)] = basename($file);
        }

        return $javascripts;

    }
}