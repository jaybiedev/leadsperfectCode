<?php
/*
 * Smarty plugin
 * -------------------------------------------------------------
 * File:     function.youtubeiframe.php
 * Type:     function
 * Name:     sitedropdown
 * Purpose:  widget for sitedropdown
 * -------------------------------------------------------------
 */
function smarty_function_youtubeiframe($params, $content)
{
    $width = "100%";
    $height = "400";
    $autoplay = "autoplay";
    
    if (isset($params['width']))
        $width = $params['width'];
    if (isset($params['height']))
        $height = $params['height'];

    $youtube_id = null;
    $channel = null;
    if (isset($params['channel']))
        $channel = $params['channel'];
    
    if (strtoupper($channel) == 'SPOL') {
        $Widget = new \Library\Widgets\Leads\SpolId();
        $youtube_id = $Widget->getContent();
    }
        
    $html =<<<HTML
              <iframe width="{$width}" style="width:{$width};" height="400" src="https://www.youtube.com/embed/{$youtube_id}" 
                frameborder="0"  allow="{$autoplay}; encrypted-media" allowfullscreen></iframe>
HTML;

    return $html;
}



