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
}
