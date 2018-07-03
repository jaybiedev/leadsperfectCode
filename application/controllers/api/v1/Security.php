<?php
require(APPPATH.'/libraries/RESTController.php');

use \Library\UserSecurity;

class Security extends REST_Controller
{
    function __construct()
    {
        header("Access-Control-Allow-Origin: *");

        // Construct the parent class
        parent::__construct();

        // Configure limits on our controller methods
        // Ensure you have created the 'limits' table and enabled 'limits' within application/config/rest.php
        /*
        $this->methods['users_get']['limit'] = 500; // 500 requests per hour per user/key
        $this->methods['users_post']['limit'] = 100; // 100 requests per hour per user/key
        $this->methods['users_delete']['limit'] = 50; // 50 requests per hour per user/key
        */
    }

    function index_get()
    {

        $action = strtolower($this->get('action'));

        $UserSecurity = new UserSecurity();

        $data =  array('is_logged'=>false, 'message'=>'',  'data'=>$_SESSION);
        if ($action == 'logout') {
            $UserSecurity->logout();
        }
        elseif ($action == 'login') {
            $username = $this->get('username');
            $password = $this->get('password');
            $UserSecurity->login($username, $password);
            $data['data'] = $UserSecurity->getUser();
        }

        $data['is_logged'] = $UserSecurity->isLogged();

        return $this->set_response($data, REST_Controller::HTTP_OK); // OK (200) being the HTTP response code

    }

    function index_post()
    {
        // $output['token'] = JWT::encode($token, $this->config->item('jwt_key'));
        
        $username = $this->input->post('username');
        $password = $this->input->post('password');

        $username = $_REQUEST['username'];
        $password = $_REQUEST['password'];

        $data =  array('is_logged'=>false, 'message'=>'',  'data'=>array());

        // $data['data'] = array('username'=>$username,  'password'=>$password, 'REQUEST', $_REQUEST);
        $Security = new UserSecurity();


        if ($Security->isLogged()) {
            return $this->set_response(array('islogged'=>true, 'message'=>'Already logged'), REST_Controller::HTTP_OK);
        }


        $data['is_logged'] = $Security->Login($username, $password);
        if (false === $data['is_logged'])
            $data['message'] = 'Invalid credentials';

        $data['data'] = $_SESSION;

        return $this->set_response($data, REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
    }


}