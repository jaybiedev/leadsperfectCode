<?php

namespace Library;

use \Library\SessionManager;
use \Library\DataObject;
use \Model\User;

class UserSecurity {

    private $CI;
    private $SessionManager;
    private $salt = 'HaciendaDelicia#10';

    public $User;


    function __construct() {

        $this->CI =& get_instance();

        $this->CI->load->library('session');
        $this->SessionManager = $this->CI->session;
    }

    function isLogged() {

        $User = $this->SessionManager->User; //set_userdata($array);

        if (false == $User)
            return false;

        $this->CI->db->where('id', $User->id);
        $q = $this->CI->db->get('user');

        $Record = $q->row();
        return ($Record->sessionid == $User->sessionid);
    }

    public function getUser() {
        if (empty($this->User)) {
            $meta = $this->SessionManager->User;
            $this->User = new \Model\User((array)$meta);
        }
        return $this->User;
    }

    public function IsAdmin() {
        $User = $this->getUser();
        // todo: need to get from JSON roles
        return ($User->roles == 'A');
    }

    private function hashPassword($password) {
       // return sha1($this->salt . '@' . $password);
        $options = [
            'cost' => 11,
        ];

        return password_hash($password, PASSWORD_BCRYPT, $options);
    }

    private function verifyPassword($passwordFromPost, $hashedPasswordFromDB) {
        return password_verify($passwordFromPost, $hashedPasswordFromDB);
    }

    /**
     * @param $username
     * @param $password
     * @throws
     * @return bool
     */
    function login($username, $password) {

        if (empty($username)|| empty($password))
            return false;


        $this->CI->db->where('username', $username);
        $q = $this->CI->db->get('user');

        if ($q->num_rows() == 0) {
            return false;
        }

        $User = $q->row();

        if (false == $this->verifyPassword($password, $User->password)) {
            return false;
        }


        $sessionid = md5(time());
        $data = array(
            'sessionid' => $sessionid,
            'last_login'=> 'NOW()'
        );

        $this->CI->db->where('id', $User->id);
        $this->CI->db->update('user', $data);


        // log user activity
        $Userlog = new \Model\Userlog;
        $Userlog->user_id = $this->getUser()->id;
        $Userlog->action = 'login';


        $dataObject = new DataObject($Userlog);
        $dataObject->save();

        // retrieve refresh
        $this->CI->db->where('id', $User->id);
        $q = $this->CI->db->get('user');
        $User = $q->row();

        // $this->SessionManager->unset_userdata('User');
        $this->SessionManager->set_userdata('User', $User);


        return $this->IsLogged();
    }

    function logout() {

        $this->SessionManager->unset_userdata('User');
        $this->SessionManager->sess_destroy();

        return true;
    }
}