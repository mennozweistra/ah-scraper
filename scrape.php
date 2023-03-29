<?php

// require __DIR__ . '/vendor/autoload.php';
//
// $url = $_POST["url"];
// if (empty($url)) {
//     echo "Yawn. You sent an empty url.";
// }
// $web = new \Spekulatius\PHPScraper\PHPScraper;
// $web->go($url);
//
// $articles = $web->filter('//article'));
// $articles

require __DIR__ . '/vendor/autoload.php';
use Symfony\Component\DomCrawler\Crawler;

$url = $_POST["url"];
if (empty($url)) {
    echo "Yawn. You sent an empty url.";
    exit;
}

$html = file_get_contents($url);
$crawler = new Crawler($html);
$articles = $crawler->filterXPath('//article');
echo "<pre>";
$articles->each(function (Crawler $article) {
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

//     I was unable to find brand and promotion in the page, or in the subsequent product page
//     I don't know which data you would like me to select
//     $brand = ""; // X
//     $promotion = ""; // X

    echo $title . PHP_EOL;
    echo $image . PHP_EOL;
    echo $price . PHP_EOL;
    echo $size . PHP_EOL;
    echo PHP_EOL;
});
echo "</pre>";
