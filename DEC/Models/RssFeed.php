<?php
/*
 * @revision    $Id$
 * @author      Darryl Clarke
 * 
 */

class DEC_Models_RssFeed extends Zend_Db_Table
{
    protected $_name = 'rss_feed';
    
    public function getInfoFromUrl($url) 
    {
        $where = $this->getAdapter()->quoteInto('feedurl = ?', $url);
        return $this->fetchRow($where);

    }
    
    public function getStaleFeeds()
    {
        // SELECT * FROM `rss_feed` WHERE timediff(now(), last_checked) > '01:00:00'
        $where = $this->getAdapter()->quoteInto('TIMEDIFF(NOW(), last_checked) > ?', '01:00:00');
        return $this->fetchAll($where);
    }
}
