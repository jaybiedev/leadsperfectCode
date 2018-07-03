<?php
if (false == function_exists('pprint_r')) {
    function pprint_r($data) {
        echo "<pre>";
                print_r($data);
                echo "</pre>";

        return;
    }
}


if (false == function_exists('legacy_redirect')) {
    function legacy_redirect($url)
    {
        header("Location:" . $url);
        exit;

    }
}

if (false == function_exists('convertDate')) {

    function convertDate($date, $format = 'Y-m-d')
    {
        return date($format, strtotime($date));
    }
}