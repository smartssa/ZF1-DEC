<?php
/**
 * Partial View Caching 
 * @author  dclarke
 * @version $Id:$
 */
class DEC_View_Helper_Partial extends Zend_View_Helper_Partial
{
    /**
     * @var Zend_Cache_Core
     */
    protected $_cacheObj = null;

    function partial($name = null, $module = null, $model = null, $cache = true) {
/*
        if (($log = Zend_Registry::get('logger')) != true) {
            $log = false;
        }
*/
        if ($cache) {
            if (($this->_cacheObj = Zend_Registry::get('cache')) != true) {
                $cache = false;
            }
        }
        // rotate for happy params.
        if ((null === $model) && (null !== $module)) {
            $model  = $module;
            $module = null;
        }

        $key = false;
        if (($model || $module) && $cache) {
            // generate cache key based on module and model?!
            $key = str_replace('.', '_', $name).md5(serialize($module) . serialize($model));
            // look for cached copy
            if ($cache && ($partial = $this->_cacheObj->load($key)) == true) {
                // log
                return $partial;
            }
        }
        // get fresh rendered
        $partial = parent::partial($name, $module, $model);

        // save to cache
        if ($partial && $key && $cache) {
            $this->_cacheObj->save($partial, $key);
        }
        // return data
        return $partial;
    }
}