<?php 

class DEC_Models_AllowedAnswers extends DEC_Db_Table
{

    protected $_name = 'allowed_answers';

    public function fetchByQuestionId($questionId, $all = false) {
        $where = array();
        $where[] = $this->getAdapter()->quoteInto('survey_questions_id = ?', $questionId);
        if ($all == false) {
            $where[] = $this->getAdapter()->quoteInto('enabled = ?', 1);
        }
        $rows = $this->fetchAll($where);
        $answers = array();
        foreach ($rows as $a) {
            $answers[$a->id] = array('value' => $a->name, 'correct' => $a->correct_answer);
        }

        return $answers;
    }

    public function addAnswers($questionId, $answer, $correct = 0, $enabled = 1) {

    }

    public function disableAnswer($answerId) {

    }
}
