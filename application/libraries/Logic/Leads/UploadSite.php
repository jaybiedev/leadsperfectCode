<?php
namespace Library\Logic\Leads;

use LogicAbstract;

class UploadSite extends \Library\Logic\LogicAbstract
{

    static function uploadZip($Account, $zipfile) {
        
        $storage_dir = WEB_PATH . "/uploads/" . $Account->guid . "/temporary_files";        
        $storage_dir .= "/import_site";
        
        if (!is_dir($storage_dir))
            @mkdir($storage_dir,  0777, true);
        /*
            
        if (false == self::unzip($zipfile, $storage_dir)) {
            throw new Exception("Unable to unzip file {$zipfile}");            
        }
        */
        $csvfile = $storage_dir . "/sites.csv";
        if (false == file_exists($csvfile)) {
            throw new Exception("Sites CSV file (sites.csv) not found.");
        }
        
        if (($handle = fopen($csvfile, "r")) === FALSE) {
            throw new Exception("Unable to open csv file {$zipfile}");
        }
        
        $ContentTagRepository = new \Library\Logic\Leads\ContentTag();
        $ContentTags = $ContentTagRepository->getByTemplateId($Account->template_id)->getArray('tag');
        
        $result = array('success'=>array(), 'failed'=>array());        
        $header = fgetcsv($handle);        
        $row = 0;
        while (($data = fgetcsv($handle)) !== FALSE) 
        {
            $row++;
            $meta = array_combine($header, $data);

            if (empty($meta['name']) && empty($meta['slug']) && empty($meta['guid']))
                continue;
            
            if (!empty($meta['guid'])) {
                $Site = \Library\Logic\Leads\Site::getByGuid($meta['guid']);
            }
            elseif (!empty($meta['slug'])) {
                $Site = \Library\Logic\Leads\Site::getByGuid($meta['slug']);
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
            if (empty($meta['slug']) && empty($Site->id)) {
                // new site entry
                $Site = new \Model\Leads\Site();
                $meta['account_id'] = $Account->id;
                $meta['template_id'] = $Account->template_id;
                $meta['slug'] =  \Library\Logic\Leads\Site::getNewSlug($meta['name'], $meta['state'], $meta['city'], $category='church');
            }
            
            if (empty($meta['slug']) && empty($Site->slug)) {
                $result['failed'][] = new \Model\Error(array(
                    'code'=> '0',
                    'message'=> 'Row ' . $row . ' Unable to generate slug for ' . $meta['name'],
                ));
                continue;
            }
            
            $Site = $Site->save($meta);
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

