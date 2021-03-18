<?php

require_once './inc/init.php'; 

$url = "http://localhost/Github/php-ecommerce/php-ecommerce/shop/?page=products-list&categoryId=12&slug=Cumque-vel-";

$urlUtilities = new UrlUtilities('shop');
$newUrl = $urlUtilities->category($url);

echo $newUrl;

// var_dump( preg_match('#^category/([0-9]+)-([a-z0-9-]+)/?$#', 'category/7-officia-incidunt') );