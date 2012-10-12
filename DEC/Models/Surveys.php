<?php 

class DEC_Models_Surveys extends DEC_Db_Table
{

    protected $_name = 'surveys';

    public function createSurvey($name, $title) {
        // create and return an id.
    }
    
    public function enableSurvey($surveyId) {
        // enable it.
        // BUT FIRST.
        // amke sure it has at least one visible question with at least one visible answer!
    }
    
    public function disableSurvey($surveyId) {
        // disable it.
    }

}