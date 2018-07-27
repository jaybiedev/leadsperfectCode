<?php
namespace Library\Logic;

use LogicAbstract;

class Email extends \Library\Logic\LogicAbstract
{
    private $CI;
    private $Email;
    
    function __construct() { 
        parent::__construct();
        $this->CI =& get_instance();
        $this->CI->load->library('email'); 
        
        $config = [];
        $config['protocol'] = 'sendmail';
        $config['mailpath'] = '/usr/sbin/sendmail';
        $config['charset'] = 'iso-8859-1';
        $config['wordwrap'] = TRUE;
        
        $this->CI->email->initialize($config);
    } 
        
    function send() {
        return $this->CI->email->send();
    }
		
    function setFrom($email, $name=null) {
        $this->CI->email->from($email, $name);
    }
    
    function setTo($email, $name=null) {
        $this->CI->email->to($email, $name);
    }
    
    function setReplyTo($email, $name=null) {
        $this->CI->email->reply_to($email, $name);
    }
    
    function setCC($emails) {
        $this->CI->email->cc($emails);
    }

    function setSubject($subject) {
        $this->CI->email->subject($subject);
    }
    
    function setMessage($message) {
        $this->CI->email->message($message);
    }
    
    function setAltMessage($message) {
        $this->CI->email->set_alt_message($message);
    }

    function setHeader($header, $value) {
        $this->CI->email->set_header($header, $value);
    }
    
    function clearAttachments($clear_attachments=false) {
        $this->CI->email->clear($clear_attachments);
    }
    
    function attach($filename, $disposition=null, $newname=null) {
        $this->CI->email->attach($filename, $disposition, $newname);
    }
    
} 