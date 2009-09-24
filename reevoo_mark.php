<?php
  class ReevooMarkDocument {
    public $data;
    protected $head, $body, $headers;

    function __construct($data){
      if($this->data = $data)
        list($this->head, $this->body) = split("\n\n", $this->data, 2);
      else
        $this->head = $this->body = null;
    }

    function body(){
      return $this->body;
    }

    function statusCode(){
      $headers = split("\n", $this->head, 2);
      $status_line = $headers[0];
      if(preg_match("/^HTTP\/1.1 ([0-9]{3})/", $status_line, $matches))
        return $matches[1];
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
      $status_code = $this->statusCode();
      // 2xx, 4xx are both cached and rendered, we ignore 3xx and 5xx responses
      if($status_code >= 500 or $status_code >= 300 and $status_code < 400)
        return false;
      else
        return true;
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
      return time() - $this->date() + $this->header("Age");
    }

    function date(){
      if(preg_match("/([0-9]{2})\s+([a-zA-Z]+)\s+([0-9]{4})\s+([0-9]{2}:[0-9]{2}:[0-9]{2}\s+[A-Z]{3})$/", $this->header("Date"), $matches))
        return strtotime("$matches[1]-$matches[2]-$matches[3] $matches[4]");
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

    function render(){
      echo $this->body();
    }
    
    function body(){
      return $this->data->body();
    }

    protected function loadFromCache(){
      if($this->cache_path){
        if(file_exists($this->cachePath()))
          return file_get_contents($this->cachePath());
      }
    }

    protected function saveToCache($data){
      if($this->cache_path){
        if(!file_exists($this->cache_path))
          mkdir($this->cache_path);
        file_put_contents($this->cachePath(), $data);
      }
    }

    protected function loadFromRemote(){
      $ch = curl_init();
      curl_setopt($ch, CURLOPT_URL, $this->remote_url);
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
      curl_setopt($ch, CURLOPT_TIMEOUT_MS, 2000);
      curl_setopt($ch, CURLOPT_USERAGENT, "ReevooMark PHP Widget/1");
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
      $doc = new ReevooMarkDocument($this->loadFromCache());
      if($doc->hasExpired())
      {
        $remote_doc = new ReevooMarkDocument($this->loadFromRemote());
        if($remote_doc->isValidResponse())
        {
          $this->saveToCache($remote_doc->data);
          $doc = $remote_doc;
        }
      }
      return $doc;
    }
  };

?>
