<?php
/*
 * @revision    $Id$
 * @author      Darryl Clarke
 * 
 */

class DEC_Models_RssFeedItems extends Zend_Db_Table
{
    protected $_name = 'rss_feed_items';
    
    public function getRecentByFeedId($feedId) {
        $where = $this->getAdapter()->quoteInto('rss_feed_id = ?', $feedId);
        return $this->fetchAll($where, 'modified ASC', 10);
    }
}
