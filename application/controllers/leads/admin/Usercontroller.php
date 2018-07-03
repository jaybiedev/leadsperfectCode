<?php

use Library\Logic\Menu;
use Library\Security;

class Usercontroller extends Library\MainController {

    function index()
    {
        $user_id = $this->uri->segment(3, 0);
        $action = $this->input->get('action', 0);
        $start = $this->input->get('start', 0);
        $limit = $this->input->get('limit', 10);

        if (empty($user_id) && $action == 'add')
            return $this->add();
        elseif (empty($user_id))
            return $this->browse($start, $limit);
        elseif ($this->isPost())
            return $this->save($user_id);
        elseif ($action == 'edit')
            $this->edit($user_id);
        else
            $this->view($user_id);

    }

    private function view($user_id) {
        $User = Library\Logic\User::get($user_id);

        die('view');
    }

    private function edit($user_id) {
        $User = Library\Logic\User::get($user_id);


        $data = array('Model'=>$User->getOne(),
        );


        $content = $this->View->render( 'leads/admin/user/edit.tpl', $data, true);
        $this->View->render( 'leads/admin/index.tpl', array('content'=>$content));
    }

    private function save($user_id) {
        die('save');
    }


    private function add() {

        $Model = new \Model\User();

        $data = array('Model'=>$Model,
        );

        $content = $this->View->render( 'leads/admin/user/edit.tpl', $data, true);
        $this->View->render( 'leads/admin/index.tpl', array('content'=>$content));
    }

    private function browse($start=0, $limit=10) {

        $User = Library\Logic\User::get();

        $data = array('Users'=>$User->getArray());

        $content = $this->View->render( 'leads/admin/user/browse.tpl', $data, true);
        $this->View->render( 'leads/admin/index.tpl', array('content'=>$content));
        die('browse');
    }


}
