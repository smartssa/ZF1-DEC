<?php
/**
 * @author      Darryl E. Clarke <darryl.clarke@flatlinesystems.net>
 * @copyright   2009 Darryl E. Clarke
 * @version     $Id$
 */

require_once 'DEC/List.php';

class DEC_Flickr_PhotoList extends DEC_List
{
    private $photos = array();

    public function __construct($photos = array(), $requestObject)
    {
        $this->list = &$this->photos;

        foreach ($photos->photo as $photo) {
            try {
                $photo = new DEC_Flickr_Photo($photo, $requestObject);
                $this->photos[] = $photo;
            } catch (Exception $e) {
                // bogus photo, ignore it.
            }
        }
    }

    public function getphotos()
    {
        return $this->photos;
    }
}