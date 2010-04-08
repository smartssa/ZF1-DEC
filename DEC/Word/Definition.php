<?php

class DEC_Word_Definition {

    private $definition    = '';
    private $partsOfSpeech = '';
    private $related = array();
    private $example = array();
    
    public function __construct($definition, $partsOfSpeech, $related, $example) {
        $this->setPartsOfSpeech($partsOfSpeech);
        $this->setDefinition($definition);
        $this->setRelated($related);
        $this->setExample($example);
    }

    public function setPartsOfSpeech($partsOfSpeech) {
        $this->partsOfSpeech = $partsOfSpeech;
        return $this;
    }

    public function getPartsOfSpeech() {
        return $this->partsOfSpeech;
    }

    public function setDefinition($definition) {
        $this->definition = $definition;
        return $this;
    }

    public function getDefinition() {
        return $this->definition;
    }

    public function setRelated($words) {
        if (is_array($words)) {
            $this->related = array_merge($this->related, $words);
        } else {
            $this->related[] = $words;
        }
        return $this;
    }

    public function getRelated() {
        return $this->related;
    }

    public function setExample($example) {
        if (is_array($example)) {
            $this->example = array_merge($this->example, $words);
        } else {
            $this->example[] = $words;
        }
        return $this;

    }

    public function getExample() {
        return $this->example;
    }
}