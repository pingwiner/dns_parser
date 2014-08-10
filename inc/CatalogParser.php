<?php

require_once('inc/Config.php');
require_once('inc/Parser.php');
require_once('inc/ProductParser.php');

class CatalogParser extends Parser {
    private $mCatId;    
    
    public function parse() {        
        $this->load();
        foreach($this->mHtml->find('table.catalog_view_list td.title') as $td) {
            $a = $td->find('a', 0);
            $url = $a->href;            
            $pp = new ProductParser(Config::SITE_URL . $url);
            $pp->setCategoryId($this->mCatId);
            $pp->parse();
        };
        
    }
    
    public function getNextPageUrl() {
        $html = $this->mHtml->find('ul.pager', 0);
        if (!$html) return null;
        $a = $html->find('li a', 0);        
        $class = $a->getAttribute('class');
        if ($class == 'sel') return null;
        return $a->href;
    }
    
    public function setCategoryId($id) {
        $this->mCatId = $id;
    }
    
}

?>
