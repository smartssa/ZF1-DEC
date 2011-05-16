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
        $this->dbRoles     = new DEC_Models_Roles();
        $this->dbUserRoles = new DEC_Models_UsersHasRoles();
        // build the role list
        $rolesRs = $this->dbRoles->getRoles();
        $this->user = $user;
        foreach ($rolesRs as $role) {
            $this->addRole(new DEC_Acl_Role($role->role));
            if (isset($role->id)) {
                $roleId = $role->id;
            } else {
                $roleId = $role->ID;
            }
            $roles[$roleId] = $role->role;
        }

        // compatability with different cased userIs
        // FIXME: update this to make it more versatile.
        if (isset($this->user->id)) {
            $this->userId = $this->user->id;
        } elseif (isset($this->user->ID)) {
            $this->userId = $this->user->ID;
        } else {
            $this->userId = 0;
        }

        if ($this->userId > 0) {
            //
            $userRoles = $this->dbUserRoles->getUserRoles($this->userId);
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
        parent::addResource(new DEC_Acl_Resource($resource));
    }

    function checkPermission($resource, $action, $redirect = false)
    {
        // if we're not logged in redirect to the login page
        if ($this->userId == 0) {
            // FIXME: make this use Z_C_A's _redirect/
            if ($redirect) {
                header('Location: ' . $redirect);
                exit();
            }
        } else {
            if ($this->isAllowed($this->user->username, $resource, $action)
            || in_array('superuser', $this->user->roles))
            {
                // then let 'em in
                return true;
            } else {
                if ($redirect) {
                    header('Location: ' . $redirect);
                    exit();
                } else {
                    return false;
                }
            }
        }
        return false;
    }
}