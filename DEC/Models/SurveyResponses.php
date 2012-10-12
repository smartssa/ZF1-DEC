<?php 

class DEC_Models_SurveyResponses extends DEC_Db_Table
{
    protected $_name = 'survey_responses';

    public function completeSurvey($surveyId, $responseId, $userId) {
        $where = array();
        $where[] = $this->getAdapter()->quoteInto('id = ?', $responseId);
        $where[] = $this->getAdapter()->quoteInto('surveys_id = ?', $surveyId);
        $where[] = $this->getAdapter()->quoteInto('users_id = ?', $userId);
        $where[] = $this->getAdapter()->quoteInto('complete = ?', 0);

        $data = array('complete' => 1, 'modified' => new Zend_Db_Expr('NOW()'));

        try {
            $this->update($data, $where);
            return true;
        } catch (Exception $e) {
            // wtf.
            throw new Exception('Failed to update survey response record...', null, $e);
        }

    }
    public function startSurvey($surveyId, $userId) {
        // look for one first.
        $where = array();
        $where[] = $this->getAdapter()->quoteInto('surveys_id = ?', $surveyId);
        $where[] = $this->getAdapter()->quoteInto('users_id = ?', $userId);
        $where[] = $this->getAdapter()->quoteInto('complete = ?', 0);
        $row = $this->fetchRow($where);

        if (is_object($row)) {
            return $row->id;
        } else {
            // then make one if it doesn't exist.
            $data = array('users_id' => $userId, 'surveys_id' => $surveyId);
            $data = $this->_cleanData($data);
            try {
                return $this->insert($data);
            } catch (Exception $e) {
                throw new Exception('No Survey Reponse Record...', null, $e);
            }
        }
    }
    
    public function getStats($surveyId) {
        
        $where = array();
        $where[] = $this->getAdapter()->quoteInto('surveys_id = ?', $surveyId);
        $rows = $this->fetchAll($where);
        
        $users_started   = array(); // to track uq users
        $users_completed = array();
        $starts    = 0;
        $completes = 0;
        
        $stats = array();
        foreach ($rows as $row) {
            $users_started[$row->users_id] = true;
            $starts++;
            if ($row->complete) {
                $users_completed[$row->users_id] = true;
                $completes++;
            }
            
        }

        $stats['unique_started'] = count($users_started);
        $stats['unique_completed'] = count($users_started);
        $stats['total_started'] = $starts;
        $stats['total_completed'] = $completes;
        $stats['user_list'] = $users_started + $users_completed;
        
        return $stats;
    }
    
}