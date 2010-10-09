<?php 

class DEC_Db_Table extends Zend_Db_Table_Abstract {
    
    /**
     * Enter description here ...
     * @var Zend_Cache_Core
     */
    private $_cache = null;
    private $_log   = null;
    
    public function __construct($options = null) {
        parent::__construct($options);
        
        if (Zend_Registry::get('cache') instanceof Zend_Cache_Core) {
            $this->_cache = Zend_Registry::get('cache');
        }

        if (Zend_Registry::get('logger') instanceof Zend_Log) {
            $this->_log  = Zend_Registry::get('logger');
            $this->_log->debug('database logger enabled for ' . $this->_name);
        }
    }
    
    protected function _getCache($tag) {
        if ($this->_cache !== null) {
            if ($data = $this->_cache->load(md5($tag))) {
                $this->_log->debug('CACHE: returned ' . $tag);
                return $data;
            }            
        }
        $this->_log->debug('CACHE: not found ' . $tag);
        return false;
    }
    
    protected function _setCache($data, $tag) {
        if ($this->_cache !== null) {
            if ($this->_cache->save($data, md5($tag))) {
                $this->_log->debug('CACHE: saved ' . $tag);
                return true;
            }
        }
        $this->_log->debug('CACHE: save failed ' . $tag);
        return false;
    }
    
    protected function _removeCache($tag) {
        if ($this->_cache !== null) {
            $this->_cache->remove(md5($tag));
            $this->_log->debug('CACHE: removed ' . $tag);
            return true;
        }
        return false;
    }
}