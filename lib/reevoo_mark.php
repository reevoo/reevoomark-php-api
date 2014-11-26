<?php

  class ReevooMark {
    protected $cache_path, $trkref, $sku, $base_url;

    function ReevooMark($trkrefs, $cache_path, $base_url = 'http://mark.reevoo.com'){
      $this->cache_path = $cache_path;
      $this->trkrefs = $trkrefs;
      $this->base_url = $base_url;
      $this->http_client = new ReevooMarkHttpClient($base_url, $cache_path);
      $this->utils = new ReevooMarkUtils($trkrefs);
    }

    function cssAssets() {
      echo '<link rel="stylesheet" href="//mark.reevoo.com/stylesheets/reevoomark/embedded_reviews.css" type="text/css" />';
    }

    function productBadge($options = array()) {
      if (!$options['sku']) {
        error_log('Compulsory parameter sku has not been provided to ReevooMark#productBadge');
        return;
      }
      $variant = $this->utils->getVariant($options);
      $trkref = $this->utils->getTrkref($options);
      echo "<a class=\"reevoomark{$variant}\" href=\"{$this->base_url}/partner/{$trkref}/{$options['sku']}\"></a>";
    }

    function conversationsBadge($options = array()) {
      if (!$options['sku']) {
        error_log('Need to provide a SKU for ReevooMark#conversationBadge');
        return;
      }
      $variant = $this->utils->getVariant($options);
      $trkref = $this->utils->getTrkref($options);
      echo "<a class=\"reevoomark reevoo-conversations{$variant}\" href=\"{$this->base_url}/partner/{$trkref}/{$options['sku']}\"></a>";
    }

    function productSeriesBadge($options = array()) {
      if (!$options['sku']) {
        error_log('Need to provide a SKU for ReevooMark#productSeriesBadge');
        return;
      }
      $variant = $this->utils->getVariant($options);
      $trkref = $this->utils->getTrkref($options);
      echo "<a class=\"reevoomark{$variant}\" href=\"{$this->base_url}/partner/{$trkref}/series:{$options['sku']}\"></a>";
    }

    function conversationSeriesBadge($options = array()) {
      if (!$options['sku']) {
        error_log('Need to provide a SKU for ReevooMark#conversationSeriesBadge');
        return;
      }
      $variant = $this->utils->getVariant($options);
      $trkref = $this->utils->getTrkref($options);
      echo "<a class=\"reevoomark reevoo-conversations{$variant}\" href=\"{$this->base_url}/partner/{$trkref}/series:{$options['sku']}\"></a>";
    }

    function overallServiceRatingBadge($options = array()) {
      $variant = $this->utils->getVariant($options);
      $trkref = $this->utils->getTrkref($options);
      echo "<a class=\"reevoo_reputation{$variant}\" href=\"{$this->base_url}/retailer/{$trkref}\"></a>";
    }

    function customerServiceRatingBadge($options = array()) {
      $variant = $this->utils->getVariant($options);
      $trkref = $this->utils->getTrkref($options);
      echo "<a class=\"reevoo_reputation customer_service{$variant}\" href=\"{$this->base_url}/retailer/{$trkref}\"></a>";
    }

    function deliveryRatingBadge($options = array()) {
      $variant = $this->utils->getVariant($options);
      $trkref = $this->utils->getTrkref($options);
      echo "<a class=\"reevoo_reputation delivery{$variant}\" href=\"{$this->base_url}/retailer/{$trkref}\"></a>";
    }

    function productReviews($options = array()) {
      if (!$options['sku']) {
        error_log('Need to provide a SKU for ReevooMark#productReviews');
        return;
      }
      $trkref = $this->utils->getTrkref($options);
      $pagination_params = $this->utils->getPaginationParams($options);
      $locale_param = $this->utils->getLocaleParam($options);
      $sort_by_param = $this->utils->getSortByParam($options);
      $filter_param = $this->utils->getFilterParam($options);
      $client_url_param = $this->utils->getClientUrlParam($options);
      $data = $this->http_client->getData("/reevoomark/embeddable_reviews?trkref={$trkref}&sku={$options['sku']}{$pagination_params}{$locale_param}{$sort_by_param}{$filter_param}{$client_url_param}");
      echo $data->body();
    }

    function customerExperienceReviews($options = array()) {
      $trkref = $this->utils->getTrkref($options);
      $pagination_params = $this->utils->getPaginationParams($options);
      $locale_param = $this->utils->getLocaleParam($options);
      $sort_by_param = $this->utils->getSortByParam($options);
      $filter_param = $this->utils->getFilterParam($options);
      $client_url_param = $this->utils->getClientUrlParam($options);
      $data = $this->http_client->getData("/reevoomark/embeddable_customer_experience_reviews?trkref={$trkref}{$pagination_params}{$locale_param}{$sort_by_param}{$filter_param}{$client_url_param}");
      echo $data->body();
    }

    function conversations($options = array()) {
      if (!$options['sku']) {
        error_log('Need to provide a SKU for ReevooMark#productReviews');
        return;
      }
      $trkref = $this->utils->getTrkref($options);
      $locale_param = $this->utils->getLocaleParam($options);
      $data = $this->http_client->getData("/reevoomark/embeddable_conversations?trkref={$trkref}&sku={$options['sku']}{$locale_param}");
      echo $data->body();
    }

    function purchaseTrackingEvent($options = array()) {
      $trkref = $this->utils->getTrkref($options);
      if (!$options['skus']) {
        error_log('Need to provide the list of skus purchased for ReevooMark#purchaseTrackingEvent');
        return;
      }
      $skus = $options['skus'];
      $value = $options['value'];
      echo <<<EOT
<script type="text/javascript" charset="utf-8">
  if (typeof afterReevooMarkLoaded === 'undefined') {
    var afterReevooMarkLoaded = [];
  }
  afterReevooMarkLoaded.push(
    function(){
      ReevooApi.load('{$trkref}', function(retailer){
        retailer.track_purchase("{$skus}".split(/[ ,]+/), "{$value}");
      });
    }
  );
</script>
EOT;
    }

    function propensityToBuyTrackingEvent($options = array()) {
      $trkref = $this->utils->getTrkref($options);
      $action = $options['action'];
      $sku = $options['sku'];
      if (!$sku) {
        $sku = "Global CTA";
      }
      echo <<<EOT
<script type="text/javascript" charset="utf-8">
  if (typeof afterReevooMarkLoaded === 'undefined') {
    var afterReevooMarkLoaded = [];
  }
  afterReevooMarkLoaded.push(
    function(){
      ReevooApi.load('{$trkref}', function(retailer){
        retailer.Tracking.ga_track_event("Propensity to buy", "{$action}", "{$sku}");
        retailer.track_exit();
      });
    }
  );
</script>
EOT;
    }

    function javascriptAssets() {
      echo <<<EOT
<script id="reevoomark-loader" type="text/javascript" charset="utf-8">
  (function () {
      var script = document.createElement('script');
      script.type = 'text/javascript';
      script.src = '//cdn.mark.reevoo.com/assets/reevoo_mark.js';
      var s = document.getElementById('reevoomark-loader');
      s.parentNode.insertBefore(script, s);
   })();
   if (typeof afterReevooMarkLoaded === 'undefined') {
      var afterReevooMarkLoaded = [];
   }
   afterReevooMarkLoaded.push(
     function () {
       ReevooApi.each('{$this->trkrefs}'.split(/[ ,]+/), function (retailer) {
       retailer.init_badges();
       retailer.init_reevoo_reputation_badges();
     });
   }
  );
</script>
EOT;
    }

  };

  class ReevooMarkHttpClient {

    function ReevooMarkHttpClient($base_url, $cache_path) {
      $this->base_url = $base_url;
      $this->cache = new ReevooMarkCache($cache_path);
    }

    function getData($url_path){
      $doc = $this -> cache ->newDocumentFromCache($url_path);
      if($doc->hasExpired())
      {
        $remote_doc = new ReevooMarkDocument($this-> loadFromRemote($url_path), time());
        if(!$doc->isCachableResponse() || $remote_doc->isCachableResponse())
        {
          $this->cache->saveToCache($remote_doc->data, $url_path);
          $doc = $remote_doc;
        }
      }
      return $doc;
    }

    protected function loadFromRemote($url_path){
      $remote_url = $this->base_url . $url_path;
      $ch = curl_init();
      curl_setopt($ch, CURLOPT_URL, $remote_url);
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
      curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
      curl_setopt($ch, CURLOPT_TIMEOUT_MS, 2000);
      curl_setopt($ch, CURLOPT_USERAGENT, "ReevooMark PHP Widget/8");
      curl_setopt($ch, CURLOPT_REFERER, "http://{$_SERVER['HTTP_HOST']}{$_SERVER['REQUEST_URI']}");
      curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 2);
      curl_setopt($ch, CURLOPT_HEADER, 1);

      if($result = curl_exec($ch))
        return str_replace("\r", "", $result);
      else
        return false;
    }
  }

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

  class ReevooMarkDocument {
    protected $head, $body, $headers;

    function __construct($data, $mtime){
      if($this->data = $data)
        list($this->head, $this->body) = explode("\n\n", $this->data, 2);
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
      $headers = explode("\n", $this->head, 2);
      $status_line = $headers[0];
      if(preg_match("/^HTTP\/[^ ]* ([0-9]{3})/", $status_line, $matches))
        return $matches[1];
      else
        return 500;
    }

    function header($name){
      $headers = $this->headers();
      $name = strtolower($name);
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
        $headers = explode("\n", $this->head);
        array_shift($headers); // Status line is no use here
        $parsed_headers = Array();
        foreach($headers as $header){
          list($key, $value) = explode(":", $header, 2);
          $parsed_headers[strtolower($key)] = trim($value);
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

  class ReevooMarkUtils {

    function ReevooMarkUtils($trkrefs) {
      $this->trkref = explode(',', $trkrefs)[0];
    }

    function getVariant($options = array()) {
      return $options['variant'] ? ' '.$options['variant'].'_variant' : '';
    }

    function getTrkref($options = array()) {
      return $options['trkref'] ? $options['trkref'] : $this->trkref;
    }

    function getPaginationParams($options = array()) {
      $page = $_GET['reevoo_page']? $_GET['reevoo_page'] : 1;
      $paginated = $options['paginated'];
      if ($paginated) {
        $numberOfReviews = $options['numberOfReviews'] ? $options['numberOfReviews'] : "default";
        $pagination_params = "&per_page={$numberOfReviews}&page={$page}";
      } elseif ($options['numberOfReviews']) {
        $pagination_params = "&reviews={$options['numberOfReviews']}";
      } else {
        $pagination_params = '';
      }
      return $pagination_params;
    }

    function getLocaleParam($options = array()) {
      if ($options['locale']) {
        return "&locale={$options['locale']}";
      } else {
        return "";
      }
    }

    function getSortByParam($options = array()) {
      if ($_GET['reevoo_sort_by']) {
        return "&sort_by={$_GET['reevoo_sort_by']}";
      } else {
        return "";
      }
    }

    function getFilterParam($options = array()) {
      if ($_GET['reevoo_filter']) {
        return "&filter={$_GET['reevoo_filter']}";
      } else {
        return "";
      }
    }

    function getClientUrlParam($options = array()) {
      if ($options['paginated']) {
        $current_url = urlencode($this->getCurrentURL());
        return "&client_url={$current_url}";
      } else {
        return "";
      }
    }

    function reviewCount($data){
      return $data->header("X-Reevoo-ReviewCount");
    }

    function offerCount($data){
      return $data->header("X-Reevoo-OfferCount");
    }

    function conversationCount($data){
      return $data->header("X-Reevoo-ConversationCount");
    }

    function bestPrice($data){
      return $data->header("X-Reevoo-BestPrice");
    }

    function getCurrentURL() {
      $protocol = "http";
      if($_SERVER["SERVER_PORT"]==443 || (!empty($_SERVER["HTTPS"]) && $_SERVER["HTTPS"]=="on")) {
        $protocol .= "s";
        $protocol_port = $_SERVER["SERVER_PORT"];
      } else {
        $protocol_port = 80;
      }
      $host = $_SERVER["HTTP_HOST"];
      $port = $_SERVER["SERVER_PORT"];
      $request_path = $_SERVER["PHP_SELF"];
      $querystr = $_SERVER["QUERY_STRING"];
      $url = $protocol."://".$host.(($port!=$protocol_port && strpos($host,":")==-1)?":".$port:"").$request_path.(empty($querystr)?"":"?".$querystr);
      return $url;
    }

  }

?>
