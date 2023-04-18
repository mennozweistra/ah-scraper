<?php

require __DIR__ . '/vendor/autoload.php';
use Symfony\Component\DomCrawler\Crawler;

$cachefileName = date('Y-m-d') . ".json";
$articles = [];

if (!file_exists($cachefileName)) {
    if (php_sapi_name() === 'cli') {
        // Called from commandline
        $url = "main.html";
    } else {
        $url = $_POST["url"];
        if (empty($url)) {
            echo "Yawn. You sent an empty url.";
            exit;
        }
    }

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
        $articlesCrawler = $crawler->filterXPath('//article');
        try {
            $articlesCrawler->each(function (Crawler $article) use (&$articles) {
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
    //             echo $title . PHP_EOL;
    //             echo $price . PHP_EOL;
    //             echo PHP_EOL;
                $articles[] = ['title' => $title, 'price' => $price];
            });
        }   catch (Exception $e) {
            // echo 'Caught exception: ',  $e->getMessage(), PHP_EOL;
        }
        break;
    }

    file_put_contents($cachefileName, json_encode($articles, JSON_PRETTY_PRINT));
} else {
    // Cache file exists
    $data = file_get_contents($cachefileName);
    $articles = json_decode($data, true);
}

$searchStrings = [
    'Courgette', 'Paprika'
];

echo "<pre>";
foreach ($articles as $article) {
    foreach ($searchStrings as $needle) {
        if (str_contains($article['title'], $needle)) {
            echo $article['title'] . PHP_EOL;
            echo $article['price'] . PHP_EOL;
            echo PHP_EOL;
        }
    }
}
echo "</pre>";
echo "<br><hr><br>";
echo "<pre>";

foreach ($articles as $article) {
    echo $article['title'] . PHP_EOL;
    echo $article['price'] . PHP_EOL;
    echo PHP_EOL;
}
echo "</pre>";
