<?php
namespace Library\Logic\Leads;

use LogicAbstract;

class Site extends \Library\Logic\LogicAbstract
{

    /**
     * @return object \Model\Leads\Site
     */
    static function getBySlug($slug) {
        $Repository = new \Library\Repository\Leads\Site();

        $DataRecord = $Repository->getBySlug($slug);
        $ModelRecord = $DataRecord->getOne();
            
        return $ModelRecord;
    }

    /**
     * @return object \Model\Leads\Site
     */
    static function getByGuid($guid) {
        $Repository = new \Library\Repository\Leads\Site();
        
        $DataRecord = $Repository->getByGuid($guid);
        $ModelRecord = $DataRecord->getOne();
        
        return $ModelRecord;
    }
    
    
    /**
     * @return object \Model\Leads\Site
     */
    static function getByName($name, $operator='LOWER') {
        $Repository = new \Library\Repository\Leads\Site();
        
        $DataRecord = $Repository->getByName($name, $operator='LOWER');
        $ModelRecord = $DataRecord->getOne();
        
        return $ModelRecord;
    }
    
    /**
     * returns multiple
     */
    static function getByAccount($account_id, $where=null, $orderby=null) {
        $Repository = new \Library\Repository\Leads\Site();
        
        return $Repository->getByAccount($account_id, $where, $orderby);
    }
    
    /**
     * returns multiple
     */
    static public function getSitesByUserId($user_id) {
        $Repository = new \Library\Repository\Leads\Site();
        return $Repository->getSitesByUserId($user_id);        
    }
    
    // returns all sites based on account Id given user_id
    static public function getSitesByAccountUserId($user_id) {
        $Repository = new \Library\Repository\Leads\Site();
        return $Repository->getSitesByAccountUserId($user_id);
    }    
    
    /**
     * try to generate new slug
     * @param string $name
     * @param string $state
     * @param string $city
     * @param string $category
     * @return string 
     */
    static public function getNewSlug($name, $state, $city, $category) {
        // option 1 
        $names = explode(' ', $name);
        $name1 = $names[0];
        $slug = null;
        
        $options = array(
                trim(strtolower($city)) . trim(substr(strtolower($state), 0, 2)) . '/' . trim($category),
                trim(strtolower($name1)) . trim(substr(strtolower($state), 0, 2)) . '/' . trim($category),
                trim(strtolower($name1))  . '/' . trim($category),
            );
        
        foreach ($options as $test_slug) {
            $Site = self::getBySlug($test_slug);
            if (empty($Site->id)) {
                $slug = $test_slug;
                break;
            }
        }
        
        if (!empty($slug)) {
            return $slug;
        }
        
        // option 2 generate with numbers
        for ($i=0; $i < 100; $i++) {
            $test_slug = trim(strtolower($city)) . '_' . $i . trim(substr(strtolower($state), 0, 2)) . '/' . trim($category);
            $Site = self::getBySlug($test_slug);
            if (empty($Site->id)) {
                $slug = $test_slug;
                break;
            }            
        }
        
        return $slug;
        
    }
}