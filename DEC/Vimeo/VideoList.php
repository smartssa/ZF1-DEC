<?php
/**
 * @author      Darryl E. Clarke <darryl.clarke@flatlinesystems.net>
 * @copyright   2009 Darryl E. Clarke
 * @version     $Id: IndexController.php 59 2009-03-04 07:16:20Z dclarke $
 */

class DEC_Vimeo_VideoList
{
    private $videos = array();
    
    public function __construct($videos, $apiKey, $apiSecret)
    {
        foreach ($videos->video as $video) {
            $this->videos[] = new DEC_Vimeo_Video($video, $apiKey, $apiSecret);
        }
    }
    
    public function getVideos()
    {
        return $this->videos;
    }

}