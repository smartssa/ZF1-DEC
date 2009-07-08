<?php
/**
 * DEC_Vimeo
 */
 
require_once 'Rest.php';
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

    public function testEcho($args)
    {
        return $this->call('vimeo.test.echo', $args);
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
}