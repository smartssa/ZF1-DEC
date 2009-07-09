<?php

class DEC_Vimeo_VideoList
{
    private $videos = array();
    
    public function __construct($videos)
    {
        foreach ($videos as $video) {
            $this->videos[] = new DEC_Vimeo_Video($video);
        }
    }
    
    public function getVideos()
    {
        return $this->videos;
    }

}