<?php

namespace Library;

class Log {

    static private $logs = array();
    static private $identifier = null;
    static private $current_time = null;
    static private $current_memory_usage = null;

    static public function debug($title, $message) {
        self::Record(1, $title, $message);
    }

    static public function error($title, $message) {
        self::Record(2, $title, $message);
    }


    private function Record($level=1, $title, $message) {
        if (empty(self::$identifier))
            self::$identifier = uniqid('');

        self::$current_time = time();
        self::$current_memory_usage = memory_get_usage () / 1048576; //MB

        self::$logs[] = array(
            'level'=>$level,
            'title'=>$title,
            'module'=>'',
            'message'=>json_encode($message),
            'time' => self::$current_time,
            'memory_usage'=> self::$current_memory_usage,
        );
    }

    static public function Save()
    {

    }

    static public function Show() {
        pprint_r(self::$logs);
    }
}