<?php 

class DEC_Models_SurveyQuestions extends DEC_Db_Table
{

    protected $_name = 'survey_questions';

    public function fetchBySurveyId($surveyId) {
        $where = array();
        $where[] = $this->getAdapter()->quoteInto('surveys_id = ?', $surveyId);
        $where[] = $this->getAdapter()->quoteInto('enabled = ?', 1);
        
        $rows = $this->fetchAll($where);
        
        // TODO: if empty, return empty array.
        return $rows;
    }
    
    public function addQuestion($surveyId, $question, $type, $required = 0, $enabled = 0) {
        
    }
    
    public function enableQuestion($questionId) {
        // enable a question, as long as it has at least one answer enabled.
    }
    
    public function disableQuestion($questionId) {
        
    }
}
