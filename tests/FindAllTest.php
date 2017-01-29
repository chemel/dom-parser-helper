<?php

require __DIR__.'/../vendor/autoload.php';

use Alc\HtmlDomParserHelper;

$helper = new HtmlDomParserHelper();

$parser = $helper->parse('https://news.ycombinator.com/');

print_r($helper->findAllUrls());
print_r($helper->findAllImages());
