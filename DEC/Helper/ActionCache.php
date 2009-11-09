<?php

class Helper_ActionCache extends Zend_Controller_Action_Helper_Abstract {

    public function preDispatch() {
        $this->fire('>');
    }
    public function postDispatch() {
        $this->fire('<');
    }

    protected function fire($when) {
        $req = $this->getRequest();
        $actionName = strtoupper($req->getActionName());
        $this->getResponse()
        ->appendBody("$when $actionName\n");
    }
}
