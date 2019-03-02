<?php

// Custom Controller for Dashboard Webservices
 
class WebserviceController extends \Library\MainController {
    
    function __construct() {
        parent::__construct();
        $this->initSecurity();        
    }

    public function index()
    {
        die('Default method');
    }
    
    public function site() {
        $User = $this->UserSecurity->getUser();
        $guid = $this->inputRequest('guid');
        
        $data = array();
        $UserSites = $this->SessionManager->get('UserSites');        
        $Site = $UserSites[$guid];
        
        // $ActiveAccount = $this->SessionManager->get('ActiveAccount');
        $Template = \Library\Logic\Leads\Template::get($Site->template_id);
        
        $ContentTags = \Library\Logic\Leads\ContentTag::getByTemplateId($Site->template_id);
        
        $data['Site'] = $Site;
        $data['SiteData'] = \Library\Logic\Leads\SiteData::getAll($Site->id)->getArray('field');
         
        while ($ContentTag = $ContentTags->getNext()) {
            if (isset($data['SiteData'][$ContentTag->tag])) {
                continue;
            }
            
            if (property_exists($Site, $ContentTag->tag)) {
                continue;
            }
            
            $Data = new \Model\Leads\SiteData();
            $Data->site_id = $Site->id;
            $Data->field = $ContentTag->tag;
            $Data->content_tag_name = $ContentTag->name;
            $Data->content_tag_type_id = $ContentTag->tag_type_id;
            $Data->content_tag_system_name = $ContentTag->tag_system_name;
            
            $data['SiteData'][$ContentTag->tag] = $Data;
            unset($Data);
            
        }
        unset($data['SiteData']['address']);
        unset($data['SiteData']['address1']);
        
        return $this->renderJson($data);
    }
    
    public function account() {
        $User = $this->UserSecurity->getUser();
        $guid = $this->inputRequest('guid');
        
        $data = array();
        
        $ActiveAccount = $this->SessionManager->get('ActiveAccount');
        $Template = \Library\Logic\Leads\Template::get($ActiveAccount->template_id);
        
        $data['Account'] = $ActiveAccount;
        
        return $this->renderJson($data);
        
    }
    
    public function user() {
        $data = array();
        if ($this->isPost()) {
            // save current user
            $Meta = $this->getInputStream(null, true, true);
            $UserModel = \Library\Logic\User::get($Meta->id);
            $UserModel->email = $Meta->email;
            $UserModel->first_name = $Meta->first_name;
            $UserModel->last_name = $Meta->last_name;
            
            if ($Meta->password  && $Meta->password_confirm) {
                $UserModel->password =  $this->Helper->getSecurity()->hashPassword($Meta->password);
            }
            
            $UserModel->saveModel();             
            // update session
            $User = $this->UserSecurity->setUser($UserModel);            
        }
        
        // get. always return User model
        $User = $this->UserSecurity->getUser();
        unset($User->table);
        unset($User->admin_id);
        unset($User->date_expire);
        unset($User->roles);
        
        $data['User'] = $User;
        return $this->renderJson($data);
        
    }
    
    function image() {
        $data = array();
        if ($this->isPost()) {
            
            $accepted_origins = array("http://localhost", "http://192.168.1.1", "http://example.com");
            
            $guid = $this->inputRequest('site');
            $UserSites = $this->SessionManager->get('UserSites');

            $Site = reset($UserSites);
            $Account = $Site->getAccount();
            
            $accountFolder = $Account->guid;
            $storageFolder = $accountFolder;
            if (!empty($guid)) {
                $Site = $UserSites[$guid];
                if (empty($Site)) {
                    header("HTTP/1.1 400 Invalid site.");
                    return;
                }
                $storageFolder = "{$accountFolder}/{$guid}";
            }
            
            $tempFolder = WEB_PATH . "/uploads/{$accountFolder}/temporary_files";
            $imageFolder = WEB_PATH . "/uploads/{$storageFolder}";
            $imageUrl =  "/uploads/{$storageFolder}";
            $success = true;
            
            if (!file_exists($tempFolder)) {
                mkdir($tempFolder, null, true);
            }
            if (!file_exists($imageFolder)) {
                mkdir($imageFolder, null, true);
            }
            
            reset ($_FILES);
            $temp = current($_FILES);
            if (is_uploaded_file($temp['tmp_name'])){
                if (isset($_SERVER['HTTP_ORIGIN'])) {
                    // same-origin requests won't set an origin. If the origin is set, it must be valid.
                    /*
                    if (in_array($_SERVER['HTTP_ORIGIN'], $accepted_origins)) {
                        header('Access-Control-Allow-Origin: ' . $_SERVER['HTTP_ORIGIN']);
                    } else {
                        header("HTTP/1.1 403 Origin Denied");
                        return;
                    } */
                }
                
                /*
                 If your script needs to receive cookies, set images_upload_credentials : true in
                 the configuration and enable the following two headers.
                 */
                // header('Access-Control-Allow-Credentials: true');
                // header('P3P: CP="There is no P3P policy."');
                
                // Sanitize input
                if (preg_match("/([^\w\s\d\-_~,;:\[\]\(\).])|([\.]{2,})/", $temp['name'])) {
                    header("HTTP/1.1 400 Invalid file name. (" . $temp['name'] + ")");
                    return;
                }
                
                // Verify extension
                if (!in_array(strtolower(pathinfo($temp['name'], PATHINFO_EXTENSION)), array("gif", "jpg", "png"))) {
                    header("HTTP/1.1 400 Invalid extension.");
                    return;
                }
                
                $filename = str_replace(' ', '_', $temp['name']);
                $filetowrite = $tempFolder . "/" . $filename . "." . time();
                $filetoSave = $imageFolder . "/" . $filename;
                $imageUrl .= "/" . $temp['name'];

                if (move_uploaded_file($temp['tmp_name'], $filetowrite)) {
                    // resize to prevent oversize
                    $filetowrite = \Library\Logic\Image::Resize($filetowrite);
                    if (copy($filetowrite, $filetoSave)) {
                        $data['location'] = $imageUrl;
                    }
                    else {
                        $success = false;
                        header("HTTP/1.1 500 Server Error. Unable to copy file.");
                    }
                    unlink($filetowrite);
                }
                else {
                    $success = false;
                    header("HTTP/1.1 500 Server Error. Unable to upload file.");
                }
                
            } else {
                // Notify editor that the upload failed
                $success = false;
                header("HTTP/1.1 500 Server Error. File is not for upload.");
            }
        }
        
        return $this->renderJson($data, $success);
    }
    
}
