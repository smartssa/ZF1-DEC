<?php

require_once 'Vimeo.php';
require_once 'Flickr.php';

$vimeo = new DEC_Vimeo();
$flickr = new DEC_Flickr();

//print_r($vimeo->testEcho(array('pants' => 'yes', 'skirt' => 'no')));
print_r($flickr->testEcho(array('flickr' => 'yes', 'weeeeeeee' => 'no')));
