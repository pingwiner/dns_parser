<?php

require_once('lib/simple_html_dom.php');
require_once('inc/Db.php');

abstract class Parser {
    protected $mUrl;
    protected $mHtml; 

    public function __construct($url) {
        $this->mUrl = $url;
    }
    
    protected function load() {
        $this->mHtml = file_get_html($this->mUrl);
        if (!$this->mHtml) return false;        
        return true;
    }
    
    protected function price($selector) {
        $str = $this->plain($selector);
        if (!$str) return null;
        return str_replace(' ', '', $str);
    }
    
    protected function plain($selector) {
        $element = $this->mHtml->find($selector, 0);
        if (!$element) return null;
        return $element->plaintext;    
    }    
    
    protected function html($selector) {
        $element = $this->mHtml->find($selector, 0);
        if (!$element) return null;
        return $element->innertext();
    }

    abstract public function parse();
    
}

?>
