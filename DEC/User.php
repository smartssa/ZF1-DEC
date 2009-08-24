<?php

class DEC_User
{

    static $_instance = null;

    private $_dbUsers;
    private $_dbUsersInfo;
    private $_dbInfoKeys;
    private $_userId;

    private $infoKeys;
    private $infoIds;

    public  $info;

    function __construct($userId = null, $cache = null)
    {
        Zend_Loader::loadClass('Users');
        Zend_Loader::loadClass('UsersInfo');
        Zend_Loader::loadClass('InfoKeys');

        $this->_userId      = $userId;
        $this->_dbUsers     = new Users();
        $this->_dbUsersInfo = new UsersInfo();
        $this->_dbInfoKeys  = new InfoKeys();

        $this->infoKeys = $this->_dbInfoKeys->getKeys();
        foreach ($this->infoKeys as $key => $value) {
            $this->infoIds[$value] = $key;
        }

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
        foreach ($infoArray as $key => $value) {
            // go for it
            if (! $this->infoIds[$key]) {
                // this key doesn't exist
                $insert['name'] = $key;
                $newId = $this->_dbInfoKeys->insert($insert);
                $this->infoIds[$key] = $newId;
                $this->infoKeys[$newId] = $key;
            }

            if ($this->info->$key) {
                // update mode
                $where[] = $this->_dbUsersInfo->getAdapter()->quoteInto('info_keys_id = ?', $this->infoIds[$key]);
                $where[] = $this->_dbUsersInfo->getAdapter()->quoteInto('users_id = ?', $this->_userId);
                $data['value'] = $value;
                $data['modified_when'] = new Zend_Db_Expr('NOW()');
                try {
                    $this->_dbUsersInfo->update($data, $where);
                } catch (Exception $e) {
                    print_r($e->getMessage());
                }
            } else {
                // insert mode
                $data['info_keys_id'] = $this->infoIds[$key];
                $data['users_id'] = $this->_userId;
                $data['value'] = $value;
                $data['modified_when'] = new Zend_Db_Expr('NOW()');
                $data['created_when'] = new Zend_Db_Expr('NOW()');
                try {
                    $this->_dbUsersInfo->insert($data);
                } catch (Exception $e) {
                    print_r($e->getMessage());
                }
            }
            $this->info->$key = $value;
        }
    }

    function getInfo()
    {
        return $this->info;
    }
}