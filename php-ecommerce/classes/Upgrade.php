<?php

class UpgradeManager extends DBManager {

  public $path;
  public $exludedFiles;

  public function __construct() {
    parent::__construct();
    $this->path = ROOT_PATH . "sql";
    $this->excludedFiles = ['.', '..', 'index.php'];
    $this->_executeFirstScript();
  }

  // Private Methods
  private function _executeFirstScript()
  {
    try 
    {
      $this->_getDbVersion();
    }
    catch(Exception $e)
    {
      $firstScriptName = "1.sql";
      $this->_executeScript($firstScriptName);
    }
  }

  private function _getDbVersion() {
    $result = $this->db->query("SELECT max(version) as version FROM version");
    return isset($result[0]['version']) ? $result[0]['version'] : "0";
  }

  private function updateDbVersion($newVersion) {
    $result = $this->db->exec("UPDATE version set version = '$newVersion'");
  }

  private function _executeScript($file) {
    $content = file_get_contents( $this->path . '/' . $file);
    $result = $this->db->exec($content);
    $result["file"] = $file;
    return $result;
  }

  // Public API

  public function GetDbVersion() {
    return $this->_getDbVersion();
  }

  public function ExecuteScripts() {

    $version = $this->_getDbVersion();
    $files = scandir($this->path);
    sort($files);
    $results = [];
    $newVersion = $version;

    foreach($files as $i => $file) {
      
      if (in_array($file, $this->excludedFiles)){
        unset($files[$i]);
        continue;
      }
      if ($file <= $version . ".sql"){
        unset($files[$i]);
        continue;
      }

      $result = $this->_executeScript($file);
      array_push($results, $result);

      if ($file > $newVersion && $result["result"] == 1) {
        $newVersion = $file;
      }
    }

    $newVersion = str_replace('.sql', '', $newVersion);
    $this->updateDbVersion($newVersion);
    return $results;
  }

  public function GetScriptsToExecute() {

    $version = $this->_getDbVersion();
    $files = scandir($this->path);
    sort($files);
    
    foreach($files as $i => $file) {
      if (in_array($file, $this->excludedFiles)) {
        unset($files[$i]);
        continue;
      }

      if ($file <= $version . ".sql"){
        unset($files[$i]);
        continue;
      }
    }
    return $files;
  }
 
 }
