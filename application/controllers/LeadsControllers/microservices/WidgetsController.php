<?php

// Custom Controller for Widgets. Open
 
class WidgetsController extends \Library\MainController {
    
    function __construct() {
        parent::__construct();
    }

    public function index()
    {
        die('Default method');
    }
    
    public function contactform()
    {
        if ($this->isPost()) {
            
            $guid = $this->inputRequest('guid');
            $token = $this->inputRequest('token');
            $timestamp = $this->inputRequest('timestamp');
            $name_from = $this->inputRequest('name');
            $email_address_from = $this->inputRequest('email');
            $subject = $this->inputRequest('subject');
            $message = $this->inputRequest('message');
            
            if (empty($timestamp)) {
                $timestamp = time();
            }
            
            if (empty($email_address_from)) {
                $data['success'] = false;
                $data['message'] = "Please provide email address.";                
            }

            if (empty($message)) {
                $data['success'] = false;
                $data['message'] = "Please provide provide message.";
            }
            
            try {
                
                $Site = \Library\Logic\Leads\Site::getByGuid($guid, true);
                
                $data = array('success'=>true);
                if ($Site->isNew()) {
                    $data['success'] = false;
                    $data['message'] = "Unable to find site requested";                
                }
    
                $from_email = "do-no-reply@gcichurches.com";
                $from_name = $Site->name . "  " . $Site->city;
                $from_subject = "Contact Form ({$subject})";
                $to_email = $Site->email;
                
                $contact_form_message = "Contact Form was submitted on " . date("Y-m-d G:ia", $timestamp);
                $contact_form_message .= "\nBy: {$name_from} <{$email_address_from}>";
                $contact_form_message .= "\nSubject: {$subject}";
                $contact_form_message .= "\nMessage: \n {$message}";
                $contact_form_message .= "\n---- end of message ---\n";
                
                $ContactForm = new \Model\Leads\ContactForm();
                $ContactForm->site_id = $Site->id;
                $ContactForm->subject = $subject;
                $ContactForm->date_added = date('Y-m-d G:i:s', $timestamp);
                $ContactForm->message = $message;
                $ContactForm->email_address_from = $email_address_from;
                $ContactForm->name_from = $name_from;
                $ContactForm->email_address_to = $to_email;
                $ContactForm->saveModel();
                
                $Email = new \Library\Logic\Email();
                
                $Email->setTo($to_email);
                $Email->setReplyTo($email_address_from, $name_from);
                $Email->setFrom($from_email, $from_name);
                $Email->setSubject($from_subject);
                $Email->setMessage($contact_form_message);
                
                if (false == $Email->send()) {
                    $data['success'] = false;
                    $data['message'] = "Failed to send message";
                }
            }
            catch (\Exception $e) {
                $data['success'] = false;
                $data['message'] = "Failed to send message";                
            }
            
            return $this->renderJson($data);
        }
    }
}
    
    
    
    