<?php
/**
 * DEC_Vimeo
 */

require_once 'DEC/Rest.php';
require_once 'DEC/Vimeo/Channel.php';
require_once 'DEC/Vimeo/ChannelList.php';
require_once 'DEC/Vimeo/User.php';
require_once 'DEC/Vimeo/UserList.php';
require_once 'DEC/Vimeo/Video.php';
require_once 'DEC/Vimeo/VideoList.php';
require_once 'Zend/Loader.php';

class DEC_Vimeo extends DEC_Rest
{
    protected $vimeoUrl    = 'http://www.vimeo.com/api/rest/';
    protected $vimeoSecret = 'aacf71d90';
    protected $vimeoKey    = '151a1fa349ba04ea6957d2a366bb2d05';

    public function __construct()
    {
        $this->setBaseUrl($this->vimeoUrl);
        $this->setApiKey($this->vimeoKey);
        $this->setApiSecret($this->vimeoSecret);
        $this->setMode('vimeo');
        //        $this->defaultOptions = array('format' => 'json');
    }
    
    public function callComplete($result) {
        // nothing here
        return $result;
    }
    
    public function setupApi($args) {
        // nothing here
        return $args;
    }

    public function generateToken($secret, $args)
    {
        ksort($args);
        $string = $secret;
        foreach ($args as $key=>$value):
        $string .= $key . $value;
        endforeach;

        return md5($string);
    }

    /**
     *     Section: Test Methods
     */

    public function testEcho($args)
    {
        return $this->call('vimeo.test.echo', $args);
    }

    public function testLogin($args)
    {
        return $this->call('vimeo.test.login', $args);
    }

    public function testNull($args)
    {
        return $this->call('vimeo.test.null', $args);
    }

    /**
     * Section: Authentication
     */

    //vimeo.auth.getToken
    //vimeo.auth.getFrob
    //vimeo.auth.checkToken

    /**
     * Section: Lists of videos
     */

    public function videosGetList($args)
    {
        return $this->call('vimeo.videos.getList', $args);
    }

    //vimeo.videos.getUploadedList
    //vimeo.videos.getAppearsInList
    //vimeo.videos.getSubscriptionsList
    //vimeo.videos.getListByTag
    //vimeo.videos.getLikeList
    //vimeo.videos.getContactsList
    //vimeo.videos.getContactsLikeList
    //vimeo.videos.search

    /**
     * Section: Dealing with specific videos
     */

    public function videosGetInfo($args)
    {
        return $this->call('vimeo.videos.getInfo', $args);
    }

    //vimeo.videos.delete
    //vimeo.videos.getThumbnailUrl
    //vimeo.videos.setTitle
    //vimeo.videos.setCaption
    //vimeo.videos.setFavorite
    //vimeo.videos.addTags
    //vimeo.videos.removeTag
    //vimeo.videos.clearTags
    //vimeo.videos.addCast
    //vimeo.videos.getCast
    //vimeo.videos.removeCast
    //vimeo.videos.setPrivacy

    /**
     * Section: Video Comments
     */

    //vimeo.videos.comments.getList
    //vimeo.videos.comments.addComment
    //vimeo.videos.comments.deleteComment
    //vimeo.videos.comments.editComment

    
    /** 
     * Section: People (Users)
     */

    //vimeo.people.findByUserName
    //vimeo.people.findByEmail
    //vimeo.people.getInfo
    //vimeo.people.getPortraitUrl
    //vimeo.people.addContact
    //vimeo.people.removeContact
    //vimeo.people.getUploadStatus
    //vimeo.people.addSubscription
    //vimeo.people.removeSubscription

    /** 
     * Section: Contacts
     */

    //vimeo.contacts.getList

    /** 
     * Section: Channels
     */

    //vimeo.channels.getList
    public function channelsGetVideos($args)
    {
        return new DEC_Vimeo_Channel($this->call('vimeo.channels.getVideos', $args));
    }
    //vimeo.channels.getSubscribers
    //vimeo.channels.getModerators

    /**
     * Section: Activity
     */

    //vimeo.activity.getThingsUserDid
    //vimeo.activity.getThingsThatHappenedToUser
    //vimeo.activity.getThingsThatHappenedToContacts
    //vimeo.activity.getThingsEverybodyDid


    /** 
     * Section: The Upload API
     */

    //vimeo.videos.getUploadTicket
    //vimeo.videos.checkUploadStatus

}