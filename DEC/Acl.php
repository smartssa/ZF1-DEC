<?php

require_once 'Zend/Acl.php';
require_once 'DEC/Acl/Role.php';
require_once 'DEC/Acl/Resource.php';

class DEC_Acl extends Zend_Acl {

    protected $user;
    protected $userId;
    protected $roles;
    protected $dbRoles;
    protected $dbUserRoles;
    protected $cache;

    function __construct($user = null)
    {
        Zend_Loader::loadClass('DEC_Models_Roles');
        Zend_Loader::loadClass('DEC_Models_UsersHasRoles');
        $this->dbRoles     = new DEC_Models_Roles();
        $this->dbUserRoles = new DEC_Models_UsersHasRoles();
        // build the role list
        $rolesRs = $this->dbRoles->fetchAll();
        $this->user = $user;
        foreach ($rolesRs as $role) {
            $this->addRole(new DEC_Acl_Role($role->role));
            $roles[$role->id] = $role->role;
        }

        // compatability with different cased userIs
        // FIXME: update this to make it more versatile.
        if (isset($this->user->id)) {
            $this->userId = $this->user->id;
        } else {
            $this->userId = $this->user->ID;
        }

        if ($this->userId > 0) {
            //
            $where = $this->dbUserRoles->getAdapter()->quoteInto('users_id = ?', $this->userId);
            $userRoles = $this->dbUserRoles->fetchAll($where);
            $this->user->roles = array();

            foreach ($userRoles as $userRole) {
                $this->user->roles[] = $roles[$userRole->roles_id];
            }
            // add the special user role
            if (count($this->user->roles) > 0) {
                $this->addRole(new DEC_Acl_Role($this->user->username), $this->user->roles);
            } else {
                $this->addRole(new DEC_Acl_Role($this->user->username));
            }

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
        if ($this->userId == 0) {
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