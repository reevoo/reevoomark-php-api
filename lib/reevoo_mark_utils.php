<?php
class ReevooMarkUtils {

  function ReevooMarkUtils($trkrefs) {
    $this->trkref = explode(',', $trkrefs)[0];
  }

  static function presence($var, $default = null) {
    return empty($var) ? $default : $var;
  }

  function getVariant($options = array()) {
    return $options['variant'] ? ' '.$options['variant'].'_variant' : '';
  }

  function getTrkref($options = array()) {
    return $options['trkref'] ? $options['trkref'] : $this->trkref;
  }

  function getPaginationParams($options = array()) {
    $page = self::presence($_GET['reevoo_page'], 1);
    $paginated = $options['paginated'];
    if ($paginated) {
      $numberOfReviews = self::presence($options['numberOfReviews'], "default");
      $pagination_params = "&per_page={$numberOfReviews}&page={$page}";
    } elseif ($options['numberOfReviews']) {
      $pagination_params = "&reviews={$options['numberOfReviews']}";
    } else {
      $pagination_params = '';
    }
    return $pagination_params;
  }

  function getLocaleParam($options = array()) {
    if (isset($options['locale'])) {
      return "&locale={$options['locale']}";
    } else {
      return "";
    }
  }

  function getSortByParam($options = array()) {
    if (isset($_GET['reevoo_sort_by'])) {
      return "&sort_by={$_GET['reevoo_sort_by']}";
    } else {
      return "";
    }
  }

  function getFilterParam($options = array()) {
    if (isset($_GET['reevoo_filter'])) {
      return "&filter={$_GET['reevoo_filter']}";
    } else {
      return "";
    }
  }

  function getClientUrlParam($options = array()) {
    if (isset($options['paginated'])) {
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
    if(self::presence($_SERVER["SERVER_PORT"]) == 443 || (!empty(self::presence($_SERVER["HTTPS"])) && self::presence($_SERVER["HTTPS"]) == "on")) {
      $protocol .= "s";
      $protocol_port = self::presence($_SERVER["SERVER_PORT"]);
    } else {
      $protocol_port = 80;
    }
    $host = self::presence($_SERVER["HTTP_HOST"]);
    $port = self::presence($_SERVER["SERVER_PORT"]);
    $request_path = self::presence($_SERVER["PHP_SELF"]);
    $querystr = self::presence($_SERVER["QUERY_STRING"]);
    $url = $protocol."://".$host.(($port!=$protocol_port && strpos($host,":")==-1)?":".$port:"").$request_path.(empty($querystr)?"":"?".$querystr);
    return $url;
  }

}
