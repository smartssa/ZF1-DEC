<?php
/*
 * @revision	$Id$
 * @author		Darryl Clarke
 *
 */

class DEC_Models_TagsReference extends Zend_Db_Table
{
    protected $_name = 'tags_reference';

    function getTags($table, $itemId, $howMany = 0) {

        // TODO: Fix this so it's proper
        // SELECT t.id, t.tag, count(t.tag) as count FROM `tags_reference` tr
        // LEFT JOIN tags t ON (tr.tags_id = t.id)
        // WHERE tr.table_reference = 'articles' and tr.table_reference_id = '739'
        // GROUP BY t.tag
        // ORDER BY count, tag ASC
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(array('tr' => 'tags_reference'), array());
        $select->join(array('t' => 'tags'), 'tr.tags_id = t.id', array('id', 'tag',
        new Zend_Db_Expr('count(tag) as count')));
        $select->where($this->getAdapter()->quoteInto('tr.table_reference = ?', $table));
        $select->where($this->getAdapter()->quoteInto('tr.table_reference_id = ?', $itemId));
        if ($howMany > 0) {
            $select->limit($howMany);
        }
        $select->group('t.tag');
        $select->order(new Zend_Db_Expr('count DESC, t.tag ASC'));
        $stmt = $this->getAdapter()->query($select);
        $result = $stmt->fetchAll();
        // returns an array
        return $result;
    }

    function getPopularTags($tableType = 'ALL') {
        // snag the top 20 tags based on the type
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(array('tr' => 'tags_reference'), array());
        $select->join(array('t' => 'tags'), 'tr.tags_id = t.id', array('id', 'tag',
        new Zend_Db_Expr('count(tag) as count')));
        $select->group('t.tag');
        $select->order(new Zend_Db_Expr('count DESC, t.tag ASC'));
        $select->limit(20);
        $stmt = $this->getAdapter()->query($select);
        $result = $stmt->fetchAll();
        // returns an array
        return $result;
    }

    function getAllTags($userId = 0) {
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(array('tr' => 'tags_reference'), array());
        $select->join(array('t' => 'tags'), 'tr.tags_id = t.id', array('id', 'tag',
        new Zend_Db_Expr('count(tag) as count')));
        if ($userId > 0) {
            $select->where($this->getAdapter()->quoteInto('tr.users_id = ?', $userId));
        }
        $select->group('t.tag');
        $stmt = $this->getAdapter()->query($select);
        $result = $stmt->fetchAll();
        // returns an array
        return $result;
    }

    function getUserTags($userId) {

        // TODO: Fix this so it's proper
        // SELECT t.id, t.tag, count(t.tag) as count FROM `tags_reference` tr
        // LEFT JOIN tags t ON (tr.tags_id = t.id)
        // WHERE tr.table_reference = 'articles' and tr.table_reference_id = '739'
        // GROUP BY t.tag
        // ORDER BY count, tag ASC
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(array('tr' => 'tags_reference'), array());
        $select->join(array('t' => 'tags'), 'tr.tags_id = t.id', array('id', 'tag',
        new Zend_Db_Expr('count(tag) as count')));
        $select->where($this->getAdapter()->quoteInto('tr.users_id = ?', $userId));
        $select->group('t.tag');
        $select->order(new Zend_Db_Expr('count DESC, t.tag ASC'));
        $stmt = $this->getAdapter()->query($select);
        $result = $stmt->fetchAll();
        // returns an array
        return $result;
    }

}
