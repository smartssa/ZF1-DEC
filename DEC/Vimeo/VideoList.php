<?php
/**
 * @author      Darryl E. Clarke <darryl.clarke@flatlinesystems.net>
 * @copyright   2009 Darryl E. Clarke
 * @version     $Id: IndexController.php 59 2009-03-04 07:16:20Z dclarke $
 */

require_once 'DEC/List.php';

class DEC_Vimeo_VideoList extends DEC_List
{
    private $videos = array();
    
    public function __construct($videos, $requestObject)
    {
        // reference this list to the parent list so the iterator works.
        $this->list = &$this->videos;

        foreach ($videos->video as $video) {
            $this->videos[] = new DEC_Vimeo_Video($video, $requestObject);
        }
    }
    
    public function getVideos()
    {
        return $this->videos;
    }

}