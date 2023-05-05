<?php

require __DIR__ . '/vendor/autoload.php';
require_once("AhScraper.php");
require_once("JumboScraper.php");

$ignoreCache = true;
$scraper = new AhScraper("cache/ah/");
$products = $scraper->getData($ignoreCache);
var_dump($products);
exit;

// $scraper = new JumboScraper("cache/jumbo/");
// $products = array_merge($products, $scraper->getData());


$searchStrings = [
    'gold espresso', 'spekreepjes', 'scharrel kipfilet', 'riblappen', 'sucadelappen', 'Verstegen',
    'kokosmelk', 'varkenshaas', 'asperges', 'chocomel', 'kipschnitzel'
];

foreach ($products as $product) {
    foreach ($searchStrings as $needle) {
        if (str_contains(strtolower($product['title']), strtolower($needle))) {
            echo $product['shop'] . " | " . $product['title'] . PHP_EOL;
            echo $product['price'] . " for ";
            echo $product['size'] . PHP_EOL;
            echo $product['url'] . PHP_EOL;
            echo PHP_EOL;
        }
    }
}

