<?php
/**
 * @author dclarke
 * @throws DEC_Badges_Exception
 */
class DEC_Badges implements Iterator, Countable
{
    private $_recentlyUnlocked   = array();
    private $_previouslyUnlocked = array();
    private $_allBadges          = array();
    private $_userId             = null;
    private $_position           = 0;

    public function __construct($userId) {
        // we can populate recent's, all, and previous.
        if (! intval($userId)) {
            throw new DEC_Badges_Exception('userId provided is not an integer.');
        }

        $this->_userId = $userId;
    }

    private function populateBadges() {
        // popluate the various arrays
    }

    public function getAllBadges() {
        return $this->_allBadges;
    }

    public function getRecentBadges() {
        return $this->_recentlyUnlocked;
    }

    public function getPreviousBadges() {
        return $this->_previouslyUnlocked;
    }

    public function count() {
        return count($this->_allBadges);
    }

    public function rewind() {
        $this->_position = 0;
    }

    public function current() {
        return $this->_allBadges[$this->_position];
    }

    public function key() {
        return $this->_position;
    }

    public function next() {
        ++$this->_position;
        return $this->_allBadges[$this->_position];
    }

    public function valid() {
        return isset($this->_allBadges[$this->_position]);
    }
}