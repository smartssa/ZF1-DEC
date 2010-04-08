<?php
class DEC_Word_Lookup_Adapter_Wordnik extends DEC_Word_Lookup_Adapter_Abstract
{
    
    
    function setupApi($config) {
        // wprdnik needs api key.
        $this->apiKey = $config->apiKey;
    }

    function getDefinition($word) {
        $this->baseUri = 'http://api.wordnik.com/api/word.json/' . $word . '/definitions';
        $this->request($this->baseUri);
        $definitions = array();
        foreach ($this->json as $entry) {
            $definitions[] = new DEC_Word_Definition($entry->text, $entry->partOfSpeech, null, null);
        }
        return $definitions;
    }

    function getRelated($word) {
        $this->baseUri = 'http://api.wordnik.com/api/word.json/' . $word . '/related';
        $this->request($this->baseUri);
        $related = array();
        foreach ($this->json as $entry) {
            $related = $entry->wordstrings;
        }
        return $related;
    }

    function getExample($word) {
        $this->baseUri = 'http://api.wordnik.com/api/word.json/' . $word . '/examples';
        $this->request($this->baseUri);
        $examples = array();
        foreach ($this->json as $entry) {
            $examples[] = $entry->display; 
        }
        return $examples;
    }

    function getPhonetic($word) {
        return ''; // wordnik doesn't have this, yet.
    }

    function filterResponse($body) {
        return $body;
    }

    function checkErrors($body) {
        if ($body->type == 'error') {
            throw new DEC_Word_Lookup_Exception('Wordnik API Failed: ' . $body->message);
        }
    }
}