<?php
/**
 * A simple list Iterator.
 * @author  Darryl E. Clarke
 * @version $Id:$
 */
class DEC_List implements Iterator
{
    /**
     * The list.
     * @var Array
     */
    protected $list = array();

    public function rewind() {
        reset($this->list);
    }

    public function current() {
        $listItem = current($this->list);
        return $listItem;
    }

    public function key() {
        $key = key($this->list);
        return $key;
    }

    public function next() {
        $listItem = next($this->list);
        return $listItem;
    }

    public function valid() {
        $valid = $this->current() !== false;
        return $valid;
    }
    
    public function count() {
        $count = count($this->list);
        return $count;
    }
}