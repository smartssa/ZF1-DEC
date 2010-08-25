<?php
/*
 * @revision    $Id$
 * @author      Darryl Clarke
 *
 */

class DEC_Models_Groups extends Zend_Db_Table
{
    protected $_name = 'groups';

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

    public function createGroup($userId, $groupName, $groupStatus, $linkOwner = true) {
        // create a group, return the new Id.
        
        // link the owner?
        if ($linkOwner && $newGroupId) {
            $UhG = new DEC_Models_UsersHasGroups();
            $UhG->linkUserGroup($userId, $newGroupId);
        }
    }

}
