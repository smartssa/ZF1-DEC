<?php
/*
 * @revision    $Id$
 * @author      Darryl Clarke
 *
 */

class DEC_Models_Groups extends Zend_Db_Table
{
    protected $_name = 'groups';
    
    const STATUS_PUBLIC   = 1;  // anybody can join, everybody can see.
    const STATUS_PRIVATE  = 2;  // owner can add/remove, nobody else can see
    const STATUS_FACEBOOK = 4;  // uh.. yeah.
    const STATUS_TWITTER  = 8;  // twitter list
    const STATUS_CLOSED   = 16; // only owner can add, but everybody sees.
    const STATUS_DISABLED = 32;
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
