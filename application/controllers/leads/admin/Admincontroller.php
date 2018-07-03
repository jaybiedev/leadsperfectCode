<?php

use Library\Logic\Menu;
use Library\Security;

class Admincontroller extends Library\MainController {

    public function index_get() {
        die("Admin get index");
    }
    
    public function index()
    {
        $data = array('content'=>'');
        $this->View->setPageTitle("Leads Perfect");
        $this->View->render( 'leads/admin/index.tpl', $data);
    }

    public function user() {
        die('not in index user');
        $this->View->render( 'leads/admin/user.browse.tpl', $data);
    }

    public function catchall() {
        $segments = $this->uri->segment_array();

        $slug = strtolower(implode('/', $segments));


        $Content = Library\Logic\Leads\Content::getBySlug($slug);

        if (is_object($Content) && !empty($Content->content)) {
            echo $Content->content;
        }
        else {
            echo "404 inadmin";
        }


        echo "<br >";
        echo $slug;
        die('Catch All');
    }

}
