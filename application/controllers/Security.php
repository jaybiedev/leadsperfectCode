<?php

class Security extends Library\MainController {

    public function index()
    {
        // legacy support
        require_once(APPPATH . '/controllers/finance/legacy//helpers/php4compat.php');

        $username = $this->input->post('username');
        $password = $this->input->post('password');


        if ($this->isPost() && $this->Helper->getSecurity()->Login($username, $password)) {
            // redirect to destination;
            $destination = $this->input->post('$destination');
            if (empty($destination))
                redirect($destination);
            else
                redirect(base_url());
        }


        $data = array('menu' => array(), 'admin' => false, 'user' => false);
        $data['destination'] = $this->input->get('destination');
        $this->View->render('security/login.tpl', $data);
    }


    public function home() {
        $data = array();
        $this->View->render('finance/home.tpl', $data);
    }

    public function logout() {

        // @todo:  update user_log table

        if ($this->Helper->getSecurity()->Logout()) {
            redirect('/login');
        }
    }
}
