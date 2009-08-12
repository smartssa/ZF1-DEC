<?php
/**
 * @author      Darryl E. Clarke <darryl.clarke@flatlinesystems.net>
 * @copyright   2009 Darryl E. Clarke
 * @version     $Id$
 */

require_once 'DEC/List.php';

class DEC_Vimeo_UserList extends DEC_List
{
    private $users = array();
    
    public function __construct($users, $requestObject)
    {
        $this->list = &$this->users;
        // TODO: add users
    }
    
    public function getUsers()
    {
        return $this->users;
    }
}
