<?php

require_once('inc/Parser.php');

class ProductParser extends Parser {
    private $mProduct; 
    private $mImages;
    private $mCatId;
    
    public function parse() {
        if (!$this->load()) return;
        $this->mProduct = array();
        $this->mProduct['name'] = $this->plain('h1.price_item_heading');
        $this->mProduct['url'] = $this->mUrl;
        $this->mProduct['specs'] = $this->plain('.price_specs');
        $this->mProduct['price'] = $this->price('.price_col .item_value');
        $this->mProduct['code'] = $this->plain('li.code span');
        $this->mProduct['warranty'] = $this->plain('li.guarantee');
        $this->mProduct['warehouses'] = $this->plain('.warehouses');        
        $this->mProduct['shops'] = $this->html('.shops');        
        $this->mProduct['description'] = $this->html('#tab-description .col1');
        $this->mProduct['category_id'] = $this->mCatId;        
        $this->parseImages();
        $this->save();
    }
 
    public function parseImages() {
        $this->mImages = array();
        foreach($this->mHtml->find('.price_item_photo_item a') as $a) {
            $this->mImages[] = $a->href; 
        }
    }
    
    private function save() {
        print '  product: ' . $this->mProduct['name'] . "\n";
        Db::insert('products', $this->mProduct);
        $q = Db::select('products', array('code' => $this->mProduct['code']), array('id'));
        $row = Db::row($q);
        $product_id = $row['id'];
        foreach($this->mImages as $url) {
            Db::insert('images', array('url' => $url));
            $q = Db::select('images', array('url' => $url), array('id'));
            $row = Db::row($q);
            $image_id = $row['id'];
            Db::insert('product_images', array(
                'product_id' => $product_id, 
                'image_id' => $image_id));
        }
        
    } 
    
    public function setCategoryId($id) {
        $this->mCatId = $id;
    }
    
}

?>
