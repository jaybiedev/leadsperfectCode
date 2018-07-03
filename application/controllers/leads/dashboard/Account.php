<?php

use Library\Logic\Menu;
use Library\Security;

class Leadsaccount extends Library\MainController {

    public function index()
    {
        $data = array();
        $this->View->setPageTitle("Leads Perfect");
        $this->View->render( 'leads/account/index.tpl', $data);
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
