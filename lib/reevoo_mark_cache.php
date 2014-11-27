<?php
require_once("reevoo_mark_document.php");

class ReevooMarkCache {

  function ReevooMarkCache($cache_path) {
    $this -> cache_path = $cache_path;
  }

  function saveToCache($data, $url_path){
    if($this->cache_path){
      if(!file_exists($this->cache_path))
        mkdir($this->cache_path);
      file_put_contents($this->cachePath($url_path), $data);
    }
  }

  function loadFromCache($url_path){
    if($this->cache_path){
      if(file_exists($this->cachePath($url_path))){
        return file_get_contents($this->cachePath($url_path));
      }
    }
  }

  function cacheMTime($url_path){
    if($this->cache_path){
      if(file_exists($this->cachePath($url_path))){
        return filemtime($this->cachePath($url_path));
      }
    }
  }

  function newDocumentFromCache($url_path){
    return new ReevooMarkDocument($this->loadFromCache($url_path), $this->cacheMTime($url_path));
  }

  protected function cachePath($url_path){
    $digest = md5($url_path);
    return  "$this->cache_path/$digest.cache";
  }

}
