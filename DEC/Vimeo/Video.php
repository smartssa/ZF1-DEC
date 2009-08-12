<?php
/**
 * @author      Darryl E. Clarke <darryl.clarke@flatlinesystems.net>
 * @copyright   2009 Darryl E. Clarke
 * @version     $Id$
 */

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

    public function __construct($video, $requestObject)
    {
        // TODO: verify video is actually a video element.
        $attributes = $video->attributes();
        $this->id = (string)$attributes['id'];
        $config = Zend_Registry::get('config');

        $vimeo = $requestObject;
        /*
         * There's a chance we didn't get a full response from some API calls
         * if that's the case, we'll need to do a separate query to get full
         * details.
         */
        if ($info->title) {
            $info = $video;
        } else {
            // title is not included in the short response.
            $info = $vimeo->videosGetInfo(array('video_id' => $this->id));
        }

        // populate other data, eh?
        $this->title      = (string)$info->title;
        $this->caption    = (string)$info->caption;
        $this->uploadDate = (string)$info->upload_date;
        $this->url        = (string)$info->urls->url;
        $this->thumbnails = (array)$info->thumbnails->thumbnail;
        $this->tags       = (array)$info->tags->tag;
        $this->duration   = (string)$info->duration;
        
        
        $this->dimensions['height'] = (string)$info->height;
        $this->dimensions['width']  = (string)$info->width; 
    }
    
    public function getId() {
        return $this->id;
    }
}