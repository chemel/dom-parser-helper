<?php

namespace Alc;

use Alc\Curl\Curl;
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
    public function getCurl() {

        $curl = new Curl();

        $this->configureCurl( $curl );

        return $curl;
    }

    /**
     * Configure Curl instance
     *
     * @param Curl curl
     */
    protected function configureCurl( Curl &$curl ) {

        $curl->useChrome();
    }

    /**
     * Perform HTTP request
     *
     * @param string url
     *
     * @return CurlResponse response
     */
    public function performRequest( $url ) {

        $curl = $this->getCurl();

        return $this->response = $curl->get( $url );
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

        $content = $this->performRequest( $url )->getContent();

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
     * Clean up memory
     */
    public function clear() {

        $this->response = null;

        if( $this->parser )
            $this->parser->clear();

        $this->parser = null;
    }
}
