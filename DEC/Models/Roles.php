<?php
/*
 * @revision    $Id$
 * @author      Darryl Clarke
 * 
 */

class DEC_Models_Roles extends DEC_Db_Table
{
    protected $_name = 'roles';
    
    public function getRoles() {
        $tag = 'roles_cache_site_' . str_replace('.', '_', $_SERVER['HTTP_HOST']);
        if ($return = $this->_getCache($tag)) {
            return $return;
        }
        $roles = $this->fetchAll();
        $this->_setCache($roles, $tag);
        return $roles;
    }
}
