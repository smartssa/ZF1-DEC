<?php

/**
 * Enter description here ...
 * @author DClarke
 *
 */
class DEC_Badges_UsersHasBadges extends DEC_Db_Table
{
    protected $_name = 'users_has_badges';

    /**
     * Enter description here ...
     * @param unknown_type $userId
     * @param unknown_type $badgeId
     */
    public function linkBadge($userId, $badgeId) {
        // yeah, you got it. link it.
        $data = array('users_id' => $userId,
			'badges_id' => $badgeId,
			'date_unlocked' => new Zend_Db_Expr('NOW()'));
        try {
            return $this->insert($data);
        } catch (Exception $e) {
            // duplicate, fuck off.
            return false;
        }
    }
    
    public function getBadges($userId) {
        // get the users badges
        $where = $this->getAdapter()->quoteInto('users_id = ?', $userId);
        $rowset = $this->fetchAll($where);
        return $rowset;
    }
}