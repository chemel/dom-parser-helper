<?php

require __DIR__.'/../vendor/autoload.php';

use Alc\HtmlDomParserHelper;

$helper = new HtmlDomParserHelper();

$parser = $helper->parse('https://news.ycombinator.com/');

echo $helper->getPageTitle(), "\n";

$nodes = $parser->find('td.title a');

foreach( $nodes as $node ) {

    $title = $node->innertext;

    echo $title, "\n";
}
