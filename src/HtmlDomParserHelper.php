<?php

namespace Alc;

use GuzzleHttp\ClientInterface;
use GuzzleHttp\Client as HttpClient;
use KubAT\PhpSimple\HtmlDomParser;
use Psr\Http\Message\ResponseInterface;

/**
 * HtmlDomParserHelper
 */
class HtmlDomParserHelper
{
    protected $client;

    protected $response;

    protected $parser;

    /**
     * Set client
     *
     * @param ClientInterface client
     */
    public function setClient(ClientInterface $client)
    {
        $this->client = $client;
    }

    /**
     * Get Client instance
     *
     * @return HttpClient client
     */
    public function getClient()
    {
        if (!$this->client) {
            $client = new HttpClient();

            $this->configureClient($client);

            return $this->client = $client;
        }

        return $this->client;
    }

    /**
     * Configure HttpClient instance
     *
     * @param HttpClient client
     */
    protected function configureClient(&$client)
    {

    }

    /**
     * Perform HTTP request
     *
     * @param string url
     *
     * @return ResponseInterface response
     */
    public function performRequest($url): ResponseInterface
    {
        $client = $this->getClient();

        return $this->response = $client->get($url);
    }

    /**
     * Get HtmlDomParser
     *
     * @param string html
     *
     * @return HtmlDomParser parser
     */
    public function getHtmlDomParser($html)
    {
        return $this->parser = HtmlDomParser::str_get_html($html);
    }

    /**
     * Parse webpage
     *
     * @return HtmlDomParser parser;
     */
    public function parse($url)
    {
        $content = $this->performRequest($url)->getBody()->getContents();

        return $this->getHtmlDomParser($content);
    }

    /**
     * Get curl response
     *
     * @return ResponseInterface response
     */
    public function getResponse(): ResponseInterface
    {
        return $this->response;
    }

    /**
     * Get parser
     *
     * @return HtmlDomParser parser
     */
    public function getParser()
    {
        return $this->parser;
    }

    /**
     * Get page title
     *
     * @return string title
     */
    public function getPageTitle()
    {
        if (!$this->parser) {
            return;
        }

        $node = $this->parser->find('title', 0);

        if ($node) {
            return $node->innertext;
        }
    }

    /**
     * Get page description
     *
     * @return string description
     */
    public function getPageDescription()
    {
        if (!$this->parser) {
            return;
        }

        $node = $this->parser->find('meta[name=description]', 0);

        if ($node) {
            return $node->getAttribute('content');
        }
    }

    /**
     * Get page keywords
     *
     * @return string url
     */
    public function getPageKeywords()
    {
        if (!$this->parser) {
            return;
        }

        $node = $this->parser->find('meta[name=keywords]', 0);

        if ($node) {
            return $node->getAttribute('content');
        }
    }

    /**
     * Get page canonical url
     *
     * @return string url
     */
    public function getPageCanonical()
    {
        if (!$this->parser) {
            return;
        }

        $node = $this->parser->find('link[rel=canonical]', 0);

        if ($node) {
            return $node->getAttribute('href');
        }
    }

    /**
     * Get page favicon url
     *
     * @return string url
     */
    public function getPageFavicon()
    {
        if (!$this->parser) {
            return;
        }

        $node = $this->parser->find('link[rel=shortcut], link[rel=icon], link[rel=shortcut icon]', 0);

        if ($node) {
            return $node->getAttribute('href');
        }
    }

    /**
     * Get meta description
     *
     * @return array metas
     */
    public function getPageMetas()
    {
        if (!$this->parser) {
            return;
        }

        $nodes = $this->parser->find('meta');

        $metas = array();

        foreach ($nodes as $node) {
            if ($node->hasAttribute('name')) {
                $metas[ $node->getAttribute('name') ] = $node->getAttribute('content');
            } elseif ($node->hasAttribute('property')) {
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
    public function getPageFeeds()
    {
        if (!$this->parser) {
            return;
        }

        $nodes = $this->parser->find('link');

        $feeds = array();

        $types = array(
            'application/rss+xml',
            'application/atom+xml',
            'text/xml',
        );

        foreach ($nodes as $node) {
            $type = strtolower($node->getAttribute('type'));

            if (in_array($type, $types)) {
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
    public function findAll($selector, $function, $arguments = array())
    {
        if (!$this->parser) {
            return;
        }

        $nodes = $this->parser->find($selector);

        if (!$nodes) {
            return;
        }

        if (!is_array($arguments)) {
            $arguments = array($arguments);
        }

        $results = array();

        foreach ($nodes as $node) {
            $results[] = call_user_func_array(array($node, $function), $arguments);
        }

        return $results;
    }

    /**
     * Find all urls
     *
     * @return array results
     */
    public function findAllUrls()
    {
        return $this->findAll('a', 'getAttribute', 'href');
    }

    /**
     * Find all images urls
     *
     * @return array results
     */
    public function findAllImages()
    {
        return $this->findAll('img', 'getAttribute', 'src');
    }

    /**
     * Clean up memory
     */
    public function clear()
    {
        $this->response = null;

        if ($this->parser) {
            $this->parser->clear();
        }

        $this->parser = null;
    }
}
