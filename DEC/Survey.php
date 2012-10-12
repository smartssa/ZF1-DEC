<?php 

class DEC_Survey extends DEC_List {


    protected $_surveys = array();
    protected $_userId;

    protected $_cache;

    protected $_surveyModel = null;
    protected $_questionsModel = null;
    protected $_answersModel = null;
    protected $_responsesModel = null;

    protected $_action; // where to send the form to.
    
    protected $_status = 0;
    
    const STATUS_SUCCESS_DB   = 1;
    const STATUS_SUCCESS_FORM = 2;
    const STATUS_FAIL_FORM    = 4;
    const STATUS_FAIL_ANSWER  = 8;
    const STATUS_FAIL_DB      = 16;

    public function __construct($userId = null, $action = null) {
        // connect the DEC_List iterator to the survey list
        $this->list = & $this->_surveys;

        $this->getSurveys();

        $this->_userId = $userId;
        $this->_action = $action;
    }

    public function getSurveys() {
        if ($this->_surveyModel === null) {
            $this->_surveyModel = new DEC_Models_Surveys();
        }

        $rows = $this->_surveyModel->fetchAll();
        foreach ($rows as $row) {
            // TODO: process as required.
            $new_survey = $row;
            array_push($this->_surveys, $new_survey);
        }
    }

    private function getSurvey($surveyId) {
        // as long as it's in the array, fetch it and the questions for it.
        if ($this->_questionsModel == null ) {
            $this->_questionsModel = new DEC_Models_SurveyQuestions();
        }

        if ($this->_answersModel == null) {
            $this->_answersModel = new DEC_Models_AllowedAnswers();
        }

        // merge with allowed answers
        $qrows = $this->_questionsModel->fetchBySurveyId($surveyId);

        $rows = array();
        foreach ($qrows as $q) {
            $answers = $this->_answersModel->fetchByQuestionId($q->id);
            $awesome = array('q_id' => $q->id, 'question' => $q->question,
                    'required' => $q->required,
                    'type' => $q->type, 'answers' => $answers);
            $rows[] = $awesome;
        }

        return $rows;
    }

    public function getSurveyForm($surveyId) {
        // get a zend_form for the survey.
        // fetch the questions, allowed answers, and users last response if applicable.
        $survey = $this->getSurvey($surveyId);

        // shove this ID in the form.
        $responseId = $this->startSurvey($surveyId);

        $form = new Zend_Form();

        $form->setAction($this->_action);

        // hiddens.
        $el = new Zend_Form_Element_hidden('r_id');
        $el->setDecorators(array('ViewHelper'));
        $el->setValue($responseId);
        $form->addElement($el);

        $el = new Zend_Form_Element_Hidden('s_id');
        $el->setDecorators(array('ViewHelper'));
        $el->setValue($surveyId);
        $form->addElement($el);

        $ua = new DEC_Models_UsersAnswers();

        foreach ($survey as $question) {
            // re-align answers for form-friendly arrays.
            $answers = array();
            foreach ($question['answers'] as $k => $a) {
                $answers[$k] = $a['value'];
            }

            switch ($question['type']) {
                case 'radio':
                    // TODO: alow override of the divider.
                    $el = new Zend_Form_Element_Radio('question_' . $question['q_id']);
                    $el->setMultiOptions($answers);
                    $el->setSeparator('');
                    break;
                case 'select':
                    $el = new Zend_Form_Element_Select('question_' . $question['q_id']);
                    $el->setMultiOptions(array('' => '--') + $answers);
                    break;
                case 'checkbox':
                    $el = new Zend_Form_Element_MultiCheckbox('question_' . $question['q_id']);
                    $el->setMultiOptions($answers);
                    $el->setSeparator('');
                    break;
                case 'text':
                    $el = new Zend_Form_Element_Text('question_' . $question['q_id']);
                    $el->addFilter('StripTags')
                    ->addFilter('StringTrim');
                    break;
                case 'textarea':
                    $el = new Zend_Form_Element_Textarea('question_' . $question['q_id']);
                    $el->addFilter('StripTags')
                    ->addFilter('StringTrim');
                    break;
            }
            $el->setLabel($question['question']);
            if ($question['required']) {
                $el->setRequired(true);
            }
            
            // populate, if we can.
            // fetch values for responseId and userId and questionId
            $value = $ua->fetchByRUQ($responseId, $this->_userId, $question['q_id']);
            if ($value !== null) {
                $el->setValue($value);
            }
            $form->addElement($el);
        }

        // submit button.
        // TODO: allow some sort of override for this.
        $el = new Zend_Form_Element_Submit('submit');
        $el->setLabel('Save');
        $form->addElement($el);

        return $form;
    }

