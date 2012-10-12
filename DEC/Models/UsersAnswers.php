<?php 

class DEC_Models_UsersAnswers extends DEC_Db_Table
{

    protected $_name = 'users_answers';

    public function fetchAnswers($questionId, $userId = null) {
        // TODO: include/exclude completed surveys.
        $where = array();

        if ($userId !== null) {
            $where[] = $this->getAdapter()->quoteInto('users_id = ?', (int)$userId);
        }
        $where[] = $this->getAdapter()->quoteInto('survey_questions_id = ?', $questionId);
        $rows = $this->fetchAll($where);

        $answers = array();
        foreach ($rows as $answer) {
            if (isset($answers[$answer->survey_questions_id])) {
                $new = $answers[$answer->survey_questions_id];
            } else {
                $new = array();
            }
            if (isset($new[$answer->value])) {
                $new[$answer->value]++;
            } else {
                $new[$answer->value] = 1;
            }
            $answers[$answer->survey_questions_id] = $new;
        }
        
        return $answers;
    }
    
    public function fetchByRUQ($responseId, $userId, $questionId) {
        $where = array();
        $where[] = $this->getAdapter()->quoteInto('users_id = ?', $userId);
        $where[] = $this->getAdapter()->quoteInto('survey_questions_id = ?', $questionId);
        $where[] = $this->getAdapter()->quoteInto('survey_responses_id = ?', $responseId);
        $row = $this->fetchRow($where);

        $value = null;

        if (is_object($row)) {
            $value = $row->value;
        }

        return $value;
    }

    public function saveAnswers($userId, $surveyId, $responseId, $values) {

        $success = true;
        foreach ($values as $key => $value) {
            // if it's not prefixed in question_, throw it out.
            preg_match('/question_([0-9]+)/i', $key, $matches);
            if (count($matches) == 2) {
                // split out the id, question_id
                $id = $matches[1];
                // TODO: decide what type of question this is and where the value needs to be stored
                // either 'allowed_answers_id' if it's an integer (radio/select type)
                // or 'value', if it's a text/textarea type.
                // insert the reuslt into the table
                // if it's a checkbox, we're going to have an array.
                if (is_array($value)) {
                    $value = implode(',', $value);
                }

                // if the user has already respondded to this, update the value.
                $where = array();
                $where[] = $this->getAdapter()->quoteInto('users_id = ?', $userId);
                $where[] = $this->getAdapter()->quoteInto('survey_questions_id = ?', $id);
                $where[] = $this->getAdapter()->quoteInto('survey_responses_id = ?', $responseId);

                $row = $this->fetchRow($where);

                if (is_object($row)) {
                    $row->modified = new Zend_Db_Expr('NOW()');
                    $row->value    = $value;
                    $row->save();
                } else {
                    $data = array(
                            'survey_questions_id'  => $id,
                            'users_id'             => $userId,
                            'survey_responses_id'  => $responseId,
                            'value'                => $value,
                            'modified'             => new Zend_Db_Expr('NOW()'),
                            'created'              => new Zend_Db_Expr('NOW()'));
                    try {
                        $this->insert($data);
                    } catch (Exception $e) {
                        // something happened.
                        echo $e->getMessage(), '<br/>';
                        $success = $success && false;
                    }
                }
                //echo $id, ':', $value, '<br/>';
            } else {
                continue;
            }
        }

        return $success;

    }
}