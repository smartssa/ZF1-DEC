<?php
/*
 * @revision    $Id$
 * @author      Darryl Clarke
 *
 */

class DEC_Models_Groups extends Zend_Db_Table_Abstract
{
    protected $_name = 'groups';

    const STATUS_PUBLIC   = 1;  // anybody can join, everybody can see.
    const STATUS_CLOSED   = 2;  // only owner can add, but everybody sees.
    const STATUS_PRIVATE  = 4;  // owner can add/remove, nobody else can see
    const STATUS_FACEBOOK = 8;  // uh.. yeah.
    const STATUS_TWITTER  = 16; // twitter list
    const STATUS_DISABLED = 32;
    const STATUS_SYSTEM   = 64;

    function getGroupsList($filter = self::STATUS_PUBLIC, $userId = null) {
        // return an arary for select lists
        $select  = $this->_db->select()
        ->from($this->_name, array('key' => 'id', 'value' => 'name'))
        ->where('status & ?', $filter);

        if ($userId !== null && $userId > 0) {
            // add a where clause for the user
        }
        $result = $this->getAdapter()->fetchAll($select);
        $result['--'] = "";
        return $result;
    }

    public function getPublic($userId = null) {
        // if user id is provided, exclude groups they are members of
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(array('g' => 'groups'));
        $select->joinLeft(array('u' => 'users'), 'g.users_id = u.id', array('firstname', 'lastname', 'username'));
        $select->where($this->getAdapter()->quoteInto("g.status & ? ", self::STATUS_PUBLIC));
        if ($userId > 0) {
            $select->where($this->getAdapter()->quoteInto('g.users_id <> ?', $userId));
            $ids = $this->getUserInIds($userId);
            if (count($ids) > 0) {
                $userInGroups = implode(',', $ids);
                $expr = new Zend_Db_Expr('g.id NOT IN (' . $userInGroups . ')');
                $select->where($expr);
            }
        }

        $rowset = $this->fetchAll($select);
        return $rowset;

    }

    public function getClosed($userId) {
        $where = $this->getAdapter()->quoteInto("status & ? ", self::STATUS_CLOSED);
        $rowset = $this->fetchAll($where);
        return $rowset;
        
    }

    public function getPrivate($userId) {
        $where = array();
        $where[] = $this->getAdapter()->quoteInto("status & ? ", self::STATUS_PRIVATE);
        $where[] = $this->getAdapter()->quoteInto('users_id = ?', $userId);
        $rowset = $this->fetchAll($where);
        return $rowset;
    }
    
    public function getUserOwn($userId) {
        // get a list of the groups the user owns.
        $where  = $this->getAdapter()->quoteInto('users_id = ?', $userId);
        $rowset = $this->fetchAll($where);
        return $rowset;
    }
    
    public function getUserIn($userId) {
        // get a list of groups the user is in, exclude non-owned private
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(array('g' => 'groups'));
        $select->joinLeft(array('uhg' => 'users_has_groups'), 'g.id = uhg.groups_id', array('users_id'));
        $select->joinLeft(array('u' => 'users'), 'g.users_id = u.id', array('firstname', 'lastname', 'username'));
        $select->where($this->getAdapter()->quoteInto("g.status & ? ", self::STATUS_PUBLIC + self::STATUS_CLOSED));
        if ($userId > 0) {
            // not user's group
            $select->where($this->getAdapter()->quoteInto('g.users_id <> ?', $userId));
            // user is connected
            $select->where($this->getAdapter()->quoteInto('uhg.users_id = ?', $userId));
        }
        $rowset = $this->fetchAll($select);
        return $rowset;
    }

    public function getUserInIds($userId) {
        // return IDs only.
        $rowset = $this->getUserIn($userId);
        $ids = array();
        foreach ($rowset as $row) {
            $ids[] = $row->id;
        } 
        return $ids;
    }
    
    public function getMembers($groupId, $userId) {

        $matchedGroupId = $this->isAllowed($groupId, $userId, true);

        if ($matchedGroupId > 0) {
            $select = $this->select();
            $select->setIntegrityCheck(false);
            $select->from(array('g' => 'groups'));
            $select->joinLeft(array('uhg' => 'users_has_groups'), 'g.id = uhg.groups_id', array('users_id'));
            $select->joinLeft(array('u' => 'users'), 'uhg.users_id = u.id', array('firstname', 'lastname', 'username'));
            $select->where($this->getAdapter()->quoteInto('g.id = ?', $matchedGroupId));
            $rowset = $this->fetchAll($select);
            return $rowset;
        } 
        return false;
    }

