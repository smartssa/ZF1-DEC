<?php
/**
 * @version     $Id$
 * @author      Darryl Clarke
 */

class DEC_Models_Users extends Zend_Db_Table
{
    protected $_name = 'users';

    function getPrimaryKey() {
        // yeah, this table only has one
        return $this->_primary[1];
    }
    
    function fetchUsernames()
    {
        // return an array(userid => username);

        $select  = $this->_db->select()
        ->from($this->_name, array('key' => $this->_primary, 'value' => 'username'));

        $result = $this->getAdapter()->fetchAll($select);

        foreach ($result as $row) {
            $newResult[$row['key']] = $row['value'];
        }

        return $newResult;

    }
    
    function verifyToken($token) {
        // check the token provided against a user record.
        $where = $this->getAdapter()->quoteInto('authcode = ?', $token);
        $row   = $this->fetchRow($where);
        return $row;
    }
}