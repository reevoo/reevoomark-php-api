<?php
  class ReevooMarkDocument {
    public $data;
    protected $head, $body, $headers;

    function __construct($data, $mtime){
      if($this->data = $data)
        list($this->head, $this->body) = split("\n\n", $this->data, 2);
      else
        $this->head = $this->body = null;
      $this->mtime = $mtime;
    }

    function body(){
      if($this->isValidResponse())
        return $this->body;
      else
        return "";
    }

    function statusCode(){
      $headers = split("\n", $this->head, 2);
      $status_line = $headers[0];
      if(preg_match("/^HTTP\/1.1 ([0-9]{3})/", $status_line, $matches))
        return $matches[1];
      else
        return 500;
    }

    function header($name){
      $headers = $this->headers();
      if(array_key_exists($name, $headers))
        return $headers[$name];
      else
        return null;
    }

    function headers(){
      if($this->headers)
        return $this->headers;
      else
      {
        $headers = split("\n", $this->head);
        array_shift($headers); // Status line is no use here
        $parsed_headers = Array();
        foreach($headers as $header){
          list($key, $value) = split(":", $header, 2);
          $parsed_headers[$key] = trim($value);
        }
        $this->headers = $parsed_headers;
        return $this->headers;
      }
    }

    function isValidResponse(){
      if(!$this->data)
        return false;
      return 200 == $this->statusCode();
    }

    function isCachableResponse(){
      return $this->statusCode() < 500;
    }

    function hasExpired(){
      if(!$this->data)
        return true;
      return $this->maxAge() < $this->currentAge();
    }

    function maxAge(){
      if(preg_match("/max-age=([0-9]+)/", $this->header("Cache-Control"), $matches))
        return $matches[1];
    }

    function currentAge(){
      return time() - $this->mtime + $this->header("Age");
    }
  };


  class ReevooMark {
    protected $cache_path, $retailer, $sku, $data, $remote_url;

    function ReevooMark($cache_path, $mark_url, $retailer, $sku){
      $this->cache_path = $cache_path;
      $this->retailer = $retailer;
      $this->sku = $sku;
      $this->remote_url = "{$mark_url}?sku=$this->sku&retailer=$this->retailer";
      $this->data = $this->getData();
    }

    function reviewCount(){
      return $this->data->header("X-Reevoo-ReviewCount");
    }

    function offerCount(){
      return $this->data->header("X-Reevoo-OfferCount");
    }

    function bestPrice(){
      return $this->data->header("X-Reevoo-BestPrice");
    }

    function render(){
      echo $this->body();
    }

    function body(){
      return $this->data->body();
    }

    protected function saveToCache($data){
      if($this->cache_path){
        if(!file_exists($this->cache_path))
          mkdir($this->cache_path);
        file_put_contents($this->cachePath(), $data);
      }
    }

    protected function loadFromCache(){
      if($this->cache_path){
        if(file_exists($this->cachePath())){
          return file_get_contents($this->cachePath());
        }
      }
    }

    protected function cacheMTime(){
      if($this->cache_path){
        if(file_exists($this->cachePath())){
          return filemtime($this->cachePath());
        }
      }
    }

    protected function newDocumentFromCache(){
      return new ReevooMarkDocument($this->loadFromCache(), $this->cacheMTime());
    }

    protected function loadFromRemote(){
      $ch = curl_init();
      curl_setopt($ch, CURLOPT_URL, $this->remote_url);
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
      curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
      curl_setopt($ch, CURLOPT_TIMEOUT_MS, 2000);
      curl_setopt($ch, CURLOPT_USERAGENT, "ReevooMark PHP Widget/6");
      curl_setopt($ch, CURLOPT_REFERER, "http://{$_SERVER['HTTP_HOST']}{$_SERVER['REQUEST_URI']}");
      curl_setopt($ch, CURLOPT_CONNECTTIMEOUT_MS, 2000);
      curl_setopt($ch, CURLOPT_HEADER, 1);

      if($result = curl_exec($ch))
        return str_replace("\r", "", $result);
      else
        return false;
    }

    protected function cachePath(){
      $digest = md5($this->remote_url);
      return  "$this->cache_path/$digest.cache";
    }

    protected function getData(){
      $doc = $this->newDocumentFromCache();
      if($doc->hasExpired())
      {
        $remote_doc = new ReevooMarkDocument($this->loadFromRemote(), time());
        if(!$doc->isCachableResponse() || $remote_doc->isCachableResponse())
        {
          $this->saveToCache($remote_doc->data);
          $doc = $remote_doc;
        }
      }
      return $doc;
    }
  };

?>
