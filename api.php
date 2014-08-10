<?php

require_once('inc/Config.php');
require_once('inc/Db.php');

$action = null;
if (isset($_GET['action'])) {
    $action = $_GET['action'];    
}
if (!$action) return;
Db::connect();

switch($action) {
    case 'catalog':
        catalog();
        break;
    case 'products':
        products();
        break;
    case 'product':
        product();
        break;
}

function catalog() {
    if (!isset($_GET['id'])) return;
    $id = intval($_GET['id']);
    $q = Db::select('categories', array('parent' => $id), array('id', 'name', 'final'));
    $result = array();
    while($row = Db::row($q)) {
        $result[] = array(
            'id' => $row['id'],
            'name' => $row['name'],
            'final' => $row['final']
        );
    }
    header('Content-Type: application/json');
    print json_encode($result, JSON_UNESCAPED_UNICODE + JSON_NUMERIC_CHECK);
}

function products() {
    if (!isset($_GET['id'])) return;
    $id = intval($_GET['id']);
    $offset = 0;
    if (isset($_GET['offset'])) {
        $offset = intval($_GET['offset']);
    }
    $result = array();
    $q = Db::select('products', array('category_id' => $id), 
            array(
                'id', 
                'name', 
                //'specs', 
                'price', 
                //'code', 
                //'warranty', 
                //'warehouses', 
                //'shops', 
                //'description'
            ),
            array(
                'fields' => array('id'),
                'desc' => false
            ),
            array(
                'offset' => $offset,
                'count' => 50
            ));
    while($row = Db::row($q)) {
        $result[] = array(
            'id' => $row['id'], 
            'name' => $row['name'], 
            //'specs' => $row['specs'], 
            'price' => $row['price'], 
            //'code' => $row['code'], 
            //'warranty' => $row['warranty'], 
            //'warehouses' => $row['warehouses'], 
            //'shops' => $row['shops'], 
            //'description' => $row['description']
        );
    }
    header('Content-Type: application/json');
    print json_encode(array(
        'offset' => $offset,
        'category' => $id,
        'products' => $result
    ), JSON_UNESCAPED_UNICODE + JSON_NUMERIC_CHECK);    
}

function product() {
    if (!isset($_GET['id'])) return;
    $id = intval($_GET['id']);
    $q = Db::select('products', array('id' => $id),  
            array('id', 'name', 'specs', 'price', 'code', 'warranty', 'warehouses', 'shops', 'description'));
    $row = Db::row($q);
    if (!$row) return;
    $result = array(
        'id' => $row['id'], 
        'name' => $row['name'], 
        'specs' => $row['specs'], 
        'price' => $row['price'], 
        'code' => $row['code'], 
        'warranty' => $row['warranty'], 
        'warehouses' => $row['warehouses'], 
        'shops' => $row['shops'], 
        'description' => $row['description']
    );
    header('Content-Type: application/json');
    print json_encode($result, JSON_UNESCAPED_UNICODE + JSON_NUMERIC_CHECK);    
}

?>
