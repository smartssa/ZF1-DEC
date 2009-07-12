<?php
/**
 * @author      Darryl E. Clarke <darryl.clarke@flatlinesystems.net>
 * @copyright   2009 Darryl E. Clarke
 * @version     $Id$
 */

require_once 'DEC/List.php';

class DEC_Flickr_GroupList extends DEC_List
{

    private $groups = array();
    
    public function __construct($groups, $requestObject)
    {
        $this->list = &$this->groups;
    }
}
