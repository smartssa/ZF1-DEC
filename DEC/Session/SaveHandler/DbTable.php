<?php 

/**
 * A brief save handler extension to enable reads from slaves
 * and writes to masters.
 * @author dclarke
 *
 */
class DEC_Session_SaveHandler_DbTable extends Zend_Session_SaveHandler_DbTable
{

    protected $_readDb  = null;
    protected $_writeDb = null;
    
    public function __construct($config)
    {
        parent::__construct($config);
        // setup write/read params
        if (Zend_Registry::isRegistered('config')) {
            $config = Zend_Registry::get('config');
            if (isset($config->resources->multidb)) {
                $this->_readDb = Zend_Db::factory($config->resources->multidb->readonly->adapter, 
                    $config->resources->multidb->readonly->toArray());
                    
                $this->_writeDb = Zend_Db::factory($config->resources->multidb->master->adapter, 
                    $config->resources->multidb->master->toArray());

            } else {
                $this->_readDb = Zend_Db::factory($config->db->adapter, $config->db->toArray());
                $this->_writeDb = $this->_readDb;
            }
        }       
    }
    
    public function read($id)
    {
        $this->_setAdapter($this->_readDb);
        $return = '';

        $rows = call_user_func_array(array(&$this, 'find'), $this->_getPrimary($id));

        if (count($rows)) {
            if ($this->_getExpirationTime($row = $rows->current()) > time()) {
                $return = $row->{$this->_dataColumn};
            } else {
                $this->destroy($id);
            }
        }

        return $return;
    }
    
    public function write($id, $data)
    {
        $this->_setAdapter($this->_writeDb);
        return parent::write($id, $data);
    }
    
    public function destroy($id)
    {
        $this->_setAdapter($this->_writeDb);
        return parent::destroy($id);
    }

    public function gc($maxlifetime) 
    {
        $this->_setAdapter($this->_writeDb);
        return parent::gc($maxlifetime);
    }
    
}