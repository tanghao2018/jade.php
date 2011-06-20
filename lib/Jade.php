<?php

namespace lib;

class Jade {

    protected $parser;
    protected $dumper;
    protected $cache;

    public function __construct(Parser $parser, Dumper $dumper, $cache = null) {
        $this->parser = $parser;
        $this->dumper = $dumper;
        $this->cache  = $cache;
    }

    public function render($input) {
        $source = ( is_file($input) ) ? file_get_contents($input) : (string) $input;
        $parsed = $this->parser->parse($source);

        return $this->dumper->dump($parsed);
    }

    public function cache($input) {
        if ( !is_dir($this->cache) ) {
            throw new \Exception('You must provide correct cache path to Jade for caching.');
        }
        if ( !is_file($input) ) {
            throw new \InvalidArgumentException('Only file templates can be cached.');
        } 

        $cacheKey = basename($input, '.jade');
        $path = $this->cache . '/' . $cacheKey . '.php';
        $cacheTime = filemtime($path);

        if ( $cacheTime && filemtime($input) < $cacheTime ) {
            return $path;
        }

        if ( !is_writable($this->cache) ) {
            throw new \Exception(sprintf('Cache directory must be writable. "%s" is not.', $this->cache));
        }

        $rendered = $this->render($input);
        file_put_contents($path, $rendered);

        return $path;
    }

}