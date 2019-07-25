<?php
require_once("reevoo_mark_utils.php");
require_once("reevoo_mark_http_client.php");

class ReevooMarkApi {

  function __construct($trkrefs, $cache_path, $base_url = 'http://mark.reevoo.com'){
    $this->trkrefs = $trkrefs;
    $this->base_url = $base_url;
    $this->http_client = new ReevooMarkHttpClient($base_url, $cache_path);
    $this->utils = new ReevooMarkUtils($trkrefs);
  }

  function cssAssets() {
    return '<link rel="stylesheet" href="//mark.reevoo.com/stylesheets/reevoomark/embedded_reviews.css" type="text/css" />';
  }

  function productBadge($options = array()) {
    if (!isset($options['sku']) && !isset($options['registration'])) {
      error_log('Compulsory parameter sku or registration number has not been provided to ReevooMark#productBadge');
      return;
    }
    $product_identifier = isset($options['sku']) ? $options['sku'] : "{$options['registration']}?identifier=registration";
    $variant = $this->utils->getVariant($options);
    $trkref = $this->utils->getTrkref($options);
    return "<a class=\"reevoomark{$variant}\" href=\"{$this->base_url}/partner/{$trkref}/{$product_identifier}\"></a>";
  }

  function conversationsBadge($options = array()) {
    if (!isset($options['sku']) && !isset($options['registration'])) {
      error_log('Need to provide a SKU or registration number for ReevooMark#conversationBadge');
      return;
    }
    $product_identifier = isset($options['sku']) ? $options['sku'] : "{$options['registration']}?identifier=registration";
    $variant = $this->utils->getVariant($options);
    $trkref = $this->utils->getTrkref($options);
    return "<a class=\"reevoomark reevoo-conversations{$variant}\" href=\"{$this->base_url}/partner/{$trkref}/{$product_identifier}\"></a>";
  }

  function productSeriesBadge($options = array()) {
    if (!isset($options['sku'])) {
      error_log('Need to provide a SKU for ReevooMark#productSeriesBadge');
      return;
    }
    $variant = $this->utils->getVariant($options);
    $trkref = $this->utils->getTrkref($options);
    return "<a class=\"reevoomark{$variant}\" href=\"{$this->base_url}/partner/{$trkref}/series:{$options['sku']}\"></a>";
  }

  function conversationSeriesBadge($options = array()) {
    if (!isset($options['sku'])) {
      error_log('Need to provide a SKU for ReevooMark#conversationSeriesBadge');
      return;
    }
    $variant = $this->utils->getVariant($options);
    $trkref = $this->utils->getTrkref($options);
    return "<a class=\"reevoomark reevoo-conversations{$variant}\" href=\"{$this->base_url}/partner/{$trkref}/series:{$options['sku']}\"></a>";
  }

  function overallServiceRatingBadge($options = array()) {
    $variant = $this->utils->getVariant($options);
    $trkref = $this->utils->getTrkref($options);
    return "<a class=\"reevoo_reputation{$variant}\" href=\"{$this->base_url}/retailer/{$trkref}\"></a>";
  }

  function customerServiceRatingBadge($options = array()) {
    $variant = $this->utils->getVariant($options);
    $trkref = $this->utils->getTrkref($options);
    return "<a class=\"reevoo_reputation customer_service{$variant}\" href=\"{$this->base_url}/retailer/{$trkref}\"></a>";
  }

  function deliveryRatingBadge($options = array()) {
    $variant = $this->utils->getVariant($options);
    $trkref = $this->utils->getTrkref($options);
    return "<a class=\"reevoo_reputation delivery{$variant}\" href=\"{$this->base_url}/retailer/{$trkref}\"></a>";
  }

