<?php
/**
 * Enter description here ...
 * @author DClarke
 *
 */
class DEC_Badges_RulesTally extends DEC_Db_Table
{
    protected $_name = 'badges_rules_tally';

    /**
     * Enter description here ...
     * @param unknown_type $userId
     * @param unknown_type $rulesId
     * @return number|boolean
     */
    public function incrementTally($userId, $rulesId)
    {
        $data = array('users_id' => $userId, 'badges_rules_id' => $rulesId, 'tally' => 1);
        try {
            $this->insert($data);
            return 1;
        } catch (Exception $e) {
            // update instead
            $where = array(
            $this->getAdapter()->quoteInto('users_id = ?', $userId),
            $this->getAdapter()->quoteInto('badges_rules_id = ?', $rulesId));
            $data = array('tally' => new Zend_Db_Expr('tally + 1'));
            try {
                // set a new tally
                $this->update($data, $where);
                // get the new tally
                $row = $this->fetchRow($where);
                return $row->tally;
            } catch (Exception $e) {
                echo $e->getMessage();
            }
        }
        return false;
    }

    /**
     * Enter description here ...
     * @param unknown_type $userId
     * @param unknown_type $rulesId
     * @return number
     */
    public function getTally($userId, $rulesId)
    {
        $where = array(
        $this->getAdapter()->quoteInto('users_id = ?', $userId),
        $this->getAdapter()->quoteInto('badges_rules_id = ?', $rulesId));
        	
        $row = $this->fetchRow($where);
        if ($row) {
            return $row->tally;
        } else {
            return 0;
        }

    }
}