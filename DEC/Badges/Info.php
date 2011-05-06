<?php 

class DEC_Badges_Info extends DEC_Db_Table
{
    protected $_name = 'badges';

    static function getInfo($badgeId) {
        // populate me!
        $dbInfo = new self();
        $where = $dbInfo->getAdapter()->quoteInto('id = ?', $badgeId);
        $row   = $dbInfo->fetchRow($where);
        $info['name']        =  $row->name;
        $info['description'] = $row->description;
        
        return $info;
    }
}