<?php
/**
 * @author      Darryl E. Clarke <darryl.clarke@flatlinesystems.net>
 * @copyright   2009 Darryl E. Clarke
 * @version     $Id$
 */

require_once 'DEC/List.php';

class DEC_Flickr_CommentList extends DEC_List
{
    private $comments = array();
    
    public function __construct($comments, $requestObject)
    {
        $this->list = &$this->comments;
    }
    
}