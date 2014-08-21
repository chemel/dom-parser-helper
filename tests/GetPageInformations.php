<?php

require __DIR__.'/../vendor/autoload.php';

use Alc\HtmlDomParserHelper;

$helper = new HtmlDomParserHelper();
$helper->parse('http://www.lemonde.fr/');

print_r( $helper->getResponse()->getInfos() );

var_dump( $helper->getPageTitle() );
var_dump( $helper->getPageDescription() );

var_dump( $helper->getPageFavicon() );
var_dump( $helper->getPageCanonical() );

print_r( $helper->getPageMetas() );

print_r( $helper->getPageFeeds() );

$helper->clear();
