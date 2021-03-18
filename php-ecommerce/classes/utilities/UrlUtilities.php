<?php

class UrlUtilities {

  private $htaccess = '.htaccess';
  private $htaccessExists;
  private $rootUrl;

  public function __construct($path = '') {
    $this->rootUrl =  $path == '' ? ROOT_URL : rtrim(ROOT_URL, '/') . '/' . rtrim($path, '/') . '/';
    $path = $path == '' ? ROOT_PATH : rtrim(ROOT_PATH, '\\') . '\\' . rtrim($path, '\\') . '\\';
    $this->htaccessExists = file_exists( $path . $this->htaccess);
  }

  public function category($id, $name){
    return $this->rewriteUrl( (object) [
      'page' => 'products-list',
      'id' => $id,
      'name' => $name,
      'segment' => 'category'
    ]);
  }

  public function product($id, $name){
    return $this->rewriteUrl( (object) [
      'page' => 'view-product',
      'id' => $id,
      'name' => $name,
      'segment' => 'product'
    ]);
  }

  public function static($page) {
    return $this->rewriteUrl( (object) [
      'page' => $page,
      'segment' => $page
    ]);
  }

  // Private Methods

  private function rewriteUrl($params) {
    if (isset($params->name)) {
      $slug = strtolower(rtrim(preg_replace("![^a-z0-9]+!i", "-", $params->name), '-'));
    }

    if ($this->htaccessExists) {
      $url = $this->rootUrl . "$params->segment";
      $url .= isset($slug) && isset($params->id) ? "/$params->id-$slug" : "";
    } else {
      $url = $this->rootUrl . "?page=$params->page";
      $url .= isset($slug) && isset($params->id) ? "&id=$params->id&slug=$slug" : "";
    }
    return $url;
  }

}

