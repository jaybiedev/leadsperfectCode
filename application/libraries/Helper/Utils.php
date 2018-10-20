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


}