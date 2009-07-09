<?php

class DEC_Vimeo_Channel
{
    private $name;
    private $videoList;
    private $userList;
    private $moderaterList;

    function __construct(Zend_Rest_Client_Result $rsp)
    {
        $videos = $rsp->videos->video;
        $this->videoList = new DEC_Vimeo_VideoList($videos);
    }
    
    public function getVideoList()
    {
        return $this->videoList;
    }
}