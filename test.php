<?php

require __DIR__ . '/vendor/autoload.php';
require_once("AhScraper.php");
require_once("JumboScraper.php");

// $scraper = new AhScraper("cache/ah/");
// $data = $scraper->getData();
// var_dump($data);

$scraper = new JumboScraper("cache/jumbo/");
$data = $scraper->getData();
var_dump($data);