<?php

/**
 * @author dclarke
 *
 */
class DEC_Db_Table extends Zend_Db_Table_Abstract {

    /**
     * Enter description here ...
     * @var Zend_Cache_Core
     */
    private $_cache   = null;
    private $_log     = null;
    private $_readDb  = null;
    private $_writeDb = null;

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
            // $this->_log->debug('database logger enabled for ' . $this->_name);
        }

        // setup write/read params
        if (Zend_Registry::isRegistered('config')) {
            $config = Zend_Registry::get('config');
            if (isset($config->resources->multidb)) {
                $this->_readDb = Zend_Db::factory($config->resources->multidb->readonly->adapter,
                $config->resources->multidb->readonly->toArray());
                // $this->_log->debug('Enabled read only adapter');
                $this->_writeDb = Zend_Db::factory($config->resources->multidb->master->adapter,
                $config->resources->multidb->master->toArray());
                // $this->_log->debug('Enabled master adapter');
            } elseif (isset($config->db)) {
                $this->_readDb = Zend_Db::factory($config->db->adapter, $config->db->toArray());
                $this->_writeDb = $this->_readDb;
                // $this->_log->debug('Enabled single adapter operations');
            } else {
            	$this->_readDb = Zend_Db_Table::getDefaultAdapter();
            	$this->_writeDb = $this->_readDb;
            }
        }

    }

    public function delete($where)
    {
        $this->_setAdapter($this->_writeDb);
        return parent::delete($where);
    }

    public function update($data, $where = null)
    {
        $this->_setAdapter($this->_writeDb);
        return parent::update($data, $where);
    }

    public function insert($data)
    {
        // $this->_log->debug('Insert to master');
        $this->_setAdapter($this->_writeDb);
        return parent::insert($data);
    }

    public function fetchAll($where = null, $order = null, $count = null, $offset = null)
    {
        $this->_setAdapter($this->_readDb);
        return parent::fetchAll($where, $order, $count, $offset);
    }

    public function fetchRow($where = null, $order = null)
    {
        $this->_setAdapter($this->_readDb);
        return parent::fetchRow($where, $order);
    }

    public function find()
    {
        $this->_setAdapter($this->_readDb);
        return parent::find();
    }

    protected function _getCache($tag) {
        if ($this->_cache !== null) {
            if ($data = $this->_cache->load(md5($tag))) {
                // $this->_log->debug('CACHE: returned ' . $tag);
                return $data;
            }
        }
        // $this->_log->debug('CACHE: not found ' . $tag);
        return false;
    }

    protected function _setCache($data, $tag) {
        if ($this->_cache !== null) {
            if ($this->_cache->save($data, md5($tag))) {
                // $this->_log->debug('CACHE: saved ' . $tag);
                return true;
            }
        }
        // $this->_log->debug('CACHE: save failed ' . $tag);
        return false;
    }

    protected function _removeCache($tag) {
        if ($this->_cache !== null) {
            $this->_cache->remove(md5($tag));
            // $this->_log->debug('CACHE: removed ' . $tag);
            return true;
        }
        return false;
    }
    
    /**
     * @deprecated use public getQueue();
     */
    protected function _getQueue() {
        return $this->getQueue();
    }

    public function getQueue()
    {
        $this->_queue = false;
        $config = Zend_Registry::get('config');
        if (isset($config->queue->enabled) && $config->queue->enabled) {
            $options = array('name' => $config->queue->name);
            try {
                $adapter = new DEC_Queue_Adapter_Memcacheq($options);
                $this->_queue = new Zend_Queue($adapter, $options);
                // $this->_log->debug('Set Queue!');
            } catch (Zend_Queue_Exception $e) {
                // no queue
            }
        }
        return $this->_queue;
    }

    public function queueUpdate($data, $where)
    {
        // queue an update using Zend_Queue_MemcacheQ
        $queue = $this->getQueue();
        if ($queue) {
            // build a nice array of shit to shove in it

            // save it and return peacefully.
        }
        return false;
    }

    public function queueInsert($data, $class)
    {
        $queue = $this->getQueue();
        if ($queue) {
            $queueMe = array('action' => 'insert',
                'data' => $data,
                'table' => $this->_name,
                'class' => $class);
            if ($queue->send($queueMe) == true) {
                // $this->_log->debug('Sent Insert Request to Queue.');
                return true;
            }
        }
        return false;
    }
}
