# dom-parser-helper

PHP Simple HTML DOM Parser Helper

## Install:

```bash

composer install

```

## Usage

```php

require __DIR__.'/vendor/autoload.php';

use Alc\HtmlDomParserHelper;

$helper = new HtmlDomParserHelper();
$parser = $helper->parse('http://www.lemonde.fr/');

// Get curl informations (status code, etc..)
print_r( $helper->getResponse()->getInfos() );

// Get page title
var_dump( $helper->getPageTitle() );

// Get page description
var_dump( $helper->getPageDescription() );

// Get favicon url
var_dump( $helper->getPageFavicon() );

// Get canonical url
var_dump( $helper->getPageCanonical() );

// Get all metas info
print_r( $helper->getPageMetas() );

// Get rss feeds
print_r( $helper->getPageFeeds() );

// Get all urls
print_r( $helper->findAllUrls() );

// Get Sunra HtmlDomParser and exec queries (see: https://github.com/sunra/php-simple-html-dom-parser)
$nodes = $parser->find('a');

foreach( $nodes as $node ) {
    echo $node->innertext, "\n";
}

// Clean memory
$helper->clear();
