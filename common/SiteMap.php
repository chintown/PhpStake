<?php

require 'common/SiteMapEntry.php';
// http://www.sitemaps.org/protocol.html

class SiteMap {
    public $locationRoot;
    public $entries = array();

    function __construct($root) {
        $this->locationRoot = $root;
    }
    public function add(SiteMapEntry $entry) {
        $this->entries[] = $entry;
    }
    private function getHeader() {
        return <<<HEADER
<?xml version="1.0" encoding="UTF-8"?>
<urlset
    xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"
    xmlns:image="http://www.google.com/schemas/sitemap-image/1.1">
HEADER;
    }
    private function getEntry($entry) {
        $loc = trim($this->locationRoot,'/').'/'.$entry->location;
        return <<<ENTRY
<url>
    <loc>$loc</loc>
    <changefreq>$entry->changeFrequency</changefreq>
    <priority>$entry->priorityRatio</priority>
</url>
ENTRY;
    }
    private function getFooter() {
        return <<<FOOTER
</urlset>
FOOTER;
    }
    public function render() {
        echo $this->getHeader();
        foreach ($this->entries as $entry) {
            echo $this->getEntry($entry);
        }
        echo $this->getFooter();
    }
}