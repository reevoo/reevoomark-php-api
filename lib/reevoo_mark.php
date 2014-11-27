<?php
require_once("reevoo_mark_utils.php");
require_once("reevoo_mark_http_client.php");

class ReevooMark {

  function ReevooMark($trkrefs, $cache_path, $base_url = 'http://mark.reevoo.com'){
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
    $showEmptyMessage = array_key_exists('showEmptyMessage', $options) ? $options['showEmptyMessage'] : true;

    $data = $this->http_client->getData("/reevoomark/embeddable_reviews?trkref={$trkref}&sku={$options['sku']}{$pagination_params}{$locale_param}{$sort_by_param}{$filter_param}{$client_url_param}");
    $notEmpty = !!$data->header("X-Reevoo-ReviewCount");
    if ($notEmpty || $showEmptyMessage) {
      echo $data->body();
    }
    return $notEmpty;
  }

  function customerExperienceReviews($options = array()) {
    $trkref = $this->utils->getTrkref($options);
    $pagination_params = $this->utils->getPaginationParams($options);
    $locale_param = $this->utils->getLocaleParam($options);
    $sort_by_param = $this->utils->getSortByParam($options);
    $filter_param = $this->utils->getFilterParam($options);
    $client_url_param = $this->utils->getClientUrlParam($options);
    $showEmptyMessage = array_key_exists('showEmptyMessage', $options) ? $options['showEmptyMessage'] : true;

    $data = $this->http_client->getData("/reevoomark/embeddable_customer_experience_reviews?trkref={$trkref}{$pagination_params}{$locale_param}{$sort_by_param}{$filter_param}{$client_url_param}");
    $notEmpty = !!$data->header("X-Reevoo-ReviewCount");
    if ($notEmpty || $showEmptyMessage) {
      echo $data->body();
    }
    return $notEmpty;
  }

  function conversations($options = array()) {
    if (!$options['sku']) {
      error_log('Need to provide a SKU for ReevooMark#productReviews');
      return;
    }
    $trkref = $this->utils->getTrkref($options);
    $locale_param = $this->utils->getLocaleParam($options);
    $showEmptyMessage = array_key_exists('showEmptyMessage', $options) ? $options['showEmptyMessage'] : true;

    $data = $this->http_client->getData("/reevoomark/embeddable_conversations?trkref={$trkref}&sku={$options['sku']}{$locale_param}");
    $notEmpty = !!$data->header("X-Reevoo-ConversationCount");
    if ($notEmpty || $showEmptyMessage) {
      echo $data->body();
    }
    return $notEmpty;
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

}



