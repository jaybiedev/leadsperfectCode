<?php


use Library\Logic\Menu;
use Library\Security;

class Leads extends Library\MainController {

    public function index()
    {
        //$segments = $this->uri->segment_array();
        $data = array();
        $this->View->setPageTitle("Leads Perfect");
        $this->View->render( 'leads/dashboard/index.tpl', $data);

    }

    public function slug() {
        $segments = $this->uri->segment_array();

        $slug = strtolower(implode('/', $segments));


        $Content = Library\Logic\Leads\Content::getBySlug($slug);

        if (is_object($Content) && !empty($Content->content)) {
            echo $Content->content;
        }
        else {
            echo "404";
        }


        echo "<br >";
        echo $slug;
        die('Catch All');
    }

    public function catchall() {
        $segments = $this->uri->segment_array();

        $slug = strtolower(implode('/', $segments));


        $Content = Library\Logic\Leads\Content::getBySlug($slug);

        if (is_object($Content) && !empty($Content->content)) {
            echo $Content->content;
        }
        else {
            echo "404";
        }


        echo "<br >";
        echo $slug;
        die('Catch All');
    }

}
