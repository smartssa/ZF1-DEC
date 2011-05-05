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
    private $_dbUserHasBadges    = null;

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

        $this->populateBadges();
    }

    /**
     * Enter description here ...
     */
    private function populateBadges() {
        // popluate the various arrays
        $allBadges = $this->_dbUserHasBadges->getBadges($this->_userId);
        
        foreach ($allBadges as $badge) {
            $this->_allBadges[$badge->badges_id] = $badge->date_unlocked;
            if ($badge->seen_by_user == 0) {
                $this->_recentlyUnlocked[$badge->badges_id] = $badge->date_unlocked;
            } else {
                $this->_previouslyUnlocked[$badge->badges_id] = $badge->date_unlocked;
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
        return $this->_recentlyUnlocked;
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
    }
}