<?php
/**
 * @version     $Id: TwitterUsers.php 167 2010-10-19 12:17:46Z dclarke $
 * @author      Darryl Clarke
 */

class DEC_Models_TwitterUsers extends Zend_Db_Table
{
    protected $_name = 'twitter_users';
    
    public function getIdsByTwitterIds($TwitterUsers) {
        // return a list of local user id's for all the Twitter users presented.
        $ids = array();
        if (is_array($TwitterUsers) && count($TwitterUsers) > 0) {
            $ids     = implode(',', $TwitterUsers);
            $where   = array(); 
            $where[] = new Zend_Db_Expr('twitter_id IN (' . $ids . ')');
            $rowset  = $this->fetchAll($where);
            $ids     = array();
            foreach ($rowset as $row) {
                $ids[] = $row->users_id;
            }
        }
        return $ids;
    }
    
    public function getIdByTwitterId($TwitterId) {
        $return = 0;
        $where  = $this->getAdapter()->quoteInto('twitter_id = ?', $TwitterId);
        $row    = $this->fetchRow($where);
        if (is_object($row) && $row->users_id > 0) {
            $return = $row->users_id;
        }
        return $return;
    }
}