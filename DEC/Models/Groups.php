<?php
/*
 * @revision    $Id$
 * @author      Darryl Clarke
 *
 */

class DEC_Models_Groups extends Zend_Db_Table
{
    protected $_name = 'groups';
    
    const STATUS_PUBLIC   = 1;
    const STATUS_PRIVATE  = 2;
    const STATUS_FACEBOOK = 4;
    const STATUS_TWITTER  = 8;
    const STATUS_DISABLED = 16;
    const STATUS_SYSTEM   = 64;

    function getGroupsList() {
        // return an arary for select lists
        $select  = $this->_db->select()
        ->from($this->_name, array('key' => 'id', 'value' => 'name'));
        $result = $this->getAdapter()->fetchAll($select);
        $result['--'] = "";
        return $result;
    }

    public function getUserOwnGroups($userId) {
        // return a list of names and id's the user owns
    }

    public function getUserGroups($userId) {
        // return names and id's of groups the user is part of
    }

    public function createGroup($userId, $groupName, $groupStatus = self::STATUS_PUBLIC, $linkOwner = true) {
        // create a group, return the new Id.
        $data = array(
        	'users_id' => $userId,
            'name'     => $groupName,
            'status'   => $groupStatus,
            'created_when'  => new Zend_Db_Expr('NOW()'),
            'modified_when' => new Zend_Db_Expr('NOW()'));
        
        $newGroupId = $this->insert($data);

        // link the owner?
        if ($linkOwner && $newGroupId) {
            $UhG = new DEC_Models_UsersHasGroups();
            $UhG->linkUserGroup($userId, $newGroupId);
        }
        return $newGroupId;
    }
    
    public function updateStatus($groupId, $newStatus) {
        // modify the records status
    }

}
