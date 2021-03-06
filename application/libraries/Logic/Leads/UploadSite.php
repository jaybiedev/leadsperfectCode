<?php
namespace Library\Logic\Leads;

use LogicAbstract;
use Helper;

class UploadSite extends \Library\Logic\LogicAbstract
{

    static function uploadSites($Account, $file) {
        
        $is_zip = false;
        $storage_dir = WEB_PATH . "/uploads/" . $Account->guid . "/temporary_files";        
        $storage_dir .= "/import_site";
        
        if (!is_dir($storage_dir)) {
            @mkdir($storage_dir,  0777, true);
        }
        
        if ($is_zip && false == ($csvfile = self::unzip($file, $storage_dir))) {
            throw new Exception("Unable to unzip file {$file}");  
        }
        else {
            // @todo, pass csvfile
            $file =  $storage_dir . '/' . $file;
        }

        return self::uploadSiteCSV($Account, $file);
    }
    
    /*
     * route: http://www.gcichurches.org/dashboard/uploadSite
     */
    static function uploadSiteCSV($Account, $csvfile) {
        
        if (($handle = @fopen($csvfile, "r")) === FALSE) {
            throw new \Exception("Unable to open csv file {$csvfile}");
        }
        
        $ContentTagRepository = new \Library\Logic\Leads\ContentTag();
        $ContentTags = $ContentTagRepository->getByTemplateId($Account->template_id)->getArray('tag');
        
        $result = array('success'=>array(), 'failed'=>array());        
        $header = fgetcsv($handle);       
        $header = array_map('strtolower', $header);
        
        $row = 0;
        while (($data = fgetcsv($handle)) !== FALSE) 
        {
            $row++;
            $meta = array_combine($header, $data);
            
            // temporary gap fill
            if (empty($meta['country'])) {
                $meta['country'] = 'US';
            }
            
            if (empty($meta['name']) && empty($meta['slug']) && empty($meta['guid'])) {
                continue;
            }
            
            if (isset($meta['guid']) && !empty(trim($meta['guid']))) {
                $Site = \Library\Logic\Leads\Site::getByGuid(trim($meta['guid']), false);
            }
            elseif (isset($meta['slug']) && !empty(trim($meta['slug']))) {
                $Site = \Library\Logic\Leads\Site::getBySlug(trim($meta['slug']), false);
            }
            else {
                $Site = \Library\Logic\Leads\Site::getByName($meta['name']);                
            }

            // protected columns
            unset($meta['id']);
            unset($meta['guid']);

            // belongs to different account
            if (!empty($Site->id) && $Site->account_id != $Account->id) {
                $result['failed'][] = new \Model\Error(array(
                    'code'=> '0',
                    'message'=> 'Row ' . $row . ' Slug already exists ' . $meta['name'],
                ));                
                continue;
            }            
            
            // validate slug
            if (empty($Site->id)) {
                // new site entry
                $Site = new \Model\Leads\Site();
                $meta['account_id'] = $Account->id;
                $meta['template_id'] = $Account->template_id;
                $meta['guid'] =  guid4();
                
                if (empty($meta['slug']))
                $meta['slug'] =  \Library\Logic\Leads\Site::getNewSlug($meta['name'], $meta['state'], $meta['city'], $category='church');
            }
            
            // hack to fix data just regenerate guid again it's blank
            if (empty($Site->guid)) {
                $meta['account_id'] = $Account->id;
                $meta['template_id'] = $Account->template_id;
                $meta['guid'] =  guid4();                
            }
            
            if (empty($meta['slug']) && empty($Site->slug)) {
                $result['failed'][] = new \Model\Error(array(
                    'code'=> '0',
                    'message'=> 'Row ' . $row . ' Unable to generate slug for ' . $meta['name'],
                ));
                continue;
            }

            $Site = $Site->save($meta);

            // create user
            if (!empty($meta['user_email'])) {
                $default_password = 'changeMe18!';
                $user_email = strtolower(trim($meta['user_email']));
                
                $User = \Library\Logic\User::getByEmail($user_email);
                
                if ($User->isNew()) {
                    $user_meta = array();
                    $Helper = new \Library\Helper();
                    $user_meta['username'] = $user_email;
                    $user_meta['username_canonical'] = $user_email;
                    $user_meta['email_canonical'] = $user_email;
                    $user_meta['email'] = $user_email;
                    $user_meta['password'] = $Helper->getSecurity()->hashPassword($default_password);
                    $User->save($user_meta);
                    
                    /*
                    $UserAccountXref = $User->getAccountXref($Account->id);
                    if ($UserAccountXref->isNew()) {
                        $UserAccountXref->save(array('user_id'=>$User->id, 'account_id'=>$Account->id));
                    }
                    */

                    $UserSiteXref = $User->getUserSiteXref($Site->id);
                    if ($UserSiteXref->isNew()) {
                        $UserSiteXref->save(array('user_id'=>$User->id, 'site_id'=>$Site->id));
                    }                    
                }
            }
            
            foreach ($ContentTags as $field=>$Tag) {
                
                if (empty($field) || !isset($meta[$field]))
                    continue;
                
                $value = $meta[$field];
                
                // copy image file.  @todo: resize
                if ($Tag->isImage() && !empty($value)) {
                    $image_source = $storage_dir . '/' . $value;
                    $image_destination = WEB_PATH . "/uploads/" . $Account->guid . '/' . $Site->guid . '/' . $value;
                    @copy($image_source, $image_destination);
                }                
                    
                if (property_exists($Site, $field))
                    continue;
                
                $SiteData = \Library\Logic\Leads\SiteData::getByField($Site->id, $field)->getOne();
                $SiteData = $SiteData->save(array('site_id'=>$Site->id,
                            'field'=>$field, 
                            'field_value'=>$value                    
                        ));
            }
        }
        fclose($handle);
        
        return $result;
    }
    
    public static function unzip($zipfile, $destination_folder) {
        
        $ZipArchive = new \ZipArchive;
        
        $success = false;
        if ($ZipArchive->open($zipfile) === TRUE) {
            $ZipArchive->extractTo($destination_folder);
            $ZipArchive->close();
            $success = true;
        }
        
        return $success;
    }
    
}

