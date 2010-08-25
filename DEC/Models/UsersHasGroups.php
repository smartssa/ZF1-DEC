<?php
/*
 * @revision    $Id$
 * @author      Darryl Clarke
 * 
 */

class DEC_Models_UsersHasGroups extends Zend_Db_Table
{
    protected $_name = 'users_has_groups';
    
    public function linkUserGroup($userId, $groupId) {
        // make sure group exists, then link 'em.
    }
    
    public function unlinkUserGroup($userId, $groupId) {
        // smoke 'em.
    }
}
