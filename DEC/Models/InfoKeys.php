<?php
/*
 * @revision    $Id$
 * @author      Darryl Clarke
 * 
 */

class DEC_Models_InfoKeys extends Zend_Db_Table
{
    protected $_name = 'info_keys';
    
    function getKeys()
    {
        // return an arary for select lists
        $select  = $this->_db->select()
        ->from($this->_name, array('key' => 'id', 'value' => 'name'));

        $result = $this->getAdapter()->fetchAll($select);

        foreach ($result as $row) {
            $id = $row['key'];
            $newResult[$id] = $row['value'];
        }
        return $newResult;
        
    }
}