    public function saveSurveyForm($surveyId, $values) {
        // validate values against the form
        $form = $this->getSurveyForm($surveyId);
        $form->populate($values);
        // no saving if fail
        $valid = null;
        if ($form->isValid($values)) {
            // yay!
            $valid = true;
            $this->_status = self::STATUS_SUCCESS_FORM;
        } else {
            // boo.
            $valid = false;
            $this->_status = self::STATUS_FAIL_FORM;
        }

        $responseId = $form->getValue('r_id');
        // check answers, any wrong answers set STATUS_ANSWER_FAIL
        $questions = $this->getSurvey($surveyId);
        $answers   = $form->getValues();
        // realign questions w/ correct answers.
        $correct_answers = array();
        foreach ($questions as $q) {
            if($q['type'] == 'text' || $q['type'] == 'textarea') {
                continue;
            }
            $x = array();
            foreach ($q['answers'] as $k => $a) {
                if ($a['correct']) {
                    $x[] = $k;
                }
            }
            if (count($x) > 0) { 
                $correct_answers[$q['q_id']] = $x;
            }
        }
        foreach ($answers as $k => $a) {
            // extract question id from key            
            preg_match('/question_([0-9]+)/i', $k, $matches);
            if (count($matches) == 2) {
                $id = $matches[1];
                // lookup and see if the answer value is 'correct'
                if (isset($correct_answers[$id])) {
                    if (! in_array($a, $correct_answers[$id])) {
                        // set status and break, no point in checking the rest.
                        $this->_status += self::STATUS_FAIL_ANSWER;
                        break;
                    } else {
                        //
                    }
                }
            }
        }

        // carry on.
        $result = $this->saveSurvey($surveyId, $responseId, $answers); // run all values through filters.
        if ($result) {
            $this->_status += self::STATUS_SUCCESS_DB;
        } else {
            $this->_status += self::STATUS_FAIL_DB;
        }

        // save partial answers to db, but don't flag as complete.
        if ($valid && $result) {
            $result = $this->completeSurvey($surveyId, $responseId);
            if ($result) {
                $this->_status += self::STATUS_SUCCESS_DB;
            } else {
                $this->_status += self::STATUS_FAIL_DB;
            }
        }
        return $this->_status; // magic!
    }

    public function completeSurvey($surveyId, $responseId) {
        $sr = new DEC_Models_SurveyResponses();
        return $sr->completeSurvey($surveyId, $responseId, $this->_userId);
    }

    public function startSurvey($surveyId) {
        // insert or update a record into survey responses and return the id.
        $sr = new DEC_Models_SurveyResponses();
        return $sr->startSurvey($surveyId, $this->_userId);
    }

    public function getStats($surveyId, $userId = null) {
        // return some stats:
        if ($this->_responsesModel === null) {
            $this->_responsesModel = new DEC_Models_SurveyResponses();
        }
        if ($this->_questionsModel === null) {
            $this->_questionsModel = new DEC_Models_SurveyQuestions();
        }
        // started, completed, unique starts, unique completes
        // user list of all completed+started
        $stats = $this->_responsesModel->getStats($surveyId);

        // question + answers
        $answers = $this->_questionsModel->getUserAnswers($surveyId, $userId);

        // drill down all the way.
        
        return array('stats' => $stats, 'dataset' => $answers);
    }
    
    public function getUserResponses($userId, $surveyId) {
        
    }

    private function saveSurvey($surveyId, $responseId, $values) {
        // save survey response values.
        $ua = new DEC_Models_UsersAnswers();
        return $ua->saveAnswers($this->_userId, $surveyId, $responseId, $values);
    }

    public function createSurvey() {

    }

    private function fetchSurveys() {
        // get surveys from database.
    }

    public function setUser($userId) {
        $this->_userId = $userId;
    }
    
    public function getStatus()
    {
        return $this->_status;
    }

}