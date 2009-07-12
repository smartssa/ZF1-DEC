<?php
/**
 * @author      Darryl E. Clarke <darryl.clarke@flatlinesystems.net>
 * @copyright   2009 Darryl E. Clarke
 * @version     $Id: IndexController.php 59 2009-03-04 07:16:20Z dclarke $
 */

class DEC_Tags_Info
{
    private $name;
    private $id;
    private $uniqueId;
    private $source;
    private $raw;

    public function __construct($tag)
    {

    }

    public function getName()
    {
        return $this->name;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getUniqueId()
    {
        return $this->uniqueId;
    }

    public function getSource()
    {
        return $this->source;
    }

    public function getRaw()
    {
        return $this->raw;
    }

}