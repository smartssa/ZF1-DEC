<?php
/**
 * Caching plugin
 *
 * @uses Zend_Controller_Plugin_Abstract
 */
class DEC_Plugin_ViewCaching extends Zend_Controller_Plugin_Abstract
{
    /**
     *  @var bool Whether or not to disable caching
     */
    public static $doNotCache = false;

    /**
     * @var Zend_Cache_Frontend
     */
    public $cache;

    /**
     * @var string Cache key
     */
    public $key;

    /**
     * Constructor: initialize cache
     *
     * @param  array|Zend_Config $options
     * @return void
     * @throws Exception
     */
    public function __construct($options)
    {
        $this->cache      = $options['cache'];
        $this->keyOptions = $options['keyOptions'];
    }

    /**
     * Start caching
     *
     * Determine if we have a cache hit. If so, return the response; else,
     * start caching.
     *
     * @param  Zend_Controller_Request_Abstract $request
     * @return void
     */
    public function dispatchLoopStartup(Zend_Controller_Request_Abstract $request)
    {
        if (!$request->isGet()) {
            self::$doNotCache = true;
            return;
        }

        $path = $request->getPathInfo();

        $this->key = md5($path);
        if (false !== ($response = $this->getCache())) {
            $response->sendResponse();
            exit;
        }
    }

    /**
     * Store cache
     *
     * @return void
     */
    public function dispatchLoopShutdown()
    {
        if (self::$doNotCache
        || $this->getResponse()->isRedirect()
        || (null === $this->key)
        ) {
            return;
        }

        $this->cache->save($this->getResponse(), $this->key);
    }
}
