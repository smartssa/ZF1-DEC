<?php
/**
 * @version     $Id$
 * @author      Darryl Clarke
 */

class DEC_Models_Users extends Zend_Db_Table
{
    protected $_name = 'users';
    
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
}