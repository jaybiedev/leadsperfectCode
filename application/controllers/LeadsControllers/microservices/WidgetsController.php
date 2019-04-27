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
            
            $localtimezone = $this->inputRequest('localtimezone');
            $localtimestamp = $this->inputRequest('localtimestamp');
            
            $name_from = $this->inputRequest('name');
            $email_address_from = $this->inputRequest('email');
            $subject = $this->inputRequest('subject');
            $message = $this->inputRequest('message');
            
            if (empty($timestamp)) {
                $timestamp = time();
            }
            
            if (empty($localtimestamp)) {
                $localtimestamp = time();
                $dateTime = new DateTime();
                $dateTime->setTimeZone(new DateTimeZone(date_default_timezone_get()));
                $localtimeszone = $dateTime->format('T');
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
    
                $from_email = "noreply@gcichurches.com";
                $from_name = $Site->name . "  " . $Site->city;
                $from_subject = "Contact Form ({$subject})";
                $to_email = $Site->email;
                
                $contact_form_message = "Contact Form was submitted on " . date("Y-m-d G:ia", $localtimestamp) . ' ' . $localtimezone;
                $contact_form_message .= "<br /><br />By: {$name_from} <{$email_address_from}>";
                $contact_form_message .= "<br />Subject: {$subject}";
                $contact_form_message .= "<br />Message: <br /> {$message}";
                $contact_form_message .= "<br />---- end of message ---<br />";
                
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
                
                $Email->setHeader('X-Sender','');
                $Email->setHeader('X-Mailer','PHP/' . phpversion());
                $Email->setHeader('X-Priority', 1);
                $Email->setHeader('Return-Path', 'formsender@gcichurches.org');
                $Email->setHeader('MIME-Version', '1.0');
                $Email->setHeader('Content-Type', 'text/html; charset=iso-8859-1');
                
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
    
    
    
    