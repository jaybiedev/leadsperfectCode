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
        
        $ContentTagRepository = new \Library\Repository\Leads\ContentTag();        
        $Tags = $ContentTagRepository->getAll($Content->id)->getArray();
        
        $SiteRepository = new \Library\Repository\Leads\Site();
        $Site = $SiteRepository->getBySlug($slug)->getOne();

        $AccountRepository = new \Library\Repository\Account();
        $Account = $AccountRepository->getById($Site->account_id)->getOne();
        
        $SiteDataRepository = new \Library\Repository\Leads\SiteData();
        $SiteData = $SiteDataRepository->getAll($Site->id)->getArray($key='field');
        
        $TagParser = new \Library\Logic\TagParser;
        $TagParser->Site = $Site;
        $TagParser->Account = $Account;
        $TagParser->SiteData = $SiteData;
        
        foreach ($Tags as $Tag) {            
            $TagParser->load($Tag);
            $value = $TagParser->getContent();

            $Content->content = str_replace($Tag->tag, $value, $Content->content);
        }

        // using $Content->content
        // loop thru Tags
        // replace SiteData
        return $Content;
    }
}