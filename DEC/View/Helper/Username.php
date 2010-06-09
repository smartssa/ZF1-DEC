<?php

require_once 'DEC/View/Helper/Helper.php';

class DEC_View_Helper_Username extends DEC_View_Helper_Helper
{
    public function Username($id, $link = false)
    {
        // fetch a list of usernames and id's from the db
        // link if provided
        return "user".$id;
    }
}