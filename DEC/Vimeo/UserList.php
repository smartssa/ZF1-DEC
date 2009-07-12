<?php
/**
 * @author      Darryl E. Clarke <darryl.clarke@flatlinesystems.net>
 * @copyright   2009 Darryl E. Clarke
 * @version     $Id: IndexController.php 59 2009-03-04 07:16:20Z dclarke $
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
