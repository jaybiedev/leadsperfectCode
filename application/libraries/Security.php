<?php

namespace Library;

use \Library\SessionManager;
use \Library\DataObject;
use \Model\Admin;

class Security {

    private $CI;
    private $SessionManager;

    public $Admin;

    const LEGACY_SESSION_PRIVATIZER =  'LEGACY';

    function __construct() {
        $this->SessionManager = new SessionManager(null, self::LEGACY_SESSION_PRIVATIZER);
        $this->CI =& get_instance();
    }

    function IsLogged() {

        // check for admin sessionid
        $Admin = $this->SessionManager->get('ADMIN');

        if (false == $Admin)
            return false;

        $this->CI->db->where('admin_id', $Admin['admin_id']);
        $q = $this->CI->db->get('admin');

        $Record = $q->row();

        return ($Record->sessionid == $Admin['sessionid']);
    }

    public function getUser() {
        if (empty($this->Admin)) {
            $meta = $this->SessionManager->get('ADMIN');
            $this->Admin = new \Model\Admin($meta);
        }
        return $this->Admin;
    }

    public function IsAdmin() {
        $User = $this->getUser();
        return ($User->usergroup == 'A');
    }

    /**
     * @param $username
     * @param $password
     * @throws
     * @return bool
     */
    function Login($username, $password) {

        if (empty($username)|| empty($password))
            return false;

        $mpassword = md5($password);

        // @todo:  salt and sha1

        $this->CI->db->where('username', $username);
        $this->CI->db->where('mpassword', $mpassword);
        $q = $this->CI->db->get('admin');

        if ($q->num_rows() == 0)
            return false;

        // check for enable, expiration date

        $sessionId = md5(time());
        $data = array(
            'sessionid' => $sessionId,
        );

        $Admin = $q->row();

        if (empty($Admin->branch_id))
            $Admin->branch_id = 0;

        $Admin->sessionid = $sessionId;

        $this->CI->db->where('admin_id', $Admin->admin_id);
        $this->CI->db->update('admin', $data);

        // globalize a must for for legacy global variables
        global $ADMIN, $SYSCONF;
        $Admin->sessionId = $Admin->sessionid;
        $ADMIN = $this->SessionManager->put('ADMIN', (array)$Admin);

        // legacy hooks
        require_once(APPPATH . '/controllers/finance/legacy/var/system.conf.php');

        if (function_exists('save_legacy_session_variables'))
            save_legacy_session_variables();


        // log user activity
        $Userlog = new \Model\Userlog;
        $Userlog->admin_id = $this->getUser()->admin_id;


        $dataObject = new DataObject($Userlog);
        $dataObject->save();

        return $this->IsLogged();
    }

    function Logout() {

        $this->SessionManager->Destroy();

        return true;
    }
}