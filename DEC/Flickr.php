<?php
/**
 * DEC_Flickr
 */

require_once 'Rest.php';

class DEC_Flickr extends DEC_Rest
{
    protected $flickrUrl = 'http://api.flickr.com/services/rest/';
    protected $flickrApiKey = '222331b8dc95c1f353ec4d482042b208';
    protected $flickrSecret = '0e98066710a2b25e';

    public function __construct()
    {
        $this->setBaseUrl($this->flickrUrl);
        $this->setApiKey($this->flickrApiKey);
        $this->setApiSecret($this->flickrSecret);
        $this->setMode('vimeo');
    //        $this->defaultOptions = array('format' => 'json');
    }

    public function testEcho($args)
    {
        return $this->call('flickr.test.echo', $args);
    }

    public function generateToken($secret, $args)
    {
        // no signature for flickr
        return '';
    }


}