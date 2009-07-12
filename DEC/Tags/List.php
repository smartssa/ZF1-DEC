<?php
/**
 * @author      Darryl E. Clarke <darryl.clarke@flatlinesystems.net>
 * @copyright   2009 Darryl E. Clarke
 * @version     $Id: IndexController.php 59 2009-03-04 07:16:20Z dclarke $
 */

class DEC_Tags_List
{
    private $tags = array();

    public function __construct($tags)
    {
        // generic magic
    }

    public function getTags()
    {
        return $this->tags;
    }
}