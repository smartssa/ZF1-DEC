<?php
/**
 * @author      Darryl E. Clarke <darryl.clarke@flatlinesystems.net>
 * @copyright   2009 Darryl E. Clarke
 * @version     $Id$
 */

require_once 'DEC/List.php';

class DEC_Vimeo_ChannelList extends DEC_List 
{
    
    private $channels = array();
    
    public function __construct($channels, $requestObject) 
    {
        $this->list = &$this->channels;
        
    }
    
    public function getChannels() {
        return $this->channels;
    }
}