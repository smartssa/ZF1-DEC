<?php
/*
 * @revision    $Id$
 * @author      Darryl Clarke
 *
 */

class DEC_Models_Groups extends Zend_Db_Table
{
    protected $_name = 'groups';

    function getGroupsList() {
        // return an arary for select lists
        $select  = $this->_db->select()
        ->from($this->_name, array('key' => 'id', 'value' => 'name'));

        $result = $this->getAdapter()->fetchAll($select);

        $result['--'] = "";

        return $result;
    }
    
}
