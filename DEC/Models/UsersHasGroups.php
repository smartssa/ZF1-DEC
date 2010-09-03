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
        $data = array(
            'users_id'  => $userId,
            'groups_id' => $groupId );
        
        $this->insert($data);
    }
    
    public function unlinkUserGroup($userId, $groupId) {
        // smoke 'em.
        $where = array(
            $this->getAdapter()->quoteInto('users_id = ?', $userId),
            $this->getAdapter()->quoteInto('groups_id = ?', $groupId));
        
        $this->delete($where);
    }
}
