<?php 

class DEC_User
{

    static $_instance = null;
    
    private $_dbUsers;
    private $_dbUsersInfo;
    private $_dbInfoKeys;
    
    private $infoKeys;
    private $infoIds;

    public  $info;
    
    function __construct($userId = null, $cache = null)
    {
        Zend_Loader::loadClass('Users');
        Zend_Loader::loadClass('UsersInfo');
        Zend_Loader::loadClass('InfoKeys');

        $this->_dbUsers     = new Users();
        $this->_dbUsersInfo = new UsersInfo();
        $this->_dbInfoKeys  = new InfoKeys();
        
        $this->infoKeys = $this->_dbInfoKeys->getKeys();
        $this->infoIds  = array_keys($this->infoKeys);
        $this->info     = new stdClass;
        if ($userId > 0)  {
            // populate since we got a user
            $where  = $this->_dbUsersInfo->getAdapter()->quoteInto('users_id = ?', $userId);
            $infoRS = $this->_dbUsersInfo->fetchAll();
            foreach ($infoRS as $row) {
                $key = $this->infoKeys[$row->id];
                $this->info->$key = $row->value;
            }
        }
    }

    function getInstance($userId = null)
    {
        if (self::$_instance === null) {
            self::$_instance = new DEC_User($userId);
        } 
        return self::$_instance;
    }
    
    function updateUserInfo($infoArray = array()) {
        //
        
    }
    
    function getInfo()
    {
        return $this->info;
    }
}