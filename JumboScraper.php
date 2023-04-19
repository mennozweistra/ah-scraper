<?php

require_once("BaseScraper.php");
use Symfony\Component\DomCrawler\Crawler;

// This scraper returns all AH products that are currently discounted
class JumboScraper extends BaseScraper {

    protected function scrape() {
        $products = [];
        $url = "https://www.jumbo.com/aanbiedingen/actieprijs";
        $html = file_get_contents($url);        
        $crawler = new Crawler($html);
        $productsCrawler = $crawler->filterXPath('//article');
        try {
            $productsCrawler->each(function (Crawler $article) use (&$products) {
                try {
                    $title = $article->evaluate('div[2]/h3/a')->text();
                    $price = $article->evaluate('div[1]/div[2]/span')->text();
                    $products[] = ['title' => $title, 'price' => $price];
                } catch (Exception $e) {
                    // echo 'Caught minor exception: ',  $e->getMessage(), PHP_EOL;
                }                
            });
        } catch (Exception $e) {
            // echo 'Caught exception: ',  $e->getMessage(), PHP_EOL;
        }
        return $products;
    }

}


