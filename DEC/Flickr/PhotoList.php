<?php
/**
 * @author      Darryl E. Clarke <darryl.clarke@flatlinesystems.net>
 * @copyright   2009 Darryl E. Clarke
 * @version     $Id$
 */

require_once 'DEC/Flickr/Photo.php';

class DEC_Flickr_PhotoList
{
    private $photos = array();

    public function __construct($photos = array(), $requestObject)
    {
        foreach ($photos->photo as $photo) {
            $this->photos[] = new DEC_Flickr_Photo($photo, $requestObject);
        }
    }

    public function getphotos()
    {
        return $this->photos;
    }


}