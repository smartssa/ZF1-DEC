<?php

class DEC_Flickr_PhotoList
{
    private $photos = array();

    public function __construct()
    {
        foreach ($photos as $photo) {
            $this->photos[] = new DEC_Flickr_Photo($photo);
        }
    }

    public function getphotos()
    {
        return $this->photos;
    }


}