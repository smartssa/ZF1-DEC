<?php 

class DEC_Models_UsersAnswers extends DEC_Db_Table
{

    protected $_name = 'users_answers';

    public function saveAnswers($userId, $surveyId, $responseId, $values) {

        $success = true;
        foreach ($values as $key => $value) {
            // if it's not prefixed in question_, throw it out.
            preg_match('/question_([0-9]+)/i', $key, $matches);
            if (count($matches) == 2) {
                // split out the id, question_id
                $id = $matches[1];
                // decide what type of question this is and where the value needs to be stored
                // either 'allowed_answers_id' if it's an integer (radio/select type)
                // or 'value', if it's a text/textarea type.
                // insert the reuslt into the table
                // if it's a checkbox, we're going to have an array.
                if (is_array($value)) {
                    $value = implode(',', $value);
                }

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
                echo $id, ':', $value, '<br/>';
            } else {
                continue;
            }
        }

        return $success;

    }
}