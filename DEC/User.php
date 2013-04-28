<?php
/**
 * A fancy awesome singleton User Object
 * @author  dclarke
 * @version $Id$
 *
 */
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
        $this->_userId      = $userId;
        $this->_dbUsers     = new DEC_Models_Users();
        $this->_dbUsersInfo = new DEC_Models_UsersInfo();
        $this->_dbInfoKeys  = new DEC_Models_InfoKeys();

        $this->infoKeys = $this->_dbInfoKeys->getKeys();
        foreach ($this->infoKeys as $key => $value) {
            $this->infoIds[$value] = $key;
        }

        $this->info     = new stdClass;
        if ($userId > 0)  {
            // general user info
            $where   = $this->_dbUsers->getAdapter()->quoteInto('id = ?', $userId);
            $userRow = $this->_dbUsers->fetchRow($where);

            $pk = $this->_dbUsers->getPrimaryKey();

            if (isset($userRow->$pk) && $userRow->$pk > 0) {
                $this->$pk       = $userRow->$pk;
                $this->firstname = $userRow->firstname;
                $this->lastname  = $userRow->lastname;
                $this->email     = $userRow->email;
                $this->username  = $userRow->username;
                $this->grav_url['small'] = "https://secure.gravatar.com/avatar/" . md5( strtolower( trim( $this->email ) ) ) . "?d=monsterid&s=64";
                $this->grav_url['large'] = "https://secure.gravatar.com/avatar/" . md5( strtolower( trim( $this->email ) ) ) . "?d=monsterid&s=120";

                $this->registerdate = $userRow->created;
                // populate since we got a user
                $where  = $this->_dbUsersInfo->getAdapter()->quoteInto('users_id = ?', $userId);
                $infoRS = $this->_dbUsersInfo->fetchAll($where);
                foreach ($infoRS as $row) {
                    $key = $this->infoKeys[$row->info_keys_id];
                    $this->info->$key = $row->value;
                }
            }
        }
    }

    static function getInstance($userId = null)
    {
        $cache = Zend_Registry::get('cache');
        $tag = 'user_instance_' . $userId . '_' . str_replace('.', '_', $_SERVER['HTTP_HOST']);
        if (self::$_instance === null) {
            if (! self::$_instance = $cache->load($tag)) {
                self::$_instance = new DEC_User($userId);
                $cache->save(self::$_instance, $tag);
            }
        }
        return self::$_instance;
    }

    function updateUserInfo($infoArray = array()) {
        //
        // destroy parent cache
        $cache = Zend_Registry::get('cache');
        $tag = 'user_instance_' . $this->_userId . '_' . str_replace('.', '_', $_SERVER['HTTP_HOST']);
        $cache->remove($tag);
        
        foreach ($infoArray as $key => $value) {
            $insert = array();
            $where  = array();
            $data   = array();

            // go for it
            if (! isset($this->infoIds[$key])) {
                // this key doesn't exist
                $insert['name'] = $key;
                $newId = $this->_dbInfoKeys->insert($insert);
                $this->infoIds[$key] = $newId;
                $this->infoKeys[$newId] = $key;
            }

            if (isset($this->info->$key)) {
                // update mode
                $where[] = $this->_dbUsersInfo->getAdapter()->quoteInto('info_keys_id = ?', $this->infoIds[$key]);
                $where[] = $this->_dbUsersInfo->getAdapter()->quoteInto('users_id = ?', $this->_userId);
                $data['value'] = $value;
                $data['modified_when'] = new Zend_Db_Expr('NOW()');
                try {
                    $this->_dbUsersInfo->update($data, $where);
                } catch (Exception $e) {
                    //print_r($e->getMessage());
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
                    //print_r($e->getMessage());
                }
            }
            $this->info->$key = $value;
        }
    }

    function getInfo()
    {
        return $this->info;
    }

    function getInfoKeys()
    {
        return $this->infoKeys;
    }
}