<?php

require_once 'Zend/Acl.php';
require_once 'DEC/Acl/Role.php';
require_once 'DEC/Acl/Resource.php';

class DEC_Acl extends Zend_Acl {

    protected $user;
    protected $dbRoles;
    protected $dbUserRoles;
    protected $cache;

    function __construct($user)
    {
        $this->user = $user;
        //
        $this->dbRoles     = new Roles();
        $this->dbUserRoles = new UserHasRoles();
        // build the role list
        $roles = $this->dbRoles->fetchAll();
        foreach ($roles as $role) {
            $this->addRole(new DEC_Acl_Role($role->role));
        }
        $where = $this->dbUserRoles->getAdapter()->quoteInto('users_id = ?', $this->user->id);
        $userRoles = $this->dbUserRoles->fetchAll($where);
//        foreach ()
        // add the user role
        $acl->addRole(new DEC_Acl_Role($this->user->username));

        // resources and actions are to be added by the controllers
    }

    function checkPermission($resource, $action, $redirect = false)
    {
        // if we're not logged in redirect to the login page
        if ($this->user->id == 0) {
            // bugger off - login required
            if ($redirect) {
                $this->_redirect($redirect);
            }
        } else {
            if ($this->acl->isAllowed($this->user->username, $resource, $action) 
                || in_array('superuser', $this->user->userRoles))
            {
                // then let 'em in
                return true;
            } else {
                if ($redirect) {
                    echo 'no permissions';
                } else {
                    return false;
                }
            }
        }
        return false;
    }
}