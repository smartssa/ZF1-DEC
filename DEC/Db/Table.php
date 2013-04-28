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
        } elseif (Zend_Registry::isRegistered('Zend_Cache') && Zend_Registry::get('Zend_Cache') instanceof Zend_Cache_Core){
            $this->_cache = Zend_Registry::get('Zend_Cache');
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

            if ($config->debug == '1') {
                $profiler = new Zend_Db_Profiler_Firebug();
                $profiler->setEnabled(true);
                $this->getAdapter()->setProfiler($profiler);
                //$this->_readDb->setProfiler($profiler);
                //$this->_writeDb->setProfiler($profiler);
            }
        }


    }

    public function delete($where)
    {
        // $this->_setAdapter($this->_writeDb);
        return parent::delete($where);
    }

    public function update(Array $data, $where = null)
    {
        // $this->_setAdapter($this->_writeDb);
        return parent::update($data, $where);
    }

    public function insertClean($data) {
        $data = $this->_cleanData($data);
        return $this->insert($data);
    }
    
    public function insert(Array $data)
    {
        // $this->_log->debug('Insert to master');
        // $this->_setAdapter($this->_writeDb);
        return parent::insert($data);
    }

    public function fetchAll($where = null, $order = null, $count = null, $offset = null)
    {
        // $this->_setAdapter($this->_readDb);
        return parent::fetchAll($where, $order, $count, $offset);
    }

    public function fetchRow($where = null, $order = null, $forceMaster = false)
    {
        if ($forceMaster) {
            // $this->_setAdapter($this->_writeDb);
        } else {
            // $this->_setAdapter($this->_readDb);
        }
        return parent::fetchRow($where, $order);
    }

    //** removed due to strange behaviour. will always read from last default adapter.
//    public function find()
//    {
//        $args = func_get_args();
//        // $this->_setAdapter($this->_readDb);
//        return parent::find($args);
//    }

    protected function _getCache($tag) {
        if ($this->_cache !== null) {
            if ($this->_cache->test(md5($tag))) {
                if ($data = $this->_cache->load(md5($tag))) {
                    $this->_log->debug('CACHE: returned ' . $tag);
                    return $data;
                }
            }
        }
        $this->_log->debug('CACHE: not found ' . $tag);
        return false;
    }

    protected function _setCache($data, $tag, $time = false) {
        if ($this->_cache !== null) {
            if ($this->_cache->save($data, md5($tag), array(), $time)) {
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

    protected function _cleanData($data) {
        // throw away array elements taht don't exist in this table.
        $fields = $this->info(Zend_Db_Table_Abstract::COLS);
        foreach ($data as $key=>$value) {
            if (! in_array($key, $fields)) {
                unset($data[$key]);
            }
        }
        if (in_array('created', $fields)) {
            $data['created']  = new Zend_Db_Expr('NOW()');
        }
        if (in_array('modified', $fields)) {
            $data['modified'] = new Zend_Db_Expr('NOW()');
        }
        return $data;
    }
}
