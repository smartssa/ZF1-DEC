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
        $newResult = array();
        // return an arary for select lists
        $select  = $this->_db->select()
        ->from($this->_name, array('key' => $this->_primary, 'value' => 'name'));

        $result = $this->_db->fetchAll($select);

        foreach ($result as $row) {
            $id = $row['key'];
            $newResult[$id] = $row['value'];
        }
        return $newResult;
        
    }
}
