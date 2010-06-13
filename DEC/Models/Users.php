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
        ->from($this->_name, array('key' => 'id', 'value' => 'username'));
        $result = $this->getAdapter()->fetchAll($select);
        foreach ($result as $row) {
            $newResult[$row['key']] = $row['value'];
        }

        return $newResult;

    }

    function fetchFirstnames()
    {
        // return an array(userid => username);
        $select  = $this->_db->select()
        ->from($this->_name, array('key' => 'id', 'value' => 'firstname'));
        $result = $this->getAdapter()->fetchAll($select);
        foreach ($result as $row) {
            if ($row['value'] == '') {
                $name = 'Anonymous';
            } else {
                $name = $row['value']; 
            }
            $newResult[$row['key']] = $name;
        }
        return $newResult;

    }

    function updateToken($userId) {
        // generate a new token
        $token = md5(time() . 'pants' . microtime(true));
        $where = $this->getAdapter()->quoteInto('id = ?', $userId);
        $data  = array('authcode' => $token);
        try {
            $this->update($data, $where);
            return $token;
        } catch (Exception $e) {
            //  failed to update token
        }
    }

    function verifyToken($token) {
        // check the token provided against a user record.
        if ($token != '') {
            $where = $this->getAdapter()->quoteInto('authcode = ?', $token);
            $row   = $this->fetchRow($where);
            return $row;
        } else {
            return false;
        }
    }
}