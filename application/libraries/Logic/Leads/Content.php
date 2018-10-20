<?php
namespace Library\Logic\Leads;

use LogicAbstract;

/**
 * GA
 * user: jaybiedev@gmail.com
 * Tracking ID: UA-122186239-1
 */

class Content extends \Library\Logic\LogicAbstract
{

    static function getBySlug($slug, $replace_tags=true) {
        
        $ContentRepository = new \Library\Repository\Leads\Content();
        
        $ContentRepository = new \Library\Repository\Leads\Content();
        $Content = $ContentRepository->getBySlug($slug);
        $Record = $Content->getOne();
        
        if ($replace_tags) {
            $Record = self::replaceTags($Record, $slug);
        }
            
        return $Record;
    }
    
    static function replaceTags($Content, $slug) {

        if (empty($slug))
            return $Content;
        
        $View = new \Library\View();
        $ContentTagRepository = new \Library\Repository\Leads\ContentTag();        
        $Tags = $ContentTagRepository->getAll($Content->id)->getArray();
        
        $SiteRepository = new \Library\Repository\Leads\Site();
        $Site = $SiteRepository->getBySlug($slug)->getOne();

        $AccountRepository = new \Library\Repository\Account();
        $Account = $AccountRepository->getById($Site->account_id)->getOne();
        
        $SiteDataRepository = new \Library\Repository\Leads\SiteData();
        $SiteData = $SiteDataRepository->getAll($Site->id)->getArray($key='field');

        // for Smarty replacement
        // flatten
        $site_data_meta = array();
        foreach ($SiteData as $field=>$Data) {
            $site_data_meta[$field] = $Data->field_value;
        }

        // merge all meta information
        $site_meta = $Site->getMeta();
        $account_meta = $Account->getMeta();
        $website_meta = array_merge($site_meta, $site_data_meta, $site_meta);

        // set default values
        foreach ($Tags as $Tag) {
            $is_default = false;
            if (empty($website_meta[$Tag->tag])) {
                $is_default = true;
                $website_meta[$Tag->tag] = $Tag->default_value;
            }
            
            if ($Tag->tag_system_name == 'IMAGE') {
                if ($is_default) {
                    $website_meta[$Tag->tag] = "/uploads/" . $Account->guid . "/" . $website_meta[$Tag->tag];
                }
                else {
                    $website_meta[$Tag->tag] = "/uploads/" . $Account->guid . "/" .  $Site->guid . "/" . $website_meta[$Tag->tag];
                }
            }
        }
        
        $View->assignTemplateVariable('Account', $Account);        
        $View->assignTemplateVariable('Site', $Site);
        $View->assignTemplateVariable('web', $website_meta);
        $Content->content = $View->parseTemplateContent($Content->content);
        
        /**
         * Code below is for custom parsing
         *
        $TagParser = new \Library\Logic\TagParser;
        $TagParser->Site = $Site;
        $TagParser->Account = $Account;
        $TagParser->SiteData = $SiteData;
        
        
        foreach ($Tags as $Tag) {            
            $TagParser->load($Tag);
            $value = $TagParser->getContent();

            $Content->content = str_replace($Tag->tag, $value, $Content->content);
        }
        */
        // using $Content->content
        // loop thru Tags
        // replace SiteData

        return $Content;
    }
}