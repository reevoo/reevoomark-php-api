<?php
class ReevooMarkUtils {

  function ReevooMarkUtils($trkrefs) {
    $trkref_array = explode(',', $trkrefs);
    $this->trkref = self::presenceKey($trkref_array,0);
  }

  static function presence($var, $default = null) {
    return empty($var) ? $default : $var;
  }

  static function presenceKey($arr, $key, $default = null) {
    return (is_array($arr) && array_key_exists($key, $arr)) ? $arr[$key] : $default;
  }

  function getVariant($options = array()) {
    $variant = self::presenceKey($options, 'variant', '');
    if ($variant != 'undecorated') {
      $variant = $variant . '_variant';
    }
    if ($variant != '') {
      $variant = ' ' . $variant;
    }
    return $variant;
  }

  function getTrkref($options = array()) {
    return self::presenceKey($options, 'trkref', $this->trkref);
  }

  function getPaginationParams($options = array()) {
    $page = isset($options['page'])
          ? self::presenceKey($options, 'page')
          : self::presenceKey($_GET, 'reevoo_page', 1);

    $paginated = self::presenceKey($options, 'paginated');
    if ($paginated) {
      $numberOfReviews = self::presenceKey($options, 'numberOfReviews', "default");
      $pagination_params = "&per_page={$numberOfReviews}&page={$page}";
    } elseif (self::presenceKey($options,'numberOfReviews')) {
      $pagination_params = "&reviews={$options['numberOfReviews']}";
    } else {
      $pagination_params = '';
    }
    return $pagination_params;
  }

  function getLocaleParam($options = array()) {
    if (self::presenceKey($options, 'locale')) {
      return "&locale={$options['locale']}";
    } else {
      return "";
    }
  }

  function getSortByParam($options = array()) {
    $sortBy = isset($options['sort_by'])
            ? self::presenceKey($options, 'sort_by')
            : self::presenceKey($_GET, 'reevoo_sort_by');

    if ($sortBy) {
      return "&sort_by={$sortBy}";
    } else {
      return "";
    }
  }

  function getFilterParam($options = array()) {
    $filter = isset($options['filter'])
            ? self::presenceKey($options, 'filter')
            : self::presenceKey($_GET, 'reevoo_filter');

    if ($filter) {
      return "&filter={$filter}";
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
    $https = self::presenceKey($_SERVER, "HTTPS");
    if(self::presenceKey($_SERVER, "SERVER_PORT") == 443 || (!empty($https) && self::presenceKey($_SERVER, "HTTPS") == "on")) {
      $protocol .= "s";
      $protocol_port = self::presenceKey($_SERVER, "SERVER_PORT");
    } else {
      $protocol_port = 80;
    }
    $host = self::presenceKey($_SERVER, "HTTP_HOST");
    $port = self::presenceKey($_SERVER, "SERVER_PORT");
    $request_path = self::presenceKey($_SERVER, "PHP_SELF");
    $querystr = self::presenceKey($_SERVER, "QUERY_STRING");
    $url = $protocol."://".$host.(($port!=$protocol_port && strpos($host,":")==-1)?":".$port:"").$request_path.(empty($querystr)?"":"?".$querystr);
    return $url;
  }

}
