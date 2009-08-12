<?php
/**
 * @author      Darryl E. Clarke <darryl.clarke@flatlinesystems.net>
 * @copyright   2009 Darryl E. Clarke
 * @version     $Id$
 */

class DEC_Vimeo_Channel
{
    private $name;
    private $videoList;
    private $userList;
    private $moderaterList;

    function __construct(Zend_Rest_Client_Result $rsp, $requestObject)
    {
        $videos = $rsp->videos;
        $this->videoList = new DEC_Vimeo_VideoList($videos, $requestObject);
        // TODO: build moderator list
        // TODO: build user (subscriber) list
    }

    public function getName()
    {
        return $this->name;
    }

    public function getUserList()
    {
        return $this->userList;
    }

    public function getModeratorList()
    {
        return $this->moderaterList;
    }

    public function getVideoList()
    {
        return $this->videoList;
    }
}