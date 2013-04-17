<?php
require_once 'Zend/Validate/Abstract.php';

class DEC_Validate_PostalCode extends Zend_Validate_Abstract {

    const NOT_MATCH = 'invalidPostalCode';

    protected $_messageTemplates = array(
        self::NOT_MATCH => 'Your postal code appears invalid.'
    );

    /**
     * Constructor of this validator
     *
     * The argument to this constructor is the third argument to the elements' addValidator
     * method.
     *
     * @param array|string $fieldsToMatch
     */
    public function __construct() {
    }

    /**
     * Check if the element using this validator is valid
     *
     * This method will compare the $value of the element to the other elements
     * it needs to match. If they all match, the method returns true.
     *
     * @param $value string
     * @param $context array All other elements from the form
     * @return boolean Returns true if the element is valid
     */
    public function isValid($value, $context = null) {
        $value = (string) $value;
        $this->_setValue($value);

        $error = false;
        $regexs = array(
                "US"=>"^\d{5}([\-]?\d{4})?$",
                "UK"=>"^(GIR|[A-Z]\d[A-Z\d]??|[A-Z]{2}\d[A-Z\d]??)[ ]??(\d[A-Z]{2})$",
                "DE"=>"\b((?:0[1-46-9]\d{3})|(?:[1-357-9]\d{4})|(?:[4][0-24-9]\d{3})|(?:[6][013-9]\d{3}))\b",
                "CA"=>"^([ABCEGHJKLMNPRSTVXY]\d[ABCEGHJKLMNPRSTVWXYZ])\ {0,1}(\d[ABCEGHJKLMNPRSTVWXYZ]\d)$",
                "FR"=>"^(F-)?((2[A|B])|[0-9]{2})[0-9]{3}$",
                "IT"=>"^(V-|I-)?[0-9]{5}$",
                "AU"=>"^(0[289][0-9]{2})|([1345689][0-9]{3})|(2[0-8][0-9]{2})|(290[0-9])|(291[0-4])|(7[0-4][0-9]{2})|(7[8-9][0-9]{2})$",
                "NL"=>"^[1-9][0-9]{3}\s?([a-zA-Z]{2})?$",
                "ES"=>"^([1-9]{2}|[0-9][1-9]|[1-9][0-9])[0-9]{3}$",
                "DK"=>"^([D-d][K-k])?( |-)?[1-9]{1}[0-9]{3}$",
                "SE"=>"^(s-|S-){0,1}[0-9]{3}\s?[0-9]{2}$",
                "BE"=>"^[1-9]{1}[0-9]{3}$"
        );
        
        if (!preg_match("/".$regexs['CA']."/i", $value)){
            //Validation failed, provided zip/postal code is not valid.
            $error = true;
            $this->_error(self::NOT_MATCH);
        } else {
            //Validation passed, provided zip/postal code is valid.
        }

        return !$error;
    }
}
