<?php

require_once('inc/Config.php');
require_once('inc/Parser.php');
require_once('inc/CatalogParser.php');

class CategoriesParser extends Parser {
    private $mExisting;
    
    public function parse() {
        if (!$this->load()) return;
        $this->loadExisting();
        $html = $this->mHtml->find('#index_catalog_list', 0);
        $this->parseList($html, 0, 0);
    }
    
    public function parseList($html, $level, $parent) {
        if (!is_object($html)) return;
        $a = $html->find('a', 0);        
        $id = 0;
        if ($a) {
            if (isset($this->mExisting[$a->plaintext])) return;
            $id = $this->saveLeaf($a, $parent);        
        }
        $count = 0;
        foreach($html->find('li.level_' . $level) as $li) {
            $count++;
            $this->parseList($li, $level + 1, $id);            
        }
        if (!$count) {
            print 'Catalog entry: ' . $a->plaintext . "\n";
            Db::update('categories', array('final' => 1), array('id' => $id));
            $nextPage = $a->href;
            while($nextPage) {
                $catParser = new CatalogParser(Config::SITE_URL . $nextPage);
                $catParser->setCategoryId($id);                
                $catParser->parse();
                $nextPage = $catParser->getNextPageUrl();
            }            
        }
    }
    
    public function saveLeaf($a, $parent) {
        $name = $a->plaintext;
        $url = $a->href;
        Db::insert('categories', array(
            'name' => $name, 
            'url' => $url,
            'parent' => $parent));
        $q = Db::select('categories', array('url' => $url), array('id'));
        $row = Db::row($q);
        $id = $row['id'];
        return $id;
    }
    
    public function loadExisting() {
        $this->mExisting = array();
        $q = Db::select("categories");
        while ($row = Db::row($q)) {
            $this->mExisting[$row['name']] = $row['id'];    
        }
    }
    
}

?>
