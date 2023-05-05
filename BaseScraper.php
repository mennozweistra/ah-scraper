<?php

abstract class BaseScraper {
    protected $cacheFile = false;
    protected $cacheData = false;

    function __construct($cacheDirectory) {
        $this->cacheFile = $cacheDirectory . date('Y-m-d') . ".json";
    }

    // scrape must be implemented by subclasses
    abstract protected function scrape();

    // Get the data that this scraper collected
    // The data will be returned from cache, unless the data is not cached yet
    // In that case the scraper will run and fetch the data from the web
    public function getData($ignoreCache = false) {
        if (!$ignoreCache) {
            // If we have the data in memory, return it
            if ($this->cacheData) return $this->cacheData;
            // If we have the data cached in the cacheFile, load and return it
            if (file_exists($this->cacheFile)) {
                $this->cacheData = json_decode(file_get_contents($this->cacheFile), true);
                return $this->cacheData;
            }
        }
        // We don't have a valid cache, scrape the data and return the cache
        $this->cacheData = $this->scrape();
        file_put_contents($this->cacheFile, json_encode($this->cacheData, JSON_PRETTY_PRINT));
        return $this->cacheData;
    }

}