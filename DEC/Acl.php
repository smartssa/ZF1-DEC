<?php

require_once 'Zend/Acl.php';
require_once 'DEC/Acl/Role.php';
require_once 'DEC/Acl/Resource.php';

class DEC_Acl extends Zend_Acl {

    protected $user;
    protected $roles;
    protected $dbRoles;
    protected $dbUserRoles;
    protected $cache;

    function __construct($user)
    {
        $this->user = $user;
        if ($user->id > 0) {
            //
            Zend_Loader::loadClass('Roles');
            Zend_Loader::loadClass('UsersHasRoles');
            $this->dbRoles     = new Roles();
            $this->dbUserRoles = new UsersHasRoles();
            // build the role list
            $rolesRs = $this->dbRoles->fetchAll();
            foreach ($rolesRs as $role) {
                $this->addRole(new DEC_Acl_Role($role->role));
                $roles[$role->id] = $role->role;
            }
            $where = $this->dbUserRoles->getAdapter()->quoteInto('users_id = ?', $this->user->id);
            $userRoles = $this->dbUserRoles->fetchAll($where);
            $this->user->roles = array();
            
            foreach ($userRoles as $userRole) {
                $this->user->roles[] = $roles[$userRole->roles_id];
            } 
            // add the special user role
            $this->addRole(new DEC_Acl_Role($this->user->username), $this->user->roles);
        }
        // resources and actions are to be added by the controllers
    }

    function addResource($resource)
    {
        $this->add(new DEC_Acl_Resource($resource));
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
            if ($this->isAllowed($this->user->username, $resource, $action)
            || in_array('superuser', $this->user->roles))
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