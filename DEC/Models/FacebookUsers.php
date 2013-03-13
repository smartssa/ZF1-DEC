<?php
/**
 * @version     $Id$
 * @author      Darryl Clarke
 */

class DEC_Models_FacebookUsers extends DEC_Db_Table
{
    protected $_name = 'facebook_users';
    
    public function getIdsByFB($facebookUsers) {
        // return a list of local user id's for all the facebook users presented.
        $ids = array();
        if (is_array($facebookUsers) && count($facebookUsers) > 0) {
            $ids     = implode(',', $facebookUsers);
            $where   = array(); 
            $where[] = new Zend_Db_Expr('facebook_id IN (' . $ids . ')');
            $rowset  = $this->fetchAll($where);
            $ids     = array();
            foreach ($rowset as $row) {
                $ids[] = $row->users_id;
            }
        }
        return $ids;
    }
    
    public function getIdByFB($facebookId) {
        $tag = 'facebook_users_id_by_fb_' . $facebookId;
        if ($return = $this->_getCache($tag)) {
            return $return;
        }
        $return = '';
        $where  = $this->getAdapter()->quoteInto('facebook_id = ?', $facebookId);
        $row    = $this->fetchRow($where);
        if ($row->users_id > 0) {
            $return = $row->users_id;
            $this->_setCache($return, $tag, 14400);
        }
        return $return;
    }
    
    public function getIdById($userId) {
        $tag = 'facebook_users_id_by_id_' . $userId;
        if ($return = $this->_getCache($tag)) {
            return $return;
        }

        $return = '';
        $where  = $this->getAdapter()->quoteInto('users_id = ?', $userId);
        $row    = $this->fetchRow($where);
        if ($row->users_id > 0) {
            $return = $row->facebook_id;
            $this->_setCache($return, $tag, 14400);
        }
        return $return;
    }
}