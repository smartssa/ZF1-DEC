<?php
/*
 * @revision    $Id$
 * @author      Darryl Clarke
 * 
 */

class DEC_Models_InfoKeys extends DEC_Db_Table
{
    protected $_name = 'info_keys';
    
    function getKeys()
    {
        $tag = 'info_keys_cache';
        if ($return = $this->_getCache($tag)) {
            // return $return;
        }

        $newResult = array();
        $result = $this->fetchAll();

        foreach ($result as $row) {
            $id = $row->id;
            $newResult[$id] = $row->name;
        }
        
        $this->_setCache($newResult, $tag);
        return $newResult;
        
    }
}
