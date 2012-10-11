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
            $arows = $this->_answersModel->fetchByQuestionId($q->id);
            $answers = array();
            foreach ($arows as $a) {
                $answers[$a->id] = $a->name;
            }
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

        foreach ($survey as $question) {
            switch ($question['type']) {
                case 'radio':
                    // TODO: alow override of the divider.
                    $el = new Zend_Form_Element_Radio('question_' . $question['q_id']);
                    $el->setMultiOptions($question['answers']);
                    $el->setSeparator('');
                    break;
                case 'select':
                    $el = new Zend_Form_Element_Select('question_' . $question['q_id']);
                    $el->setMultiOptions(array_merge(array('' => '--'), $question['answers']));
                    break;
                case 'checkbox':
                    $el = new Zend_Form_Element_MultiCheckbox('question_' . $question['q_id']);
                    $el->setMultiOptions($question['answers']);
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
            $form->addElement($el);
        }

        // submit button.
        // TODO: allow some sort of override for this.
        $el = new Zend_Form_Element_Submit('submit');
        $el->setLabel('Save');
        $form->addElement($el);

        // populate, if we can.

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
        } else {
            // boo.
            $valid = false;
        }

        $responseId = $form->getValue('r_id');
Zend_Debug::dump($valid, 'valid?');
        $result = $this->saveSurvey($surveyId, $responseId, $form->getValues()); // run all values through filters.
Zend_Debug::dump($result, 'result?');
        // save partial answers to db, but don't flag as complete.
        if ($valid && $result) {
            $result = $this->completeSurvey($surveyId, $responseId);
Zend_Debug::dump($result, 'complete?');
        }
        return $valid && $result; // magic!
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

}