<?php

class UrlUtilities {

  private $htaccess = '.htaccess'; 
  private $htaccessExists;
  private $path;
  private $rootUrl;
  private $rules;

  public function __construct($path = '') {

    $this->path = ROOT_PATH . rtrim($path, '/') . '/';
    $this->rootUrl = ROOT_URL . rtrim($path, '/') . '/';
    $this->htaccessExists = file_exists($this->path . $this->htaccess);

  }

  public function category($url) {

    $originalUrl = $url;
    if (! $this->htaccessExists) 
      return $originalUrl;

    $querystring = parse_url($url)['query'];
    $parts = explode('&', $querystring);

    $i = 0;
    $page = '';
    $categoryId = 0;
    $categorySlug = '';
    while($i < count($parts)) {
      $keyValuePair = explode('=', $parts[$i]);
      if ($keyValuePair[0] == 'page') $page = $keyValuePair[1];
      if ($keyValuePair[0] == 'categoryId') $categoryId = $keyValuePair[1];
      if ($keyValuePair[0] == 'slug') $categorySlug = strtolower(rtrim($keyValuePair[1], '-'));
      $i ++;
    }
    
    if ($page == 'products-list' && $categoryId > 0 && $categorySlug != '') {
      $url = $this->rootUrl . "category/$categoryId-$categorySlug";
    }

    return $url;
    try {
      
    } catch (Exception $e) {
      return $originalUrl;
    }
  }

  public function product($url) {

    $originalUrl = $url;
    if (! $this->htaccessExists) 
      return $originalUrl;

    $querystring = parse_url($url)['query'];
    $parts = explode('&', $querystring);

    $i = 0;
    $page = '';
    $id = 0;
    $slug = '';
    while($i < count($parts)) {
      $keyValuePair = explode('=', $parts[$i]);
      if ($keyValuePair[0] == 'page') $page = $keyValuePair[1];
      if ($keyValuePair[0] == 'id') $id = $keyValuePair[1];
      if ($keyValuePair[0] == 'slug') $slug = strtolower(rtrim($keyValuePair[1], '-'));
      $i ++;
    }
    
    if ($page == 'view-product' && $id > 0 && $slug != '') {
      $url = $this->rootUrl . "product/$id-$slug";
    }

    return $url;
    try {
      
    } catch (Exception $e) {
      return $originalUrl;
    }
  }

  // public function rewrite($url){
  //   $originalUrl = $url;
  //   if (! $this->htaccessExists) 
  //     return $originalUrl;

  //   $url = ltrim($url, $this->path);
  //   try {
  //     $matchingRule = null;
  //     foreach ($this->rules as $rule) {
  //       $match = preg_match('#' . $rule->regex . '#', $url);
  //       if ($match > 0) {
  //         $matchingRule = $rule;
  //         break;
  //       }
  //     }
  //     $match = preg_match('#^category/([0-9]+)-([a-z0-9-]+)/?$#', 'category/7-officia-incidunt');

  //   } catch (Exception $e) {
  //     return $originalUrl;
  //   }
  // }

}

