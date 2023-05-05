<?php

require_once("BaseScraper.php");
use Symfony\Component\DomCrawler\Crawler;

// This scraper returns all AH specialOffers that are currently discounted
class JumboScraper extends BaseScraper {

    protected function scrape() {
        $specialOffers = [];
        $products = [];
        $url = "https://www.jumbo.com/aanbiedingen/actieprijs";
        $html = file_get_contents($url);        
        $crawler = new Crawler($html);
        $specialOffersCrawler = $crawler->filterXPath('//article');
        try {
            $specialOffersCrawler->each(function (Crawler $article) use (&$specialOffers) {
                try {
                    $title = $article->evaluate('div[2]/h3/a')->text();
                    $price = $article->evaluate('div[1]/div[2]/span')->text();
                    $detailsUrl = "https://www.jumbo.com" . $article->evaluate('div[2]/h3/a')->attr('href');
                    $specialOffers[] = ['title' => $title, 'price' => $price, 'detailsUrl' => $detailsUrl];
                } catch (Exception $e) {
                    // echo 'Caught minor exception: ',  $e->getMessage(), PHP_EOL;
                }                
            });
        } catch (Exception $e) {
            // echo 'Caught exception: ',  $e->getMessage(), PHP_EOL;
        }

        foreach($specialOffers as $offer) {
            sleep(1); //sleep for 1 second so we don't generate too much traffic
            echo "--------------------\n";
            echo $offer['title'] . "\n";
            echo "--------------------\n";
            $html = file_get_contents($offer['detailsUrl']);
            // $html = file_get_contents($offer->detailsUrl);
            $crawler = new Crawler($html);
            $productsCrawler = $crawler->filterXPath('//article');
            $count = 1;
            try {
                $productsCrawler->each(function (Crawler $article) use (&$products, &$count, $offer) {
                    $title = $article->evaluate('div[2]/div[1]/div[1]/h2/a')->text();
                    $price = $article->evaluate('div[2]/div[1]/div[2]/div/div/span')->text();
                    $size = $article->evaluate('div[2]/div[1]/div[1]/div/p[2]/span')->text();
                    echo  $count++ . ". " . $title . ", " . $price . PHP_EOL;
                    $products[] = ['shop' => 'Jumbo', 'title' => $title, 'price' => $price, 'size' => $size, 'url' => $offer['detailsUrl']];
                });
            }   catch (Exception $e) {
                echo 'Caught exception: ',  $e->getMessage(), PHP_EOL;
            }
        }
        return $products;
    }

}


