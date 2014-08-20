<?php

namespace Alc;

use Alc\Curl\Curl;
use Straube\UTF8\Encoding;
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

        return $curl->get( $url );
    }

    /**
     * Get HtmlDomParser
     *
     * @param string html_content
     *
     * @return HtmlDomParser parser
     */
    public function getHtmlDomParser( $html ) {

        $parser = HtmlDomParser::str_get_html( $html );

        return $parser;
    }

    /**
     * Parse webpage
     *
     * @return string url;
     */
    public function parse( $url ) {

        $this->response = $this->performRequest( $url );

        $content = $this->response->getContent();

        $content = Encoding::toUTF8( $content );

        return $this->parser = $this->getHtmlDomParser( $content );
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

        return $this->parser->find('title', 0)->innertext;
    }

    /**
     * Get page description
     *
     * @return string description 
     */
    public function getPageDescription() {

        $node = $this->parser->find('meta[name=description]', 0);

        if( $node ) return $node->getAttribute('content');
    }
}
