<?php
namespace Library\Logic\Leads;

use LogicAbstract;

class Spol extends \Library\Logic\LogicAbstract
{
    public $file = "spol.json";
    public $nexttime = 0;
    public $items = [];
    
    public $category = 'Speaking Of Life';
    public $interval_cache = "+7 day";
    
    public function __construct ($category=null) {
        
        $this->file = (WEB_PATH . "/uploads");
        $this->file =  realpath($this->file);

        if ($this->file !== false) {
            $this->file .= "/spol.json";
            if (file_exists($this->file)) {
                $filetime = filemtime($this->file);
                $this->nexttime = strtotime($this->interval_cache, $filetime);
            }
        }
        
        if (!empty($category)) {
            $this->category = $category;
        }
    }
    
    public function getAll() {
        
        if (!empty($this->items)) {
            return $this->items;
        }
            
        // nexttime is the filetime the cached json file should be refreshed.
        if (time() > $this->nexttime) {
            // $yt_channel_id = "UCgE-RnN9S3U_4zPz8VwvLmA";
            $yt_channel_id = "UCcjkt-3-U8mogW8QtO9Yr6Q";
            $url = "https://www.youtube.com/feeds/videos.xml?channel_id=" . $yt_channel_id;
            
            $xml = simplexml_load_file($url);
            
            $namespaces = $xml->getNamespaces(true); // get namespaces
            
            $items = array();
            foreach ($xml->entry as $item) {
                
                $title = trim((string) $item->title);
                if (false === stripos($title, $category)) {
                    continue;
                }
                
                $tmp = new \stdClass();
                $tmp->id = trim((string) $item->children($namespaces['yt'])->videoId);
                $tmp->title = $title;
                $tmp->author  = trim((string) $item->author->name);
                $tmp->uri  = trim((string) $item->author->uri);
                $tmp->updated =  date('Y-m-d', strtotime(trim((string) $item->updated)));
                $tmp->link = trim((string) $item->link->attributes()->href);
                
                // now for the data in the media:group
                $MediaGroup = $item->children($namespaces['media'])->group;
                
                $tmp->url = trim((string) $MediaGroup->children($namespaces['media'])->content->attributes()->url);
                $tmp->thumbnail = trim((string) $MediaGroup->children($namespaces['media'])->thumbnail->attributes()->url);
                $tmp->description = trim((string) $MediaGroup->children($namespaces['media'])->description);
                
                $tmp->post_date = date('Y-m-d', strtotime(trim((string) $item->published)));
                
                $remove_url_regex = "@(https?://([-\w\.]+[-\w])+(:\d+)?(/([\w/_\.#-]*(\?\S+)?[^\.\s])?)?)@";
                $tmp->caption = preg_replace($remove_url_regex, "", $tmp->description);
                
                $items[] = $tmp;
            }
            
            $fp = fopen($this->file, 'w');
            fwrite($fp, json_encode($items));
            fclose($fp);
        }
        
        $str = file_get_contents($this->file);
        $this->items = json_decode($str);
        
        return $this->items;
    }
    
    public function getFirst() {
        if (empty($this->items)) {
            $this->getAll();
        }
            
        return $this->items[0];
    }
    
    public function getNext() {
        if (empty($this->items)) {
            $this->getAll();
        }
        
        $account_id = 2;
        $Object = $this->items[0];
        $Cached = \Library\Logic\Cache::getByAccountId($this->category, $account_id)->getOne();
        
        $save_cache = true;
        if (!empty($Cached) && !empty($Cached->value)) {
            $Previous = json_decode($Cached->value);
            
            $nexttime = date('Y-m-d', strtotime($this->interval_cache, strtotime($Cached->date)));            
            
            if (is_object($Previous) && $nexttime > date('Y-m-d') ) {
                $Object = $Previous;
                $save_cache = false;
            }
            else {
                // get the next item
                $title = explode("|", $Previous->title) ;
                $title_parts = explode(' ', trim($title[0]));
                $previous_sequence_number = (int)end($title_parts);

                foreach ($this->items as $Item) {
                    $title = explode("|", $Item->title) ;
                    $title_parts = explode(' ', trim($title[0]));
                    $item_sequence_number = (int)end($title_parts);
                    if ($item_sequence_number > $previous_sequence_number) {
                        $Object = $Item;
                        break;
                    }
                }
            }
            
        }
        
        if ($save_cache) {
            \Library\Logic\Cache::update(
                array(
                    'id'=>$Cached->id,
                    'identifier'=>$this->category,
                    'date'=>date('Y-m-d'),
                    'account_id'=>$account_id,
                    'value'=>json_encode($Object),
                )
            );
        }
        
        return $Object;
    }
    
    
}