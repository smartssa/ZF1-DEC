<?php
/**
 * @author      Darryl E. Clarke <darryl.clarke@flatlinesystems.net>
 * @copyright   2009 Darryl E. Clarke
 * @version     $Id: IndexController.php 59 2009-03-04 07:16:20Z dclarke $
 */

require_once 'DEC/List.php';

class DEC_Tags_List extends DEC_List
{
    private $tags = array();

    public function __construct($tags)
    {
        // generic magic
        $this->list = &$this->tags;
    }

    public function getTags()
    {
        return $this->tags;
    }
}