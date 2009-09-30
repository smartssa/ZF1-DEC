<?php

require_once 'DEC/View/Helper/Helper.php';

class DEC_View_Helper_GetViewVariable extends DEC_View_Helper_Helper
{
    public function getViewVariable($name)
    {
        return $this->view->$name;
    }
}