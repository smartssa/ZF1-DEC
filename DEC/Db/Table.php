<?php

class DEC_Db_Table extends Zend_Db_Table_Abstract {

    /**
     * Enter description here ...
     * @var Zend_Cache_Core
     */
    private $_cache = null;
    private $_log   = null;


    /**
     * Enter description here ...
     * @var Zend_Queue
     */
    private $_queue = null;

    public function __construct($options = null) {
        parent::__construct($options);

        if (Zend_Registry::isRegistered('cache') && Zend_Registry::get('cache') instanceof Zend_Cache_Core) {
            $this->_cache = Zend_Registry::get('cache');
        }

        if (Zend_Registry::isRegistered('logger') && Zend_Registry::get('logger') instanceof Zend_Log) {
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

    protected function _getQueue()
    {
        $this->_queue = false;
        $name = str_replace('.', '_', $_SERVER['HTTP_HOST']);
        $options = array('name' => $name);
        try {
            $adapter = new DEC_Queue_Adapter_Memcacheq($options);
            $this->_queue = new Zend_Queue($adapter, $options);
            $this->_log->debug('Set Queue!');
        } catch (Zend_Queue_Exception $e) {
            // no queue
        }
        return $this->_queue;
    }

    public function queueUpdate($data, $where)
    {
        // queue an update using Zend_Queue_MemcacheQ
        $queue = $this->_getQueue();
        if ($queue) {
            // build a nice array of shit to shove in it

            // save it and return peacefully.
        }
        return false;
    }

    public function queueInsert($data, $class)
    {
        $queue = $this->_getQueue();
        if ($queue) {
            $queueMe = array('action' => 'insert',
                'data' => $data,
                'table' => $this->_name,
                'class' => $class);
            if ($queue->send($queueMe) == true) {
                $this->_log->debug('Sent Insert Request to Queue.');
                return true;
            }
        }
        return false;
    }
}