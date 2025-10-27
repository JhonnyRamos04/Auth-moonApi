<?php

Class Router {
    private $url;
    
    public function __construct($url) {
        $this->url = $url;
    }
    
    public function getUrlParts() {
        return explode('/', filter_var(trim($this->url, '/'), FILTER_SANITIZE_URL));
    }
}