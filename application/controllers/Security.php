<?php

use Library\Helper;

class Security extends Library\MainController {

    public function index()
    {
        // legacy support
        if (false)
            require_once(APPPATH . '/controllers/finance/legacy//helpers/php4compat.php');

        $data = array('menu' => array(), 'username'=>'', 'admin' => false, 'user' => false, 'success'=>true, 'is_json'=>false);
        $data['destination'] = $this->input->post('destination');
        
        if ($this->isPost()) {
            
            $username = $this->inputPost('username');
            $password = $this->input->post('password');
            $is_json = get_boolean_value($this->input->post('is_json'));
            
            $data['username'] = $username;
            $data['is_json'] = $is_json;
            
            // $pw = $this->Helper->getSecurity()->hashPassword($password);

            if ($this->Helper->getSecurity()->Login($username, $password)) {

                // redirect to destination;
                //if (!empty($data['destination']))
                //    redirect($data['destination']);
                //else
                //    redirect(WEB_URL + '/dashboard');

                // @todo: append site guid to redirect for site users only
                header('Location:/dashboard');
            }
            else {
                $data['message'] = 'Invalid user credentials.';
                $data['success'] = false;
            }
            
            if ($is_json == 'json') {
                echo json_encode($data);
                exit;
            }
        }

        
        if (false)
            $this->View->render('security/login.tpl', $data);
        else
            $this->View->render('leads/login.tpl', $data);
    }
    

    public function logout() {

        // @todo:  update user_log table

        if ($this->Helper->getSecurity()->Logout()) {
            redirect(WEB_URL . '/login?destination=');
        }
    }
}
