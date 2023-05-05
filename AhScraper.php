<?php

require_once("BaseScraper.php");
use Symfony\Component\DomCrawler\Crawler;

// This scraper returns all AH products that are currently discounted
class AhScraper extends BaseScraper {


    protected function scrape() {
        $url = "https://www.ah.nl/bonus";
        $html = file_get_contents($url);
        file_put_contents("out.html", $html);
        echo $html;
        exit;

        $crawler = new Crawler($html);
        // /html/body/div[3]/div/main/div/div[2]/section[1]/div/a[1]/div/p[1]/span
        // /html/body/div[3]/div/main/div/div[2]/section[1]/div/a[1]/picture
        // $bonusGroupCrawler = $crawler->filterXPath('//main/div/div[2]/section[1]/div/a[1]/@href');
        $bonusGroupCrawler = $crawler->filterXPath('//a');
        
        $bonusGroups = [];

        $bonusGroupCrawler->each(function (Crawler $bonusGroup) use (&$bonusGroups) {
            echo "*" . $bonusGroup->text() . "\n";
            $bonusGroups[] = 'https://www.ah.nl' . $bonusGroup->text();
        });
        // var_dump($bonusGroups);
        exit;


        foreach($categories as $url) {
            sleep(1); //sleep for 1 second so we don't generate too much traffic
            $html = file_get_contents($url);
            $crawler = new Crawler($html);
            $productsCrawler = $crawler->filterXPath('//article');
            try {
                $productsCrawler->each(function (Crawler $article) use (&$products, $url) {
                    $title = $article->evaluate('div/div/a/strong/span')->text();
                    try {
                        $price = $article->evaluate('div/a/div/div/div')->text();
                    } catch (Exception $e) {
                        $price = $article->evaluate('div/div[1]/div/div[2]/span')->text();
                    }
                    try {
                        $image = $article->evaluate('div/a/figure/div/img')->attr('src');;
                    } catch (Exception $e) {
                        $image = $article->evaluate('a/figure/div/img')->attr('src');;
                    }
                    try {
                        $size = $article->evaluate('div[1]/a/div[1]/div/span')->text();
                    } catch (Exception $e) {
                        $size = $article->evaluate('div/div[1]/div/span')->text();
                    }
                    $products[] = ['shop' => 'AH', 'title' => $title, 'price' => $price, 'size' => $size, 'url' => $url];
                    echo $title . ", " . $price . PHP_EOL;
                });
            }   catch (Exception $e) {
                echo 'Caught exception: ',  $e->getMessage(), PHP_EOL;
            }
        }

        return $products;
    }



    // Curently not used. Scrapes products from the product page, not specialOffers.
    protected function scrapeProducts() {
        $url = "https://www.ah.nl/producten";
        $html = file_get_contents($url);
        $crawler = new Crawler($html);
        $categoriesCrawler = $crawler->filterXPath('//main/div[1]/div/div/div/div/div/a/@href');
        $categories = [];
        $categoriesCrawler->each(function (Crawler $category) use (&$categories) {
            $categories[] = 'https://www.ah.nl' . $category->text();
        });

        foreach($categories as $url) {
            sleep(1); //sleep for 1 second so we don't generate too much traffic
            $html = file_get_contents($url);
            $crawler = new Crawler($html);
            $productsCrawler = $crawler->filterXPath('//article');
            try {
                $productsCrawler->each(function (Crawler $article) use (&$products, $url) {
                    $title = $article->evaluate('div/div/a/strong/span')->text();
                    try {
                        $price = $article->evaluate('div/a/div/div/div')->text();
                    } catch (Exception $e) {
                        $price = $article->evaluate('div/div[1]/div/div[2]/span')->text();
                    }
                    try {
                        $image = $article->evaluate('div/a/figure/div/img')->attr('src');;
                    } catch (Exception $e) {
                        $image = $article->evaluate('a/figure/div/img')->attr('src');;
                    }
                    try {
                        $size = $article->evaluate('div[1]/a/div[1]/div/span')->text();
                    } catch (Exception $e) {
                        $size = $article->evaluate('div/div[1]/div/span')->text();
                    }
                    $products[] = ['shop' => 'AH', 'title' => $title, 'price' => $price, 'size' => $size, 'url' => $url];
                    echo $title . ", " . $price . PHP_EOL;
                });
            }   catch (Exception $e) {
                echo 'Caught exception: ',  $e->getMessage(), PHP_EOL;
            }
        }

        return $products;
    }

}


