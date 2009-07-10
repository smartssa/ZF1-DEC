<?php

class DEC_Flickr_Photo
{
    private $id;

    public $title;
    public $description;
    public $url;
    public $tags       = array();
    public $sizes      = array();
    public $duration   = '';

    public function __construct($photo)
    {
        $attributes = $photo->attributes();
        $this->id = (string)$attributes['id'];
        
        $flickr = new DEC_Flickr();
        $info = $flickr->photosGetInfo(array('photo_id' => $this->id));
        // populate other data, eh?
        $this->title       = (string)$info->photo->title;
        $this->description = (string)$info->photo->description;
        $this->url         = (string)$info->photo->urls->url;
        $this->tags        = (array)$info->photo->tags->tag;
        
        $sizes = $flickr->photosGetSizes(array('photo_id' => $this->id));
        $this->sizes = (array)$sizes->size;

    }
}