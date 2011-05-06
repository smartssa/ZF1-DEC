<?php
/**
 * @author dclarke
 * @throws DEC_Badges_Exception
 */
class DEC_Badges extends DEC_Db_Table implements Iterator, Countable
{
    private $_recentlyUnlocked   = array();
    private $_previouslyUnlocked = array();
    private $_allBadges          = array();
    private $_userId             = null;
    private $_position           = 0;
    /**
     * Enter description here ...
     * @var DEC_Badges_UsersHasBadges
     */
    private $_dbUserHasBadges    = null;
    /**
     * Enter description here ...
     * @var DEC_Badges_RulesTally
     */
    private $_dbRulesTally       = null;

    /**
     * Enter description here ...
     * @param unknown_type $userId
     * @throws DEC_Badges_Exception
     */
    public function __construct($userId) {
        // we can populate recent's, all, and previous.
        if (! intval($userId)) {
            throw new DEC_Badges_Exception('userId provided is not an integer.');
        }
        $this->_userId = $userId;
        $this->_dbUserHasBadges = new DEC_Badges_UsersHasBadges();
        $this->_dbRulesTally    = new DEC_Badges_RulesTally();

        $this->populateBadges();
    }

    /**
     * Enter description here ...
     */
    private function populateBadges() {
        // popluate the various arrays
        $allBadges = $this->_dbUserHasBadges->getBadges($this->_userId);

        foreach ($allBadges as $badge) {
            $this->_allBadges[$badge->badges_id]['date'] = $badge->date_unlocked;
            $this->_allBadges[$badge->badges_id]['data'] = DEC_Badges_Info::getInfo($badge->badges_id);

            if ($badge->seen_by_user == 0) {
                $this->_recentlyUnlocked[$badge->badges_id]['date'] = $badge->date_unlocked;
                $this->_recentlyUnlocked[$badge->badges_id]['data'] = DEC_Badges_Info::getInfo($badge->badges_id);
            } else {
                $this->_previouslyUnlocked[$badge->badges_id]['date'] = $badge->date_unlocked;
                $this->_previouslyUnlocked[$badge->badges_id]['data'] = DEC_Badges_Info::getInfo($badge->badges_id);
            }
        }
    }

    /**
     * Enter description here ...
     * @return multitype:
     */
    public function getAllBadges() {
        return $this->_allBadges;
    }

    /**
     * Enter description here ...
     * @return multitype:
     */
    public function getRecentBadges() {
        $this->flagAsSeen(array_keys($this->_recentlyUnlocked));
        return $this->_recentlyUnlocked;
    }

    /**
     * @param badgeIds array
     */
    public function flagAsSeen($badgeIds) {
        //
        if (count($badgeIds) > 0) {
            foreach ($badgeIds as $badgeId) {
                $this->_dbUserHasBadges->flagAsSeen($this->_userId, $badgeId);
            }
        }
    }

    /**
     * Enter description here ...
     * @return multitype:
     */
    public function getPreviousBadges() {
        return $this->_previouslyUnlocked;
    }

    /* (non-PHPdoc)
     * @see Countable::count()
     */
    public function count() {
        return count($this->_allBadges);
    }

    /* (non-PHPdoc)
     * @see Iterator::rewind()
     */
    public function rewind() {
        $this->_position = 0;
    }

    /* (non-PHPdoc)
     * @see Iterator::current()
     */
    public function current() {
        return $this->_allBadges[$this->_position];
    }

    /* (non-PHPdoc)
     * @see Iterator::key()
     */
    public function key() {
        return $this->_position;
    }

    /* (non-PHPdoc)
     * @see Iterator::next()
     */
    public function next() {
        ++$this->_position;
        return $this->_allBadges[$this->_position];
    }

    /* (non-PHPdoc)
     * @see Iterator::valid()
     */
    public function valid() {
        return isset($this->_allBadges[$this->_position]);
    }

    /**
     * Enter description here ...
     * @return multitype:
     */
    public function getBadgeIds() {
        return array_keys($this->_allBadges);
    }

    /**
     * Enter description here ...
     * @param unknown_type $badgesId
     * @param unknown_type $userId
     */
    public function unlock($badgesId, $userId) {
        /// *BLIP*
        $this->_dbUserHasBadges->linkBadge($userId, $badgesId);
        // add it to the badges object for fun.
        // smoke the tallies for this cheevo
        $this->_dbRulesTally->removeTalliesForUser($badgesId, $userId);
        // reresh badges lists
        $this->populateBadges();
    }
}