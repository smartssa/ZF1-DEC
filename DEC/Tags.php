<?php
/**
 * 
 * @author dclarke
 * @version $Id:$
 */
class DEC_Tags
{
    const ALL = 0;
    const TOP = 10;

    public function __construct()
    {
        // tag stuff is here.
    }

    static function getTagsForItem($itemId, $itemTable, $howMany = self::ALL)
    {
        Zend_Loader::loadClass('DEC_Models_Tags');
        Zend_Loader::loadClass('DEC_Models_TagsReference');
        $tr   = new DEC_Models_TagsReference();
        $tags = new DEC_Models_Tags();
        $list = $tr->getTags($itemTable, $itemId, $howMany);
        if (count($list) > 0 ) {
            return $list;
        } else {
            return '';
        }
    }

    static function getUsersTags($userId) {
        Zend_Loader::loadClass('DEC_Models_Tags');
        Zend_Loader::loadClass('DEC_Models_TagsReference');
        // return the tags that a user has used
        $tr = new DEC_Models_TagsReference();
        $list = $tr->getUserTags($userId);
        if (count($list) > 0 ) {
            return $list;
        } else {
            return '';
        }

    }

    static function getPopularTags() {
        Zend_Loader::loadClass('DEC_Models_Tags');
        Zend_Loader::loadClass('DEC_Models_TagsReference');
        // return the top 20 tags
        $tr = new DEC_Models_TagsReference();
        return $tr->getPopularTags();
    }

    static function updateTagsForItem($tags, $itemId, $itemTable)
    {

    }

    static function clearTagsForItem($itemId, $itemTable)
    {

    }
}