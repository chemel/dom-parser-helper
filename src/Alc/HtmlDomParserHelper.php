<?php

namespace Alc;

use Alc\Guzzle\Guzzle;
use ForceUTF8\Encoding;
use Sunra\PhpSimple\HtmlDomParser;

/**
 * HtmlDomParserHelper
 */
class HtmlDomParserHelper {

    protected $response;

    protected $parser;

    /**
     * Get new Curl instance
     *
     * @return Curl curl
     */
    public function getClient() {

        $client = new Guzzle();

        $this->configureClient( $client );

        return $client->getClient();
    }

    /**
     * Configure Curl instance
     *
     * @param Curl curl
     */
    protected function configureClient( &$client ) {

        $client->useChrome();
    }

    /**
     * Perform HTTP request
     *
     * @param string url
     *
     * @return CurlResponse response
     */
    public function performRequest( $url ) {

        $client = $this->getClient();

        return $this->response = $client->get( $url );
    }

    /**
     * Get HtmlDomParser
     *
     * @param string html_content
     *
     * @return HtmlDomParser parser
     */
    public function getHtmlDomParser( $html ) {

        return $this->parser = HtmlDomParser::str_get_html( $html );
    }

    /**
     * Convert encoding to UTF-8
     *
     * @param string content
     *
     * @return string content
     */
    public function convertEncodingToUTF8( $content ) {

        return Encoding::toUTF8( $content );
    }

    /**
     * Parse webpage
     *
     * @return string url;
     */
    public function parse( $url ) {

        $content = $this->performRequest( $url )->getBody()->getContents();

        $content = $this->convertEncodingToUTF8( $content );

        return $this->getHtmlDomParser( $content );
    }

    /**
     * Get curl response
     *
     * @return CurlResponse reponse
     */
    public function getResponse() {

        return $this->response;
    }

    /**
     * Get parser
     *
     * @return HtmlDomParser parser
     */
    public function getParser() {

        return $this->parser;
    }

    /**
     * Get page title
     *
     * @return string title
     */
    public function getPageTitle() {

        if( !$this->parser ) return;

        return $this->parser->find('title', 0)->innertext;
    }

    /**
     * Get page description
     *
     * @return string description
     */
    public function getPageDescription() {

        if( !$this->parser ) return;

        $node = $this->parser->find('meta[name=description]', 0);

        if( $node ) return $node->getAttribute('content');
    }

    /**
     * Get page keywords
     *
     * @return string url
     */
    public function getPageKeywords() {

        if( !$this->parser ) return;

        $node = $this->parser->find('meta[name=keywords]', 0);

        if( $node ) return $node->getAttribute('content');
    }

    /**
     * Get page canonical url
     *
     * @return string url
     */
    public function getPageCanonical() {

        if( !$this->parser ) return;

        $node = $this->parser->find('link[rel=canonical]', 0);

        if( $node ) return $node->getAttribute('href');
    }

    /**
     * Get page favicon url
     *
     * @return string url
     */
    public function getPageFavicon() {

        if( !$this->parser ) return;

        $node = $this->parser->find('link[rel=shortcut], link[rel=icon], link[rel=shortcut icon]', 0);

        if( $node ) return $node->getAttribute('href');
    }

    /**
     * Get meta description
     *
     * @return array metas
     */
    public function getPageMetas() {

        if( !$this->parser ) return;

        $nodes = $this->parser->find('meta');

        $metas = array();

        foreach( $nodes as $node ) {

            if( $node->hasAttribute('name') ) {

                $metas[ $node->getAttribute('name') ] = $node->getAttribute('content');
            }
            elseif( $node->hasAttribute('property') ) {

                $metas[ $node->getAttribute('property') ] = $node->getAttribute('content');
            }
        }

        return $metas;
    }

    /**
     * Get rss feeds urls
     *
     * @return array feeds
     */
    public function getPageFeeds() {

        if( !$this->parser ) return;

        $nodes = $this->parser->find('link');

        $feeds = array();

        $types = array(
            'application/rss+xml',
            'application/atom+xml',
            'text/xml',
        );

        foreach( $nodes as $node ) {

            $type = strtolower($node->getAttribute('type'));

            if( in_array($type, $types) ) {

                $feeds[] = $node->getAttribute('href');
            }
        }

        return $feeds;
    }

    /**
     * Find all
     *
     * @param string selector
     * @param string results
     * @param array arguments
     *
     * @return array results
     */
    public function findAll($selector, $function, $arguments = array()) {

        if( !$this->parser ) return;

        $nodes = $this->parser->find($selector);

        if( !$nodes ) return;

        if( !is_array($arguments) ) $arguments = array($arguments);

        $results = array();

        foreach( $nodes as $node ) {

            $results[] = call_user_func_array(array($node, $function), $arguments);
        }

        return $results;
    }

    /**
     * Find all urls
     *
     * @return array results
     */
    public function findAllUrls() {

        return $this->findAll('a', 'getAttribute', 'href');
    }

    /**
     * Find all images urls
     *
     * @return array results
     */
    public function findAllImages() {

        return $this->findAll('img', 'getAttribute', 'src');
    }

    /**
     * Clean up memory
     */
    public function clear() {

        $this->response = null;

        if( $this->parser )
            $this->parser->clear();

        $this->parser = null;
    }
}
