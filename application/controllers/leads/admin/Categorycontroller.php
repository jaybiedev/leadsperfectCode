<?php

use Library\Logic\Menu;
use Library\Security;

class Categorycontroller extends Library\MainController {

    function index()
    {
        $category_id = $this->uri->segment(3, 0);
        $action = $this->input->get('action', 0);
        $start = $this->input->get('start', 0);
        $limit = $this->input->get('limit', 10);

        if (empty($category_id) && $action == 'add')
            return $this->add();
        elseif (empty($category_id))
            return $this->browse($start, $limit);
        elseif ($this->isPost())
            return $this->save($category_id);
        elseif ($action == 'edit')
            $this->edit($category_id);
        else
            $this->view($category_id);

    }

    private function view($category_id) {
        $Category = Library\Logic\Category::get($category_id);

        die('view');
    }

    private function edit($category_id) {
        $Category = Library\Logic\Category::get($category_id);


        $data = array('Model'=>$Category->getOne(),
        );


        $content = $this->View->render( 'leads/admin/category/edit.tpl', $data, true);
        $this->View->render( 'leads/admin/index.tpl', array('content'=>$content));
    }

    private function save($category_id) {
        die('save');
    }


    private function add() {

        $Model = new \Model\Category();

        $data = array('Model'=>$Model,
        );

        $content = $this->View->render( 'leads/admin/category/edit.tpl', $data, true);
        $this->View->render( 'leads/admin/index.tpl', array('content'=>$content));
    }

    private function browse($start=0, $limit=10) {

        $Categories = Library\Logic\Category::getCategories();

        $data = array('Categories'=>$Categories->getArray());

        $content = $this->View->render( 'leads/admin/category/browse.tpl', $data, true);
        $this->View->render( 'leads/admin/index.tpl', array('content'=>$content));
        die('browse');
    }


}
