<?php

namespace Library\Helper;


class Utils {


    private $CI;

    function __construct()
    {
            $this->CI =& get_instance();
    }

    static public function generateToken($Site, $timestamp, $seed='Jaybskie', $method='') {
        $token = md5($Site->guid . $Site->slug . $seed . $timestamp);
        return $token;
    }
    
    /**
     * simple validation method using md5
     */
    static public function isValidToken($token, $Site, $timestamp, $seed='Jaybskie', $method='') {
        $_token = md5($Site->guid . $Site->slug . $seed . $timestamp);
        return ($_token === $token);
    }
    
    
    static public function getUSStates() {
        $states = array(
            'AL'=>'Alabama',
            'AK'=>'Alaska',
            'AZ'=>'Arizona',
            'AR'=>'Arkansas',
            'CA'=>'California',
            'CO'=>'Colorado',
            'CT'=>'Connecticut',
            'DE'=>'Delaware',
            'DC'=>'District of Columbia',
            'FL'=>'Florida',
            'GA'=>'Georgia',
            'HI'=>'Hawaii',
            'ID'=>'Idaho',
            'IL'=>'Illinois',
            'IN'=>'Indiana',
            'IA'=>'Iowa',
            'KS'=>'Kansas',
            'KY'=>'Kentucky',
            'LA'=>'Louisiana',
            'ME'=>'Maine',
            'MD'=>'Maryland',
            'MA'=>'Massachusetts',
            'MI'=>'Michigan',
            'MN'=>'Minnesota',
            'MS'=>'Mississippi',
            'MO'=>'Missouri',
            'MT'=>'Montana',
            'NE'=>'Nebraska',
            'NV'=>'Nevada',
            'NH'=>'New Hampshire',
            'NJ'=>'New Jersey',
            'NM'=>'New Mexico',
            'NY'=>'New York',
            'NC'=>'North Carolina',
            'ND'=>'North Dakota',
            'OH'=>'Ohio',
            'OK'=>'Oklahoma',
            'OR'=>'Oregon',
            'PA'=>'Pennsylvania',
            'RI'=>'Rhode Island',
            'SC'=>'South Carolina',
            'SD'=>'South Dakota',
            'TN'=>'Tennessee',
            'TX'=>'Texas',
            'UT'=>'Utah',
            'VT'=>'Vermont',
            'VA'=>'Virginia',
            'WA'=>'Washington',
            'WV'=>'West Virginia',
            'WI'=>'Wisconsin',
            'WY'=>'Wyoming',
        );
        return $states;
    }
    
    /**
     * API Key: jaybiedev@gmail.com
     * 969baba53b161794de427db05764b9d7
     * ipstack.com
     */
    public static function getGeoLocation($ip) {
        $access_key = '969baba53b161794de427db05764b9d7';
        $url = 'http://api.ipstack.com/' . $ip . '?access_key=' . $access_key;
        $response = file_get_contents($url);
        
        $Geolocation = new \Model\Geolocation();
        if ($response) {
            $Geolocation->load(json_decode($response, true));
            
        }
        
        return $Geolocation;
    }
    
    public static function uploadSiteAsset($Site, $file_uploaded_meta) {
        
        $File = new \Model\Storage\File();
        $upload_dir = WEB_PATH . '/uploads/' . $Site->getAccount()->guid .'/' . $Site->guid;
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }
        
        $filename =  strtolower(preg_replace('~[\\\\/:*?"<>|\-\'\s+]~', '_', basename($file_uploaded_meta['name'])));
        $filetype =  $file_uploaded_meta['type'];
        $filetmp_name =  $file_uploaded_meta['tmp_name'];
        $filesize =  $file_uploaded_meta['size'];
        
        $fileinfo = getimagesize($file_uploaded_meta['tmp_name']);
        $width = $fileinfo[0];
        $height = $fileinfo[1];
        $mime = $fileinfo['mime'];
        
        $upload_filepath = $upload_dir . '/' . $filename;
        $upload_filepath_backup = $upload_dir . '/' . $filename . '.backup';
        if (is_file($upload_filepath)) {
            @copy($upload_filepath, $upload_filepath_backup);
            @unlink($upload_filepath);
        }
        
        if (move_uploaded_file($file_uploaded_meta['tmp_name'], $upload_filepath)) {
            if ($filesize > 200000) {
                $file_parts = pathinfo($upload_filepath);
                $upload_filepath_resized = $upload_dir . '/' . $file_parts['filename'] . '_resized.jpg';
                exec("convert {$upload_filepath} -quality 75 {$upload_filepath_resized}");
                
                if ($width > 800) {
                    @exec("convert {$upload_filepath_resized} -resize 800 {$upload_filepath_resized}");
                }
                
                if (is_file($upload_filepath_resized)) {
                    @copy($upload_filepath_resized, $upload_filepath);
                    @unlink($upload_filepath_resized); // remove original bigger sized image
                }
            }
            
            $File->filename = $filename;
            $File->fullpath = $upload_filepath;
            
            @unlink($upload_filepath_backup);
        }
        elseif (is_file($upload_filepath_backup)) {
            @copy($upload_filepath_backup, $upload_filepath);
            throw new \Exception("Unable to upload file. (" . $filename . ")");
        }
        
        return $File;
    }


}