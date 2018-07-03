<?php
$email = $_REQUEST['contact_email'];
$name = $_REQUEST['contact_name'];
$phone = $_REQUEST['contact_phone'];
$message = $_REQUEST['contact_message'];

$subject = 'LeadsPerfect ContactUs';
$body = 'From: ' . $name . ' Email: ' . $email . ' Phone: ' . $phone . ' Message: ' . $message;

@mail('jaybiedev@gmail.com', $subject, $body);
header('location: http://www.leadsperfect.com/#response');
