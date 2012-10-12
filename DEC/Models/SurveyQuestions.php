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
    
    public function getUserAnswers($surveyId, $userId = null) {
        // get the questions and user answers
        
        // survey surveyQuestions->usersAnswers
        
        $where = array();
        $where[] = $this->getAdapter()->quoteInto('surveys_id = ?', $surveyId);
        // $where[] = $this->getAdapter()->quoteInto('enabled = ?', 1);

        $rows = $this->fetchAll($where);

        $answers = array();
        
        $ua = new DEC_Models_UsersAnswers();
        
        foreach ($rows as $q) {
            $new = array();
            $new['question'] = $q->question;
            $new['q_id']     = $q->id;

            // fetch from usersAnswers
            $a = $ua->fetchAnswers($q->id, $userId);
            //
            $new['answers'] = $a;
            $answers[] = $new;
        }
        
        return $answers;
        
    }
}
