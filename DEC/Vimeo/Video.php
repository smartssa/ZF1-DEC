<?php

class DEC_Vimeo_Video
{
    private $id;

    public $title;
    public $caption;
    public $uploadDate;
    public $url;
    public $thumbnails = array();
    public $tags       = array();
    public $dimensions = array();
    public $duration   = '';

    public function __construct($video)
    {
        $attributes = $video->attributes();
        $this->id = (string)$attributes['id'];
        $vimeo = new DEC_Vimeo();
        $info = $vimeo->videosGetInfo(array('video_id' => $this->id));
        // populate other data, eh?
        $this->title      = (string)$info->video->title;
        $this->caption    = (string)$info->video->caption;
        $this->uploadDate = (string)$info->video->upload_date;
        $this->url        = (string)$info->video->urls->url;
        $this->thumbnails = (array)$info->video->thumbnails->thumbnail;
        $this->tags       = (array)$info->video->tags->tag;
        $this->dimensions['height'] = (string)$info->video->height;
        $this->dimensions['width']  = (string)$info->video->width; 
        $this->duration   = (string)$info->video->duration;
    }
}