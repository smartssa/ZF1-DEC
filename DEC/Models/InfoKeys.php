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
        $result = $this->fetchAll();

        foreach ($result as $row) {
            $id = $row->id;
            $newResult[$id] = $row->name;
        }
        return $newResult;
        
    }
}
