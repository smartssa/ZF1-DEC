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

    public function __construct($photos = array(), $apiKey, $apiSecret)
    {
        foreach ($photos->photo as $photo) {
            $this->photos[] = new DEC_Flickr_Photo($photo, $apiKey, $apiSecret);
        }
    }

    public function getphotos()
    {
        return $this->photos;
    }


}