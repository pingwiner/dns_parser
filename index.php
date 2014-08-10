<?php

require_once('inc/ProductParser.php');
require_once('inc/CategoriesParser.php');

Db::connect();

$catParser = new CategoriesParser('http://www.dns-shop.ru/catalog/');
$catParser->parse();

//$parser = new ProductParser('http://www.dns-shop.ru/catalog/i174237/116-noutbuk-dns.html#description');
//$parser->parse();
