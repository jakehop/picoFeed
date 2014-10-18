<?php

namespace PicoFeed;

/**
 * URL class
 *
 * @author  Frederic Guillot
 * @package picofeed
 */
class Url
{
    /**
     * URL
     *
     * @access private
     * @var string
     */
    private $url = '';

    /**
     * URL components
     *
     * @access private
     * @var array
     */
    private $components = array();

    /**
     * Constructor
     *
     * @access public
     * @param  string   $url    URL
     */
    public function __construct($url)
    {
        $this->url = $url;
        $this->components = parse_url($url);

        // Issue with PHP < 5.4.7 and protocol relative url
        if (version_compare(PHP_VERSION, '5.4.7', '<') && $this->isProtocolRelative()) {
            $slash = strpos($this->components['path'], '/', 2);
            $this->components['host'] = substr($this->components['path'], 2, $slash - 2);
            $this->components['path'] = substr($this->components['path'], $slash);
        }
    }

    /**
     * Shortcut method to get an absolute url from relative url
     *
     * @static
     * @access public
     * @param  string    $item_url      Unknown url (can be relative or not)
     * @param  mixed     $website_url   Website url
     * @return string
     */
    public static function resolve($item_url, $website_url)
    {
        $link = new Url($item_url);
        $website = is_string($website_url) ? new Url($website_url) : $website_url;

        if ($link->isRelativeUrl()) {
            return $link->getAbsoluteUrl($website->getAbsoluteUrl());
        }
        else if ($link->isProtocolRelative()) {
            $link->setScheme($website->getScheme());
        }

        return $link->getAbsoluteUrl();
    }

    /**
     * Get the base URL
     *
     * @access public
     * @param  string   $suffix    Add a suffix to the url
     * @return string
     */
    public function getBaseUrl($suffix = '')
    {
        return $this->hasHost() ? $this->getScheme('://').$this->getHost().$this->getPort(':').$suffix : '';
    }

    /**
     * Get the absolute URL
     *
     * @access public
     * @param  string   $base_url    Use this url as base url
     * @return string
     */
    public function getAbsoluteUrl($base_url = '')
    {
        if ($base_url) {
            $base = new Url($base_url);
            $url = $base->getAbsoluteUrl().substr($this->getFullPath(), 1);
        }
        else {
            $url = $this->hasHost() ? $this->getBaseUrl().$this->getFullPath() : '';
        }

        return $url;
    }

    /**
     * Return true if the url is relative
     *
     * @access public
     * @return boolean
     */
    public function isRelativeUrl()
    {
        return ! $this->hasScheme() && ! $this->isProtocolRelative();
    }

    /**
     * Get the path
     *
     * @access public
     * @return string
     */
    public function getPath()
    {
        $path = '/';

        if (! empty($this->components['path'])) {
            $path = $this->components['path'];

            if ($path{0} !== '/') {
                $path = '/'.$path;
            }
        }

        return $path;
    }

    /**
     * Get the full path (path + querystring + fragment)
     *
     * @access public
     * @return string
     */
    public function getFullPath()
    {
        $path = $this->getPath();
        $path .= empty($this->components['query']) ? '' : '?'.$this->components['query'];
        $path .= empty($this->components['fragment']) ? '' : '#'.$this->components['fragment'];

        return $path;
    }

    /**
     * Get the hostname
     *
     * @access public
     * @return string
     */
    public function getHost()
    {
        return empty($this->components['host']) ? '' : $this->components['host'];
    }

    /**
     * Return true if the url has a hostname
     *
     * @access public
     * @return boolean
     */
    public function hasHost()
    {
        return ! empty($this->components['host']);
    }

    /**
     * Get the scheme
     *
     * @access public
     * @param  string    $suffix   Suffix to add when there is a scheme
     * @return string
     */
    public function getScheme($suffix = '')
    {
        return ($this->hasScheme() ? $this->components['scheme'] : 'http').$suffix;
    }

    /**
     * Set the scheme
     *
     * @access public
     * @param  string    $scheme    Set a scheme
     * @return string
     */
    public function setScheme($scheme)
    {
        $this->components['scheme'] = $scheme;
    }

    /**
     * Return true if the url has a scheme
     *
     * @access public
     * @return boolean
     */
    public function hasScheme()
    {
        return ! empty($this->components['scheme']);
    }

    /**
     * Get the port
     *
     * @access public
     * @param  string    $prefix   Prefix to add when there is a port
     * @return string
     */
    public function getPort($prefix = '')
    {
        return $this->hasPort() ? $prefix.$this->components['port'] : '';
    }

    /**
     * Return true if the url has a port
     *
     * @access public
     * @return boolean
     */
    public function hasPort()
    {
        return ! empty($this->components['port']);
    }

    /**
     * Return true if the url is protocol relative (start with //)
     *
     * @access public
     * @return boolean
     */
    public function isProtocolRelative()
    {
        return strpos($this->url, '//') === 0;
    }
}
