<?php 

class DEC_Models_AllowedAnswers extends DEC_Db_Table
{

    protected $_name = 'allowed_answers';

    public function fetchByQuestionId($questionId) {
        $where = array();
        $where[] = $this->getAdapter()->quoteInto('survey_questions_id = ?', $questionId);
        $where[] = $this->getAdapter()->quoteInto('enabled = ?', 1);
        $rows = $this->fetchAll($where);
        // TODO: if empty, return empty array.
        return $rows;
    }
    
    public function addAnswers($questionId, $answer, $correct = 0, $enabled = 1) {
        
    }
    
    public function disableAnswer($answerId) {
        
    }
}
