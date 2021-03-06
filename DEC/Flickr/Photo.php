<?php
/**
 * @author      Darryl E. Clarke <darryl.clarke@flatlinesystems.net>
 * @copyright   2009 Darryl E. Clarke
 * @version     $Id$
 */

class DEC_Flickr_Photo
{
    private $id;

    public $title;
    public $description;
    public $flickrUser;
    public $url;
    public $datePosted;
    public $tags       = array();
    public $sizes      = array();

    public function __construct($photo, $requestObject)
    {
        $attributes       = $photo->attributes();
        $this->id         = (string)$attributes['id'];
        
        $flickr = $requestObject;
        $info = $flickr->photosGetInfo(array('photo_id' => $this->id));

        // populate other data, eh?
        $this->title       = (string)$info->photo->title;
        $this->description = (string)$info->photo->description;
        $this->url         = (string)$info->photo->urls->url;
        $this->tags        = (array)$info->photo->tags->tag;
        
        // username and userid
        if (! $info->owner) {
            // no owner, no photo.
            throw new Exception('Flickr_Photo failed to get info for ID ' . $this->id);
        }
        $ownerAttribs = $info->owner->attributes();
        $this->flickrUserId      = (string)$ownerAttribs['nsid'];
        $this->flickrDisplayName = (string)$ownerAttribs['realname'];
        $this->flickrUserName    = (string)$ownerAttribs['username'];

        // unix timestamp.
        $dates = $info->photo->dates->attributes();
        $this->datePosted  = (string)$dates['posted'];
        
        $sizes = $flickr->photosGetSizes(array('photo_id' => $this->id));
        foreach ($sizes->size as $size):
            $attributes = $size->attributes();
            $label = (string)$attributes['label'];
            $this->sizes[$label] = array(
                'source' => (string)$attributes['source'],
                'width'  => (string)$attributes['width'],
                'height' => (string)$attributes['height'],
                'url'    => (string)$attributes['url'],
                'media'  => (string)$attributes['media']
                ); 
        endforeach;
        

    }
    
    public function getId()
    {
        return $this->id;
    }
}