  function productReviews($options = array()) {
    if (!isset($options['sku']) && !isset($options['registration'])) {
      error_log('Need to provide a SKU or registartion number for ReevooMark#productReviews');
      return;
    }
    $product_identifier = isset($options['sku']) ? 'sku' : 'registration';
    $trkref = $this->utils->getTrkref($options);
    $pagination_params = $this->utils->getPaginationParams($options);
    $locale_param = $this->utils->getLocaleParam($options);
    $sort_by_param = $this->utils->getSortByParam($options);
    $filter_param = $this->utils->getFilterParam($options);

    return $this->get_embedded_data("/reevoomark/embeddable_reviews?trkref={$trkref}&{$product_identifier}={$options[$product_identifier]}{$pagination_params}{$locale_param}{$sort_by_param}{$filter_param}", "X-Reevoo-ReviewCount", $options);
  }

  function offersWidget($options = array()) {
    if (!isset($options['sku']) && !isset($options['registration'])) {
      error_log('Need to provide a SKU or registration number for ReevooMark#productReviews');
      return;
    }
    $product_identifier = isset($options['sku']) ? 'sku' : 'registration';
    $trkref = $this->utils->getTrkref($options);
    return $this->get_embedded_data("/widgets/offers?trkref={$trkref}&{$product_identifier}={$options[$product_identifier]}", "X-Reevoo-OfferCount", $options);
  }

  function customerExperienceReviews($options = array()) {
    $trkref = $this->utils->getTrkref($options);
    $pagination_params = $this->utils->getPaginationParams($options);
    $locale_param = $this->utils->getLocaleParam($options);
    $sort_by_param = $this->utils->getSortByParam($options);
    $filter_param = $this->utils->getFilterParam($options);
    return $this->get_embedded_data("/reevoomark/embeddable_customer_experience_reviews?trkref={$trkref}{$pagination_params}{$locale_param}{$sort_by_param}{$filter_param}", "X-Reevoo-ReviewCount", $options);
  }

  function conversations($options = array()) {
    if (!isset($options['sku']) && !isset($options['registration'])) {
      error_log('Need to provide a SKU for ReevooMark#productReviews');
      return;
    }
    $product_identifier = isset($options['sku']) ? 'sku' : 'registration';
    $trkref = $this->utils->getTrkref($options);
    $locale_param = $this->utils->getLocaleParam($options);
    return $this->get_embedded_data("/reevoomark/embeddable_conversations?trkref={$trkref}&{$product_identifier}={$options[$product_identifier]}{$locale_param}", "X-Reevoo-ConversationCount", $options);
  }

  function purchaseTrackingEvent($options = array()) {
    $trkref = $this->utils->getTrkref($options);
    if (!$options['skus']) {
      error_log('Need to provide the list of skus purchased for ReevooMark#purchaseTrackingEvent');
      return;
    }
    $skus = $options['skus'];
    $value = $options['value'];
    return $this->return_tracking_script($trkref, "retailer.track_purchase(\"{$skus}\".split(/[ ,]+/), \"{$value}\");");
  }

  function propensityToBuyTrackingEvent($options = array()) {
    $trkref = $this->utils->getTrkref($options);
    $action = $options['action'];
    $sku = $options['sku'];
    if (!$sku) {
      $sku = "Global CTA";
    }
    return $this->return_tracking_script($trkref, "retailer.Tracking.ga_track_event(\"Propensity to buy\", \"{$action}\", \"{$sku}\");retailer.track_exit();");
  }

  function javascriptAssets() {
    return <<<EOT
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


  private function get_embedded_data($embedded_data_url, $count_header, $options) {
    $showEmptyMessage = array_key_exists('showEmptyMessage', $options) ? $options['showEmptyMessage'] : true;
    $data = $this->http_client->getData($embedded_data_url);
    $notEmpty = !!($data->header($count_header));
    if ($notEmpty || $showEmptyMessage) {
      $content = $data->body();
    } else {
      $content = NULL;
    }
    return array("notEmpty" => $notEmpty, "content" => $content);
  }


  private function return_tracking_script($trkref, $tracking_call) {
    return <<<EOT
<script type="text/javascript" charset="utf-8">
if (typeof afterReevooMarkLoaded === 'undefined') {
  var afterReevooMarkLoaded = [];
}
afterReevooMarkLoaded.push(
  function(){
    ReevooApi.load('{$trkref}', function(retailer){
      {$tracking_call}
    });
  }
);
</script>
EOT;
  }

}
