<?php 

class DEC_User
{

    static $_instance = null;
    static $_dbUsers;
    static $_dbUsersInfo;
    static $_dbInfoKeys;
    
    static $infoKeys;
    static $info;
    
    function __construct($userId = null, $cache = null)
    {
        Zend_Loader::loadClass('Users');
        Zend_Loader::loadClass('UsersInfo');
        Zend_Loader::loadClass('InfoKeys');

        self::$_dbUsers     = new Users();
        self::$_dbUsersInfo = new UsersInfo();
        self::$_dbInfoKeys  = new InfoKeys();
        
        self::$infoKeys = self::$_dbInfoKeys->getKeys();
        self::$infoIds  = array_keys(self::$infoKeys);
        
        print_r(self::$infoKeys);
        print_r(self::$infoIds);
        
        if ($userId)  {
            // populate since we got a user
        }
    }

    function getInstance($userId = null)
    {
        if (self::$_instance === null) {
            self::$_instance = new self($userId);
        } 
        return self::$_instance;
    }
    
    static function updateUserInfo($userId, $infoArray = array()) {
        //
        self::getInstance($userId);
        
    }
}