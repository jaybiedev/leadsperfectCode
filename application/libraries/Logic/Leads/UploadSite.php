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
        
        if (($handle = fopen($csvfile, "r")) === FALSE) {
            throw new Exception("Unable to open csv file {$zipfile}");
        }
        
        $ContentTagRepository = new \Library\Logic\Leads\ContentTag();
        $ContentTags = $ContentTagRepository->getByTemplateId($Account->template_id)->getArray('tag');
        
        /*$site_custom_fields = array();
        foreach ($ContentTags as $Tag) {
            if ($Tag->isCustomField() == false)
                continue;
           
            $field = $Tag->getFieldName();
            if (empty($field))
                continue;
            
            $site_custom_fields[$field] = $Tag;
        }
        */
        
        $result = array('success'=>array(), 'failed'=>array());        
        $header = fgetcsv($handle);        
        
        while (($data = fgetcsv($handle)) !== FALSE) 
        {
            $meta = array_combine($header, $data);

            if (empty($meta['name']) && empty($meta['slug']) && empty($meta['guid']))
                continue;
            
            if (!empty($meta['guid'])) {
                $Site = \Library\Logic\Leads\Site::getByGuid($meta['guid']);
            }
            elseif (!empty($slug)) {
                $Site = \Library\Logic\Leads\Site::getByGuid($meta['slug']);                
            }
            else {
                
            }

            // protected columns
            unset($meta['id']);
            unset($meta['guid']);
            
            $Site = $Site->save($Site, $meta);
            pprint_r($meta);
            die;
            foreach ($ContentTags as $Tag) {
                $field = $Tag->getFieldName();
                $value = $meta[$field];
                
                // copy image file.  @todo: resize
                if ($Tag->isImage() && !empty($value)) {
                    $image_source = $storage_dir . '/' . $value;
                    $image_destination = WEB_PATH . "/uploads/" . $Account->guid . '/' . $Site->guid . '/' . $value;
                    @copy($image_source, $image_destination);
                }
                
                if ($Tag->isCustomField() == false)
                    continue;
                    
                if (property_exists($Site, $field))
                    continue;
                
                if (empty($field))
                    continue;

                $SiteData = \Library\Logic\Leads\SiteData::getByField($Site->id, $field);
                $SiteDataDataObject = new \Library\DataObject($SiteData);
                $SiteData = $SiteDataDataObject->save($SiteData, array('field'=>$field, 'field_value'=>$value));
             
                unset($SiteDataDataObject);
            }
            unset($SiteDataObject);
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

