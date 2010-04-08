<?php
class DEC_Word
{
    protected $word        = '';
    protected $phonetic    = '';
    protected $definition  = array();
    protected $related     = array();
    protected $example     = array();
    protected $adapters    = array();
    protected $lookupState = false;
    protected $sourceName  = '';
    protected $sourceUrl   = '';

    public function __construct($word, $autoLookup = true, $adapters = null) {
        if (!is_string($word) || trim($word) == '') {
            throw new DEC_Word_Exception('Word not provided.');
        }
        $this->word = $word;
        if(is_array($adapters) && count($adapters) > 0) {
            // do stuff.
            foreach ($adapters as $adapter) {
                if (! is_array($adapter)) {
                    throw new DEC_Word_Exception('Adapters are not Arrays. Need array("adapter" => ADAPTER, "priority" => INT)');
                }
                $this->addAdapter($adapter['adapter'], $adapter['priority']);
            }
        } elseif ($adapters !== null) {
            $this->addAdapter($adapters);
        }

        if ($autoLookup) {
            // do it. populate the stuff.
            $this->lookup();
        }
    }

    public function addAdapter($adapter, $priority = 0) {
        // add a given adapter to the list of lookups in order.
        if ($adapter instanceof DEC_Word_Lookup_Adapter_Abstract) {
            $this->adapters[] = $adapter;
        }
    }

    public function lookup($useStorage = false) {
        // check storage engine first.
        if ($userStorage) {

        } else {
            if (count($this->adapters) > 0 ) {
                // lookup the word and build a fancy object
                foreach ($this->adapters as $adapter) {
                    try {
                        $adapter->lookup(urlencode($this->word), $this);
                        if (count($this->getDefinition()) > 0) {
                            //  break out after one match.
                            return $this;
                        }
                    } catch (DEC_Word_Lookup_Exception $e) {
                        // caught a dec word exception
                        echo $e->getMessage();
                    }
                }
            } else {
                throw new DEC_Word_Exception('No Adapters Provided');
            }
        }
        if (count($this->getDefinition) > 0) {
            $this->lookupState = true;
        } else {
            throw new DEC_Word_Exception('Word definition not found via any provider.');
        }
    }

    public function setDefinition($definition) {
        $this->definition = $definition;
        return $this;
    }
    public function getDefinition() {
        // return the definition of whatever word was just looked up.
        // can be an array of DEC_Word_Definition's
        return $this->definition;
    }

    public function setRelated($related) {
        $this->related = $related;
        return $this;
    }
    public function getRelated() {
        // list of related words
        return $this->related;
    }

    public function setExample($example) {
        $this->example = $example;
        return $this;
    }
    public function getExample() {
        // example usage.
        return $this->example;
    }
    public function setPhonetic($phonetic) {
        $this->phonetic = $phonetic;
        return $this;
    }
    public function getPhonetic() {
        // example usage.
        return $this->phonetic;
    }
    
    public function setSource($name, $url) {
        $this->sourceName = $name;
        $this->sourceUrl  = $url;
        return $this;
    }
    
    public function getSource() {
        return '<a href="' . $this->sourceUrl . '">' . $this->sourceName . '</a>';
    }

}