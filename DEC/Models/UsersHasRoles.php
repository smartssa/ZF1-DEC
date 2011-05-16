<?php
/*
 * @revision    $Id$
 * @author      Darryl Clarke
 *
 */

class DEC_Models_UsersHasRoles extends DEC_Db_Table
{
    protected $_name = 'users_has_roles';

    public function getUserRoles($userId) {
        $tag = 'user_roles_' . $userId;
        if ($roles = $this->_getCache($tag)) {
            return $roles;
        }
        $where = $this->getAdapter()->quoteInto('users_id = ?', $userId);
        $userRoles = $this->fetchAll($where);

        $this->_setCache($userRoles, $tag);
        return $userRoles;
    }
}