    public function addUser($groupId, $addingUserId, $callingUserId = null) {
        $matchedGroupId = $this->isAllowed($groupId, $callingUserId);
        if ($matchedGroupId > 0 && $addingUserId > 0) {
            $UhG = new DEC_Models_UsersHasGroups();
            $UhG->linkUserGroup($addingUserId, $matchedGroupId);
            return true;
        }
        return false;
    }

    public function removeUser($groupId, $removingUserId, $callingUserId = null) {
        $matchedGroupId = $this->isAllowed($groupId, $callingUserId, true);
        if ($matchedGroupId > 0 && $removingUserId > 0) {
            $UhG = new DEC_Models_UsersHasGroups();
            $UhG->unlinkUserGroup($removingUserId, $matchedGroupId);
            return true;
        }
        return false;
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
    
    public function checkFacebook($userId, $friendsList) {
        // update/create a facebook group for userid
        $where   = array();
        $where[] = $this->getAdapter()->quoteInto('users_id = ?', $userId);
        $where[] = new Zend_Db_Expr('status & ' . self::STATUS_FACEBOOK); 
        $group   = $this->fetchRow($where);
        
        if ($group->id > 0) {
            // we got one
            $groupId = $group->id;
            $updateMembers = true;
        } else {
            $groupId = $this->createGroup($userId, 'Facebook Group', 
                self::STATUS_FACEBOOK + self::STATUS_PRIVATE + self::STATUS_SYSTEM, true);
            $updateMembers = true;
        }
        
        if ($groupId && $updateMembers) {
            // add anybody in friends list to it
            // or replace existing list with new list
            $dbFB = new DEC_Models_FacebookUsers();
            $ids  = $dbFB->getIdsByFB($friendsList);
            $UhG = new DEC_Models_UsersHasGroups();
            $UhG->linkUserGroup($ids, $groupId);
        }
    }

    public function updateStatus($groupId, $do = 'add', $statuscode) {
        // modify the records status
        switch ($do) {
            case 'add':
                // make sure it doesn't already have the status
                break;
            case 'remove':
                // make sure it already has the status
                break;
        }
    }

    private function isAllowed($groupId, $userId, $memberCheck = false) {
        $matchedGroupId = 0;
        // you can only get members if:
        // public
        $where   = array();
        $where[] = $this->getAdapter()->quoteInto('status & ?', self::STATUS_PUBLIC);
        $where[] = $this->getAdapter()->quoteInto('id = ?', $groupId);
        $group   = $this->fetchRow($where);
        
        if (is_object($group) && $group->id > 0) {
            $matchedGroupId = $group->id;
        } else {
            // private + is owner
            $where   = array();
            $where[] = $this->getAdapter()->quoteInto('status & ?', self::STATUS_PRIVATE);
            $where[] = $this->getAdapter()->quoteInto('users_id = ?', $userId);
            $where[] = $this->getAdapter()->quoteInto('id = ?', $groupId);
            $group   = $this->fetchRow($where);
            if (is_object($group) && $group->id > 0) {
                $matchedGroupId = $group->id;
            } else {
                //closed + is member || closed + is owner
                $where   = array();
                $where[] = $this->getAdapter()->quoteInto('status & ?', self::STATUS_CLOSED);
                $where[] = $this->getAdapter()->quoteInto('id = ?', $groupId);
                $group   = $this->fetchRow($where);
                if (is_object($group) && $group->id > 0 && $group->users_id == $userId) {
                    // owner, good. 
                    $matchedGroupId = $group->id;
                } else {
                    // check if user is a member
                    $dbUhG = new DEC_Models_UsersHasGroups();
                    if ($memberCheck && $dbUhG->isMember($userId, $group->id)) {
                        $matchedGroupId = $group->id; // member!
                    } else {
                        $matchedGroupId = 0;
                    }
                }
            }
        }
        return $matchedGroupId;
    }
}
