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
        if (is_array($userId)) {
            foreach ($userId as $id) {
                $this->linkUserGroup($id, $groupId);
            }
        } else {

            $data = array(
                'users_id'  => $userId,
                'groups_id' => $groupId,
                'created_when'  => new Zend_Db_Expr('NOW()'),
                'modified_when' => new Zend_Db_Expr('NOW()'));

            $this->insert($data);
        }
    }

    public function unlinkUserGroup($userId, $groupId) {
        // smoke 'em.
        if (is_array($userId)) {
            foreach ($userId as $id) {
                $this->unlinkUserGroup($id, $groupId);
            }
        } else {
            $where = array(
                $this->getAdapter()->quoteInto('users_id = ?', $userId),
                $this->getAdapter()->quoteInto('groups_id = ?', $groupId));
            $this->delete($where);
        }
    }

    public function isMember($userId, $groupId) {
        $where = array(
            $this->getAdapter()->quoteInto('users_id = ?', $userId),
            $this->getAdapter()->quoteInto('groups_id = ?', $groupId));
        $row = $this->fetchRow($where);
        if ($row->id > 0) {
            return true;
        }
        return false;
    }
}
