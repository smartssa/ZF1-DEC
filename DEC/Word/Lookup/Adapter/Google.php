<?php

class DEC_Word_Lookup_Adapter_Google extends DEC_Word_Lookup_Adapter_Abstract
{
    protected $phonetic    = '';
    protected $definitions = array();
    protected $related     = array();
    protected $examples    = array();

    function setupApi($config) {
        // nothing to do for google.
    }

    function getDefinition($word) {
        if ($this->json === null) {
            $this->googleMagic($word);
        }
        return $this->definitions;
    }

    function getRelated($word) {
        if ($this->json === null) {
            $this->googleMagic($word);
        }
        return $this->related;
    }

    function getExample($word) {
        if ($this->json === null) {
            $this->googleMagic($word);
        }
        return $this->examples;
    }

    function getPhonetic($word) {
        if ($this->json === null) {
            $this->googleMagic($word);
        }
        return $this->phonetic;
    }

    private function googleMagic($word) {
        $this->baseUri = "http://www.google.com/dictionary/json?callback=unwrap&q=".$word."&sl=en&tl=en";
        $this->request($this->baseUri);
        // do all the processing here.
        if ($this->json->primaries > 0) {
            $filter = new Zend_Filter_StripTags();
            $primary = $this->json->primaries[0];
            // primary contains type/terms/entries
            foreach ($primary->terms as $term) {
                switch ($term->type) {
                    case "phonetic":
                        if ($this->phonetic == '') {
                            // take the first one.
                            $this->phonetic = urldecode($term->text);
                        }
                        break;
                }
            }
            foreach ($primary->entries as $entry) {
                switch ($entry->type) {
                    case "meaning":
                        if ($entry->terms) {
                            foreach ($entry->terms as $term) {
                                switch ($term->type) {
                                    case "text":
                                        $definition = $filter->filter(urldecode($term->text));
                                        if ($term->labels) {
                                            foreach ($term->labels as $label) {
                                                switch ($label->title) {
                                                    case 'Part-of-speech':
                                                        $partofspeech = strtolower($label->text);
                                                        continue;
                                                        break;
                                                }
                                            }
                                        }
                                        break;
                                }
                                if (trim($definition) != '') {
                                    $this->definitions[] = new DEC_Word_Definition($definition, $partofspeech, $related, $example);
                                }
                            }
                        }
                        if ($entry->entries) {
                            foreach ($entry->entries as $subEntry) {
                                // extra entries
                                switch ($subEntry->type) {
                                    case "example":
                                        $this->examples[] = $filter->filter(urldecode($subEntry->terms[0]->text));
                                        break;
                                    case "related":
                                        $this->related[] = $filter->filter(urldecode($subEntry->terms[0]->text));
                                        break;
                                }
                            }
                        }
                        break;
                }
            }
        } else {
            throw new DEC_Word_Lookup_Exception('Google Failed to find ' . $word);
        }
    }

    function filterResponse($body) {
        //var_dump($googleResponse->getHeaders());
        //$jsondecoded = json_decode($googleResponse);
        $body = str_replace('unwrap(', '', $body);
        $body = str_replace(',200,null)', '', $body);
        $body = str_replace('\x', '%', $body);
        return $body;
    }

    function checkErrors($body) {
        // return what?
    }
}